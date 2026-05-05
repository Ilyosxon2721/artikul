<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\TaskMilestone;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MilestoneSubmittedNotification extends Notification
{
    use PreferenceAware, Queueable;

    protected ?string $category = 'contracts';

    public function __construct(public readonly TaskMilestone $milestone) {}

    public function via(object $notifiable): array
    {
        return $this->channelsFor($notifiable);
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('app.notifications.milestone_submitted.subject'))
            ->line(__('app.notifications.milestone_submitted.body', ['title' => $this->milestone->title]));
    }

    public function toArray(object $notifiable): array
    {
        return ['milestone_id' => $this->milestone->id];
    }
}
