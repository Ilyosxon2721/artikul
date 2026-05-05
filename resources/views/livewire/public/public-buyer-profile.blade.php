<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center gap-4">
            @if ($user->avatar_url)
                <img src="{{ $user->avatar_url }}" alt="" class="w-20 h-20 rounded-full object-cover border" />
            @else
                <div class="w-20 h-20 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-2xl font-semibold">
                    {{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}
                </div>
            @endif

            <div>
                <h1 class="text-2xl font-semibold text-gray-800">{{ $user->buyerProfile->company_name ?: $user->name }}</h1>
                <p class="text-sm text-gray-500">{{ $user->city }}{{ $user->country_code ? ', '.$user->country_code : '' }}</p>
                @if ($user->buyerProfile->rating_avg > 0)
                    <p class="text-sm text-amber-600 mt-1">★ {{ number_format($user->buyerProfile->rating_avg, 1) }} ({{ $user->buyerProfile->reviews_count }})</p>
                @endif
            </div>
        </div>

        @if ($user->buyerProfile->description)
            <p class="mt-6 text-gray-700 whitespace-pre-line">{{ $user->buyerProfile->description }}</p>
        @endif

        @if (! empty($user->buyerProfile->marketplace_codes))
            <div class="mt-6">
                <h2 class="text-sm font-semibold text-gray-700 mb-2">{{ __('app.profile.fields.marketplaces') }}</h2>
                <div class="flex flex-wrap gap-2">
                    @foreach ($user->buyerProfile->marketplace_codes as $code)
                        <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs uppercase">{{ $code }}</span>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="mt-6 border-t border-gray-100 pt-6">
            <livewire:reviews.reviews-list :userId="$user->id" :wire:key="'reviews-buyer-'.$user->id" />
        </div>

        <div class="mt-6 grid grid-cols-2 sm:grid-cols-4 gap-3 text-center">
            <div class="p-3 bg-gray-50 rounded-lg">
                <div class="text-lg font-semibold text-gray-800">{{ $user->buyerProfile->total_tasks_published }}</div>
                <div class="text-xs text-gray-500">{{ __('app.profile.stats.tasks_published') }}</div>
            </div>
            <div class="p-3 bg-gray-50 rounded-lg">
                <div class="text-lg font-semibold text-gray-800">{{ $user->buyerProfile->total_contracts_completed }}</div>
                <div class="text-xs text-gray-500">{{ __('app.profile.stats.contracts_completed') }}</div>
            </div>
            <div class="p-3 bg-gray-50 rounded-lg">
                <div class="text-lg font-semibold text-gray-800">{{ number_format($user->buyerProfile->on_time_payment_rate, 0) }}%</div>
                <div class="text-xs text-gray-500">{{ __('app.profile.stats.on_time_payment') }}</div>
            </div>
            <div class="p-3 bg-gray-50 rounded-lg">
                <div class="text-lg font-semibold text-gray-800">{{ $user->buyerProfile->skus_count ?: '—' }}</div>
                <div class="text-xs text-gray-500">{{ __('app.profile.stats.skus') }}</div>
            </div>
        </div>
    </div>
</div>
