<?php

use App\Models\User;
use App\Services\TwoFactor\TwoFactorService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $code = '';

    public bool $useRecovery = false;

    public function mount(): void
    {
        if (! session()->has('auth.2fa.user_id')) {
            $this->redirect(route('login'), navigate: true);
        }
    }

    public function toggleRecovery(): void
    {
        $this->useRecovery = ! $this->useRecovery;
        $this->code = '';
    }

    public function verify(TwoFactorService $svc): void
    {
        $userId = session('auth.2fa.user_id');
        $remember = (bool) session('auth.2fa.remember', false);

        if (! $userId) {
            $this->redirect(route('login'), navigate: true);

            return;
        }

        $user = User::find($userId);
        if ($user === null || ! $user->hasTwoFactorEnabled()) {
            $this->redirect(route('login'), navigate: true);

            return;
        }

        $this->validate([
            'code' => $this->useRecovery ? ['required', 'string', 'min:6'] : ['required', 'digits:6'],
        ]);

        $ok = $this->useRecovery
            ? $svc->consumeRecoveryCode($user, $this->code)
            : $svc->verify((string) $user->two_factor_secret, $this->code);

        if (! $ok) {
            throw ValidationException::withMessages(['code' => __('app.two_factor.invalid_code')]);
        }

        Auth::login($user, $remember);

        session()->forget(['auth.2fa.user_id', 'auth.2fa.remember']);
        Session::regenerate();

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div class="w-full max-w-md mx-auto">
    <h1 class="text-xl font-semibold text-gray-800 mb-2">{{ __('app.two_factor.challenge_title') }}</h1>
    <p class="text-sm text-gray-500 mb-6">
        {{ $useRecovery ? __('app.two_factor.use_recovery') : __('app.two_factor.enter_code') }}
    </p>

    <form wire:submit="verify" class="space-y-4">
        <div>
            <x-input-label :value="$useRecovery ? __('app.two_factor.recovery_code_label') : __('app.two_factor.code_label')" />
            <x-text-input
                wire:model="code"
                class="block w-full mt-1 text-center {{ $useRecovery ? 'font-mono' : 'tracking-widest text-lg' }}"
                type="text"
                inputmode="{{ $useRecovery ? 'text' : 'numeric' }}"
                maxlength="{{ $useRecovery ? 32 : 6 }}"
                required
                autofocus />
            <x-input-error :messages="$errors->get('code')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between">
            <button type="button" wire:click="toggleRecovery" class="text-sm text-blue-600 hover:underline">
                {{ $useRecovery ? __('app.two_factor.use_app_code') : __('app.two_factor.use_recovery_code') }}
            </button>
            <x-primary-button class="bg-blue-600 hover:bg-blue-700">{{ __('app.two_factor.verify') }}</x-primary-button>
        </div>
    </form>
</div>
