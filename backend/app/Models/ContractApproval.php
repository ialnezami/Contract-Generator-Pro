<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContractApproval extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_id',
        'approver_id',
        'level',
        'required',
        'status',
        'requested_at',
        'requested_by',
        'due_date',
        'processed_at',
        'comments',
    ];

    protected $casts = [
        'required' => 'boolean',
        'requested_at' => 'datetime',
        'due_date' => 'datetime',
        'processed_at' => 'datetime',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->status === self::STATUS_PENDING && 
               $this->due_date && 
               $this->due_date->isPast();
    }

    public function approve(?string $comments = null): bool
    {
        return $this->update([
            'status' => self::STATUS_APPROVED,
            'comments' => $comments,
            'processed_at' => now(),
        ]);
    }

    public function reject(string $reason, ?string $comments = null): bool
    {
        return $this->update([
            'status' => self::STATUS_REJECTED,
            'comments' => $comments,
            'processed_at' => now(),
        ]);
    }
}
