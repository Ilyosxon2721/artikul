<div>
    {{-- Hero --}}
    <section class="bg-gradient-to-br from-blue-50 via-white to-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24 text-center">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 tracking-tight">
                {{ __('app.landing.hero_title') }}
            </h1>
            <p class="mt-4 text-lg text-gray-600 max-w-2xl mx-auto">
                {{ __('app.landing.hero_subtitle') }}
            </p>
            <div class="mt-8 flex items-center justify-center gap-3">
                <a href="{{ route('register') }}" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg shadow-sm">
                    {{ __('app.landing.cta_create_task') }}
                </a>
                <a href="{{ route('sellers.index') }}" class="px-6 py-3 bg-white text-blue-700 border border-blue-200 hover:border-blue-400 text-sm font-medium rounded-lg">
                    {{ __('app.landing.cta_find_work') }}
                </a>
            </div>

            <div class="mt-10 grid grid-cols-3 gap-4 max-w-2xl mx-auto">
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-900">{{ $stats['sellers'] }}+</div>
                    <div class="text-xs text-gray-500 mt-1">{{ __('app.landing.stats.sellers') }}</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-900">{{ $stats['marketplaces'] }}</div>
                    <div class="text-xs text-gray-500 mt-1">{{ __('app.landing.stats.marketplaces') }}</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-900">{{ $stats['tasks'] }}+</div>
                    <div class="text-xs text-gray-500 mt-1">{{ __('app.landing.stats.tasks') }}</div>
                </div>
            </div>
        </div>
    </section>

    {{-- How it works --}}
    <section class="py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl md:text-3xl font-semibold text-center text-gray-900">{{ __('app.landing.how_title') }}</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-10">
                <div class="bg-white border border-gray-100 rounded-2xl p-6">
                    <h3 class="text-lg font-semibold text-blue-700 mb-4">{{ __('app.landing.for_buyers') }}</h3>
                    <ol class="space-y-3 text-sm text-gray-700">
                        <li class="flex gap-3"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-semibold">1</span><span>{{ __('app.landing.buyer_step1') }}</span></li>
                        <li class="flex gap-3"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-semibold">2</span><span>{{ __('app.landing.buyer_step2') }}</span></li>
                        <li class="flex gap-3"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-semibold">3</span><span>{{ __('app.landing.buyer_step3') }}</span></li>
                    </ol>
                </div>
                <div class="bg-white border border-gray-100 rounded-2xl p-6">
                    <h3 class="text-lg font-semibold text-blue-700 mb-4">{{ __('app.landing.for_sellers') }}</h3>
                    <ol class="space-y-3 text-sm text-gray-700">
                        <li class="flex gap-3"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-semibold">1</span><span>{{ __('app.landing.seller_step1') }}</span></li>
                        <li class="flex gap-3"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-semibold">2</span><span>{{ __('app.landing.seller_step2') }}</span></li>
                        <li class="flex gap-3"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-semibold">3</span><span>{{ __('app.landing.seller_step3') }}</span></li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    {{-- Marketplaces --}}
    <section class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-xl font-semibold text-center text-gray-700">{{ __('app.landing.marketplaces_title') }}</h2>
            <div class="mt-6 grid grid-cols-2 md:grid-cols-6 gap-4">
                @foreach ($marketplaces as $mp)
                    <div class="bg-white border border-gray-100 rounded-lg px-4 py-3 text-center">
                        <div class="font-semibold text-gray-800">{{ $mp->name() }}</div>
                        <div class="text-xs text-gray-400 uppercase">{{ $mp->code }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Categories --}}
    <section class="py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-semibold text-gray-900 text-center">{{ __('app.landing.categories_title') }}</h2>
            <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                @foreach ($categories as $cat)
                    <a href="{{ route('tasks.index', ['cat' => $cat->id]) }}" class="block bg-white border border-gray-100 hover:border-blue-200 rounded-2xl p-5 transition shadow-sm hover:shadow">
                        <div class="text-base font-medium text-gray-800">{{ $cat->name() }}</div>
                        @if ($cat->description_ru)
                            <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ $cat->{'description_'.app()->getLocale()} ?? $cat->description_ru }}</p>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Top sellers --}}
    @if ($topSellers->count() > 0)
        <section class="py-12 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-2xl font-semibold text-gray-900 text-center">{{ __('app.landing.top_sellers_title') }}</h2>
                <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-4">
                    @foreach ($topSellers as $profile)
                        <a href="{{ $profile->user->username ? route('public.seller', $profile->user->username) : '#' }}" class="block bg-white border border-gray-100 hover:border-blue-200 rounded-2xl p-5 text-center transition">
                            @if ($profile->user->avatar_url)
                                <img src="{{ $profile->user->avatar_url }}" alt="" class="w-14 h-14 rounded-full mx-auto object-cover" />
                            @else
                                <div class="w-14 h-14 rounded-full bg-blue-100 text-blue-700 mx-auto flex items-center justify-center text-lg font-semibold">{{ mb_strtoupper(mb_substr($profile->user->name, 0, 1)) }}</div>
                            @endif
                            <div class="font-medium text-gray-800 mt-3 truncate">{{ $profile->user->name }}</div>
                            @if ($profile->headline)
                                <div class="text-xs text-gray-500 mt-1 line-clamp-2">{{ $profile->headline }}</div>
                            @endif
                            @if ($profile->rating_avg > 0)
                                <div class="text-xs text-amber-600 mt-2">★ {{ number_format((float) $profile->rating_avg, 1) }}</div>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Fresh tasks --}}
    @if ($freshTasks->count() > 0)
        <section class="py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-semibold text-gray-900">{{ __('app.landing.fresh_tasks_title') }}</h2>
                    <a href="{{ route('tasks.index') }}" class="text-sm text-blue-600 hover:underline">{{ __('app.landing.see_all') }}</a>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach ($freshTasks as $task)
                        <a href="{{ route('tasks.show', $task->slug) }}" class="block bg-white border border-gray-100 hover:border-blue-200 rounded-2xl p-5 transition">
                            <h3 class="font-medium text-gray-800 truncate">{{ $task->title }}</h3>
                            <div class="flex items-center justify-between text-xs text-gray-500 mt-2">
                                <span>{{ $task->buyer->name }}</span>
                                <span>{{ $task->published_at?->diffForHumans() }}</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Pricing teaser --}}
    <section class="py-16 bg-blue-600 text-white">
        <div class="max-w-3xl mx-auto px-4 text-center">
            <h2 class="text-2xl md:text-3xl font-semibold">{{ __('app.landing.pricing_title') }}</h2>
            <p class="mt-3 text-blue-100">{{ __('app.landing.pricing_subtitle') }}</p>
            <a href="{{ route('register') }}" class="mt-6 inline-block px-6 py-3 bg-white text-blue-700 font-medium rounded-lg">{{ __('app.landing.cta_join') }}</a>
        </div>
    </section>

    {{-- FAQ --}}
    <section class="py-16">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-semibold text-gray-900 text-center mb-8">{{ __('app.landing.faq_title') }}</h2>
            @foreach (range(1, 8) as $i)
                <details class="bg-white border border-gray-100 rounded-lg p-4 mb-2 group">
                    <summary class="font-medium text-gray-800 cursor-pointer list-none flex items-center justify-between">
                        {{ __('app.landing.faq.q'.$i) }}
                        <span class="text-gray-400 group-open:rotate-180 transition">▾</span>
                    </summary>
                    <p class="text-sm text-gray-600 mt-3">{{ __('app.landing.faq.a'.$i) }}</p>
                </details>
            @endforeach
        </div>
    </section>
</div>
