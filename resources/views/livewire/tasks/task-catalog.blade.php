<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="flex items-end justify-between mb-6 gap-4 flex-wrap">
        <div>
            <h1 class="text-2xl font-semibold text-gray-800">{{ __('app.tasks.catalog_title') }}</h1>
            <p class="text-sm text-gray-500 mt-1">{{ __('app.tasks.catalog_subtitle') }}</p>
        </div>

        @auth
            <a href="{{ route('tasks.create') }}" wire:navigate class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg">
                {{ __('app.tasks.actions.create') }}
            </a>
        @endauth
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <aside class="lg:col-span-1">
            <div class="bg-white border border-gray-100 rounded-2xl p-4 space-y-4 sticky top-4">
                <div>
                    <x-input-label :value="__('app.tasks.fields.task_type')" />
                    <select wire:model.live="taskType" class="mt-1 block w-full border-gray-300 rounded-md text-sm">
                        <option value="">{{ __('app.tasks.any') }}</option>
                        <option value="gig">{{ __('app.tasks.type.gig') }}</option>
                        <option value="project">{{ __('app.tasks.type.project') }}</option>
                        <option value="hourly">{{ __('app.tasks.type.hourly') }}</option>
                    </select>
                </div>

                <div>
                    <x-input-label :value="__('app.tasks.fields.category')" />
                    <select wire:model.live="categoryId" class="mt-1 block w-full border-gray-300 rounded-md text-sm">
                        <option value="">{{ __('app.tasks.any') }}</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name() }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <x-input-label :value="__('app.tasks.fields.marketplaces')" />
                    <div class="mt-1 space-y-1">
                        @foreach ($marketplaces as $marketplace)
                            <label class="flex items-center gap-2 text-sm">
                                <input type="checkbox" wire:model.live="marketplaceIds" value="{{ $marketplace->id }}" class="rounded border-gray-300 text-blue-600" />
                                {{ $marketplace->name() }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <x-input-label :value="__('app.tasks.fields.budget_min')" />
                    <x-text-input wire:model.live.debounce.500ms="budgetMin" type="number" class="mt-1 block w-full text-sm" />
                </div>

                <div>
                    <x-input-label :value="__('app.tasks.fields.budget_max')" />
                    <x-text-input wire:model.live.debounce.500ms="budgetMax" type="number" class="mt-1 block w-full text-sm" />
                </div>

                <label class="flex items-center gap-2 text-sm">
                    <input type="checkbox" wire:model.live="verifiedBuyerOnly" class="rounded border-gray-300 text-blue-600" />
                    {{ __('app.tasks.verified_buyer_only') }}
                </label>

                <div class="flex items-center justify-between pt-2">
                    <button type="button" wire:click="clearFilters" class="text-xs text-blue-600 hover:underline">
                        {{ __('app.tasks.clear_filters') }}
                    </button>
                    @auth
                        <livewire:search.save-search
                            :filters="['marketplace_ids' => $marketplaceIds, 'category_id' => $categoryId, 'task_type' => $taskType, 'budget_min' => $budgetMin, 'budget_max' => $budgetMax]"
                            :query="$search"
                            :key="'save-search-'.implode('-', $marketplaceIds).'-'.($categoryId ?? '0')" />
                    @endauth
                </div>
            </div>
        </aside>

        <main class="lg:col-span-3">
            <div class="flex items-center justify-between mb-4 gap-2 flex-wrap">
                <input type="search" wire:model.live.debounce.400ms="search" placeholder="{{ __('app.tasks.search_placeholder') }}"
                       class="flex-1 min-w-0 border-gray-300 rounded-md text-sm focus:border-blue-500 focus:ring-blue-500" />

                <select wire:model.live="sort" class="border-gray-300 rounded-md text-sm">
                    <option value="newest">{{ __('app.tasks.sort.newest') }}</option>
                    <option value="budget">{{ __('app.tasks.sort.budget') }}</option>
                    <option value="deadline">{{ __('app.tasks.sort.deadline') }}</option>
                </select>
            </div>

            @if ($tasks->count() === 0)
                <div class="bg-white border border-gray-100 rounded-2xl p-12 text-center text-gray-500">
                    {{ __('app.tasks.empty') }}
                </div>
            @else
                <div class="space-y-3">
                    @foreach ($tasks as $task)
                        <a href="{{ route('tasks.show', $task->slug) }}" wire:navigate
                           class="block bg-white border border-gray-100 hover:border-blue-200 rounded-2xl p-5 transition shadow-sm hover:shadow">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1 min-w-0">
                                    <h2 class="text-lg font-medium text-gray-800 truncate">{{ $task->title }}</h2>
                                    <p class="text-sm text-gray-500 mt-1 line-clamp-2">{{ \Illuminate\Support\Str::limit($task->description, 200) }}</p>
                                    <div class="flex flex-wrap items-center gap-2 mt-3 text-xs text-gray-500">
                                        <span class="px-2 py-0.5 bg-blue-50 text-blue-700 rounded">{{ $task->type?->label() }}</span>
                                        @if ($task->category)
                                            <span>{{ $task->category->name() }}</span>
                                        @endif
                                        <span>· {{ $task->published_at?->diffForHumans() }}</span>
                                        <span>· {{ trans_choice('app.tasks.proposals_count', $task->proposals_count, ['count' => $task->proposals_count]) }}</span>
                                    </div>
                                </div>

                                <div class="text-right shrink-0">
                                    @if ($task->budget_type?->value === 'negotiable')
                                        <div class="text-sm text-gray-500">{{ __('app.tasks.budget_negotiable') }}</div>
                                    @else
                                        <div class="text-base font-semibold text-gray-800">
                                            @if ($task->budget_min)
                                                {{ number_format((float) $task->budget_min, 0, '.', ' ') }}{{ $task->budget_max ? ' – '.number_format((float) $task->budget_max, 0, '.', ' ') : '' }}
                                            @endif
                                            <span class="text-xs text-gray-500">{{ $task->currency?->value }}</span>
                                        </div>
                                    @endif
                                    @if ($task->deadline_at)
                                        <div class="text-xs text-gray-500 mt-1">{{ __('app.tasks.until') }} {{ $task->deadline_at->format('d.m.Y') }}</div>
                                    @endif
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                <div class="mt-6">{{ $tasks->links() }}</div>
            @endif
        </main>
    </div>
</div>
