<div>
    @auth
        <button type="button" wire:click="show" class="text-sm text-blue-600 hover:underline">{{ __('app.sellers.actions.invite') }}</button>
    @endauth

    @if ($open)
        <div class="fixed inset-0 z-40 bg-black/40 flex items-center justify-center p-4" wire:click.self="close">
            <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-2">{{ __('app.proposals.invite_title', ['name' => $seller->name]) }}</h2>

                @if ($tasks->count() === 0)
                    <p class="text-sm text-gray-500 mb-3">{{ __('app.proposals.no_open_tasks') }}</p>
                    <a href="{{ route('tasks.create') }}" class="inline-block px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg">{{ __('app.tasks.actions.create') }}</a>
                @else
                    <form wire:submit="send" class="space-y-3">
                        <div>
                            <x-input-label :value="__('app.proposals.fields.task')" />
                            <select wire:model="taskId" class="mt-1 block w-full border-gray-300 rounded-md text-sm">
                                <option value="">—</option>
                                @foreach ($tasks as $t)
                                    <option value="{{ $t->id }}">{{ $t->title }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('taskId')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label :value="__('app.proposals.fields.message')" />
                            <textarea wire:model="message" rows="4" class="mt-1 block w-full border-gray-300 rounded-md text-sm"></textarea>
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-1">
                            <button type="button" wire:click="close" class="px-4 py-2 text-sm text-gray-600">{{ __('app.actions.cancel') }}</button>
                            <x-primary-button class="bg-blue-600 hover:bg-blue-700">{{ __('app.proposals.actions.invite') }}</x-primary-button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    @endif
</div>
