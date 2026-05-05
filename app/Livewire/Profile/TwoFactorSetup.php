<?php

declare(strict_types=1);

namespace App\Livewire\Profile;

use App\Services\TwoFactor\TwoFactorService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class TwoFactorSetup extends Component
{
    public ?string $pendingSecret = null;

    public ?string $qrUrl = null;

    public string $code = '';

    public string $disablePassword = '';

    public function start(TwoFactorService $svc): void
    {
        $user = Auth::user();
        abort_unless($user !== null, 403);

        $this->pendingSecret = $svc->generateSecret();
        $this->qrUrl = $svc->qrUrl($user, $this->pendingSecret);
    }

    public function confirm(TwoFactorService $svc): void
    {
        $user = Auth::user();
        abort_unless($user !== null, 403);

        $this->validate([
            'code' => ['required', 'digits:6'],
            'pendingSecret' => ['required', 'string'],
        ]);

        if (! $svc->verify((string) $this->pendingSecret, $this->code)) {
            throw ValidationException::withMessages(['code' => __('app.two_factor.invalid_code')]);
        }

        $recovery = $svc->generateRecoveryCodes();

        $user->forceFill([
            'two_factor_secret' => $this->pendingSecret,
            'two_factor_recovery_codes' => $recovery,
            'two_factor_confirmed_at' => now(),
        ])->save();

        session()->flash('status', __('app.two_factor.enabled'));
        session()->flash('recovery_codes', $recovery);

        $this->reset(['pendingSecret', 'qrUrl', 'code']);
    }

    public function disable(): void
    {
        $user = Auth::user();
        abort_unless($user !== null, 403);

        $this->validate([
            'disablePassword' => ['required', 'current_password'],
        ]);

        $user->forceFill([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ])->save();

        session()->flash('status', __('app.two_factor.disabled'));
        $this->reset(['disablePassword']);
    }

    public function render(): View
    {
        $user = Auth::user();

        return view('livewire.profile.two-factor-setup', [
            'enabled' => $user !== null && $user->hasTwoFactorEnabled(),
            'recoveryCodes' => session('recovery_codes', []),
        ]);
    }
}
