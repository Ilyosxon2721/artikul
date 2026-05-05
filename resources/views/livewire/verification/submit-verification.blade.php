<div class="max-w-2xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-semibold text-gray-800 mb-2">{{ __('app.verification.title') }}</h1>
    <p class="text-sm text-gray-500 mb-6">{{ __('app.verification.subtitle') }}</p>

    @if (session('status'))
        <div class="mb-4 px-4 py-2 bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg">{{ session('status') }}</div>
    @endif

    @if ($latest)
        <div class="mb-6 p-4 rounded-lg bg-blue-50 border border-blue-200 text-sm">
            {{ __('app.verification.current_status') }}:
            <strong>
                @switch($latest->status)
                    @case($pendingStatus){{ __('app.verification.status.pending') }}@break
                    @case($approvedStatus){{ __('app.verification.status.approved') }}@break
                    @case($rejectedStatus){{ __('app.verification.status.rejected') }}@break
                @endswitch
            </strong>
            @if ($latest->status === $rejectedStatus && $latest->rejection_reason)
                <p class="mt-2 text-red-700">{{ $latest->rejection_reason }}</p>
            @endif
        </div>
    @endif

    <form wire:submit="submit" class="space-y-5 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <div>
            <x-input-label :value="__('app.verification.fields.id_document')" />
            <input type="file" wire:model="idDocument" accept="image/*,application/pdf" class="mt-1 block w-full text-sm" />
            <p class="text-xs text-gray-500 mt-1">{{ __('app.verification.id_document_hint') }}</p>
            <x-input-error :messages="$errors->get('idDocument')" class="mt-2" />
        </div>

        <div>
            <x-input-label :value="__('app.verification.fields.selfie')" />
            <input type="file" wire:model="selfie" accept="image/*" class="mt-1 block w-full text-sm" />
            <x-input-error :messages="$errors->get('selfie')" class="mt-2" />
        </div>

        <div>
            <x-input-label :value="__('app.verification.fields.marketplace_screenshots')" />
            <input type="file" wire:model="marketplaceScreenshots" multiple accept="image/*,application/pdf" class="mt-1 block w-full text-sm" />
            <p class="text-xs text-gray-500 mt-1">{{ __('app.verification.marketplace_hint') }}</p>
        </div>

        <div>
            <x-input-label :value="__('app.verification.fields.recommendation')" />
            <input type="file" wire:model="recommendation" accept=".pdf,.docx,image/*" class="mt-1 block w-full text-sm" />
        </div>

        <div>
            <x-input-label :value="__('app.verification.fields.passport_number')" />
            <x-text-input wire:model="passportNumber" type="text" class="mt-1 block w-full" />
            <p class="text-xs text-gray-500 mt-1">{{ __('app.verification.passport_hint') }}</p>
            <x-input-error :messages="$errors->get('passportNumber')" class="mt-2" />
        </div>

        <div class="flex justify-end pt-2">
            <x-primary-button class="bg-blue-600 hover:bg-blue-700">{{ __('app.verification.actions.submit') }}</x-primary-button>
        </div>
    </form>
</div>
