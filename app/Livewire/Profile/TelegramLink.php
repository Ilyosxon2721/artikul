<?php

declare(strict_types=1);

namespace App\Livewire\Profile;

use App\Services\Telegram\TelegramClient;
use App\Services\Telegram\TelegramLinkService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class TelegramLink extends Component
{
    public ?string $code = null;

    public function generate(TelegramLinkService $links): void
    {
        $user = Auth::user();
        abort_unless($user !== null, 403);
        $this->code = $links->issueCode($user);
    }

    public function unlink(TelegramLinkService $links): void
    {
        $user = Auth::user();
        if ($user !== null) {
            $links->unlink($user);
        }
    }

    public function render(): View
    {
        $user = Auth::user();

        return view('livewire.profile.telegram-link', [
            'isLinked' => $user?->telegram_chat_id !== null,
            'botUsername' => app(TelegramClient::class)->botUsername(),
            'deepLink' => $this->code !== null ? app(TelegramClient::class)->deepLink($this->code) : null,
        ]);
    }
}
