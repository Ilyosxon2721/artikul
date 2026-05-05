<?php

declare(strict_types=1);

namespace App\Livewire\Chat;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Services\Chat\MessagingService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class ChatPage extends Component
{
    public ?int $activeConversationId = null;

    public string $body = '';

    public string $search = '';

    public function mount(?int $conversation = null): void
    {
        $this->activeConversationId = $conversation;
    }

    public function open(int $conversationId): void
    {
        $this->activeConversationId = $conversationId;
        $this->body = '';

        $conv = $this->activeConversation();
        if ($conv !== null && Auth::user() !== null) {
            app(MessagingService::class)->markRead($conv, Auth::user());
        }
    }

    public function send(MessagingService $messaging): void
    {
        $this->validate([
            'body' => ['required', 'string', 'min:1', 'max:4000'],
        ]);

        $conv = $this->activeConversation();
        $user = Auth::user();
        if ($conv === null || $user === null) {
            return;
        }

        $messaging->send($conv, $user, $this->body);
        $this->body = '';
    }

    /**
     * Dynamically subscribe to the active conversation's broadcast channel.
     * Skipped when no conversation is open.
     */
    public function getListeners(): array
    {
        if ($this->activeConversationId === null) {
            return [];
        }

        return [
            'echo-private:conversation.'.$this->activeConversationId.',MessageSent' => 'refreshMessages',
        ];
    }

    public function refreshMessages(): void
    {
        // Re-render via $messages computed in render().
    }

    public function render(): View
    {
        $user = Auth::user();
        abort_unless($user !== null, 403);

        $conversations = app(MessagingService::class)->listForUser($user);

        if ($this->search !== '') {
            $term = mb_strtolower($this->search);
            $conversations = $conversations->filter(function (Conversation $c) use ($term, $user) {
                /** @var User|null $other */
                $other = $c->participants->firstWhere('id', '!=', $user->id);

                return $other !== null && mb_stripos($other->name, $term) !== false;
            })->values();
        }

        $messages = collect();
        $other = null;
        $active = $this->activeConversation();
        if ($active !== null) {
            $messages = Message::query()
                ->where('conversation_id', $active->id)
                ->orderBy('created_at')
                ->get();
            $other = $active->participants->firstWhere('id', '!=', $user->id);
        }

        return view('livewire.chat.chat-page', [
            'conversations' => $conversations,
            'active' => $active,
            'other' => $other,
            'messages' => $messages,
            'me' => $user,
        ]);
    }

    private function activeConversation(): ?Conversation
    {
        if ($this->activeConversationId === null) {
            return null;
        }

        $user = Auth::user();
        if ($user === null) {
            return null;
        }

        return Conversation::query()
            ->with('participants')
            ->whereHas('participants', fn ($q) => $q->where('users.id', $user->id))
            ->find($this->activeConversationId);
    }
}
