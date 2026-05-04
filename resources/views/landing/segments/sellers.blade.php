<x-layouts.public :title="__('app.landing.for_sellers_title')">
    <section class="bg-blue-50">
        <div class="max-w-5xl mx-auto px-4 py-16 text-center">
            <h1 class="text-4xl font-bold text-gray-900">{{ __('app.landing.for_sellers_hero_title') }}</h1>
            <p class="mt-4 text-lg text-gray-600 max-w-2xl mx-auto">{{ __('app.landing.for_sellers_hero_subtitle') }}</p>
            <a href="{{ route('register') }}" class="mt-6 inline-block px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg">{{ __('app.landing.cta_join') }}</a>
        </div>
    </section>

    <section class="max-w-5xl mx-auto px-4 py-16 grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach (['for_sellers_b1', 'for_sellers_b2', 'for_sellers_b3'] as $key)
            <div class="bg-white border border-gray-100 rounded-2xl p-6">
                <h2 class="text-lg font-semibold text-gray-800">{{ __('app.landing.'.$key.'_title') }}</h2>
                <p class="text-sm text-gray-600 mt-2">{{ __('app.landing.'.$key.'_body') }}</p>
            </div>
        @endforeach
    </section>
</x-layouts.public>
