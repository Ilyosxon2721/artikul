<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\SavedSearch;
use App\Notifications\Channels\TelegramChannel;
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
        $channels = ['database'];

        $notifyVia = (array) ($this->savedSearch->notify_via ?? []);
        if (in_array('email', $notifyVia, true)) {
            $channels[] = 'mail';
        }
        if (in_array('telegram', $notifyVia, true)) {
            $channels[] = TelegramChannel::class;
        }

        return $channels;
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

    public function toTelegram(object $notifiable): array
    {
        $lines = ['🔔 <b>'.$this->savedSearch->name.'</b>'];
        $lines[] = __('app.notifications.saved_search.body', ['count' => $this->matches->count()]);

        foreach ($this->matches->take(5) as $task) {
            $lines[] = '• '.$task->title.' — '.url('/tasks/'.$task->slug);
        }

        return ['text' => implode("\n", $lines)];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'saved_search_id' => $this->savedSearch->id,
            'matches' => $this->matches->map(fn ($t) => ['id' => $t->id, 'title' => $t->title])->all(),
        ];
    }
}
