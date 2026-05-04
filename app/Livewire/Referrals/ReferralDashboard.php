<?php

declare(strict_types=1);

namespace App\Livewire\Referrals;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class ReferralDashboard extends Component
{
    public function render(): View
    {
        $user = Auth::user();
        abort_unless($user !== null, 403);

        $referrals = User::query()
            ->where('referred_by', $user->id)
            ->orderByDesc('created_at')
            ->get(['id', 'name', 'email', 'created_at']);

        $referralUrl = url('/register?ref='.($user->referral_code ?? ''));

        return view('livewire.referrals.referral-dashboard', [
            'user' => $user,
            'referrals' => $referrals,
            'referralUrl' => $referralUrl,
        ]);
    }
}
