<div class="max-w-3xl mx-auto px-4 py-8">
    <h1 class="text-xl font-semibold text-gray-800 mb-6">{{ __('app.profile.general_title') }}</h1>

    @if (session('status'))
        <div class="mb-4 px-4 py-2 bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg">{{ session('status') }}</div>
    @endif

    <form wire:submit="save" class="space-y-5 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <div class="flex items-center gap-4">
            @if ($user->avatar_url)
                <img src="{{ $user->avatar_url }}" alt="" class="w-16 h-16 rounded-full object-cover border" />
            @else
                <div class="w-16 h-16 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xl font-semibold">
                    {{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}
                </div>
            @endif
            <div>
                <label class="text-sm text-blue-600 hover:underline cursor-pointer">
                    <input type="file" wire:model="avatar" accept="image/*" class="hidden" />
                    {{ __('app.profile.upload_avatar') }}
                </label>
                <p class="text-xs text-gray-500 mt-1">{{ __('app.profile.avatar_hint') }}</p>
                @error('avatar')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
                <x-input-label for="name" :value="__('app.profile.fields.name')" />
                <x-text-input wire:model="name" id="name" class="mt-1 block w-full" type="text" required />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="username" :value="__('app.profile.fields.username')" />
                <x-text-input wire:model="username" id="username" class="mt-1 block w-full" type="text" />
                <x-input-error :messages="$errors->get('username')" class="mt-2" />
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
                <x-input-label for="email" :value="__('app.profile.fields.email')" />
                <x-text-input wire:model="email" id="email" class="mt-1 block w-full" type="email" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="phone" :value="__('app.profile.fields.phone')" />
                <x-text-input wire:model="phone" id="phone" class="mt-1 block w-full" type="tel" placeholder="+998 ..." />
                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <x-input-label for="countryCode" :value="__('app.profile.fields.country_code')" />
                <x-text-input wire:model="countryCode" id="countryCode" class="mt-1 block w-full" type="text" maxlength="2" placeholder="UZ" />
            </div>
            <div>
                <x-input-label for="city" :value="__('app.profile.fields.city')" />
                <x-text-input wire:model="city" id="city" class="mt-1 block w-full" type="text" />
            </div>
            <div>
                <x-input-label for="locale" :value="__('app.profile.fields.locale')" />
                <select wire:model="locale" id="locale" class="mt-1 block w-full border-gray-300 rounded-md">
                    <option value="ru">Русский</option>
                    <option value="uz">O'zbekcha</option>
                    <option value="en">English</option>
                </select>
            </div>
        </div>

        <div>
            <x-input-label for="timezone" :value="__('app.profile.fields.timezone')" />
            <x-text-input wire:model="timezone" id="timezone" class="mt-1 block w-full" type="text" />
        </div>

        <div class="pt-2 flex justify-end">
            <x-primary-button class="bg-blue-600 hover:bg-blue-700">
                {{ __('app.actions.save') }}
            </x-primary-button>
        </div>
    </form>
</div>
