<div class="max-w-4xl mx-auto px-4 py-8">
    <h1 class="text-xl font-semibold text-gray-800 mb-6">{{ __('app.profile.seller_title') }}</h1>

    @if (session('status'))
        <div class="mb-4 px-4 py-2 bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg">{{ session('status') }}</div>
    @endif

    <form wire:submit="save" class="space-y-6 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <div>
            <x-input-label for="headline" :value="__('app.profile.fields.headline')" />
            <x-text-input wire:model="headline" id="headline" class="mt-1 block w-full" type="text" maxlength="80" />
            <p class="text-xs text-gray-500 mt-1">{{ __('app.profile.headline_hint') }}</p>
            <x-input-error :messages="$errors->get('headline')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="bio" :value="__('app.profile.fields.bio')" />
            <textarea wire:model="bio" id="bio" rows="6" class="mt-1 block w-full border-gray-300 rounded-md focus:border-blue-500 focus:ring-blue-500 font-mono text-sm" placeholder="**markdown**"></textarea>
            <p class="text-xs text-gray-500 mt-1">{{ __('app.profile.bio_hint') }}</p>
            <x-input-error :messages="$errors->get('bio')" class="mt-2" />
        </div>

        <div>
            <x-input-label :value="__('app.profile.fields.specializations')" />
            <p class="text-xs text-gray-500 mb-2">{{ __('app.profile.specializations_hint') }}</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-72 overflow-y-auto pr-2">
                @foreach ($specializations as $spec)
                    <label class="flex items-center gap-2 px-3 py-2 border rounded-lg cursor-pointer text-sm {{ in_array($spec->id, $specializationIds) ? 'border-blue-500 bg-blue-50 text-blue-700' : 'border-gray-200 text-gray-700' }}">
                        <input type="checkbox" wire:model.live="specializationIds" value="{{ $spec->id }}" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
                        {{ $spec->name() }}
                    </label>
                @endforeach
            </div>
            <x-input-error :messages="$errors->get('specializationIds')" class="mt-2" />
        </div>

        <div>
            <x-input-label :value="__('app.profile.fields.marketplaces_levels')" />
            <div class="mt-2 space-y-2">
                @foreach ($marketplaces as $marketplace)
                    <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                        <div class="flex items-center gap-2">
                            <span class="font-medium text-gray-800">{{ $marketplace->name() }}</span>
                            <span class="text-xs text-gray-400 uppercase">{{ $marketplace->code }}</span>
                        </div>
                        <select wire:model.live="marketplaceLevels.{{ $marketplace->id }}" class="text-sm border-gray-200 rounded-md focus:border-blue-500 focus:ring-blue-500">
                            <option value="">{{ __('app.profile.no_level') }}</option>
                            <option value="junior">Junior</option>
                            <option value="middle">Middle</option>
                            <option value="senior">Senior</option>
                        </select>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <x-input-label for="hourlyRate" :value="__('app.profile.fields.hourly_rate')" />
                <x-text-input wire:model="hourlyRate" id="hourlyRate" class="mt-1 block w-full" type="number" min="0" step="0.01" />
            </div>
            <div>
                <x-input-label for="currency" :value="__('app.profile.fields.currency')" />
                <select wire:model="currency" id="currency" class="mt-1 block w-full border-gray-300 rounded-md">
                    <option value="UZS">UZS</option>
                    <option value="RUB">RUB</option>
                    <option value="KZT">KZT</option>
                    <option value="USD">USD</option>
                </select>
            </div>
            <div>
                <x-input-label for="minOrderAmount" :value="__('app.profile.fields.min_order_amount')" />
                <x-text-input wire:model="minOrderAmount" id="minOrderAmount" class="mt-1 block w-full" type="number" min="0" step="0.01" />
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
                <x-input-label for="hoursPerWeek" :value="__('app.profile.fields.hours_per_week')" />
                <x-text-input wire:model="hoursPerWeek" id="hoursPerWeek" class="mt-1 block w-full" type="number" min="1" max="168" />
            </div>
            <div>
                <x-input-label for="languages" :value="__('app.profile.fields.languages')" />
                <select multiple wire:model="languages" id="languages" class="mt-1 block w-full border-gray-300 rounded-md text-sm">
                    <option value="ru">Русский</option>
                    <option value="uz">O'zbekcha</option>
                    <option value="en">English</option>
                    <option value="kk">Қазақша</option>
                </select>
            </div>
        </div>

        <div>
            <label class="inline-flex items-center gap-2">
                <input type="checkbox" wire:model="openToLongTerm" class="rounded border-gray-300 text-blue-600" />
                <span class="text-sm text-gray-700">{{ __('app.profile.open_to_long_term') }}</span>
            </label>
        </div>

        <div>
            <x-input-label for="videoIntroUrl" :value="__('app.profile.fields.video_intro_url')" />
            <x-text-input wire:model="videoIntroUrl" id="videoIntroUrl" class="mt-1 block w-full" type="url" placeholder="https://youtu.be/..." />
        </div>

        <div class="pt-2 flex justify-end gap-2">
            @if (auth()->user()?->username)
                <a href="{{ route('public.seller', auth()->user()->username) }}" target="_blank" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900">
                    {{ __('app.profile.preview_public') }}
                </a>
            @endif
            <x-primary-button class="bg-blue-600 hover:bg-blue-700">
                {{ __('app.actions.save') }}
            </x-primary-button>
        </div>
    </form>
</div>
