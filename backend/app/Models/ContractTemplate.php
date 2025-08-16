<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContractTemplate extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'content',
        'category',
        'is_public',
        'is_active',
        'user_id',
        'version',
        'tags',
        'variables_schema',
        'preview_image',
        'usage_count',
        'rating',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_public' => 'boolean',
        'is_active' => 'boolean',
        'tags' => 'array',
        'variables_schema' => 'array',
        'usage_count' => 'integer',
        'rating' => 'decimal:1',
        'metadata' => 'array',
    ];

    /**
     * Get the user that owns the template.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the contracts generated from this template.
     */
    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class, 'template_id');
    }

    /**
     * Get the template variables.
     */
    public function variables(): HasMany
    {
        return $this->hasMany(TemplateVariable::class, 'template_id');
    }

    /**
     * Scope a query to only include public templates.
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope a query to only include active templates.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include templates by category.
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope a query to only include popular templates.
     */
    public function scopePopular($query)
    {
        return $query->orderBy('usage_count', 'desc');
    }

    /**
     * Scope a query to only include highly rated templates.
     */
    public function scopeHighlyRated($query)
    {
        return $query->where('rating', '>=', 4.0);
    }

    /**
     * Increment the usage count.
     */
    public function incrementUsage(): void
    {
        $this->increment('usage_count');
    }

    /**
     * Get the template preview URL.
     */
    public function getPreviewUrlAttribute(): string
    {
        if ($this->preview_image) {
            return asset('storage/templates/' . $this->preview_image);
        }
        
        return asset('images/default-template.png');
    }
}
