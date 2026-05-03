<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Enums\UserMode;
use App\Models\SellerProfile;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ModeSwitcher extends Component
{
    public function switch(string $mode): void
    {
        $target = UserMode::tryFrom($mode) ?? UserMode::Buyer;

        $user = Auth::user();
        abort_unless($user !== null, 403);

        if ($target === UserMode::Seller && ! $user->sellerProfile) {
            session()->put('mode_after_onboarding', $target->value);
            $this->redirect(route('onboarding.seller'), navigate: true);

            return;
        }

        if ($target === UserMode::Seller && ! $user->sellerProfile) {
            SellerProfile::create(['user_id' => $user->id]);
        }

        $user->forceFill(['current_mode' => $target])->save();
        session()->put('current_mode', $target->value);

        $this->redirect(route('dashboard'), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.mode-switcher', [
            'currentMode' => Auth::user()?->current_mode ?? UserMode::Buyer,
        ]);
    }
}
