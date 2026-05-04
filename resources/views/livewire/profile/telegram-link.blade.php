<div class="max-w-2xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-semibold text-gray-800 mb-6">{{ __('app.telegram.title') }}</h1>

    <div class="bg-white border border-gray-100 rounded-2xl p-6">
        @if ($isLinked)
            <div class="flex items-center gap-2 text-green-700 mb-4">✓ {{ __('app.telegram.already_linked') }}</div>
            <button wire:click="unlink" class="px-4 py-2 bg-white border border-red-200 text-red-700 text-sm rounded-lg hover:bg-red-50">{{ __('app.telegram.unlink') }}</button>
        @else
            <p class="text-sm text-gray-600 mb-4">{{ __('app.telegram.subtitle') }}</p>

            @if ($code)
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                    <div class="text-xs text-blue-700 uppercase tracking-wider">{{ __('app.telegram.code_label') }}</div>
                    <div class="font-mono text-2xl font-bold text-blue-900 mt-1">{{ $code }}</div>
                </div>
                <a href="{{ $deepLink }}" target="_blank" class="inline-block px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg">
                    {{ __('app.telegram.open_bot') }}
                </a>
            @else
                <button wire:click="generate" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg">
                    {{ __('app.telegram.generate_code') }}
                </button>
            @endif

            <p class="text-xs text-gray-500 mt-4">@{{ $botUsername }}</p>
        @endif
    </div>
</div>
