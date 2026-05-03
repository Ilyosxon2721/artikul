<div class="inline-flex items-center bg-gray-100 rounded-lg p-1 text-sm">
    <button type="button"
            wire:click="switch('buyer')"
            class="px-3 py-1.5 rounded-md font-medium transition {{ $currentMode->value === 'buyer' ? 'bg-white text-blue-700 shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
        {{ __('app.mode.buyer') }}
    </button>
    <button type="button"
            wire:click="switch('seller')"
            class="px-3 py-1.5 rounded-md font-medium transition {{ $currentMode->value === 'seller' ? 'bg-white text-blue-700 shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
        {{ __('app.mode.seller') }}
    </button>
</div>
