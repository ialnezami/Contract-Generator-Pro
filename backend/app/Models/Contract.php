<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contract extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'content',
        'status',
        'template_id',
        'user_id',
        'generated_at',
        'expires_at',
        'total_value',
        'currency',
        'is_signed',
        'signed_at',
        'signed_by',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'generated_at' => 'datetime',
        'expires_at' => 'datetime',
        'signed_at' => 'datetime',
        'total_value' => 'decimal:2',
        'is_signed' => 'boolean',
        'metadata' => 'array',
    ];

    /**
     * Get the user that owns the contract.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the template used for this contract.
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(ContractTemplate::class, 'template_id');
    }

    /**
     * Get the parties involved in this contract.
     */
    public function parties(): HasMany
    {
        return $this->hasMany(ContractParty::class);
    }

    /**
     * Get the contract variables.
     */
    public function variables(): HasMany
    {
        return $this->hasMany(ContractVariable::class);
    }

    /**
     * Get the contract documents.
     */
    public function documents(): HasMany
    {
        return $this->hasMany(ContractDocument::class);
    }

    /**
     * Scope a query to only include active contracts.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include signed contracts.
     */
    public function scopeSigned($query)
    {
        return $query->where('is_signed', true);
    }

    /**
     * Scope a query to only include expired contracts.
     */
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', now());
    }

    /**
     * Get the contract status badge class.
     */
    public function getStatusBadgeClass(): string
    {
        return match($this->status) {
            'draft' => 'bg-gray-100 text-gray-800',
            'active' => 'bg-green-100 text-green-800',
            'expired' => 'bg-red-100 text-red-800',
            'cancelled' => 'bg-yellow-100 text-yellow-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}
