<div class="max-w-5xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-semibold text-gray-800 mb-6">{{ __('app.proposals.my_title') }}</h1>

    @if (session('status'))
        <div class="mb-4 px-4 py-2 bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg">{{ session('status') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-4 px-4 py-2 bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg">{{ session('error') }}</div>
    @endif

    <div class="flex gap-2 mb-6 border-b border-gray-200">
        @foreach (['pending' => __('app.proposals.tabs.pending'), 'accepted' => __('app.proposals.tabs.accepted'), 'rejected' => __('app.proposals.tabs.rejected')] as $key => $label)
            <button type="button" wire:click="setTab('{{ $key }}')"
                    class="px-4 py-2 text-sm font-medium border-b-2 transition {{ $tab === $key ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                {{ $label }}
            </button>
        @endforeach
    </div>

    @if ($proposals->count() === 0)
        <div class="bg-white border border-gray-100 rounded-2xl p-12 text-center text-gray-500">{{ __('app.proposals.empty') }}</div>
    @else
        <div class="space-y-3">
            @foreach ($proposals as $proposal)
                <div class="bg-white border border-gray-100 rounded-2xl p-5">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <a href="{{ route('tasks.show', $proposal->task->slug) }}" class="font-medium text-gray-800 hover:underline truncate">{{ $proposal->task->title }}</a>
                            <p class="text-sm text-gray-500 mt-1 line-clamp-2">{{ \Illuminate\Support\Str::limit($proposal->cover_letter, 200) }}</p>
                            <div class="flex items-center gap-3 text-xs text-gray-500 mt-2">
                                @if ($proposal->proposed_amount)
                                    <span>{{ number_format((float) $proposal->proposed_amount, 0, '.', ' ') }} {{ $proposal->currency?->value }}</span>
                                @endif
                                @if ($proposal->proposed_deadline_days)
                                    <span>· {{ trans_choice('app.proposals.deadline_days', $proposal->proposed_deadline_days, ['count' => $proposal->proposed_deadline_days]) }}</span>
                                @endif
                                <span>· {{ $proposal->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                        <div class="text-right shrink-0">
                            @if ($proposal->status->value === 'pending')
                                <button type="button" wire:click="withdraw({{ $proposal->id }})" class="text-sm text-red-600 hover:underline">{{ __('app.proposals.actions.withdraw') }}</button>
                            @elseif ($proposal->status->value === 'accepted' && $proposal->contract)
                                <a href="{{ route('contracts.show', $proposal->contract) }}" class="text-sm text-blue-600 hover:underline">{{ __('app.proposals.actions.open_contract') }}</a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-6">{{ $proposals->links() }}</div>
    @endif
</div>
