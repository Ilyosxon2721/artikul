<div class="max-w-2xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-semibold text-gray-800 mb-2">{{ __('app.disputes.open_title') }}</h1>
    <p class="text-sm text-gray-500 mb-6">{{ __('app.disputes.subtitle') }}</p>

    <form wire:submit="open" class="space-y-5 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <div>
            <x-input-label :value="__('app.disputes.fields.reason')" />
            <select wire:model="reasonCode" class="mt-1 block w-full border-gray-300 rounded-md">
                <option value="quality">{{ __('app.disputes.reasons.quality') }}</option>
                <option value="deadline">{{ __('app.disputes.reasons.deadline') }}</option>
                <option value="communication">{{ __('app.disputes.reasons.communication') }}</option>
                <option value="scope">{{ __('app.disputes.reasons.scope') }}</option>
                <option value="other">{{ __('app.disputes.reasons.other') }}</option>
            </select>
        </div>

        <div>
            <x-input-label :value="__('app.disputes.fields.description')" />
            <textarea wire:model="description" rows="6" class="mt-1 block w-full border-gray-300 rounded-md text-sm focus:border-blue-500 focus:ring-blue-500" maxlength="2000"></textarea>
            <x-input-error :messages="$errors->get('description')" class="mt-2" />
        </div>

        <div class="flex justify-end pt-2">
            <x-primary-button class="bg-amber-600 hover:bg-amber-700">{{ __('app.disputes.actions.open') }}</x-primary-button>
        </div>
    </form>
</div>
