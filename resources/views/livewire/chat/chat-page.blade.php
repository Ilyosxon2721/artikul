<div class="max-w-7xl mx-auto px-4 py-6">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 h-[80vh]">
        {{-- Sidebar --}}
        <aside class="bg-white border border-gray-100 rounded-2xl flex flex-col">
            <div class="p-3 border-b border-gray-100">
                <input type="search" wire:model.live.debounce.300ms="search" placeholder="{{ __('app.chat.search_placeholder') }}"
                       class="w-full text-sm border-gray-300 rounded-md focus:border-blue-500 focus:ring-blue-500" />
            </div>
            <div class="overflow-y-auto flex-1">
                @forelse ($conversations as $conv)
                    @php($otherParticipant = $conv->participants->firstWhere('id', '!=', $me->id))
                    <button type="button" wire:click="open({{ $conv->id }})"
                            class="w-full px-4 py-3 text-left flex items-center gap-3 border-b border-gray-50 hover:bg-gray-50 {{ $active?->id === $conv->id ? 'bg-blue-50' : '' }}">
                        @if ($otherParticipant?->avatar_url)
                            <img src="{{ $otherParticipant->avatar_url }}" alt="" class="w-9 h-9 rounded-full object-cover" />
                        @else
                            <div class="w-9 h-9 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-semibold">
                                {{ mb_strtoupper(mb_substr($otherParticipant?->name ?? '?', 0, 1)) }}
                            </div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-medium text-gray-800 truncate">{{ $otherParticipant?->name }}</div>
                            <div class="text-xs text-gray-500 truncate">
                                {{ $conv->lastMessage->first()?->body }}
                            </div>
                        </div>
                        @if ($conv->last_message_at)
                            <span class="text-[10px] text-gray-400">{{ $conv->last_message_at->diffForHumans(short: true) }}</span>
                        @endif
                    </button>
                @empty
                    <div class="p-6 text-center text-sm text-gray-500">{{ __('app.chat.empty_conversations') }}</div>
                @endforelse
            </div>
        </aside>

        {{-- Active conversation --}}
        <main class="md:col-span-2 bg-white border border-gray-100 rounded-2xl flex flex-col overflow-hidden">
            @if ($active)
                <header class="px-5 py-3 border-b border-gray-100 flex items-center gap-3">
                    @if ($other?->avatar_url)
                        <img src="{{ $other->avatar_url }}" alt="" class="w-9 h-9 rounded-full object-cover" />
                    @else
                        <div class="w-9 h-9 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-semibold">
                            {{ mb_strtoupper(mb_substr($other?->name ?? '?', 0, 1)) }}
                        </div>
                    @endif
                    <div class="flex-1">
                        <div class="font-medium text-gray-800">{{ $other?->name }}</div>
                        @if ($active->task_id)
                            <a href="{{ route('tasks.show', $active->task->slug) }}" class="text-xs text-blue-600 hover:underline">{{ $active->task?->title }}</a>
                        @endif
                    </div>
                    @if ($active->contact_filter_enabled)
                        <span class="text-[11px] text-amber-600">⚠ {{ __('app.chat.contact_filter_active') }}</span>
                    @endif
                </header>

                <div class="flex-1 overflow-y-auto px-5 py-4 space-y-2 bg-gray-50/50" id="chat-thread">
                    @foreach ($messages as $message)
                        @php($mine = $message->user_id === $me->id)
                        <div class="flex {{ $mine ? 'justify-end' : 'justify-start' }}">
                            <div class="max-w-[75%] px-3 py-2 rounded-2xl text-sm {{ $mine ? 'bg-blue-600 text-white' : 'bg-white border border-gray-200 text-gray-800' }} {{ $message->is_filtered ? 'opacity-80 italic' : '' }}">
                                <div class="whitespace-pre-line">{{ $message->body }}</div>
                                <div class="text-[10px] mt-1 {{ $mine ? 'text-blue-100' : 'text-gray-400' }}">{{ $message->created_at->format('H:i') }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <form wire:submit="send" class="border-t border-gray-100 p-3 flex gap-2">
                    <input type="text" wire:model="body" placeholder="{{ __('app.chat.placeholder') }}"
                           class="flex-1 border-gray-300 rounded-md text-sm focus:border-blue-500 focus:ring-blue-500" />
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg">{{ __('app.chat.send') }}</button>
                </form>
            @else
                <div class="flex-1 flex items-center justify-center text-gray-500 text-sm">{{ __('app.chat.pick_conversation') }}</div>
            @endif
        </main>
    </div>
</div>
