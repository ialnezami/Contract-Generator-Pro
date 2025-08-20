<?php

namespace App\Services;

use App\Models\ContractTemplate;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class TemplateManagementService
{
    /**
     * Get templates with advanced filtering and search
     */
    public function getTemplates(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = ContractTemplate::query()
            ->with(['category', 'variables', 'user'])
            ->where('is_public', true);

        // Apply filters
        $this->applyFilters($query, $filters);

        // Apply search
        if (isset($filters['search']) && !empty($filters['search'])) {
            $this->applySearch($query, $filters['search']);
        }

        // Apply sorting
        $this->applySorting($query, $filters['sort'] ?? 'created_at', $filters['order'] ?? 'desc');

        return $query->paginate($perPage);
    }

    /**
     * Get popular templates
     */
    public function getPopularTemplates(int $limit = 10): Collection
    {
        return ContractTemplate::query()
            ->with(['category', 'variables'])
            ->where('is_public', true)
            ->orderBy('usage_count', 'desc')
            ->orderBy('rating', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get highly rated templates
     */
    public function getHighlyRatedTemplates(int $limit = 10): Collection
    {
        return ContractTemplate::query()
            ->with(['category', 'variables'])
            ->where('is_public', true)
            ->where('rating', '>=', 4.0)
            ->orderBy('rating', 'desc')
            ->orderBy('rating_count', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get templates by category
     */
    public function getTemplatesByCategory(string $categorySlug, int $perPage = 15): LengthAwarePaginator
    {
        return ContractTemplate::query()
            ->with(['category', 'variables', 'user'])
            ->whereHas('category', function (Builder $query) use ($categorySlug) {
                $query->where('slug', $categorySlug);
            })
            ->where('is_public', true)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get template categories
     */
    public function getCategories(): Collection
    {
        return \App\Models\TemplateCategory::query()
            ->withCount('templates')
            ->orderBy('name')
            ->get();
    }

    /**
     * Clone a template
     */
    public function cloneTemplate(ContractTemplate $template, int $userId): ContractTemplate
    {
        $clonedTemplate = $template->replicate();
        $clonedTemplate->user_id = $userId;
        $clonedTemplate->title = $template->title . ' (Copy)';
        $clonedTemplate->is_public = false;
        $clonedTemplate->usage_count = 0;
        $clonedTemplate->rating = 0;
        $clonedTemplate->rating_count = 0;
        $clonedTemplate->save();

        // Clone template variables
        foreach ($template->variables as $variable) {
            $clonedVariable = $variable->replicate();
            $clonedVariable->template_id = $clonedTemplate->id;
            $clonedVariable->save();
        }

        return $clonedTemplate;
    }

    /**
     * Rate a template
     */
    public function rateTemplate(ContractTemplate $template, int $rating, int $userId): array
    {
        // Check if user has already rated this template
        $existingRating = \App\Models\TemplateRating::where('template_id', $template->id)
            ->where('user_id', $userId)
            ->first();

        if ($existingRating) {
            // Update existing rating
            $existingRating->update(['rating' => $rating]);
        } else {
            // Create new rating
            \App\Models\TemplateRating::create([
                'template_id' => $template->id,
                'user_id' => $userId,
                'rating' => $rating,
            ]);
        }

        // Recalculate template rating
        $this->recalculateTemplateRating($template);

        return [
            'current_rating' => $template->fresh()->rating,
            'rating_count' => $template->fresh()->rating_count,
        ];
    }

    /**
     * Increment template usage count
     */
    public function incrementUsageCount(ContractTemplate $template): void
    {
        $template->increment('usage_count');
    }

    /**
     * Get template statistics
     */
    public function getTemplateStatistics(): array
    {
        return [
            'total_templates' => ContractTemplate::count(),
            'public_templates' => ContractTemplate::where('is_public', true)->count(),
            'private_templates' => ContractTemplate::where('is_public', false)->count(),
            'total_usage' => ContractTemplate::sum('usage_count'),
            'average_rating' => ContractTemplate::where('rating_count', '>', 0)->avg('rating'),
            'top_category' => \App\Models\TemplateCategory::withCount('templates')
                ->orderBy('templates_count', 'desc')
                ->first(),
        ];
    }

    /**
     * Apply filters to query
     */
    private function applyFilters(Builder $query, array $filters): void
    {
        // Category filter
        if (isset($filters['category_id']) && !empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        // Type filter
        if (isset($filters['type']) && !empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        // Rating filter
        if (isset($filters['min_rating']) && is_numeric($filters['min_rating'])) {
            $query->where('rating', '>=', $filters['min_rating']);
        }

        // Usage filter
        if (isset($filters['min_usage']) && is_numeric($filters['min_usage'])) {
            $query->where('usage_count', '>=', $filters['min_usage']);
        }

        // Date range filter
        if (isset($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }
        if (isset($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        // User filter
        if (isset($filters['user_id']) && !empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }
    }

    /**
     * Apply search to query
     */
    private function applySearch(Builder $query, string $search): void
    {
        $query->where(function (Builder $q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhere('content', 'like', "%{$search}%")
              ->orWhereHas('category', function (Builder $categoryQuery) use ($search) {
                  $categoryQuery->where('name', 'like', "%{$search}%");
              });
        });
    }

    /**
     * Apply sorting to query
     */
    private function applySorting(Builder $query, string $sortBy, string $order): void
    {
        $allowedSortFields = [
            'title', 'created_at', 'updated_at', 'rating', 'usage_count', 'price'
        ];

        if (in_array($sortBy, $allowedSortFields)) {
            $query->orderBy($sortBy, $order);
        } else {
            $query->orderBy('created_at', 'desc');
        }
    }

    /**
     * Recalculate template rating
     */
    private function recalculateTemplateRating(ContractTemplate $template): void
    {
        $ratings = \App\Models\TemplateRating::where('template_id', $template->id)->get();
        
        if ($ratings->count() > 0) {
            $averageRating = $ratings->avg('rating');
            $template->update([
                'rating' => round($averageRating, 2),
                'rating_count' => $ratings->count(),
            ]);
        }
    }

    /**
     * Get templates for user dashboard
     */
    public function getUserTemplates(int $userId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = ContractTemplate::query()
            ->with(['category', 'variables'])
            ->where('user_id', $userId);

        // Apply filters
        $this->applyFilters($query, $filters);

        // Apply search
        if (isset($filters['search']) && !empty($filters['search'])) {
            $this->applySearch($query, $filters['search']);
        }

        // Apply sorting
        $this->applySorting($query, $filters['sort'] ?? 'created_at', $filters['order'] ?? 'desc');

        return $query->paginate($perPage);
    }

    /**
     * Get template suggestions based on user preferences
     */
    public function getTemplateSuggestions(int $userId, int $limit = 5): Collection
    {
        // Get user's most used categories
        $userCategories = ContractTemplate::where('user_id', $userId)
            ->with('category')
            ->get()
            ->pluck('category.name')
            ->filter()
            ->countBy()
            ->sortDesc()
            ->keys()
            ->take(3);

        // Get templates from user's preferred categories
        return ContractTemplate::query()
            ->with(['category', 'variables'])
            ->where('is_public', true)
            ->whereIn('category_id', function ($query) use ($userCategories) {
                $query->select('id')
                    ->from('template_categories')
                    ->whereIn('name', $userCategories);
            })
            ->orderBy('rating', 'desc')
            ->limit($limit)
            ->get();
    }
}
