<?php

declare(strict_types=1);

namespace App\Http\Controllers\Telegram;

use App\Enums\ContractStatus;
use App\Enums\ProposalStatus;
use App\Enums\TaskStatus;
use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\Proposal;
use App\Models\Task;
use App\Models\User;
use App\Services\Contracts\ProposalService;
use App\Services\Telegram\TelegramClient;
use App\Services\Telegram\TelegramConversationState;
use App\Services\Telegram\TelegramLinkService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RuntimeException;

class WebhookController extends Controller
{
    public function __invoke(
        Request $request,
        TelegramClient $client,
        TelegramLinkService $links,
        TelegramConversationState $state,
        ProposalService $proposals,
    ): JsonResponse {
        $update = $request->all();

        // Inline button presses (Apply to task, etc.)
        $callback = $update['callback_query'] ?? null;
        if (is_array($callback)) {
            $this->handleCallback($callback, $client, $state);

            return response()->json(['ok' => true]);
        }

        $message = $update['message'] ?? null;
        if (! is_array($message)) {
            return response()->json(['ok' => true]);
        }

        $chatId = (string) ($message['chat']['id'] ?? '');
        $text = trim((string) ($message['text'] ?? ''));

        if ($chatId === '') {
            return response()->json(['ok' => true]);
        }

        // Mid-conversation (apply flow) handling
        $current = $state->get($chatId);
        if (is_array($current) && ! str_starts_with($text, '/')) {
            $this->handleConversation($chatId, $text, $current, $client, $state, $proposals);

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
        } elseif ($text === '/cancel') {
            $state->clear($chatId);
            $client->sendMessage($chatId, __('app.telegram.cancelled'));
        } elseif ($text === '/tasks') {
            $this->handleTasks($client, $chatId);
        } elseif ($text === '/proposals') {
            $this->handleProposals($client, $chatId);
        } else {
            $client->sendMessage($chatId, __('app.telegram.unknown'));
        }

        return response()->json(['ok' => true]);
    }

    private function handleCallback(array $callback, TelegramClient $client, TelegramConversationState $state): void
    {
        $chatId = (string) ($callback['message']['chat']['id'] ?? '');
        $data = (string) ($callback['data'] ?? '');

        if ($chatId === '' || ! str_starts_with($data, 'apply:')) {
            return;
        }

        $taskId = (int) substr($data, 6);
        $task = Task::query()->where('id', $taskId)->where('status', TaskStatus::Published)->first();
        if ($task === null) {
            $client->sendMessage($chatId, __('app.telegram.apply_task_unavailable'));

            return;
        }

        $user = User::query()->where('telegram_chat_id', $chatId)->first();
        if ($user === null) {
            $client->sendMessage($chatId, __('app.telegram.not_linked'));

            return;
        }

        $state->put($chatId, [
            'flow' => 'apply',
            'task_id' => $task->id,
            'step' => 'amount',
        ]);

        $client->sendMessage($chatId, __('app.telegram.apply_intro', ['title' => $task->title]));
        $client->sendMessage($chatId, __('app.telegram.apply_ask_amount'));
    }

    private function handleConversation(
        string $chatId,
        string $text,
        array $state,
        TelegramClient $client,
        TelegramConversationState $stateStore,
        ProposalService $proposals,
    ): void {
        if (($state['flow'] ?? '') !== 'apply') {
            $stateStore->clear($chatId);

            return;
        }

        switch ($state['step']) {
            case 'amount':
                $amount = (float) preg_replace('/[^\d\.]+/', '', $text);
                if ($amount <= 0) {
                    $client->sendMessage($chatId, __('app.telegram.apply_amount_invalid'));

                    return;
                }
                $state['amount'] = $amount;
                $state['step'] = 'days';
                $stateStore->put($chatId, $state);
                $client->sendMessage($chatId, __('app.telegram.apply_ask_days'));
                break;

            case 'days':
                $days = (int) preg_replace('/\D+/', '', $text);
                if ($days < 1 || $days > 365) {
                    $client->sendMessage($chatId, __('app.telegram.apply_days_invalid'));

                    return;
                }
                $state['days'] = $days;
                $state['step'] = 'cover';
                $stateStore->put($chatId, $state);
                $client->sendMessage($chatId, __('app.telegram.apply_ask_cover'));
                break;

            case 'cover':
                $cover = trim($text);
                if (mb_strlen($cover) < 30) {
                    $client->sendMessage($chatId, __('app.telegram.apply_cover_invalid'));

                    return;
                }

                $task = Task::find($state['task_id']);
                $user = User::query()->where('telegram_chat_id', $chatId)->first();

                if ($task === null || $user === null) {
                    $stateStore->clear($chatId);
                    $client->sendMessage($chatId, __('app.telegram.apply_failed'));

                    return;
                }

                try {
                    $proposals->create($task, $user, [
                        'cover_letter' => $cover,
                        'proposed_amount' => $state['amount'],
                        'currency' => $task->currency->value ?? 'UZS',
                        'rate_type' => 'fixed',
                        'proposed_deadline_days' => $state['days'],
                        'via_telegram' => true,
                    ]);
                    $client->sendMessage($chatId, __('app.telegram.apply_done', ['title' => $task->title]));
                } catch (RuntimeException $e) {
                    $client->sendMessage($chatId, __('app.telegram.apply_error', ['error' => $e->getMessage()]));
                }

                $stateStore->clear($chatId);
                break;

            default:
                $stateStore->clear($chatId);
        }
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
