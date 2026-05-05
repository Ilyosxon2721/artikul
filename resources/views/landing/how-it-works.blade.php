<x-layouts.public :title="__('app.how.title')">
    <article class="max-w-3xl mx-auto px-4 py-16">
        <h1 class="text-3xl font-semibold text-gray-900">{{ __('app.how.title') }}</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-10">
            <div class="bg-white border border-gray-100 rounded-2xl p-6">
                <h2 class="text-lg font-semibold text-blue-700 mb-4">{{ __('app.landing.for_buyers') }}</h2>
                <ol class="space-y-3 text-sm text-gray-700 list-decimal list-inside">
                    <li>{{ __('app.landing.buyer_step1') }}</li>
                    <li>{{ __('app.landing.buyer_step2') }}</li>
                    <li>{{ __('app.landing.buyer_step3') }}</li>
                </ol>
            </div>
            <div class="bg-white border border-gray-100 rounded-2xl p-6">
                <h2 class="text-lg font-semibold text-blue-700 mb-4">{{ __('app.landing.for_sellers') }}</h2>
                <ol class="space-y-3 text-sm text-gray-700 list-decimal list-inside">
                    <li>{{ __('app.landing.seller_step1') }}</li>
                    <li>{{ __('app.landing.seller_step2') }}</li>
                    <li>{{ __('app.landing.seller_step3') }}</li>
                </ol>
            </div>
        </div>
    </article>
</x-layouts.public>
