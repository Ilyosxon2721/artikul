<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.app')] class extends Component
{
    public string $current_password = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function update(): void
    {
        $user = Auth::user();
        abort_unless($user !== null, 403);

        $this->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(10)->letters()->numbers()->mixedCase(), 'different:current_password'],
        ]);

        $user->forceFill([
            'password' => Hash::make($this->password),
            'password_changed_at' => now(),
        ])->save();

        session()->flash('status', __('app.password_rotation.updated'));
        $this->redirect(route('dashboard'), navigate: true);
    }
}; ?>

<div class="max-w-md mx-auto px-4 py-12">
    <div class="bg-white border border-gray-100 rounded-2xl p-6">
        <h1 class="text-xl font-semibold text-gray-800 mb-2">{{ __('app.password_rotation.title') }}</h1>
        <p class="text-sm text-gray-600 mb-6">{{ __('app.password_rotation.subtitle') }}</p>

        <form wire:submit="update" class="space-y-4">
            <div>
                <x-input-label :value="__('app.password_rotation.current_password')" />
                <x-text-input wire:model="current_password" type="password" class="block w-full" required />
                <x-input-error :messages="$errors->get('current_password')" class="mt-2" />
            </div>

            <div>
                <x-input-label :value="__('app.password_rotation.new_password')" />
                <x-text-input wire:model="password" type="password" class="block w-full" required />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div>
                <x-input-label :value="__('app.password_rotation.confirm_password')" />
                <x-text-input wire:model="password_confirmation" type="password" class="block w-full" required />
            </div>

            <div class="flex justify-end pt-2">
                <x-primary-button class="bg-blue-600 hover:bg-blue-700">{{ __('app.password_rotation.update') }}</x-primary-button>
            </div>
        </form>
    </div>
</div>
