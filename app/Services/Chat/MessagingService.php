<?php

declare(strict_types=1);

namespace App\Services\Chat;

use App\Events\MessageSent;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class MessagingService
{
    public function __construct(private readonly ContactFilter $filter) {}

    /**
     * Find or create a 1:1 conversation between two users (optionally tied
     * to a task or contract).
     */
    public function openDirect(User $a, User $b, ?Task $task = null, ?int $contractId = null): Conversation
    {
        return DB::transaction(function () use ($a, $b, $task, $contractId): Conversation {
            $conversationId = DB::table('user_conversations as uca')
                ->join('user_conversations as ucb', 'uca.conversation_id', '=', 'ucb.conversation_id')
                ->where('uca.user_id', $a->id)
                ->where('ucb.user_id', $b->id)
                ->when($task !== null, fn ($q) => $q->join('conversations as c', 'c.id', '=', 'uca.conversation_id')->where('c.task_id', $task->id))
                ->value('uca.conversation_id');

            if ($conversationId !== null) {
                return Conversation::query()->findOrFail($conversationId);
            }

            $conversation = Conversation::create([
                'task_id' => $task?->id,
                'contract_id' => $contractId,
                'contact_filter_enabled' => $contractId === null,
            ]);

            $conversation->participants()->attach([$a->id, $b->id]);

            return $conversation;
        });
    }

    public function send(Conversation $conversation, User $author, string $body, array $attachments = []): Message
    {
        $body = trim($body);
        $reason = null;
        $isFiltered = false;

        if ($conversation->contact_filter_enabled) {
            $result = $this->filter->inspect($body);
            if ($result['matched']) {
                $isFiltered = true;
                $reason = $result['reason'];
                $body = __('app.chat.filtered_replacement');
            }
        }

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'user_id' => $author->id,
            'body' => $body,
            'attachments' => $attachments ?: null,
            'is_filtered' => $isFiltered,
            'filter_reason' => $reason,
        ]);

        $conversation->forceFill(['last_message_at' => $message->created_at])->save();

        broadcast(new MessageSent($message))->toOthers();

        return $message;
    }

    public function markRead(Conversation $conversation, User $user): void
    {
        $conversation->participants()
            ->updateExistingPivot($user->id, ['last_read_at' => now()]);

        Message::query()
            ->where('conversation_id', $conversation->id)
            ->where('user_id', '!=', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);
    }

    /**
     * Disable the contact filter once a deal is accepted.
     */
    public function unlock(Conversation $conversation, int $contractId): void
    {
        $conversation->forceFill([
            'contract_id' => $contractId,
            'contact_filter_enabled' => false,
        ])->save();
    }

    /**
     * @return Collection<int, Conversation>
     */
    public function listForUser(User $user, bool $archived = false): Collection
    {
        return Conversation::query()
            ->whereHas('participants', fn ($q) => $q->where('users.id', $user->id)->where('user_conversations.is_archived', $archived))
            ->with(['participants', 'lastMessage'])
            ->orderByDesc('last_message_at')
            ->get();
    }
}
