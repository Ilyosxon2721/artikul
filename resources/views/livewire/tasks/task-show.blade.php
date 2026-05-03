<div class="max-w-5xl mx-auto px-4 py-8">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-2 space-y-6">
            <div class="bg-white border border-gray-100 rounded-2xl p-6">
                <div class="flex items-center gap-2 text-xs text-gray-500 mb-2">
                    @if ($task->category)
                        <span>{{ $task->category->name() }}</span>
                    @endif
                    @if ($task->subcategory)
                        <span>·</span>
                        <span>{{ $task->subcategory->name() }}</span>
                    @endif
                    <span>·</span>
                    <span>{{ $task->published_at?->diffForHumans() }}</span>
                </div>

                <h1 class="text-2xl font-semibold text-gray-800">{{ $task->title }}</h1>

                <div class="flex flex-wrap items-center gap-2 mt-3 text-xs">
                    <span class="px-2 py-0.5 bg-blue-50 text-blue-700 rounded">{{ $task->type?->label() }}</span>
                    @foreach ($marketplaces as $mp)
                        <span class="px-2 py-0.5 bg-gray-100 text-gray-700 rounded uppercase">{{ $mp->code }}</span>
                    @endforeach
                </div>

                @if ($task->description)
                    <div class="prose prose-sm max-w-none mt-6 text-gray-700 whitespace-pre-line">{!! e($task->description) !!}</div>
                @endif

                @if ($task->attachments->count() > 0)
                    <div class="mt-6 border-t border-gray-100 pt-4">
                        <h2 class="text-sm font-semibold text-gray-700 mb-2">{{ __('app.tasks.attachments') }}</h2>
                        <ul class="space-y-1 text-sm">
                            @foreach ($task->attachments as $att)
                                <li>
                                    <a href="/storage/{{ $att->path }}" target="_blank" class="text-blue-600 hover:underline">{{ $att->filename }}</a>
                                    <span class="text-gray-400 text-xs">({{ number_format($att->size_bytes / 1024, 0) }} KB)</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>

        <aside class="space-y-4">
            <div class="bg-white border border-gray-100 rounded-2xl p-6 space-y-3">
                @if ($task->budget_type?->value === 'negotiable')
                    <div class="text-lg font-semibold text-gray-800">{{ __('app.tasks.budget_negotiable') }}</div>
                @else
                    <div>
                        <div class="text-2xl font-semibold text-gray-800">
                            @if ($task->budget_min)
                                {{ number_format((float) $task->budget_min, 0, '.', ' ') }}{{ $task->budget_max ? ' – '.number_format((float) $task->budget_max, 0, '.', ' ') : '' }}
                            @endif
                            <span class="text-sm text-gray-500">{{ $task->currency?->value }}</span>
                        </div>
                        <div class="text-xs text-gray-500">{{ $task->budget_type?->label() }}</div>
                    </div>
                @endif

                @if ($task->deadline_at)
                    <div class="text-sm text-gray-700">{{ __('app.tasks.deadline') }}: <strong>{{ $task->deadline_at->format('d.m.Y') }}</strong></div>
                @endif

                @if ($task->required_seller_level)
                    <div class="text-sm text-gray-700">{{ __('app.tasks.fields.required_seller_level') }}: <strong>{{ $task->required_seller_level->label() }}+</strong></div>
                @endif

                @if ($task->require_verified_seller)
                    <div class="text-sm text-blue-700">✓ {{ __('app.tasks.verified_only') }}</div>
                @endif

                <div class="border-t border-gray-100 pt-3 grid grid-cols-2 text-center text-xs">
                    <div>
                        <div class="text-base font-semibold text-gray-800">{{ $task->proposals_count }}</div>
                        <div class="text-gray-500">{{ __('app.tasks.stats.proposals') }}</div>
                    </div>
                    <div>
                        <div class="text-base font-semibold text-gray-800">{{ $task->views_count }}</div>
                        <div class="text-gray-500">{{ __('app.tasks.stats.views') }}</div>
                    </div>
                </div>

                @if ($canRespond)
                    <button type="button" disabled class="w-full px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg opacity-70 cursor-not-allowed" title="{{ __('app.tasks.respond_coming_soon') }}">
                        {{ __('app.tasks.actions.respond') }}
                    </button>
                @elseif ($canEdit)
                    <a href="{{ route('tasks.edit', $task->slug) }}" wire:navigate class="block text-center w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg">
                        {{ __('app.actions.edit') }}
                    </a>
                @else
                    @guest
                        <a href="{{ route('login') }}" class="block text-center w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg">
                            {{ __('app.tasks.login_to_respond') }}
                        </a>
                    @endguest
                @endif
            </div>

            <div class="bg-white border border-gray-100 rounded-2xl p-6">
                <h2 class="text-sm font-semibold text-gray-700 mb-3">{{ __('app.tasks.about_buyer') }}</h2>
                <div class="flex items-center gap-3">
                    @if ($task->buyer->avatar_url)
                        <img src="{{ $task->buyer->avatar_url }}" alt="" class="w-10 h-10 rounded-full object-cover" />
                    @else
                        <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-sm font-semibold">
                            {{ mb_strtoupper(mb_substr($task->buyer->name, 0, 1)) }}
                        </div>
                    @endif
                    <div>
                        @if ($task->buyer->username)
                            <a href="{{ route('public.buyer', $task->buyer->username) }}" wire:navigate class="text-sm font-medium text-gray-800 hover:underline">{{ $task->buyer->name }}</a>
                        @else
                            <span class="text-sm font-medium text-gray-800">{{ $task->buyer->name }}</span>
                        @endif
                        <div class="text-xs text-gray-500">
                            {{ $task->buyer->city }}{{ $task->buyer->country_code ? ', '.$task->buyer->country_code : '' }}
                        </div>
                    </div>
                </div>
                @if ($task->buyer->buyerProfile?->total_tasks_published)
                    <div class="text-xs text-gray-500 mt-3">
                        {{ trans_choice('app.tasks.buyer_history', $task->buyer->buyerProfile->total_tasks_published, ['count' => $task->buyer->buyerProfile->total_tasks_published]) }}
                    </div>
                @endif
            </div>
        </aside>
    </div>
</div>
