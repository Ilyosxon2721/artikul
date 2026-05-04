<?php

declare(strict_types=1);

namespace App\Http\Controllers\Telegram;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Telegram\TelegramClient;
use App\Services\Telegram\TelegramLinkService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function __invoke(
        Request $request,
        TelegramClient $client,
        TelegramLinkService $links,
    ): JsonResponse {
        $update = $request->all();

        $message = $update['message'] ?? null;
        if (! is_array($message)) {
            return response()->json(['ok' => true]);
        }

        $chatId = (string) ($message['chat']['id'] ?? '');
        $text = trim((string) ($message['text'] ?? ''));

        if ($chatId === '') {
            return response()->json(['ok' => true]);
        }

        if (str_starts_with($text, '/start')) {
            $payload = trim(mb_substr($text, 6));
            if ($payload !== '') {
                $user = $links->consumeCode($payload, $chatId);
                if ($user !== null) {
                    $client->sendMessage($chatId, __('app.telegram.linked', ['name' => $user->name]));

                    return response()->json(['ok' => true]);
                }
            }

            $client->sendMessage($chatId, __('app.telegram.welcome'));
        } elseif (str_starts_with($text, '/link')) {
            $code = trim(mb_substr($text, 5));
            if ($code === '') {
                $client->sendMessage($chatId, __('app.telegram.link_usage'));
            } else {
                $user = $links->consumeCode($code, $chatId);
                $client->sendMessage($chatId, $user !== null
                    ? __('app.telegram.linked', ['name' => $user->name])
                    : __('app.telegram.link_failed'));
            }
        } elseif ($text === '/unlink') {
            $user = User::query()->where('telegram_chat_id', $chatId)->first();
            if ($user) {
                $links->unlink($user);
            }
            $client->sendMessage($chatId, __('app.telegram.unlinked'));
        } elseif ($text === '/help') {
            $client->sendMessage($chatId, __('app.telegram.help'));
        } else {
            $client->sendMessage($chatId, __('app.telegram.unknown'));
        }

        return response()->json(['ok' => true]);
    }
}
