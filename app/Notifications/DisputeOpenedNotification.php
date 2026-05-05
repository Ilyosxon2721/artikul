<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Dispute;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DisputeOpenedNotification extends Notification
{
    use Queueable;

    public function __construct(public readonly Dispute $dispute) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('app.notifications.dispute_opened.subject'))
            ->line(__('app.notifications.dispute_opened.body'));
    }

    public function toArray(object $notifiable): array
    {
        return ['dispute_id' => $this->dispute->id];
    }
}
