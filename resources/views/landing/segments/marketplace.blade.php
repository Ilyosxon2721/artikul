<x-layouts.public :title="$marketplace->name()">
    <section class="bg-gradient-to-br from-blue-50 to-white">
        <div class="max-w-5xl mx-auto px-4 py-16 text-center">
            <div class="text-xs uppercase tracking-widest text-blue-700">{{ $marketplace->code }}</div>
            <h1 class="text-4xl font-bold text-gray-900 mt-2">{{ __('app.landing.marketplace_hero_title', ['marketplace' => $marketplace->name()]) }}</h1>
            <p class="mt-4 text-lg text-gray-600 max-w-2xl mx-auto">{{ __('app.landing.marketplace_hero_subtitle', ['marketplace' => $marketplace->name()]) }}</p>
            <div class="mt-6 flex items-center justify-center gap-3">
                <a href="{{ route('register') }}" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg">{{ __('app.landing.cta_create_task') }}</a>
                <a href="{{ route('sellers.index', ['mp' => [$marketplace->id]]) }}" class="px-6 py-3 bg-white border border-blue-200 text-blue-700 text-sm font-medium rounded-lg">{{ __('app.landing.cta_find_work') }}</a>
            </div>
        </div>
    </section>

    @if ($sellers->count() > 0)
        <section class="max-w-5xl mx-auto px-4 py-16">
            <h2 class="text-2xl font-semibold text-gray-900 mb-6">{{ __('app.landing.marketplace_top_sellers', ['marketplace' => $marketplace->name()]) }}</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @foreach ($sellers as $profile)
                    <div class="bg-white border border-gray-100 rounded-2xl p-5">
                        <div class="font-medium text-gray-800">{{ $profile->user?->name ?? '—' }}</div>
                        @if ($profile->headline)
                            <div class="text-sm text-gray-600 mt-1 line-clamp-2">{{ $profile->headline }}</div>
                        @endif
                    </div>
                @endforeach
            </div>
        </section>
    @endif
</x-layouts.public>
