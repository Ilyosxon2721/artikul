<div class="max-w-3xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-semibold text-gray-800 mb-6">{{ __('app.notifications_prefs.title') }}</h1>

    @if (session('status'))
        <div class="mb-4 px-4 py-2 bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg">{{ session('status') }}</div>
    @endif

    <form wire:submit="save" class="bg-white border border-gray-100 rounded-2xl p-6 space-y-6">
        <div>
            <x-input-label :value="__('app.notifications_prefs.frequency')" />
            <select wire:model="frequency" class="mt-1 block w-full border-gray-300 rounded-md">
                <option value="instant">{{ __('app.notifications_prefs.instant') }}</option>
                <option value="daily">{{ __('app.notifications_prefs.daily_digest') }}</option>
                <option value="important_only">{{ __('app.notifications_prefs.important_only') }}</option>
            </select>
        </div>

        <div>
            <h2 class="text-sm font-semibold text-gray-700 mb-3">{{ __('app.notifications_prefs.channels_title') }}</h2>
            <table class="w-full text-sm">
                <thead class="text-xs text-gray-500 uppercase tracking-wider">
                    <tr>
                        <th class="text-left py-2">{{ __('app.notifications_prefs.category') }}</th>
                        @foreach ($channels as $channel)
                            <th class="text-center px-2">{{ $channel }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($categories as $cat)
                        <tr>
                            <td class="py-2 text-gray-800">{{ __('app.notifications_prefs.cat.'.$cat) }}</td>
                            @foreach ($channels as $channel)
                                <td class="text-center px-2">
                                    <input type="checkbox" wire:model="prefs.{{ $cat }}.{{ $channel }}" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="flex justify-end">
            <x-primary-button class="bg-blue-600 hover:bg-blue-700">{{ __('app.actions.save') }}</x-primary-button>
        </div>
    </form>
</div>
