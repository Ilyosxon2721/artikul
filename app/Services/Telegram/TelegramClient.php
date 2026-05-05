<?php

declare(strict_types=1);

namespace App\Services\Telegram;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Tiny Telegram Bot API client. Skips network calls when no token is set
 * (local/testing) and only logs the payload.
 */
final class TelegramClient
{
    public function __construct(
        private readonly ?string $token,
        private readonly string $botUsername,
    ) {}

    public function botUsername(): string
    {
        return $this->botUsername;
    }

    public function isStub(): bool
    {
        return $this->token === null || $this->token === '';
    }

    public function sendMessage(string $chatId, string $text, array $extra = []): bool
    {
        if ($this->isStub()) {
            Log::info('[Telegram stub] sendMessage', ['chat_id' => $chatId, 'text' => $text, 'extra' => $extra]);

            return true;
        }

        $response = Http::asJson()->post("https://api.telegram.org/bot{$this->token}/sendMessage", array_merge([
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'HTML',
            'disable_web_page_preview' => true,
        ], $extra));

        return $response->successful();
    }

    public function deepLink(string $payload): string
    {
        return 'https://t.me/'.$this->botUsername.'?start='.urlencode($payload);
    }
}
