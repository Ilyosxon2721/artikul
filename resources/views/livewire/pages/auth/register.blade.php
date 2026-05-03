<?php

use App\Enums\UserMode;
use App\Services\Auth\PhoneVerificationService;
use App\Services\Auth\UserRegistrar;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $tab = 'email';

    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public string $password = '';

    public string $password_confirmation = '';

    public string $referral_code = '';

    public string $intent = 'buyer';

    public function mount(): void
    {
        $this->referral_code = (string) request()->query('ref', '');
    }

    public function setTab(string $tab): void
    {
        $this->tab = in_array($tab, ['email', 'phone'], true) ? $tab : 'email';
        $this->resetErrorBag();
    }

    public function register(UserRegistrar $registrar): void
    {
        $this->validate([
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'email' => ['required_if:tab,email', 'nullable', 'email:rfc', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
            'referral_code' => ['nullable', 'string', 'max:32'],
            'intent' => ['required', 'in:buyer,seller'],
        ]);

        $user = $registrar->register([
            'name' => $this->name,
            'email' => $this->email !== '' ? mb_strtolower($this->email) : null,
            'password' => $this->password,
            'referral_code' => $this->referral_code !== '' ? $this->referral_code : null,
            'locale' => app()->getLocale(),
        ], UserMode::from($this->intent));

        event(new Registered($user));
        Auth::login($user);

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }

    public function sendCode(PhoneVerificationService $codes): void
    {
        $this->validate([
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'phone' => ['required', 'regex:/^\+?\d{9,15}$/', 'unique:users,phone'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
            'intent' => ['required', 'in:buyer,seller'],
        ]);

        $codes->issue($this->phone, PhoneVerificationService::PURPOSE_REGISTER, request()->ip());

        session()->put('register.phone_payload', [
            'name' => $this->name,
            'phone' => $codes->normalize($this->phone),
            'password' => $this->password,
            'referral_code' => $this->referral_code,
            'intent' => $this->intent,
            'locale' => app()->getLocale(),
        ]);

        $this->redirect(route('register.verify-phone'), navigate: true);
    }
}; ?>

<div class="w-full max-w-md mx-auto">
    <div class="flex border-b border-gray-200 mb-6">
        <button type="button"
                wire:click="setTab('email')"
                class="flex-1 py-2 text-sm font-medium border-b-2 transition {{ $tab === 'email' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
            {{ __('auth.tab_email') }}
        </button>
        <button type="button"
                wire:click="setTab('phone')"
                class="flex-1 py-2 text-sm font-medium border-b-2 transition {{ $tab === 'phone' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
            {{ __('auth.tab_phone') }}
        </button>
    </div>

    <form wire:submit="{{ $tab === 'email' ? 'register' : 'sendCode' }}" class="space-y-4">
        <div>
            <x-input-label for="name" :value="__('auth.fields.name')" />
            <x-text-input wire:model="name" id="name" class="block mt-1 w-full" type="text" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        @if ($tab === 'email')
            <div>
                <x-input-label for="email" :value="__('auth.fields.email')" />
                <x-text-input wire:model="email" id="email" class="block mt-1 w-full" type="email" required autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>
        @else
            <div>
                <x-input-label for="phone" :value="__('auth.fields.phone')" />
                <x-text-input wire:model="phone" id="phone" class="block mt-1 w-full" type="tel" required placeholder="+998 90 123 45 67" autocomplete="tel" />
                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                <p class="text-xs text-gray-500 mt-1">{{ __('auth.phone_hint') }}</p>
            </div>
        @endif

        <div>
            <x-input-label for="password" :value="__('auth.fields.password')" />
            <x-text-input wire:model="password" id="password" class="block mt-1 w-full" type="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password_confirmation" :value="__('auth.fields.password_confirmation')" />
            <x-text-input wire:model="password_confirmation" id="password_confirmation" class="block mt-1 w-full" type="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div>
            <x-input-label :value="__('auth.fields.intent')" />
            <div class="grid grid-cols-2 gap-2 mt-1">
                <label class="flex items-center justify-center px-3 py-2 border rounded-lg cursor-pointer transition {{ $intent === 'buyer' ? 'border-blue-600 bg-blue-50 text-blue-700' : 'border-gray-200 text-gray-600' }}">
                    <input type="radio" wire:model.live="intent" value="buyer" class="sr-only" />
                    {{ __('auth.intent_buyer') }}
                </label>
                <label class="flex items-center justify-center px-3 py-2 border rounded-lg cursor-pointer transition {{ $intent === 'seller' ? 'border-blue-600 bg-blue-50 text-blue-700' : 'border-gray-200 text-gray-600' }}">
                    <input type="radio" wire:model.live="intent" value="seller" class="sr-only" />
                    {{ __('auth.intent_seller') }}
                </label>
            </div>
            <x-input-error :messages="$errors->get('intent')" class="mt-2" />
        </div>

        @if ($referral_code !== '')
            <div>
                <x-input-label for="referral_code" :value="__('auth.fields.referral_code')" />
                <x-text-input wire:model="referral_code" id="referral_code" class="block mt-1 w-full" type="text" />
            </div>
        @endif

        <div class="flex items-center justify-between pt-2">
            <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('login') }}" wire:navigate>
                {{ __('auth.have_account') }}
            </a>

            <x-primary-button class="bg-blue-600 hover:bg-blue-700 focus:bg-blue-700">
                {{ $tab === 'email' ? __('auth.actions.register') : __('auth.actions.send_code') }}
            </x-primary-button>
        </div>
    </form>

    <div class="my-6 text-center text-xs uppercase tracking-wider text-gray-400">{{ __('auth.or') }}</div>

    <a href="{{ route('auth.google') }}"
       class="w-full flex items-center justify-center gap-2 px-4 py-2 border border-gray-200 rounded-lg text-sm text-gray-700 hover:bg-gray-50">
        <svg class="w-4 h-4" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
            <path fill="#FFC107" d="M43.6 20.5H42V20H24v8h11.3C33.7 32.6 29.3 36 24 36c-6.6 0-12-5.4-12-12s5.4-12 12-12c3.1 0 5.9 1.2 8 3.1l5.7-5.7C34.3 6 29.5 4 24 4 12.9 4 4 12.9 4 24s8.9 20 20 20c11 0 20-8.9 20-20 0-1.2-.1-2.3-.4-3.5z"/>
            <path fill="#FF3D00" d="M6.3 14.7l6.6 4.8C14.7 16 19 13 24 13c3.1 0 5.9 1.2 8 3.1l5.7-5.7C34.3 6 29.5 4 24 4 16.3 4 9.7 8.3 6.3 14.7z"/>
            <path fill="#4CAF50" d="M24 44c5.4 0 10.3-2.1 14-5.5l-6.5-5.5c-2 1.4-4.5 2.3-7.5 2.3-5.3 0-9.7-3.4-11.3-8.1l-6.5 5C9.6 39.5 16.2 44 24 44z"/>
            <path fill="#1976D2" d="M43.6 20.5H42V20H24v8h11.3c-.8 2.3-2.3 4.3-4.3 5.7l6.5 5.5C40.4 36.7 44 30.9 44 24c0-1.2-.1-2.3-.4-3.5z"/>
        </svg>
        {{ __('auth.continue_with_google') }}
    </a>
</div>
