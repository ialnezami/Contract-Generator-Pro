<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TemplateRating extends Model
{
    use HasFactory;

    protected $fillable = [
        'template_id',
        'user_id',
        'rating',
        'comment',
        'is_verified',
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_verified' => 'boolean',
    ];

    /**
     * Get the template that was rated
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(ContractTemplate::class, 'template_id');
    }

    /**
     * Get the user who gave the rating
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get verified ratings only
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope to get ratings by specific user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to get ratings for specific template
     */
    public function scopeForTemplate($query, $templateId)
    {
        return $query->where('template_id', $templateId);
    }

    /**
     * Get the rating as stars (1-5)
     */
    public function getStarsAttribute(): string
    {
        return str_repeat('★', $this->rating) . str_repeat('☆', 5 - $this->rating);
    }

    /**
     * Check if rating is positive (4-5 stars)
     */
    public function isPositive(): bool
    {
        return $this->rating >= 4;
    }

    /**
     * Check if rating is negative (1-2 stars)
     */
    public function isNegative(): bool
    {
        return $this->rating <= 2;
    }

    /**
     * Check if rating is neutral (3 stars)
     */
    public function isNeutral(): bool
    {
        return $this->rating == 3;
    }
}
