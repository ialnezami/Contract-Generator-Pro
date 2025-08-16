<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Template\StoreTemplateRequest;
use App\Http\Requests\Template\UpdateTemplateRequest;
use App\Models\ContractTemplate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TemplateController extends Controller
{
    /**
     * Display a listing of templates.
     */
    public function index(Request $request): JsonResponse
    {
        $query = ContractTemplate::with(['user', 'variables'])
            ->where('is_active', true);

        // Show public templates or user's own templates
        $user = Auth::user();
        if ($user) {
            $query->where(function($q) use ($user) {
                $q->where('is_public', true)
                  ->orWhere('user_id', $user->id);
            });
        } else {
            $query->where('is_public', true);
        }

        // Apply filters
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('tags', 'like', "%{$search}%");
            });
        }

        // Apply sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        if ($sortBy === 'popular') {
            $query->orderBy('usage_count', 'desc');
        } elseif ($sortBy === 'rating') {
            $query->orderBy('rating', 'desc');
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }

        $templates = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'templates' => $templates->items(),
            'pagination' => [
                'current_page' => $templates->currentPage(),
                'last_page' => $templates->lastPage(),
                'per_page' => $templates->perPage(),
                'total' => $templates->total(),
            ],
        ]);
    }

    /**
     * Store a newly created template.
     */
    public function store(StoreTemplateRequest $request): JsonResponse
    {
        $user = Auth::user();
        
        $template = ContractTemplate::create([
            'name' => $request->name,
            'description' => $request->description,
            'content' => $request->content,
            'category' => $request->category,
            'is_public' => $request->is_public ?? false,
            'is_active' => true,
            'user_id' => $user->id,
            'version' => '1.0',
            'tags' => $request->tags ?? [],
            'variables_schema' => $request->variables_schema ?? [],
        ]);

        // Create template variables if provided
        if ($request->has('variables')) {
            foreach ($request->variables as $variable) {
                $template->variables()->create([
                    'name' => $variable['name'],
                    'type' => $variable['type'],
                    'label' => $variable['label'],
                    'required' => $variable['required'] ?? false,
                    'default_value' => $variable['default_value'] ?? null,
                    'options' => $variable['options'] ?? null,
                ]);
            }
        }

        return response()->json([
            'message' => 'Template created successfully',
            'template' => $template->load(['user', 'variables']),
        ], 201);
    }

    /**
     * Display the specified template.
     */
    public function show(ContractTemplate $template): JsonResponse
    {
        $user = Auth::user();
        
        // Check if template is accessible
        if (!$template->is_public && (!$user || $template->user_id !== $user->id)) {
            return response()->json([
                'message' => 'Template not found or not accessible',
            ], 404);
        }

        // Increment usage count if user is authenticated
        if ($user && $template->user_id !== $user->id) {
            $template->incrementUsage();
        }

        return response()->json([
            'template' => $template->load(['user', 'variables']),
        ]);
    }

    /**
     * Update the specified template.
     */
    public function update(UpdateTemplateRequest $request, ContractTemplate $template): JsonResponse
    {
        $user = Auth::user();
        
        if ($template->user_id !== $user->id && !$user->isAdmin()) {
            return response()->json([
                'message' => 'Unauthorized access to template',
            ], 403);
        }

        $template->update($request->validated());

        // Update template variables if provided
        if ($request->has('variables')) {
            // Delete existing variables
            $template->variables()->delete();
            
            // Create new variables
            foreach ($request->variables as $variable) {
                $template->variables()->create([
                    'name' => $variable['name'],
                    'type' => $variable['type'],
                    'label' => $variable['label'],
                    'required' => $variable['required'] ?? false,
                    'default_value' => $variable['default_value'] ?? null,
                    'options' => $variable['options'] ?? null,
                ]);
            }
        }

        return response()->json([
            'message' => 'Template updated successfully',
            'template' => $template->fresh()->load(['user', 'variables']),
        ]);
    }

    /**
     * Remove the specified template.
     */
    public function destroy(ContractTemplate $template): JsonResponse
    {
        $user = Auth::user();
        
        if ($template->user_id !== $user->id && !$user->isAdmin()) {
            return response()->json([
                'message' => 'Unauthorized access to template',
            ], 403);
        }

        // Check if template is being used
        if ($template->contracts()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete template that is being used by contracts',
            ], 400);
        }

        $template->delete();

        return response()->json([
            'message' => 'Template deleted successfully',
        ]);
    }

    /**
     * Get template categories.
     */
    public function categories(): JsonResponse
    {
        $categories = ContractTemplate::where('is_active', true)
            ->distinct()
            ->pluck('category')
            ->filter()
            ->values();

        return response()->json([
            'categories' => $categories,
        ]);
    }

    /**
     * Get popular templates.
     */
    public function popular(): JsonResponse
    {
        $templates = ContractTemplate::with(['user'])
            ->where('is_active', true)
            ->where('is_public', true)
            ->popular()
            ->limit(10)
            ->get();

        return response()->json([
            'templates' => $templates,
        ]);
    }

    /**
     * Get highly rated templates.
     */
    public function highlyRated(): JsonResponse
    {
        $templates = ContractTemplate::with(['user'])
            ->where('is_active', true)
            ->where('is_public', true)
            ->highlyRated()
            ->limit(10)
            ->get();

        return response()->json([
            'templates' => $templates,
        ]);
    }

    /**
     * Clone a template.
     */
    public function clone(ContractTemplate $template): JsonResponse
    {
        $user = Auth::user();
        
        if (!$template->is_public && $template->user_id !== $user->id) {
            return response()->json([
                'message' => 'Template not accessible for cloning',
            ], 403);
        }

        $clonedTemplate = $template->replicate();
        $clonedTemplate->user_id = $user->id;
        $clonedTemplate->name = $template->name . ' (Copy)';
        $clonedTemplate->is_public = false;
        $clonedTemplate->usage_count = 0;
        $clonedTemplate->rating = null;
        $clonedTemplate->save();

        // Clone variables
        foreach ($template->variables as $variable) {
            $clonedVariable = $variable->replicate();
            $clonedVariable->template_id = $clonedTemplate->id;
            $clonedVariable->save();
        }

        return response()->json([
            'message' => 'Template cloned successfully',
            'template' => $clonedTemplate->load(['user', 'variables']),
        ], 201);
    }
}
