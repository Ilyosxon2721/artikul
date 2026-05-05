<?php

declare(strict_types=1);

namespace App\Services\Telegram;

use Illuminate\Support\Facades\Cache;

/**
 * Lightweight in-memory state for short multi-step bot conversations.
 * Keyed by chat_id; lives 30 minutes.
 */
class TelegramConversationState
{
    private const TTL_MINUTES = 30;

    public function get(string $chatId): ?array
    {
        $value = Cache::get($this->key($chatId));

        return is_array($value) ? $value : null;
    }

    public function put(string $chatId, array $state): void
    {
        Cache::put($this->key($chatId), $state, now()->addMinutes(self::TTL_MINUTES));
    }

    public function clear(string $chatId): void
    {
        Cache::forget($this->key($chatId));
    }

    private function key(string $chatId): string
    {
        return 'tg.state.'.$chatId;
    }
}
