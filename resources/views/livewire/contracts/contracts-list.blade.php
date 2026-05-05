<div class="max-w-5xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-semibold text-gray-800 mb-6">{{ __('app.contracts.my_title') }}</h1>

    @if ($contracts->count() === 0)
        <div class="bg-white border border-gray-100 rounded-2xl p-12 text-center text-gray-500">{{ __('app.contracts.empty') }}</div>
    @else
        <div class="space-y-3">
            @foreach ($contracts as $contract)
                <a href="{{ route('contracts.show', $contract) }}" class="block bg-white border border-gray-100 hover:border-blue-200 rounded-2xl p-5 transition">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <div class="font-medium text-gray-800 truncate">{{ $contract->task?->title }}</div>
                            <div class="text-xs text-gray-500 mt-1">
                                {{ $contract->buyer_id === auth()->id() ? __('app.contracts.with_seller', ['name' => $contract->seller?->name]) : __('app.contracts.with_buyer', ['name' => $contract->buyer?->name]) }}
                                · {{ $contract->started_at?->diffForHumans() }}
                            </div>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="text-xs px-2 py-0.5 rounded uppercase {{ $contract->status?->value === 'completed' ? 'bg-green-50 text-green-700' : ($contract->status?->value === 'cancelled' ? 'bg-gray-100 text-gray-600' : 'bg-blue-50 text-blue-700') }}">{{ $contract->status?->label() }}</span>
                            @if ($contract->agreed_amount)
                                <div class="text-sm font-medium text-gray-800 mt-1">{{ number_format((float) $contract->agreed_amount, 0, '.', ' ') }} {{ $contract->currency?->value }}</div>
                            @endif
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
        <div class="mt-6">{{ $contracts->links() }}</div>
    @endif
</div>
