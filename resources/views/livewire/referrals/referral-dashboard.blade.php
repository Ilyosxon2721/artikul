<div class="max-w-3xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-semibold text-gray-800 mb-2">{{ __('app.referrals.title') }}</h1>
    <p class="text-sm text-gray-500 mb-6">{{ __('app.referrals.subtitle') }}</p>

    <div class="bg-white border border-gray-100 rounded-2xl p-6 mb-6">
        <div class="text-xs text-gray-500 uppercase tracking-wider">{{ __('app.referrals.your_link') }}</div>
        <div class="font-mono text-sm text-blue-700 mt-1 break-all">{{ $referralUrl }}</div>
        <div class="text-xs text-gray-500 mt-3">{{ __('app.referrals.code_label') }}: <strong>{{ $user->referral_code }}</strong></div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-6">
        <div class="bg-white border border-gray-100 rounded-2xl p-5 text-center">
            <div class="text-2xl font-semibold text-gray-800">{{ $referrals->count() }}</div>
            <div class="text-xs text-gray-500 mt-1">{{ __('app.referrals.stats.total') }}</div>
        </div>
        <div class="bg-white border border-gray-100 rounded-2xl p-5 text-center">
            <div class="text-2xl font-semibold text-gray-800">0</div>
            <div class="text-xs text-gray-500 mt-1">{{ __('app.referrals.stats.active') }}</div>
        </div>
        <div class="bg-white border border-gray-100 rounded-2xl p-5 text-center">
            <div class="text-2xl font-semibold text-gray-800">$0</div>
            <div class="text-xs text-gray-500 mt-1">{{ __('app.referrals.stats.earned') }}</div>
        </div>
    </div>

    <div class="bg-white border border-gray-100 rounded-2xl p-6">
        <h2 class="font-medium text-gray-800 mb-3">{{ __('app.referrals.invited_users') }}</h2>
        @if ($referrals->count() === 0)
            <p class="text-sm text-gray-500">{{ __('app.referrals.no_invites') }}</p>
        @else
            <ul class="divide-y divide-gray-100 text-sm">
                @foreach ($referrals as $ref)
                    <li class="py-2 flex items-center justify-between">
                        <span class="text-gray-800">{{ $ref->name }}</span>
                        <span class="text-xs text-gray-500">{{ $ref->created_at->format('d.m.Y') }}</span>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>
