<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Contract;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReviewRequestNotification extends Notification
{
    use PreferenceAware, Queueable;

    protected ?string $category = 'reviews';

    public function __construct(public readonly Contract $contract) {}

    public function via(object $notifiable): array
    {
        return $this->channelsFor($notifiable);
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('app.notifications.review_request.subject'))
            ->line(__('app.notifications.review_request.body'))
            ->action(__('app.notifications.cta_open'), route('contracts.show', $this->contract));
    }

    public function toArray(object $notifiable): array
    {
        return ['contract_id' => $this->contract->id];
    }
}
