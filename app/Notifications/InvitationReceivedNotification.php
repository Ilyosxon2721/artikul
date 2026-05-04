<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Proposal;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvitationReceivedNotification extends Notification
{
    use Queueable;

    public function __construct(public readonly Proposal $proposal) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('app.notifications.invitation.subject'))
            ->line(__('app.notifications.invitation.body', ['task' => $this->proposal->task?->title ?? '']))
            ->action(__('app.notifications.cta_open'), route('tasks.show', $this->proposal->task?->slug));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'proposal_id' => $this->proposal->id,
            'task_id' => $this->proposal->task_id,
        ];
    }
}
