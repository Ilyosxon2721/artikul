<div>
    @auth
        <button type="button" wire:click="show" class="text-xs text-blue-600 hover:underline">
            ★ {{ __('app.saved_searches.save_button') }}
        </button>
    @endauth

    @if ($open)
        <div class="fixed inset-0 z-40 bg-black/40 flex items-center justify-center p-4" wire:click.self="close">
            <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">{{ __('app.saved_searches.modal_title') }}</h2>

                <form wire:submit="save" class="space-y-4">
                    <div>
                        <x-input-label :value="__('app.saved_searches.fields.name')" />
                        <x-text-input wire:model="name" class="mt-1 block w-full" type="text" required maxlength="120" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label :value="__('app.saved_searches.fields.frequency')" />
                        <select wire:model="frequency" class="mt-1 block w-full border-gray-300 rounded-md">
                            <option value="instant">{{ __('app.saved_searches.frequency.instant') }}</option>
                            <option value="daily">{{ __('app.saved_searches.frequency.daily') }}</option>
                            <option value="weekly">{{ __('app.saved_searches.frequency.weekly') }}</option>
                        </select>
                    </div>

                    <div>
                        <x-input-label :value="__('app.saved_searches.fields.channels')" />
                        <div class="grid grid-cols-2 gap-2 mt-1">
                            <label class="flex items-center gap-2 text-sm">
                                <input type="checkbox" wire:model="notifyVia" value="email" class="rounded text-blue-600" />
                                Email
                            </label>
                            <label class="flex items-center gap-2 text-sm">
                                <input type="checkbox" wire:model="notifyVia" value="telegram" class="rounded text-blue-600" />
                                Telegram
                            </label>
                        </div>
                        <x-input-error :messages="$errors->get('notifyVia')" class="mt-2" />
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" wire:click="close" class="px-4 py-2 text-sm text-gray-600">{{ __('app.actions.cancel') }}</button>
                        <x-primary-button class="bg-blue-600 hover:bg-blue-700">{{ __('app.actions.save') }}</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
