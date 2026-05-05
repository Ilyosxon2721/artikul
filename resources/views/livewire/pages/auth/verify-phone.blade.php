<?php

use App\Enums\UserMode;
use App\Services\Auth\PhoneVerificationService;
use App\Services\Auth\UserRegistrar;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $code = '';

    public string $phone = '';

    public function mount(): void
    {
        $payload = session('register.phone_payload');

        if (! is_array($payload) || empty($payload['phone'])) {
            $this->redirect(route('register'), navigate: true);

            return;
        }

        $this->phone = $payload['phone'];
    }

    public function confirm(PhoneVerificationService $codes, UserRegistrar $registrar): void
    {
        $payload = session('register.phone_payload');

        if (! is_array($payload) || empty($payload['phone'])) {
            $this->redirect(route('register'), navigate: true);

            return;
        }

        $this->validate([
            'code' => ['required', 'digits:6'],
        ]);

        if ($codes->isLocked($payload['phone'])) {
            throw ValidationException::withMessages([
                'code' => __('auth.phone_locked', ['minutes' => PhoneVerificationService::LOCK_MINUTES]),
            ]);
        }

        if (! $codes->verify($payload['phone'], $this->code)) {
            throw ValidationException::withMessages([
                'code' => __('auth.phone_code_invalid'),
            ]);
        }

        $user = $registrar->register([
            'name' => $payload['name'],
            'phone' => $payload['phone'],
            'password' => $payload['password'],
            'referral_code' => $payload['referral_code'] ?? null,
            'locale' => $payload['locale'] ?? config('app.locale'),
        ], UserMode::from($payload['intent'] ?? 'buyer'));

        $user->forceFill(['phone_verified_at' => now()])->save();

        session()->forget('register.phone_payload');

        event(new Registered($user));
        Auth::login($user);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }

    public function resend(PhoneVerificationService $codes): void
    {
        $payload = session('register.phone_payload');

        if (! is_array($payload) || empty($payload['phone'])) {
            $this->redirect(route('register'), navigate: true);

            return;
        }

        $codes->issue($payload['phone'], PhoneVerificationService::PURPOSE_REGISTER, request()->ip());
        session()->flash('status', __('auth.phone_code_resent'));
    }
}; ?>

<div class="w-full max-w-md mx-auto">
    <h1 class="text-xl font-semibold text-gray-800 mb-2">{{ __('auth.verify_phone_title') }}</h1>
    <p class="text-sm text-gray-500 mb-6">{{ __('auth.verify_phone_subtitle', ['phone' => $phone]) }}</p>

    @if (session('status'))
        <div class="mb-4 text-sm text-green-600">{{ session('status') }}</div>
    @endif

    <form wire:submit="confirm" class="space-y-4">
        <div>
            <x-input-label for="code" :value="__('auth.fields.sms_code')" />
            <x-text-input wire:model="code" id="code" class="block mt-1 w-full text-center tracking-widest text-lg" type="text" inputmode="numeric" maxlength="6" required autofocus />
            <x-input-error :messages="$errors->get('code')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between">
            <button type="button" wire:click="resend" class="text-sm text-blue-600 hover:underline">
                {{ __('auth.actions.resend_code') }}
            </button>

            <x-primary-button class="bg-blue-600 hover:bg-blue-700">
                {{ __('auth.actions.verify') }}
            </x-primary-button>
        </div>
    </form>
</div>
