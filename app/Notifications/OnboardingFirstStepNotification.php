<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OnboardingFirstStepNotification extends Notification
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('app.email.onboarding_step1.subject'))
            ->line(__('app.email.onboarding_step1.body'))
            ->action(__('app.email.onboarding_step1.cta'), url('/tasks/create'));
    }
}
