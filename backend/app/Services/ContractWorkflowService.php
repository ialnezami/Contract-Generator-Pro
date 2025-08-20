<?php

namespace App\Services;

use App\Models\Contract;
use App\Models\User;
use App\Models\ContractStatus;
use App\Models\ContractApproval;
use App\Models\ContractSignature;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ContractStatusChanged;
use App\Notifications\ContractApprovalRequested;
use App\Notifications\ContractExpiringSoon;
use App\Notifications\ContractRenewalReminder;
use Carbon\Carbon;

class ContractWorkflowService
{
    /**
     * Available contract statuses
     */
    private array $statuses = [
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

    /**
     * Status transitions and their requirements
     */
    private array $statusTransitions = [
        'draft' => ['pending_review', 'under_review'],
        'pending_review' => ['under_review', 'draft'],
        'under_review' => ['pending_approval', 'rejected', 'draft'],
        'pending_approval' => ['approved', 'rejected', 'under_review'],
        'approved' => ['active', 'draft'],
        'rejected' => ['draft'],
        'active' => ['suspended', 'expired', 'completed', 'terminated'],
        'suspended' => ['active', 'terminated'],
        'expired' => ['renewed', 'terminated'],
        'terminated' => ['draft'],
        'completed' => ['renewed', 'draft'],
        'renewed' => ['active', 'draft'],
    ];

    /**
     * Change contract status
     */
    public function changeStatus(Contract $contract, string $newStatus, User $user, ?string $reason = null): array
    {
        if (!$this->canTransitionTo($contract->status, $newStatus)) {
            throw new \InvalidArgumentException("Cannot transition from {$contract->status} to {$newStatus}");
        }

        $oldStatus = $contract->status;
        
        return DB::transaction(function () use ($contract, $newStatus, $user, $reason, $oldStatus) {
            // Update contract status
            $contract->update([
                'status' => $newStatus,
                'status_changed_at' => now(),
                'status_changed_by' => $user->id,
            ]);

            // Create status history record
            $contract->statusHistory()->create([
                'status' => $newStatus,
                'previous_status' => $oldStatus,
                'changed_by' => $user->id,
                'reason' => $reason,
                'changed_at' => now(),
            ]);

            // Handle status-specific actions
            $this->handleStatusChange($contract, $newStatus, $user);

            // Send notifications
            $this->sendStatusChangeNotifications($contract, $oldStatus, $newStatus, $user);

            return [
                'success' => true,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'message' => "Contract status changed from {$oldStatus} to {$newStatus}",
            ];
        });
    }

    /**
     * Request contract approval
     */
    public function requestApproval(Contract $contract, User $requester, array $approvers): array
    {
        return DB::transaction(function () use ($contract, $requester, $approvers) {
            // Change status to pending approval
            $this->changeStatus($contract, 'pending_approval', $requester);

            // Create approval requests for each approver
            foreach ($approvers as $approverData) {
                $approver = User::find($approverData['user_id']);
                if ($approver) {
                    ContractApproval::create([
                        'contract_id' => $contract->id,
                        'approver_id' => $approver->id,
                        'level' => $approverData['level'] ?? 1,
                        'required' => $approverData['required'] ?? true,
                        'status' => 'pending',
                        'requested_at' => now(),
                        'requested_by' => $requester->id,
                        'due_date' => $approverData['due_date'] ?? now()->addDays(7),
                    ]);

                    // Send approval request notification
                    $approver->notify(new ContractApprovalRequested($contract, $requester));
                }
            }

            return [
                'success' => true,
                'message' => 'Approval requests sent successfully',
                'approvers_count' => count($approvers),
            ];
        });
    }

    /**
     * Approve or reject contract
     */
    public function processApproval(Contract $contract, User $approver, string $decision, ?string $comments = null): array
    {
        $approval = ContractApproval::where('contract_id', $contract->id)
            ->where('approver_id', $approver->id)
            ->where('status', 'pending')
            ->first();

        if (!$approval) {
            throw new \InvalidArgumentException('No pending approval found for this approver');
        }

        return DB::transaction(function () use ($contract, $approver, $decision, $comments, $approval) {
            // Update approval record
            $approval->update([
                'status' => $decision,
                'comments' => $comments,
                'processed_at' => now(),
            ]);

            // Check if all required approvals are complete
            $this->checkApprovalCompletion($contract);

            return [
                'success' => true,
                'decision' => $decision,
                'message' => "Contract {$decision} by {$approver->name}",
            ];
        });
    }

    /**
     * Sign contract
     */
    public function signContract(Contract $contract, User $signer, string $signatureType = 'digital'): array
    {
        if ($contract->status !== 'approved') {
            throw new \InvalidArgumentException('Contract must be approved before signing');
        }

        return DB::transaction(function () use ($contract, $signer, $signatureType) {
            // Create signature record
            $signature = ContractSignature::create([
                'contract_id' => $contract->id,
                'signer_id' => $signer->id,
                'signature_type' => $signatureType,
                'signed_at' => now(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            // Update contract
            $contract->update([
                'is_signed' => true,
                'signed_at' => now(),
                'signed_by' => $signer->name,
                'status' => 'active',
            ]);

            // Create status change record
            $this->changeStatus($contract, 'active', $signer, 'Contract signed');

            return [
                'success' => true,
                'message' => 'Contract signed successfully',
                'signature_id' => $signature->id,
                'signed_at' => $signature->signed_at,
            ];
        });
    }

    /**
     * Handle contract expiration
     */
    public function handleExpiration(Contract $contract): array
    {
        if ($contract->expires_at && $contract->expires_at->isPast()) {
            return DB::transaction(function () use ($contract) {
                // Change status to expired
                $this->changeStatus($contract, 'expired', $contract->user, 'Contract expired automatically');

                // Send expiration notifications
                $this->sendExpirationNotifications($contract);

                return [
                    'success' => true,
                    'message' => 'Contract marked as expired',
                    'expired_at' => $contract->expires_at,
                ];
            });
        }

        return [
            'success' => false,
            'message' => 'Contract has not expired yet',
        ];
    }

    /**
     * Renew contract
     */
    public function renewContract(Contract $contract, User $user, array $renewalData): array
    {
        if (!in_array($contract->status, ['expired', 'completed'])) {
            throw new \InvalidArgumentException('Only expired or completed contracts can be renewed');
        }

        return DB::transaction(function () use ($contract, $user, $renewalData) {
            // Create new contract based on the expired one
            $newContract = $contract->replicate();
            $newContract->title = $renewalData['title'] ?? $contract->title . ' (Renewed)';
            $newContract->status = 'draft';
            $newContract->parent_contract_id = $contract->id;
            $newContract->renewed_at = now();
            $newContract->renewed_by = $user->id;
            $newContract->expires_at = $renewalData['expires_at'] ?? now()->addYear();
            $newContract->save();

            // Copy contract variables
            foreach ($contract->variables as $variable) {
                $newContract->variables()->create([
                    'name' => $variable->name,
                    'type' => $variable->type,
                    'value' => $renewalData['variables'][$variable->name] ?? $variable->value,
                ]);
            }

            // Copy contract parties
            foreach ($contract->parties as $party) {
                $newContract->parties()->create($party->toArray());
            }

            // Update original contract status
            $this->changeStatus($contract, 'renewed', $user, 'Contract renewed');

            // Change new contract status to approved if auto-approval is enabled
            if ($renewalData['auto_approve'] ?? false) {
                $this->changeStatus($newContract, 'approved', $user, 'Auto-approved renewal');
            }

            return [
                'success' => true,
                'message' => 'Contract renewed successfully',
                'new_contract_id' => $newContract->id,
                'original_contract_id' => $contract->id,
            ];
        });
    }

    /**
     * Get contract workflow history
     */
    public function getWorkflowHistory(Contract $contract): array
    {
        return [
            'status_history' => $contract->statusHistory()->with('changedBy')->orderBy('changed_at', 'desc')->get(),
            'approvals' => $contract->approvals()->with('approver')->orderBy('created_at', 'desc')->get(),
            'signatures' => $contract->signatures()->with('signer')->orderBy('signed_at', 'desc')->get(),
            'current_status' => $contract->status,
            'can_transition_to' => $this->getAvailableTransitions($contract->status),
        ];
    }

    /**
     * Check if status transition is allowed
     */
    private function canTransitionTo(string $currentStatus, string $newStatus): bool
    {
        return in_array($newStatus, $this->statusTransitions[$currentStatus] ?? []);
    }

    /**
     * Get available status transitions
     */
    private function getAvailableTransitions(string $currentStatus): array
    {
        return $this->statusTransitions[$currentStatus] ?? [];
    }

    /**
     * Handle status-specific actions
     */
    private function handleStatusChange(Contract $contract, string $newStatus, User $user): void
    {
        switch ($newStatus) {
            case 'active':
                $contract->update(['activated_at' => now()]);
                break;
            case 'completed':
                $contract->update(['completed_at' => now()]);
                break;
            case 'terminated':
                $contract->update(['terminated_at' => now()]);
                break;
        }
    }

    /**
     * Check if all required approvals are complete
     */
    private function checkApprovalCompletion(Contract $contract): void
    {
        $pendingApprovals = ContractApproval::where('contract_id', $contract->id)
            ->where('status', 'pending')
            ->where('required', true)
            ->count();

        if ($pendingApprovals === 0) {
            // All required approvals complete
            $this->changeStatus($contract, 'approved', $contract->user, 'All approvals received');
        }
    }

    /**
     * Send status change notifications
     */
    private function sendStatusChangeNotifications(Contract $contract, string $oldStatus, string $newStatus, User $user): void
    {
        // Notify contract owner
        $contract->user->notify(new ContractStatusChanged($contract, $oldStatus, $newStatus, $user));

        // Notify contract parties if status is active
        if ($newStatus === 'active') {
            foreach ($contract->parties as $party) {
                if ($party->email) {
                    // Send email notification to party
                    // Implementation depends on your email service
                }
            }
        }
    }

    /**
     * Send expiration notifications
     */
    private function sendExpirationNotifications(Contract $contract): void
    {
        // Notify contract owner
        $contract->user->notify(new ContractExpiringSoon($contract));

        // Notify contract parties
        foreach ($contract->parties as $party) {
            if ($party->email) {
                // Send email notification to party
                // Implementation depends on your email service
            }
        }
    }

    /**
     * Get all available statuses
     */
    public function getAvailableStatuses(): array
    {
        return $this->statuses;
    }

    /**
     * Get status transitions
     */
    public function getStatusTransitions(): array
    {
        return $this->statusTransitions;
    }

    /**
     * Check if contract needs renewal reminder
     */
    public function checkRenewalReminder(Contract $contract): bool
    {
        if (!$contract->expires_at || $contract->status !== 'active') {
            return false;
        }

        $daysUntilExpiry = now()->diffInDays($contract->expires_at, false);
        return $daysUntilExpiry <= 30 && $daysUntilExpiry > 0;
    }

    /**
     * Send renewal reminder
     */
    public function sendRenewalReminder(Contract $contract): void
    {
        if ($this->checkRenewalReminder($contract)) {
            $contract->user->notify(new ContractRenewalReminder($contract));
        }
    }
}
