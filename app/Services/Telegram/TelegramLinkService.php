<?php

declare(strict_types=1);

namespace App\Services\Telegram;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class TelegramLinkService
{
    private const CACHE_TTL_MINUTES = 15;

    public function issueCode(User $user): string
    {
        $code = Str::upper(Str::random(6));
        Cache::put($this->cacheKey($code), $user->id, now()->addMinutes(self::CACHE_TTL_MINUTES));

        return $code;
    }

    public function consumeCode(string $code, string $chatId): ?User
    {
        $userId = Cache::pull($this->cacheKey(mb_strtoupper(trim($code))));

        if ($userId === null) {
            return null;
        }

        $user = User::find($userId);
        if ($user === null) {
            return null;
        }

        $user->forceFill(['telegram_chat_id' => $chatId])->save();

        return $user;
    }

    public function unlink(User $user): void
    {
        $user->forceFill(['telegram_chat_id' => null])->save();
    }

    private function cacheKey(string $code): string
    {
        return 'tg.link.'.$code;
    }
}
