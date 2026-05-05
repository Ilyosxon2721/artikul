<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Proposal;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProposalRejectedNotification extends Notification
{
    use PreferenceAware, Queueable;

    protected ?string $category = 'proposals';

    public function __construct(public readonly Proposal $proposal) {}

    public function via(object $notifiable): array
    {
        return $this->channelsFor($notifiable);
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('app.notifications.proposal_rejected.subject'))
            ->line(__('app.notifications.proposal_rejected.body', ['task' => $this->proposal->task?->title ?? '']));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'proposal_id' => $this->proposal->id,
            'task_id' => $this->proposal->task_id,
        ];
    }
}
