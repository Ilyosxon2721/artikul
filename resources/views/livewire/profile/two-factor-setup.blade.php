<div class="max-w-2xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-semibold text-gray-800 mb-2">{{ __('app.two_factor.title') }}</h1>
    <p class="text-sm text-gray-500 mb-6">{{ __('app.two_factor.subtitle') }}</p>

    @if (session('status'))
        <div class="mb-4 px-4 py-2 bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg">{{ session('status') }}</div>
    @endif

    @if (! empty($recoveryCodes))
        <div class="mb-4 px-4 py-3 bg-amber-50 border border-amber-200 rounded-lg">
            <div class="text-sm font-medium text-amber-800 mb-2">{{ __('app.two_factor.recovery_title') }}</div>
            <div class="text-xs text-amber-700 mb-2">{{ __('app.two_factor.recovery_hint') }}</div>
            <ul class="font-mono text-sm text-gray-800 grid grid-cols-2 gap-1">
                @foreach ($recoveryCodes as $code)
                    <li>{{ $code }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white border border-gray-100 rounded-2xl p-6">
        @if ($enabled)
            <div class="flex items-center gap-2 text-green-700 mb-4">✓ {{ __('app.two_factor.is_enabled') }}</div>

            <form wire:submit="disable" class="space-y-3">
                <x-input-label :value="__('app.two_factor.disable_password')" />
                <x-text-input wire:model="disablePassword" type="password" class="block w-full" required />
                <x-input-error :messages="$errors->get('disablePassword')" class="mt-2" />

                <button type="submit" class="px-4 py-2 bg-white border border-red-200 text-red-700 text-sm rounded-lg hover:bg-red-50">
                    {{ __('app.two_factor.disable') }}
                </button>
            </form>
        @elseif ($pendingSecret === null)
            <p class="text-sm text-gray-600 mb-4">{{ __('app.two_factor.start_hint') }}</p>
            <button wire:click="start" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg">
                {{ __('app.two_factor.start') }}
            </button>
        @else
            <div class="space-y-4">
                <div>
                    <p class="text-sm text-gray-600">{{ __('app.two_factor.scan_qr') }}</p>
                    <div class="mt-3 inline-block p-3 bg-gray-50 border border-gray-100 rounded-lg break-all text-xs font-mono">
                        {{ $qrUrl }}
                    </div>
                    <p class="text-xs text-gray-500 mt-2">{{ __('app.two_factor.manual_secret') }}: <strong class="font-mono">{{ $pendingSecret }}</strong></p>
                </div>

                <form wire:submit="confirm" class="space-y-3">
                    <x-input-label :value="__('app.two_factor.code_label')" />
                    <x-text-input wire:model="code" type="text" inputmode="numeric" maxlength="6" class="block w-full text-center tracking-widest" required />
                    <x-input-error :messages="$errors->get('code')" class="mt-2" />
                    <x-primary-button class="bg-blue-600 hover:bg-blue-700">{{ __('app.two_factor.confirm') }}</x-primary-button>
                </form>
            </div>
        @endif
    </div>
</div>
