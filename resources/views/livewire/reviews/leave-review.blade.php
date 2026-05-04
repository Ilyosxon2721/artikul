<div class="max-w-2xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-semibold text-gray-800 mb-2">{{ __('app.reviews.leave_title') }}</h1>
    <p class="text-sm text-gray-500 mb-6">{{ $isBuyer ? __('app.reviews.about_seller') : __('app.reviews.about_buyer') }}</p>

    <form wire:submit="save" class="space-y-5 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <div>
            <x-input-label :value="__('app.reviews.fields.rating_overall')" />
            <select wire:model="ratingOverall" class="mt-1 block w-full border-gray-300 rounded-md">
                @for ($i = 5; $i >= 1; $i--)
                    <option value="{{ $i }}">{{ str_repeat('★', $i) }}</option>
                @endfor
            </select>
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <x-input-label :value="__('app.reviews.fields.rating_quality')" />
                <select wire:model="ratingQuality" class="mt-1 block w-full border-gray-300 rounded-md">@for ($i = 5; $i >= 1; $i--)<option value="{{ $i }}">{{ $i }}</option>@endfor</select>
            </div>
            <div>
                <x-input-label :value="__('app.reviews.fields.rating_communication')" />
                <select wire:model="ratingCommunication" class="mt-1 block w-full border-gray-300 rounded-md">@for ($i = 5; $i >= 1; $i--)<option value="{{ $i }}">{{ $i }}</option>@endfor</select>
            </div>
            <div>
                <x-input-label :value="__('app.reviews.fields.rating_deadline')" />
                <select wire:model="ratingDeadline" class="mt-1 block w-full border-gray-300 rounded-md">@for ($i = 5; $i >= 1; $i--)<option value="{{ $i }}">{{ $i }}</option>@endfor</select>
            </div>
            <div>
                <x-input-label :value="$isBuyer ? __('app.reviews.fields.rating_professionalism') : __('app.reviews.fields.rating_clarity')" />
                <select wire:model="{{ $isBuyer ? 'ratingProfessionalism' : 'ratingClarity' }}" class="mt-1 block w-full border-gray-300 rounded-md">@for ($i = 5; $i >= 1; $i--)<option value="{{ $i }}">{{ $i }}</option>@endfor</select>
            </div>
        </div>

        <div>
            <x-input-label :value="__('app.reviews.fields.comment')" />
            <textarea wire:model="comment" rows="6" class="mt-1 block w-full border-gray-300 rounded-md text-sm focus:border-blue-500 focus:ring-blue-500" maxlength="1500"></textarea>
            <p class="text-xs text-gray-500 mt-1">{{ __('app.reviews.comment_hint') }}</p>
            <x-input-error :messages="$errors->get('comment')" class="mt-2" />
        </div>

        <div>
            <x-input-label :value="__('app.reviews.fields.would_work_again')" />
            <div class="grid grid-cols-3 gap-2 mt-1">
                @foreach (['yes' => __('app.reviews.yes'), 'maybe' => __('app.reviews.maybe'), 'no' => __('app.reviews.no')] as $val => $label)
                    <label class="flex items-center justify-center px-3 py-2 border rounded-lg cursor-pointer text-sm {{ $wouldWorkAgain === $val ? 'border-blue-500 bg-blue-50 text-blue-700' : 'border-gray-200 text-gray-700' }}">
                        <input type="radio" wire:model.live="wouldWorkAgain" value="{{ $val }}" class="sr-only" />
                        {{ $label }}
                    </label>
                @endforeach
            </div>
        </div>

        <div class="text-xs text-gray-500 italic">{{ __('app.reviews.blind_method_hint') }}</div>

        <div class="flex justify-end pt-2">
            <x-primary-button class="bg-blue-600 hover:bg-blue-700">{{ __('app.actions.save') }}</x-primary-button>
        </div>
    </form>
</div>
