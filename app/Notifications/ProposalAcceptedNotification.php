<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Contract;
use App\Models\Proposal;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProposalAcceptedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public readonly Proposal $proposal,
        public readonly Contract $contract,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('app.notifications.proposal_accepted.subject'))
            ->line(__('app.notifications.proposal_accepted.body', ['task' => $this->proposal->task?->title ?? '']))
            ->action(__('app.notifications.cta_open'), route('contracts.show', $this->contract));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'proposal_id' => $this->proposal->id,
            'contract_id' => $this->contract->id,
        ];
    }
}
