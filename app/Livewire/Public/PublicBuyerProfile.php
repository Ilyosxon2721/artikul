<?php

declare(strict_types=1);

namespace App\Livewire\Public;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class PublicBuyerProfile extends Component
{
    public User $user;

    public function mount(string $username): void
    {
        $this->user = User::query()
            ->where('username', $username)
            ->where('is_banned', false)
            ->whereNotNull('username')
            ->with('buyerProfile')
            ->firstOrFail();
    }

    public function render(): View
    {
        return view('livewire.public.public-buyer-profile');
    }
}
