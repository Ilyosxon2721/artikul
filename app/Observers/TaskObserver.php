<?php

declare(strict_types=1);

namespace App\Observers;

use App\Enums\TaskStatus;
use App\Models\Task;
use App\Services\Telegram\TelegramClient;

class TaskObserver
{
    public function updated(Task $task): void
    {
        $this->maybeBroadcastJob($task);
    }

    public function created(Task $task): void
    {
        $this->maybeBroadcastJob($task);
    }

    private function maybeBroadcastJob(Task $task): void
    {
        if (! $task->wasChanged('status') && ! $task->wasRecentlyCreated) {
            return;
        }

        if ($task->status !== TaskStatus::Published) {
            return;
        }

        $channel = (string) config('services.telegram.jobs_channel', '');
        if ($channel === '') {
            return;
        }

        $client = app(TelegramClient::class);

        $lines = [
            '🆕 <b>'.$task->title.'</b>',
            url('/tasks/'.$task->slug),
        ];

        if ($task->budget_min !== null) {
            $lines[] = '💰 '.number_format((float) $task->budget_min, 0, '.', ' ').' '.($task->currency?->value ?? '');
        }

        $client->sendMessage($channel, implode("\n", $lines));
    }
}
