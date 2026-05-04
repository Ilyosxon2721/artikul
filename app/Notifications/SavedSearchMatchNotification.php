<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\SavedSearch;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

class SavedSearchMatchNotification extends Notification
{
    use Queueable;

    public function __construct(
        public readonly SavedSearch $savedSearch,
        public readonly Collection $matches,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject(__('app.notifications.saved_search.subject', ['name' => $this->savedSearch->name]))
            ->line(__('app.notifications.saved_search.body', ['count' => $this->matches->count()]));

        foreach ($this->matches->take(5) as $task) {
            $message->line('• '.$task->title);
        }

        return $message->action(__('app.notifications.cta_open'), route('tasks.index'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'saved_search_id' => $this->savedSearch->id,
            'matches' => $this->matches->map(fn ($t) => ['id' => $t->id, 'title' => $t->title])->all(),
        ];
    }
}
