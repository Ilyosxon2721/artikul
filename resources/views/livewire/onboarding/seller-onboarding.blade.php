<div class="max-w-3xl mx-auto px-4 py-10">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-semibold text-gray-800">{{ __('app.onboarding.seller_title') }}</h1>
            <span class="text-sm text-gray-500">{{ __('app.onboarding.step_n', ['current' => $step, 'total' => 3]) }}</span>
        </div>

        <div class="flex gap-2 mb-8">
            @for ($i = 1; $i <= 3; $i++)
                <div class="flex-1 h-1.5 rounded-full {{ $i <= $step ? 'bg-blue-600' : 'bg-gray-200' }}"></div>
            @endfor
        </div>

        @if ($step === 1)
            <h2 class="text-lg font-medium text-gray-800 mb-2">{{ __('app.onboarding.step1_title') }}</h2>
            <p class="text-sm text-gray-500 mb-4">{{ __('app.onboarding.step1_subtitle') }}</p>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-96 overflow-y-auto pr-2">
                @foreach ($specializations as $spec)
                    <label class="flex items-center gap-3 px-3 py-2 border rounded-lg cursor-pointer transition {{ in_array($spec->id, $specializationIds) ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:bg-gray-50' }}">
                        <input type="checkbox" wire:model.live="specializationIds" value="{{ $spec->id }}" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
                        <span class="text-sm text-gray-800">{{ $spec->name() }}</span>
                    </label>
                @endforeach
            </div>
            <x-input-error :messages="$errors->get('specializationIds')" class="mt-2" />
        @endif

        @if ($step === 2)
            <h2 class="text-lg font-medium text-gray-800 mb-2">{{ __('app.onboarding.step2_title') }}</h2>
            <p class="text-sm text-gray-500 mb-4">{{ __('app.onboarding.step2_subtitle') }}</p>

            <div class="space-y-3">
                @foreach ($marketplaces as $marketplace)
                    @php($id = $marketplace->id)
                    <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                        <div class="flex items-center gap-3">
                            <span class="font-medium text-gray-800">{{ $marketplace->name() }}</span>
                            <span class="text-xs text-gray-400">{{ $marketplace->code }}</span>
                        </div>
                        <select wire:model.live="marketplaceLevels.{{ $id }}" class="text-sm border-gray-200 rounded-md focus:border-blue-500 focus:ring-blue-500">
                            <option value="">{{ __('app.onboarding.skip') }}</option>
                            @foreach ($levels as $level)
                                <option value="{{ $level->value }}">{{ $level->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                @endforeach
            </div>
            <x-input-error :messages="$errors->get('marketplaceLevels')" class="mt-2" />
        @endif

        @if ($step === 3)
            <h2 class="text-lg font-medium text-gray-800 mb-2">{{ __('app.onboarding.step3_title') }}</h2>
            <p class="text-sm text-gray-500 mb-4">{{ __('app.onboarding.step3_subtitle') }}</p>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <x-input-label :value="__('app.onboarding.fields.hourly_rate')" />
                    <x-text-input wire:model="hourlyRate" type="number" min="0" step="0.01" class="block mt-1 w-full" />
                </div>
                <div>
                    <x-input-label :value="__('app.onboarding.fields.currency')" />
                    <select wire:model="currency" class="block mt-1 w-full border-gray-300 rounded-md">
                        <option value="UZS">UZS</option>
                        <option value="RUB">RUB</option>
                        <option value="KZT">KZT</option>
                        <option value="USD">USD</option>
                    </select>
                </div>
                <div>
                    <x-input-label :value="__('app.onboarding.fields.hours_per_week')" />
                    <x-text-input wire:model="hoursPerWeek" type="number" min="1" max="168" class="block mt-1 w-full" />
                </div>
            </div>
        @endif

        <div class="flex justify-between mt-8 pt-6 border-t border-gray-100">
            <button type="button" wire:click="back" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900 {{ $step === 1 ? 'invisible' : '' }}">
                {{ __('app.actions.back') }}
            </button>

            @if ($step < 3)
                <button type="button" wire:click="next" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg">
                    {{ __('app.actions.next') }}
                </button>
            @else
                <button type="button" wire:click="finish" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg">
                    {{ __('app.actions.finish') }}
                </button>
            @endif
        </div>
    </div>
</div>
