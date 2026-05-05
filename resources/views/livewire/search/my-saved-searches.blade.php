<div class="max-w-3xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-semibold text-gray-800 mb-6">{{ __('app.saved_searches.my_title') }}</h1>

    @if ($searches->count() === 0)
        <div class="bg-white border border-gray-100 rounded-2xl p-12 text-center text-gray-500">
            {{ __('app.saved_searches.empty') }}
        </div>
    @else
        <div class="space-y-3">
            @foreach ($searches as $search)
                <div class="bg-white border border-gray-100 rounded-2xl p-5">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <div class="font-medium text-gray-800">{{ $search->name }}</div>
                            <div class="text-xs text-gray-500 mt-1">
                                {{ $search->frequency }} · {{ implode(' / ', $search->notify_via ?? []) }}
                                @if ($search->last_alert_at)
                                    · {{ __('app.saved_searches.last_alert', ['time' => $search->last_alert_at->diffForHumans()]) }}
                                @endif
                            </div>
                            @if ($search->query)
                                <div class="text-sm text-gray-700 mt-2">«{{ $search->query }}»</div>
                            @endif
                        </div>
                        <div class="flex items-center gap-2">
                            <button wire:click="toggle({{ $search->id }})" class="text-xs text-blue-600 hover:underline">
                                {{ $search->is_active ? __('app.saved_searches.pause') : __('app.saved_searches.resume') }}
                            </button>
                            <button wire:click="delete({{ $search->id }})" wire:confirm="{{ __('app.saved_searches.confirm_delete') }}" class="text-xs text-red-600 hover:underline">{{ __('app.actions.delete') }}</button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
