<?php

declare(strict_types=1);

namespace App\Notifications\Channels;

use App\Models\User;
use App\Services\Telegram\TelegramClient;
use Illuminate\Notifications\Notification;

class TelegramChannel
{
    public function __construct(private readonly TelegramClient $client) {}

    public function send(object $notifiable, Notification $notification): void
    {
        if (! method_exists($notification, 'toTelegram')) {
            return;
        }

        if (! $notifiable instanceof User || $notifiable->telegram_chat_id === null) {
            return;
        }

        $payload = $notification->toTelegram($notifiable);

        if (is_string($payload)) {
            $payload = ['text' => $payload];
        }

        $text = (string) ($payload['text'] ?? '');
        if ($text === '') {
            return;
        }

        $extra = $payload;
        unset($extra['text']);

        $this->client->sendMessage((string) $notifiable->telegram_chat_id, $text, $extra);
    }
}
