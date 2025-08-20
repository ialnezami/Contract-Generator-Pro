<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContractStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_id',
        'status',
        'previous_status',
        'changed_by',
        'reason',
        'changed_at',
    ];

    protected $casts = [
        'changed_at' => 'datetime',
    ];

    /**
     * Get the contract that this status belongs to
     */
    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    /**
     * Get the user who changed the status
     */
    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    /**
     * Scope to get status changes for a specific contract
     */
    public function scopeForContract($query, $contractId)
    {
        return $query->where('contract_id', $contractId);
    }

    /**
     * Scope to get status changes by a specific user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('changed_by', $userId);
    }

    /**
     * Scope to get status changes within a date range
     */
    public function scopeInDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('changed_at', [$startDate, $endDate]);
    }

    /**
     * Get the status display name
     */
    public function getStatusDisplayNameAttribute(): string
    {
        $statusNames = [
            'draft' => 'Draft',
            'pending_review' => 'Pending Review',
            'under_review' => 'Under Review',
            'pending_approval' => 'Pending Approval',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'active' => 'Active',
            'suspended' => 'Suspended',
            'expired' => 'Expired',
            'terminated' => 'Terminated',
            'completed' => 'Completed',
            'renewed' => 'Renewed',
        ];

        return $statusNames[$this->status] ?? ucfirst(str_replace('_', ' ', $this->status));
    }

    /**
     * Get the previous status display name
     */
    public function getPreviousStatusDisplayNameAttribute(): string
    {
        if (!$this->previous_status) {
            return 'N/A';
        }

        $statusNames = [
            'draft' => 'Draft',
            'pending_review' => 'Pending Review',
            'under_review' => 'Under Review',
            'pending_approval' => 'Pending Approval',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'active' => 'Active',
            'suspended' => 'Suspended',
            'expired' => 'Expired',
            'terminated' => 'Terminated',
            'completed' => 'Completed',
            'renewed' => 'Renewed',
        ];

        return $statusNames[$this->previous_status] ?? ucfirst(str_replace('_', ' ', $this->previous_status));
    }

    /**
     * Check if this is a significant status change
     */
    public function getIsSignificantChangeAttribute(): bool
    {
        $significantTransitions = [
            'draft' => ['pending_review', 'under_review'],
            'pending_review' => ['under_review'],
            'under_review' => ['pending_approval', 'rejected'],
            'pending_approval' => ['approved', 'rejected'],
            'approved' => ['active'],
            'active' => ['suspended', 'expired', 'completed', 'terminated'],
            'expired' => ['renewed'],
        ];

        return isset($significantTransitions[$this->previous_status]) &&
               in_array($this->status, $significantTransitions[$this->previous_status]);
    }

    /**
     * Get the time elapsed since the status change
     */
    public function getTimeElapsedAttribute(): string
    {
        return $this->changed_at->diffForHumans();
    }

    /**
     * Get the status change summary
     */
    public function getSummaryAttribute(): string
    {
        $summary = "Status changed from {$this->previous_status_display_name} to {$this->status_display_name}";
        
        if ($this->reason) {
            $summary .= " - {$this->reason}";
        }
        
        return $summary;
    }
}
