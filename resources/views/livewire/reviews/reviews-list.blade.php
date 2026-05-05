<div>
    <div class="flex items-center justify-between mb-3">
        <h2 class="text-sm font-semibold text-gray-700">{{ __('app.reviews.list_title', ['count' => $reviews->total()]) }}</h2>
        <select wire:model.live="sort" class="text-xs border-gray-200 rounded-md">
            <option value="newest">{{ __('app.reviews.sort.newest') }}</option>
            <option value="rating_desc">{{ __('app.reviews.sort.rating_desc') }}</option>
            <option value="rating_asc">{{ __('app.reviews.sort.rating_asc') }}</option>
        </select>
    </div>

    @if ($reviews->count() === 0)
        <p class="text-sm text-gray-500">{{ __('app.reviews.empty') }}</p>
    @else
        <div class="space-y-3">
            @foreach ($reviews as $review)
                <div class="border border-gray-100 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div class="text-sm font-medium text-gray-800">{{ $review->author?->name }}</div>
                        <div class="text-amber-600 text-sm">{{ str_repeat('★', $review->rating_overall) }}<span class="text-gray-300">{{ str_repeat('★', 5 - $review->rating_overall) }}</span></div>
                    </div>
                    <p class="text-sm text-gray-700 mt-2 whitespace-pre-line">{{ $review->comment }}</p>
                    @if ($review->reply_text)
                        <div class="mt-3 pl-3 border-l-2 border-blue-200 text-sm text-gray-600">
                            <div class="text-xs text-gray-400 uppercase tracking-wider mb-1">{{ __('app.reviews.reply') }}</div>
                            {{ $review->reply_text }}
                        </div>
                    @endif
                    <div class="text-xs text-gray-400 mt-2">{{ $review->published_at?->format('d.m.Y') }}</div>
                </div>
            @endforeach
        </div>
        <div class="mt-3">{{ $reviews->links() }}</div>
    @endif
</div>
