<div class="max-w-5xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-semibold text-gray-800 mb-6">{{ __('app.search.title') }}</h1>

    <div class="mb-6">
        <input type="search" wire:model.live.debounce.300ms="query" placeholder="{{ __('app.search.placeholder') }}"
               class="w-full text-base border-gray-300 rounded-lg focus:border-blue-500 focus:ring-blue-500" />
    </div>

    <div class="flex gap-2 mb-6 border-b border-gray-200">
        @foreach (['tasks' => __('app.search.tabs.tasks'), 'sellers' => __('app.search.tabs.sellers')] as $key => $label)
            <button type="button" wire:click="setTab('{{ $key }}')"
                    class="px-4 py-2 text-sm font-medium border-b-2 transition {{ $tab === $key ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                {{ $label }}
            </button>
        @endforeach
    </div>

    @if ($query === '')
        <p class="text-sm text-gray-500">{{ __('app.search.start_typing') }}</p>
    @elseif ($tab === 'tasks')
        @if ($tasks->count() === 0)
            <p class="text-sm text-gray-500">{{ __('app.search.no_results') }}</p>
        @else
            <div class="space-y-3">
                @foreach ($tasks as $task)
                    <a href="{{ route('tasks.show', $task->slug) }}" class="block bg-white border border-gray-100 hover:border-blue-200 rounded-2xl p-5 transition">
                        <h2 class="font-medium text-gray-800">{{ $task->title }}</h2>
                        <p class="text-sm text-gray-500 mt-1 line-clamp-2">{{ \Illuminate\Support\Str::limit($task->description, 200) }}</p>
                    </a>
                @endforeach
            </div>
        @endif
    @else
        @if ($sellers->count() === 0)
            <p class="text-sm text-gray-500">{{ __('app.search.no_results') }}</p>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @foreach ($sellers as $profile)
                    <a href="{{ $profile->user?->username ? route('public.seller', $profile->user->username) : '#' }}" class="block bg-white border border-gray-100 hover:border-blue-200 rounded-2xl p-5 transition">
                        <div class="font-medium text-gray-800">{{ $profile->user?->name }}</div>
                        @if ($profile->headline)
                            <p class="text-sm text-gray-600 mt-1 line-clamp-2">{{ $profile->headline }}</p>
                        @endif
                    </a>
                @endforeach
            </div>
        @endif
    @endif
</div>
