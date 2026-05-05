<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeNotification extends Notification
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('app.email.welcome.subject'))
            ->greeting(__('app.email.welcome.greeting', ['name' => $notifiable->name]))
            ->line(__('app.email.welcome.body'))
            ->action(__('app.email.welcome.cta'), url('/dashboard'))
            ->line(__('app.email.welcome.footer'));
    }
}
