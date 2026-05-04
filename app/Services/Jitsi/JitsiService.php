<?php

declare(strict_types=1);

namespace App\Services\Jitsi;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Str;

class JitsiService
{
    /**
     * Generate a unique meet.jit.si room URL and post a system message in the
     * conversation with the link.
     */
    public function startCall(Conversation $conversation, User $initiator): string
    {
        $roomName = 'artikul-'.($conversation->contract_id ?? $conversation->id).'-'.Str::lower(Str::random(6));
        $url = 'https://meet.jit.si/'.$roomName;

        Message::create([
            'conversation_id' => $conversation->id,
            'user_id' => $initiator->id,
            'body' => __('app.jitsi.invitation', ['url' => $url]),
            'is_system' => true,
        ]);

        $conversation->forceFill(['last_message_at' => now()])->save();

        return $url;
    }
}
