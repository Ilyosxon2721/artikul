<div class="max-w-4xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-semibold text-gray-800 mb-6">{{ __('app.portfolio.title') }}</h1>

    @if (session('status'))
        <div class="mb-4 px-4 py-2 bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg">{{ session('status') }}</div>
    @endif

    <form wire:submit="save" class="bg-white border border-gray-100 rounded-2xl p-6 mb-6 space-y-4">
        <h2 class="font-medium text-gray-800">{{ $editingId ? __('app.portfolio.edit') : __('app.portfolio.add') }}</h2>

        <div>
            <x-input-label :value="__('app.portfolio.fields.title')" />
            <x-text-input wire:model="title" class="mt-1 block w-full" type="text" maxlength="160" required />
            <x-input-error :messages="$errors->get('title')" class="mt-2" />
        </div>

        <div>
            <x-input-label :value="__('app.portfolio.fields.description')" />
            <textarea wire:model="description" rows="4" class="mt-1 block w-full border-gray-300 rounded-md text-sm"></textarea>
            <x-input-error :messages="$errors->get('description')" class="mt-2" />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
                <x-input-label :value="__('app.portfolio.fields.client_name')" />
                <x-text-input wire:model="clientName" class="mt-1 block w-full" type="text" />
            </div>
            <div>
                <x-input-label :value="__('app.portfolio.fields.completed_on')" />
                <x-text-input wire:model="completedOn" type="date" class="mt-1 block w-full" />
            </div>
        </div>

        <div>
            <x-input-label :value="__('app.portfolio.fields.cover')" />
            <input type="file" wire:model="cover" accept="image/*" class="mt-1 block w-full text-sm" />
            <x-input-error :messages="$errors->get('cover')" class="mt-2" />
        </div>

        <div class="flex justify-end gap-2 pt-2">
            @if ($editingId)
                <button type="button" wire:click="resetForm" class="px-4 py-2 text-sm text-gray-600">{{ __('app.actions.cancel') }}</button>
            @endif
            <x-primary-button class="bg-blue-600 hover:bg-blue-700">{{ __('app.actions.save') }}</x-primary-button>
        </div>
    </form>

    @if ($items->count() === 0)
        <p class="text-sm text-gray-500">{{ __('app.portfolio.empty') }}</p>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach ($items as $item)
                <div class="bg-white border border-gray-100 rounded-2xl p-4">
                    @if ($item->cover_url)
                        <img src="{{ $item->cover_url }}" alt="" class="w-full aspect-video object-cover rounded mb-3" />
                    @endif
                    <div class="font-medium text-gray-800">{{ $item->title }}</div>
                    @if ($item->description)
                        <p class="text-sm text-gray-500 mt-1 line-clamp-3">{{ $item->description }}</p>
                    @endif
                    <div class="mt-3 flex items-center gap-3 text-sm">
                        <button wire:click="edit({{ $item->id }})" class="text-blue-600 hover:underline">{{ __('app.actions.edit') }}</button>
                        <button wire:click="delete({{ $item->id }})" wire:confirm="{{ __('app.actions.confirm_delete') }}" class="text-red-600 hover:underline">{{ __('app.actions.delete') }}</button>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
