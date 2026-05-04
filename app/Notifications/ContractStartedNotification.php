<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Contract;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ContractStartedNotification extends Notification
{
    use Queueable;

    public function __construct(public readonly Contract $contract) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('app.notifications.contract_started.subject'))
            ->line(__('app.notifications.contract_started.body', ['task' => $this->contract->task?->title ?? '']))
            ->action(__('app.notifications.cta_open'), route('contracts.show', $this->contract));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'contract_id' => $this->contract->id,
        ];
    }
}
