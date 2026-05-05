<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Proposal;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewProposalNotification extends Notification
{
    use PreferenceAware, Queueable;

    protected ?string $category = 'proposals';

    public function __construct(public readonly Proposal $proposal) {}

    public function via(object $notifiable): array
    {
        return $this->channelsFor($notifiable);
    }

    public function toTelegram(object $notifiable): array
    {
        return ['text' => '🆕 '.__('app.notifications.new_proposal.body', ['task' => $this->proposal->task?->title ?? ''])
            ."\n".url('/tasks/'.($this->proposal->task?->slug ?? ''))];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('app.notifications.new_proposal.subject'))
            ->line(__('app.notifications.new_proposal.body', ['task' => $this->proposal->task?->title ?? '']))
            ->action(__('app.notifications.cta_open'), route('contracts.proposals.task', ['task' => $this->proposal->task?->slug]));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'proposal_id' => $this->proposal->id,
            'task_id' => $this->proposal->task_id,
            'seller_id' => $this->proposal->seller_id,
        ];
    }
}
