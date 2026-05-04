<div class="max-w-4xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-semibold text-gray-800 mb-6">{{ __('app.services.title') }}</h1>

    @if (session('status'))
        <div class="mb-4 px-4 py-2 bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg">{{ session('status') }}</div>
    @endif

    <form wire:submit="save" class="bg-white border border-gray-100 rounded-2xl p-6 mb-6 space-y-4">
        <h2 class="font-medium text-gray-800">{{ $editingId ? __('app.services.edit') : __('app.services.add') }}</h2>

        <div>
            <x-input-label :value="__('app.services.fields.title')" />
            <x-text-input wire:model="title" class="mt-1 block w-full" type="text" maxlength="160" required />
            <x-input-error :messages="$errors->get('title')" class="mt-2" />
        </div>

        <div>
            <x-input-label :value="__('app.services.fields.description')" />
            <textarea wire:model="description" rows="4" class="mt-1 block w-full border-gray-300 rounded-md text-sm"></textarea>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <x-input-label :value="__('app.services.fields.price')" />
                <x-text-input wire:model="price" type="number" min="0" step="0.01" class="mt-1 block w-full" required />
                <x-input-error :messages="$errors->get('price')" class="mt-2" />
            </div>
            <div>
                <x-input-label :value="__('app.services.fields.currency')" />
                <select wire:model="currency" class="mt-1 block w-full border-gray-300 rounded-md">
                    <option value="UZS">UZS</option>
                    <option value="RUB">RUB</option>
                    <option value="KZT">KZT</option>
                    <option value="USD">USD</option>
                </select>
            </div>
            <div>
                <x-input-label :value="__('app.services.fields.delivery_days')" />
                <x-text-input wire:model="deliveryDays" type="number" min="1" max="365" class="mt-1 block w-full" required />
                <x-input-error :messages="$errors->get('deliveryDays')" class="mt-2" />
            </div>
        </div>

        <div class="flex justify-end gap-2 pt-2">
            @if ($editingId)
                <button type="button" wire:click="resetForm" class="px-4 py-2 text-sm text-gray-600">{{ __('app.actions.cancel') }}</button>
            @endif
            <x-primary-button class="bg-blue-600 hover:bg-blue-700">{{ __('app.actions.save') }}</x-primary-button>
        </div>
    </form>

    @if ($services->count() === 0)
        <p class="text-sm text-gray-500">{{ __('app.services.empty') }}</p>
    @else
        <div class="space-y-3">
            @foreach ($services as $service)
                <div class="bg-white border border-gray-100 rounded-2xl p-4 flex items-start justify-between gap-4">
                    <div class="flex-1">
                        <div class="font-medium text-gray-800">{{ $service->title }}</div>
                        @if ($service->description)
                            <p class="text-sm text-gray-500 mt-1 line-clamp-2">{{ $service->description }}</p>
                        @endif
                        <div class="text-xs text-gray-500 mt-2">
                            {{ number_format((float) $service->price, 0, '.', ' ') }} {{ $service->currency?->value }}
                            · {{ trans_choice('app.services.delivery_days', $service->delivery_days, ['count' => $service->delivery_days]) }}
                        </div>
                    </div>
                    <div class="flex items-center gap-3 text-sm">
                        <button wire:click="edit({{ $service->id }})" class="text-blue-600 hover:underline">{{ __('app.actions.edit') }}</button>
                        <button wire:click="delete({{ $service->id }})" wire:confirm="{{ __('app.actions.confirm_delete') }}" class="text-red-600 hover:underline">{{ __('app.actions.delete') }}</button>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
