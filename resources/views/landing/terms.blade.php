<x-layouts.public :title="__('app.terms.title')">
    <article class="max-w-3xl mx-auto px-4 py-16 prose prose-blue">
        <h1>{{ __('app.terms.title') }}</h1>
        <p class="text-sm text-gray-500">{{ __('app.terms.last_updated', ['date' => '04.05.2026']) }}</p>

        <h2>1. {{ __('app.terms.s1_title') }}</h2>
        <p>{{ __('app.terms.s1_body') }}</p>

        <h2>2. {{ __('app.terms.s2_title') }}</h2>
        <p>{{ __('app.terms.s2_body') }}</p>

        <h2>3. {{ __('app.terms.s3_title') }}</h2>
        <p>{{ __('app.terms.s3_body') }}</p>

        <h2>4. {{ __('app.terms.s4_title') }}</h2>
        <p>{{ __('app.terms.s4_body') }}</p>

        <h2>5. {{ __('app.terms.s5_title') }}</h2>
        <p>{{ __('app.terms.s5_body') }}</p>

        <h2>6. {{ __('app.terms.s6_title') }}</h2>
        <p>{{ __('app.terms.s6_body') }}</p>

        <h2>7. {{ __('app.terms.s7_title') }}</h2>
        <p>{{ __('app.terms.s7_body') }}</p>

        <p class="mt-8 text-sm text-gray-500">{{ __('app.terms.contact') }} <a href="mailto:legal@artikul.uz">legal@artikul.uz</a>.</p>
    </article>
</x-layouts.public>
