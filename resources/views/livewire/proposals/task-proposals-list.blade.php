<div class="max-w-5xl mx-auto px-4 py-8">
    <div class="mb-6">
        <a href="{{ route('tasks.show', $task->slug) }}" class="text-sm text-blue-600 hover:underline">← {{ $task->title }}</a>
        <h1 class="text-2xl font-semibold text-gray-800 mt-2">{{ __('app.proposals.task_proposals_title', ['count' => $proposals->total()]) }}</h1>
    </div>

    @if (session('status'))
        <div class="mb-4 px-4 py-2 bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg">{{ session('status') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-4 px-4 py-2 bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg">{{ session('error') }}</div>
    @endif

    <div class="flex items-center justify-end mb-3">
        <select wire:model.live="sort" class="border-gray-300 rounded-md text-sm">
            <option value="newest">{{ __('app.proposals.sort.newest') }}</option>
            <option value="rating">{{ __('app.proposals.sort.rating') }}</option>
            <option value="price">{{ __('app.proposals.sort.price') }}</option>
        </select>
    </div>

    @if ($proposals->count() === 0)
        <div class="bg-white border border-gray-100 rounded-2xl p-12 text-center text-gray-500">{{ __('app.proposals.no_proposals_yet') }}</div>
    @else
        <div class="space-y-3">
            @foreach ($proposals as $proposal)
                <div class="bg-white border border-gray-100 rounded-2xl p-5">
                    <div class="flex items-start gap-4">
                        @if ($proposal->seller->avatar_url)
                            <img src="{{ $proposal->seller->avatar_url }}" alt="" class="w-12 h-12 rounded-full object-cover" />
                        @else
                            <div class="w-12 h-12 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-sm font-semibold">
                                {{ mb_strtoupper(mb_substr($proposal->seller->name, 0, 1)) }}
                            </div>
                        @endif

                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                @if ($proposal->seller->username)
                                    <a href="{{ route('public.seller', $proposal->seller->username) }}" class="font-medium text-gray-800 hover:underline">{{ $proposal->seller->name }}</a>
                                @else
                                    <span class="font-medium text-gray-800">{{ $proposal->seller->name }}</span>
                                @endif

                                @if ($proposal->seller->sellerProfile?->is_verified)
                                    <span class="text-xs bg-blue-50 text-blue-700 px-1.5 py-0.5 rounded">✓</span>
                                @endif

                                @if ($proposal->seller->sellerProfile?->rating_avg > 0)
                                    <span class="text-xs text-amber-600">★ {{ number_format((float) $proposal->seller->sellerProfile->rating_avg, 1) }}</span>
                                @endif
                            </div>

                            <p class="text-sm text-gray-700 mt-2 whitespace-pre-line">{{ $proposal->cover_letter }}</p>

                            <div class="flex items-center gap-3 text-xs text-gray-500 mt-3">
                                @if ($proposal->proposed_amount)
                                    <span class="font-medium text-gray-800">{{ number_format((float) $proposal->proposed_amount, 0, '.', ' ') }} {{ $proposal->currency?->value }}</span>
                                @endif
                                @if ($proposal->proposed_deadline_days)
                                    <span>· {{ trans_choice('app.proposals.deadline_days', $proposal->proposed_deadline_days, ['count' => $proposal->proposed_deadline_days]) }}</span>
                                @endif
                                <span>· {{ $proposal->created_at->diffForHumans() }}</span>
                                @if ($proposal->via_invitation)
                                    <span class="text-blue-600">· {{ __('app.proposals.via_invitation') }}</span>
                                @endif
                            </div>

                            @if ($proposal->status->value === 'pending')
                                <div class="flex items-center gap-2 mt-4">
                                    <button wire:click="accept({{ $proposal->id }})" class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg">{{ __('app.proposals.actions.accept') }}</button>
                                    <button wire:click="reject({{ $proposal->id }})" class="px-4 py-1.5 bg-white border border-gray-200 text-gray-700 text-sm rounded-lg hover:bg-gray-50">{{ __('app.proposals.actions.reject') }}</button>
                                </div>
                            @else
                                <div class="text-xs mt-4">
                                    <span class="px-2 py-0.5 rounded uppercase {{ $proposal->status->value === 'accepted' ? 'bg-green-50 text-green-700' : ($proposal->status->value === 'rejected' ? 'bg-red-50 text-red-700' : 'bg-gray-100 text-gray-600') }}">{{ $proposal->status->value }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-6">{{ $proposals->links() }}</div>
    @endif
</div>
