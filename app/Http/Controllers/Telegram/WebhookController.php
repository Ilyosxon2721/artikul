<?php

declare(strict_types=1);

namespace App\Http\Controllers\Telegram;

use App\Enums\ContractStatus;
use App\Enums\ProposalStatus;
use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\Proposal;
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
        } elseif ($text === '/tasks') {
            $this->handleTasks($client, $chatId);
        } elseif ($text === '/proposals') {
            $this->handleProposals($client, $chatId);
        } else {
            $client->sendMessage($chatId, __('app.telegram.unknown'));
        }

        return response()->json(['ok' => true]);
    }

    private function handleTasks(TelegramClient $client, string $chatId): void
    {
        $user = User::query()->where('telegram_chat_id', $chatId)->first();
        if ($user === null) {
            $client->sendMessage($chatId, __('app.telegram.not_linked'));

            return;
        }

        $contracts = Contract::query()
            ->whereIn('status', [ContractStatus::InProgress, ContractStatus::Submitted, ContractStatus::InReview, ContractStatus::Revision])
            ->where(fn ($q) => $q->where('buyer_id', $user->id)->orWhere('seller_id', $user->id))
            ->latest()
            ->limit(10)
            ->get();

        if ($contracts->isEmpty()) {
            $client->sendMessage($chatId, __('app.telegram.tasks_empty'));

            return;
        }

        $lines = ['<b>'.__('app.telegram.tasks_header').'</b>'];
        foreach ($contracts as $contract) {
            $lines[] = '• '.($contract->task?->title ?? '').' — '.url('/contracts/'.$contract->id);
        }
        $client->sendMessage($chatId, implode("\n", $lines));
    }

    private function handleProposals(TelegramClient $client, string $chatId): void
    {
        $user = User::query()->where('telegram_chat_id', $chatId)->first();
        if ($user === null) {
            $client->sendMessage($chatId, __('app.telegram.not_linked'));

            return;
        }

        $proposals = Proposal::query()
            ->where('seller_id', $user->id)
            ->where('status', ProposalStatus::Pending)
            ->with('task')
            ->latest()
            ->limit(10)
            ->get();

        if ($proposals->isEmpty()) {
            $client->sendMessage($chatId, __('app.telegram.proposals_empty'));

            return;
        }

        $lines = ['<b>'.__('app.telegram.proposals_header').'</b>'];
        foreach ($proposals as $proposal) {
            $lines[] = '• '.($proposal->task?->title ?? '').' — '.url('/tasks/'.($proposal->task?->slug ?? ''));
        }
        $client->sendMessage($chatId, implode("\n", $lines));
    }
}
