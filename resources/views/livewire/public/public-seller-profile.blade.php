@php($profile = $user->sellerProfile)
<div class="max-w-5xl mx-auto px-4 py-8">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-start gap-4">
                    @if ($user->avatar_url)
                        <img src="{{ $user->avatar_url }}" alt="" class="w-20 h-20 rounded-full object-cover border" />
                    @else
                        <div class="w-20 h-20 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-2xl font-semibold">
                            {{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}
                        </div>
                    @endif

                    <div class="flex-1">
                        <div class="flex items-center gap-2">
                            <h1 class="text-2xl font-semibold text-gray-800">{{ $user->name }}</h1>
                            @if ($profile->is_verified)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-blue-50 text-blue-700 rounded text-xs font-medium">
                                    ✓ {{ __('app.profile.verified') }}
                                </span>
                            @endif
                        </div>
                        @if ($profile->headline)
                            <p class="text-gray-600 mt-1">{{ $profile->headline }}</p>
                        @endif
                        <div class="flex items-center gap-4 mt-2 text-sm text-gray-500">
                            <span>{{ $user->city }}{{ $user->country_code ? ', '.$user->country_code : '' }}</span>
                            @if ($profile->rating_avg > 0)
                                <span class="text-amber-600">★ {{ number_format($profile->rating_avg, 1) }} ({{ $profile->reviews_count }})</span>
                            @endif
                        </div>
                    </div>
                </div>

                @if ($profile->bio)
                    <div class="prose prose-sm max-w-none mt-6 text-gray-700">{!! nl2br(e($profile->bio)) !!}</div>
                @endif
            </div>

            @if ($profile->specializations->count() > 0)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-sm font-semibold text-gray-700 mb-3">{{ __('app.profile.fields.specializations') }}</h2>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($profile->specializations as $spec)
                            <span class="px-3 py-1 bg-blue-50 text-blue-700 rounded-full text-sm">{{ $spec->name() }}</span>
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($profile->marketplaces->count() > 0)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-sm font-semibold text-gray-700 mb-3">{{ __('app.profile.fields.marketplaces_levels') }}</h2>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                        @foreach ($profile->marketplaces as $marketplace)
                            <div class="flex items-center justify-between px-3 py-2 bg-gray-50 rounded-lg">
                                <span class="text-sm text-gray-800">{{ $marketplace->name() }}</span>
                                <span class="text-xs uppercase text-gray-500">{{ $marketplace->pivot->level }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <aside class="space-y-4">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-3">
                @if ($profile->hourly_rate)
                    <div>
                        <div class="text-2xl font-semibold text-gray-800">{{ number_format((float) $profile->hourly_rate, 0, '.', ' ') }} {{ $profile->currency?->value }}</div>
                        <div class="text-xs text-gray-500">{{ __('app.profile.per_hour') }}</div>
                    </div>
                @endif

                @if ($profile->available_hours_per_week)
                    <div class="text-sm text-gray-700">{{ __('app.profile.hours_available', ['hours' => $profile->available_hours_per_week]) }}</div>
                @endif

                @if (! empty($profile->languages))
                    <div>
                        <div class="text-xs text-gray-500 uppercase tracking-wider mb-1">{{ __('app.profile.fields.languages') }}</div>
                        <div class="text-sm text-gray-700">{{ implode(', ', $profile->languages) }}</div>
                    </div>
                @endif
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 grid grid-cols-2 gap-3 text-center">
                <div>
                    <div class="text-lg font-semibold text-gray-800">{{ $profile->total_contracts_completed }}</div>
                    <div class="text-xs text-gray-500">{{ __('app.profile.stats.completed') }}</div>
                </div>
                <div>
                    <div class="text-lg font-semibold text-gray-800">{{ number_format((float) $profile->completion_rate, 0) }}%</div>
                    <div class="text-xs text-gray-500">{{ __('app.profile.stats.completion_rate') }}</div>
                </div>
                <div>
                    <div class="text-lg font-semibold text-gray-800">{{ number_format((float) $profile->on_time_delivery_rate, 0) }}%</div>
                    <div class="text-xs text-gray-500">{{ __('app.profile.stats.on_time') }}</div>
                </div>
                <div>
                    <div class="text-lg font-semibold text-gray-800">{{ $profile->reviews_count }}</div>
                    <div class="text-xs text-gray-500">{{ __('app.profile.stats.reviews') }}</div>
                </div>
            </div>
        </aside>
    </div>
</div>
