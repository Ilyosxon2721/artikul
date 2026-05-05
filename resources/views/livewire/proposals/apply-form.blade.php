<div>
    <button type="button" wire:click="show" class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg">
        {{ __('app.proposals.actions.respond') }}
    </button>

    @if ($open)
        <div class="fixed inset-0 z-40 bg-black/40 flex items-center justify-center p-4" wire:click.self="close">
            <div class="bg-white rounded-2xl shadow-xl max-w-lg w-full p-6 max-h-[90vh] overflow-y-auto">
                <h2 class="text-lg font-semibold text-gray-800 mb-1">{{ __('app.proposals.modal_title') }}</h2>
                <p class="text-sm text-gray-500 mb-4">{{ $task->title }}</p>

                <form wire:submit="submit" class="space-y-4">
                    <div>
                        <x-input-label :value="__('app.proposals.fields.cover_letter')" />
                        <textarea wire:model="coverLetter" rows="6" class="mt-1 block w-full border-gray-300 rounded-md text-sm focus:border-blue-500 focus:ring-blue-500" maxlength="2000" placeholder="{{ __('app.proposals.cover_placeholder') }}"></textarea>
                        <x-input-error :messages="$errors->get('coverLetter')" class="mt-2" />
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <x-input-label :value="__('app.proposals.fields.amount')" />
                            <x-text-input wire:model="proposedAmount" type="number" min="0" step="0.01" class="mt-1 block w-full" />
                        </div>
                        <div>
                            <x-input-label :value="__('app.proposals.fields.currency')" />
                            <select wire:model="currency" class="mt-1 block w-full border-gray-300 rounded-md">
                                <option value="UZS">UZS</option>
                                <option value="RUB">RUB</option>
                                <option value="KZT">KZT</option>
                                <option value="USD">USD</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <x-input-label :value="__('app.proposals.fields.rate_type')" />
                            <select wire:model="rateType" class="mt-1 block w-full border-gray-300 rounded-md">
                                <option value="fixed">{{ __('app.proposals.rate.fixed') }}</option>
                                <option value="hourly">{{ __('app.proposals.rate.hourly') }}</option>
                            </select>
                        </div>
                        <div>
                            <x-input-label :value="__('app.proposals.fields.deadline_days')" />
                            <x-text-input wire:model="proposedDeadlineDays" type="number" min="1" max="365" class="mt-1 block w-full" />
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button type="button" wire:click="close" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900">{{ __('app.actions.cancel') }}</button>
                        <x-primary-button class="bg-blue-600 hover:bg-blue-700">{{ __('app.proposals.actions.send') }}</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
