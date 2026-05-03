<x-app-layout>
    <div class="max-w-xl mx-auto px-4 py-12 text-center">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
            <h1 class="text-xl font-semibold text-gray-800">{{ __('app.mode.required_title') }}</h1>
            <p class="text-sm text-gray-500 mt-2">{{ __('app.mode.required_subtitle', ['mode' => __('app.mode.'.$mode)]) }}</p>

            <div class="mt-6">
                <livewire:mode-switcher />
            </div>
        </div>
    </div>
</x-app-layout>
