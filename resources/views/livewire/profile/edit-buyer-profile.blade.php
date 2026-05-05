<div class="max-w-3xl mx-auto px-4 py-8">
    <h1 class="text-xl font-semibold text-gray-800 mb-6">{{ __('app.profile.buyer_title') }}</h1>

    @if (session('status'))
        <div class="mb-4 px-4 py-2 bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg">{{ session('status') }}</div>
    @endif

    <form wire:submit="save" class="space-y-5 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <div>
            <x-input-label for="companyName" :value="__('app.profile.fields.company_name')" />
            <x-text-input wire:model="companyName" id="companyName" class="mt-1 block w-full" type="text" />
            <x-input-error :messages="$errors->get('companyName')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="description" :value="__('app.profile.fields.description')" />
            <textarea wire:model="description" id="description" rows="3" class="mt-1 block w-full border-gray-300 rounded-md focus:border-blue-500 focus:ring-blue-500"></textarea>
            <x-input-error :messages="$errors->get('description')" class="mt-2" />
        </div>

        <div>
            <x-input-label :value="__('app.profile.fields.marketplaces')" />
            <div class="mt-1 grid grid-cols-2 sm:grid-cols-3 gap-2">
                @foreach ($marketplaces as $marketplace)
                    <label class="flex items-center gap-2 px-3 py-2 border rounded-lg cursor-pointer text-sm {{ in_array($marketplace->code, $marketplaceCodes, true) ? 'border-blue-500 bg-blue-50 text-blue-700' : 'border-gray-200 text-gray-700' }}">
                        <input type="checkbox" wire:model.live="marketplaceCodes" value="{{ $marketplace->code }}" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
                        {{ $marketplace->name() }}
                    </label>
                @endforeach
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <x-input-label for="skusCount" :value="__('app.profile.fields.skus_count')" />
                <x-text-input wire:model="skusCount" id="skusCount" class="mt-1 block w-full" type="number" min="0" />
            </div>
            <div>
                <x-input-label for="revenueRange" :value="__('app.profile.fields.revenue_range')" />
                <x-text-input wire:model="revenueRange" id="revenueRange" class="mt-1 block w-full" type="text" placeholder="0–10M UZS" />
            </div>
            <div>
                <x-input-label for="defaultCurrency" :value="__('app.profile.fields.default_currency')" />
                <select wire:model="defaultCurrency" id="defaultCurrency" class="mt-1 block w-full border-gray-300 rounded-md">
                    <option value="UZS">UZS</option>
                    <option value="RUB">RUB</option>
                    <option value="KZT">KZT</option>
                    <option value="USD">USD</option>
                </select>
            </div>
        </div>

        <div class="pt-2 flex justify-end">
            <x-primary-button class="bg-blue-600 hover:bg-blue-700">
                {{ __('app.actions.save') }}
            </x-primary-button>
        </div>
    </form>
</div>
