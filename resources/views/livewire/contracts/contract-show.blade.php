<div class="max-w-5xl mx-auto px-4 py-8">
    <div class="bg-white border border-gray-100 rounded-2xl p-6 mb-6">
        <div class="flex items-start justify-between gap-4">
            <div>
                <a href="{{ route('tasks.show', $contract->task->slug) }}" class="text-sm text-blue-600 hover:underline">{{ $contract->task->title }}</a>
                <h1 class="text-2xl font-semibold text-gray-800 mt-1">{{ __('app.contracts.contract_n', ['id' => $contract->id]) }}</h1>
                <div class="text-sm text-gray-500 mt-2">
                    {{ __('app.contracts.parties', ['buyer' => $contract->buyer?->name, 'seller' => $contract->seller?->name]) }}
                </div>
            </div>
            <div class="text-right shrink-0">
                <span class="text-xs px-2 py-0.5 rounded uppercase bg-blue-50 text-blue-700">{{ $contract->status?->label() }}</span>
                @if ($contract->agreed_amount)
                    <div class="text-lg font-semibold text-gray-800 mt-1">{{ number_format((float) $contract->agreed_amount, 0, '.', ' ') }} {{ $contract->currency?->value }}</div>
                @endif
                @if ($contract->agreed_deadline)
                    <div class="text-xs text-gray-500">{{ __('app.tasks.deadline') }}: {{ $contract->agreed_deadline->format('d.m.Y') }}</div>
                @endif
            </div>
        </div>
    </div>

    @if (session('status'))
        <div class="mb-4 px-4 py-2 bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg">{{ session('status') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-4 px-4 py-2 bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg">{{ session('error') }}</div>
    @endif

    {{-- Tabs --}}
    <div class="flex gap-2 mb-6 border-b border-gray-200">
        @foreach (['overview' => __('app.contracts.tabs.overview'), 'milestones' => __('app.contracts.tabs.milestones'), 'hours' => __('app.contracts.tabs.hours'), 'chat' => __('app.contracts.tabs.chat'), 'history' => __('app.contracts.tabs.history')] as $key => $label)
            @if (($key === 'milestones' && ! $isProject) || ($key === 'hours' && ! $isHourly))
                @continue
            @endif
            <button type="button" wire:click="setTab('{{ $key }}')"
                    class="px-4 py-2 text-sm font-medium border-b-2 transition {{ $tab === $key ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                {{ $label }}
            </button>
        @endforeach
    </div>

    {{-- Overview --}}
    @if ($tab === 'overview')
        <div class="bg-white border border-gray-100 rounded-2xl p-6 space-y-4">
            <div>
                <div class="text-xs uppercase tracking-wider text-gray-500">{{ __('app.contracts.fields.type') }}</div>
                <div class="text-sm text-gray-800">{{ $contract->contract_type?->label() }}</div>
            </div>
            <div>
                <div class="text-xs uppercase tracking-wider text-gray-500">{{ __('app.contracts.fields.started_at') }}</div>
                <div class="text-sm text-gray-800">{{ $contract->started_at?->format('d.m.Y H:i') }}</div>
            </div>
            @if ($contract->revisions_used > 0)
                <div>
                    <div class="text-xs uppercase tracking-wider text-gray-500">{{ __('app.contracts.fields.revisions') }}</div>
                    <div class="text-sm text-gray-800">{{ $contract->revisions_used }} / {{ $contract->revisions_limit }}</div>
                </div>
            @endif

            <div class="pt-3 border-t border-gray-100 flex flex-wrap gap-2">
                @if ($isSeller && in_array($contract->status?->value, ['in_progress', 'revision'], true))
                    <button wire:click="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg">{{ __('app.contracts.actions.submit') }}</button>
                @endif
                @if ($isBuyer && in_array($contract->status?->value, ['submitted', 'in_review'], true))
                    <button wire:click="approve" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm rounded-lg">{{ __('app.contracts.actions.approve') }}</button>
                    <button wire:click="requestRevision" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-sm rounded-lg">{{ __('app.contracts.actions.request_revision') }}</button>
                @endif
                @if (! in_array($contract->status?->value, ['completed', 'cancelled'], true))
                    <button wire:click="cancel" wire:confirm="{{ __('app.contracts.confirm_cancel') }}" class="ml-auto px-4 py-2 bg-white border border-red-200 text-red-700 text-sm rounded-lg hover:bg-red-50">{{ __('app.contracts.actions.cancel') }}</button>
                @endif
            </div>
        </div>
    @endif

    {{-- Milestones --}}
    @if ($tab === 'milestones' && $isProject)
        <div class="space-y-3">
            @foreach ($milestones as $m)
                <div class="bg-white border border-gray-100 rounded-2xl p-5">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-gray-400">#{{ $m->order_index }}</span>
                                <span class="font-medium text-gray-800">{{ $m->title }}</span>
                                <span class="text-xs px-2 py-0.5 rounded uppercase bg-gray-100 text-gray-600">{{ $m->status?->value }}</span>
                            </div>
                            @if ($m->description)
                                <p class="text-sm text-gray-600 mt-2">{{ $m->description }}</p>
                            @endif
                            <div class="text-xs text-gray-500 mt-2">
                                {{ number_format((float) $m->amount, 0, '.', ' ') }} {{ $m->currency?->value }}
                                @if ($m->deadline_at) · {{ $m->deadline_at->format('d.m.Y') }} @endif
                            </div>
                        </div>
                        <div class="text-right">
                            @if ($isSeller && $m->status?->value === 'pending')
                                <button wire:click="submitMilestone({{ $m->id }})" class="text-sm text-blue-600 hover:underline">{{ __('app.contracts.actions.submit_milestone') }}</button>
                            @elseif ($isBuyer && $m->status?->value === 'submitted')
                                <button wire:click="approveMilestone({{ $m->id }})" class="text-sm text-green-700 hover:underline">{{ __('app.contracts.actions.approve_milestone') }}</button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach

            @if ($isBuyer)
                <div class="bg-white border border-gray-100 rounded-2xl p-5">
                    <h3 class="font-medium text-gray-800 mb-3">{{ __('app.contracts.add_milestone') }}</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <x-text-input wire:model="newMilestoneTitle" placeholder="{{ __('app.contracts.fields.milestone_title') }}" class="block w-full" />
                        <x-text-input wire:model="newMilestoneAmount" type="number" min="0" step="0.01" placeholder="{{ __('app.contracts.fields.milestone_amount') }}" class="block w-full" />
                        <x-text-input wire:model="newMilestoneDeadline" type="date" class="block w-full" />
                        <textarea wire:model="newMilestoneDescription" rows="1" placeholder="{{ __('app.contracts.fields.milestone_description') }}" class="block w-full border-gray-300 rounded-md text-sm"></textarea>
                    </div>
                    <button wire:click="addMilestone" class="mt-3 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg">{{ __('app.actions.save') }}</button>
                </div>
            @endif
        </div>
    @endif

    {{-- Hours --}}
    @if ($tab === 'hours' && $isHourly)
        <div class="space-y-3">
            @if ($isSeller)
                <div class="bg-white border border-gray-100 rounded-2xl p-5">
                    <h3 class="font-medium text-gray-800 mb-3">{{ __('app.contracts.log_hours') }}</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <x-text-input wire:model="newWorkDate" type="date" class="block w-full" />
                        <x-text-input wire:model="newMinutes" type="number" min="1" max="1440" placeholder="{{ __('app.contracts.fields.minutes') }}" class="block w-full" />
                        <x-text-input wire:model="newWorkDescription" placeholder="{{ __('app.contracts.fields.work_description') }}" class="block w-full" />
                    </div>
                    <button wire:click="addTimeLog" class="mt-3 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg">{{ __('app.actions.save') }}</button>
                </div>
            @endif

            <div class="bg-white border border-gray-100 rounded-2xl p-5">
                <h3 class="font-medium text-gray-800 mb-3">{{ __('app.contracts.time_logs_title') }}</h3>
                @if ($timeLogs->count() === 0)
                    <p class="text-sm text-gray-500">{{ __('app.contracts.no_logs') }}</p>
                @else
                    <ul class="divide-y divide-gray-100 text-sm">
                        @foreach ($timeLogs as $log)
                            <li class="py-2 flex items-center justify-between">
                                <div>
                                    <div class="text-gray-800">{{ $log->work_date->format('d.m.Y') }} — {{ $log->description }}</div>
                                    @if ($log->is_disputed)
                                        <div class="text-xs text-red-600">⚠ {{ $log->dispute_reason }}</div>
                                    @endif
                                </div>
                                <div class="text-gray-600 text-sm">
                                    {{ floor($log->minutes / 60) }}ч {{ $log->minutes % 60 }}м
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    @endif

    {{-- Chat --}}
    @if ($tab === 'chat')
        <div class="bg-white border border-gray-100 rounded-2xl flex flex-col h-[60vh] overflow-hidden">
            <div class="flex-1 overflow-y-auto px-5 py-4 space-y-2 bg-gray-50/50">
                @foreach ($messages as $message)
                    @php($mine = $message->user_id === $me->id)
                    <div class="flex {{ $mine ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-[75%] px-3 py-2 rounded-2xl text-sm {{ $mine ? 'bg-blue-600 text-white' : 'bg-white border border-gray-200 text-gray-800' }} {{ $message->is_filtered ? 'opacity-80 italic' : '' }}">
                            <div class="whitespace-pre-line">{{ $message->body }}</div>
                            <div class="text-[10px] mt-1 {{ $mine ? 'text-blue-100' : 'text-gray-400' }}">{{ $message->created_at->format('H:i d.m') }}</div>
                        </div>
                    </div>
                @endforeach
                @if ($messages->isEmpty())
                    <div class="text-center text-sm text-gray-500 py-8">{{ __('app.chat.empty_thread') }}</div>
                @endif
            </div>

            <form wire:submit="sendChatMessage" class="border-t border-gray-100 p-3 flex gap-2">
                <input type="text" wire:model="chatBody" placeholder="{{ __('app.chat.placeholder') }}"
                       class="flex-1 border-gray-300 rounded-md text-sm focus:border-blue-500 focus:ring-blue-500" />
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg">{{ __('app.chat.send') }}</button>
            </form>
        </div>
    @endif

    {{-- History --}}
    @if ($tab === 'history')
        <div class="bg-white border border-gray-100 rounded-2xl p-5">
            <ul class="text-sm text-gray-700 space-y-1">
                <li>{{ __('app.contracts.history.started') }}: {{ $contract->started_at?->format('d.m.Y H:i') }}</li>
                @if ($contract->submitted_at)<li>{{ __('app.contracts.history.submitted') }}: {{ $contract->submitted_at->format('d.m.Y H:i') }}</li>@endif
                @if ($contract->completed_at)<li>{{ __('app.contracts.history.completed') }}: {{ $contract->completed_at->format('d.m.Y H:i') }}</li>@endif
                @if ($contract->cancelled_at)<li>{{ __('app.contracts.history.cancelled') }}: {{ $contract->cancelled_at->format('d.m.Y H:i') }}</li>@endif
            </ul>
        </div>
    @endif
</div>
