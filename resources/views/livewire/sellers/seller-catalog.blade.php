<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">{{ __('app.sellers.catalog_title') }}</h1>
        <p class="text-sm text-gray-500 mt-1">{{ __('app.sellers.catalog_subtitle') }}</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <aside class="lg:col-span-1">
            <div class="bg-white border border-gray-100 rounded-2xl p-4 space-y-4 sticky top-4">
                <div>
                    <x-input-label :value="__('app.sellers.fields.specializations')" />
                    <div class="mt-1 space-y-1 max-h-48 overflow-y-auto pr-1">
                        @foreach ($specializations as $spec)
                            <label class="flex items-center gap-2 text-sm">
                                <input type="checkbox" wire:model.live="specializationIds" value="{{ $spec->id }}" class="rounded border-gray-300 text-blue-600" />
                                {{ $spec->name() }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <x-input-label :value="__('app.sellers.fields.marketplaces')" />
                    <div class="mt-1 space-y-1">
                        @foreach ($marketplaces as $mp)
                            <label class="flex items-center gap-2 text-sm">
                                <input type="checkbox" wire:model.live="marketplaceIds" value="{{ $mp->id }}" class="rounded border-gray-300 text-blue-600" />
                                {{ $mp->name() }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <x-input-label :value="__('app.sellers.fields.rate_min')" />
                        <x-text-input wire:model.live.debounce.500ms="rateMin" type="number" class="mt-1 block w-full text-sm" />
                    </div>
                    <div>
                        <x-input-label :value="__('app.sellers.fields.rate_max')" />
                        <x-text-input wire:model.live.debounce.500ms="rateMax" type="number" class="mt-1 block w-full text-sm" />
                    </div>
                </div>

                <div>
                    <x-input-label :value="__('app.sellers.fields.min_rating')" />
                    <select wire:model.live="minRating" class="mt-1 block w-full border-gray-300 rounded-md text-sm">
                        <option value="">{{ __('app.sellers.any') }}</option>
                        <option value="3">★ 3+</option>
                        <option value="4">★ 4+</option>
                        <option value="5">★ 5</option>
                    </select>
                </div>

                <div>
                    <x-input-label :value="__('app.sellers.fields.country')" />
                    <x-text-input wire:model.live.debounce.500ms="country" type="text" maxlength="2" placeholder="UZ" class="mt-1 block w-full text-sm" />
                </div>

                <label class="flex items-center gap-2 text-sm">
                    <input type="checkbox" wire:model.live="verifiedOnly" class="rounded border-gray-300 text-blue-600" />
                    {{ __('app.sellers.verified_only') }}
                </label>

                <button type="button" wire:click="clearFilters" class="text-xs text-blue-600 hover:underline">
                    {{ __('app.sellers.clear_filters') }}
                </button>
            </div>
        </aside>

        <main class="lg:col-span-3">
            <div class="flex items-center justify-between mb-4 gap-2 flex-wrap">
                <input type="search" wire:model.live.debounce.400ms="search" placeholder="{{ __('app.sellers.search_placeholder') }}"
                       class="flex-1 min-w-0 border-gray-300 rounded-md text-sm focus:border-blue-500 focus:ring-blue-500" />

                <select wire:model.live="sort" class="border-gray-300 rounded-md text-sm">
                    <option value="recommended">{{ __('app.sellers.sort.recommended') }}</option>
                    <option value="rating">{{ __('app.sellers.sort.rating') }}</option>
                    <option value="newest">{{ __('app.sellers.sort.newest') }}</option>
                </select>
            </div>

            @if ($sellers->count() === 0)
                <div class="bg-white border border-gray-100 rounded-2xl p-12 text-center text-gray-500">
                    {{ __('app.sellers.empty') }}
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach ($sellers as $profile)
                        <div class="bg-white border border-gray-100 rounded-2xl p-5 hover:border-blue-200 hover:shadow transition">
                            <div class="flex items-start gap-3">
                                @if ($profile->user->avatar_url)
                                    <img src="{{ $profile->user->avatar_url }}" alt="" class="w-12 h-12 rounded-full object-cover" />
                                @else
                                    <div class="w-12 h-12 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-sm font-semibold">
                                        {{ mb_strtoupper(mb_substr($profile->user->name, 0, 1)) }}
                                    </div>
                                @endif

                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2">
                                        @if ($profile->user->username)
                                            <a href="{{ route('public.seller', $profile->user->username) }}" wire:navigate class="text-base font-medium text-gray-800 hover:underline truncate">{{ $profile->user->name }}</a>
                                        @else
                                            <span class="text-base font-medium text-gray-800 truncate">{{ $profile->user->name }}</span>
                                        @endif
                                        @if ($profile->is_verified)
                                            <span class="text-xs bg-blue-50 text-blue-700 px-1.5 py-0.5 rounded">✓</span>
                                        @endif
                                    </div>
                                    @if ($profile->headline)
                                        <p class="text-sm text-gray-600 mt-1 line-clamp-2">{{ $profile->headline }}</p>
                                    @endif

                                    <div class="flex items-center gap-3 mt-2 text-xs text-gray-500">
                                        @if ($profile->rating_avg > 0)
                                            <span class="text-amber-600">★ {{ number_format((float) $profile->rating_avg, 1) }}</span>
                                            <span>({{ $profile->reviews_count }})</span>
                                        @endif
                                        @if ($profile->user->city)
                                            <span>· {{ $profile->user->city }}</span>
                                        @endif
                                    </div>

                                    @if ($profile->specializations->count() > 0)
                                        <div class="flex flex-wrap gap-1 mt-2">
                                            @foreach ($profile->specializations->take(3) as $spec)
                                                <span class="text-xs bg-gray-100 text-gray-700 px-2 py-0.5 rounded">{{ $spec->name() }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>

                                <div class="text-right shrink-0">
                                    @if ($profile->hourly_rate)
                                        <div class="text-sm font-semibold text-gray-800">{{ number_format((float) $profile->hourly_rate, 0, '.', ' ') }}</div>
                                        <div class="text-xs text-gray-500">{{ $profile->currency?->value }} / {{ __('app.sellers.hour') }}</div>
                                    @endif
                                </div>
                            </div>

                            @if ($profile->user->username)
                                <div class="mt-4 flex items-center gap-2">
                                    <a href="{{ route('public.seller', $profile->user->username) }}" wire:navigate class="text-sm text-blue-600 hover:underline">
                                        {{ __('app.sellers.actions.view_profile') }}
                                    </a>
                                    @auth
                                        <a href="#" class="ml-auto text-xs text-gray-500 hover:text-gray-700">{{ __('app.sellers.actions.invite') }}</a>
                                    @endauth
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <div class="mt-6">{{ $sellers->links() }}</div>
            @endif
        </main>
    </div>
</div>
