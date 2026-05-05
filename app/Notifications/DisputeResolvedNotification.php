<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Dispute;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DisputeResolvedNotification extends Notification
{
    use PreferenceAware, Queueable;

    protected ?string $category = 'disputes';

    public function __construct(public readonly Dispute $dispute) {}

    public function via(object $notifiable): array
    {
        return $this->channelsFor($notifiable);
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('app.notifications.dispute_resolved.subject'))
            ->line(__('app.notifications.dispute_resolved.body', ['status' => $this->dispute->status?->label() ?? '']));
    }

    public function toArray(object $notifiable): array
    {
        return ['dispute_id' => $this->dispute->id];
    }
}
