<?php

namespace App\Notifications;

use App\Models\Contract;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ContractStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Contract $contract,
        public string $oldStatus,
        public string $newStatus,
        public User $changedBy
    ) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Contract Status Changed: {$this->contract->title}")
            ->line("The status of your contract '{$this->contract->title}' has been changed.")
            ->line("Previous Status: {$this->oldStatus}")
            ->line("New Status: {$this->newStatus}")
            ->line("Changed by: {$this->changedBy->name}")
            ->action('View Contract', url("/contracts/{$this->contract->id}"))
            ->line('Thank you for using Contract Generator Pro!');
    }

    public function toArray($notifiable): array
    {
        return [
            'contract_id' => $this->contract->id,
            'contract_title' => $this->contract->title,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'changed_by' => $this->changedBy->id,
            'changed_by_name' => $this->changedBy->name,
            'message' => "Contract '{$this->contract->title}' status changed from {$this->oldStatus} to {$this->newStatus}",
        ];
    }
}
