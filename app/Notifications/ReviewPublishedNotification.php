<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReviewPublishedNotification extends Notification
{
    use Queueable;

    public function __construct(public readonly Review $review) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('app.notifications.review_published.subject'))
            ->line(__('app.notifications.review_published.body'));
    }

    public function toArray(object $notifiable): array
    {
        return ['review_id' => $this->review->id];
    }
}
