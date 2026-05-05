<div class="max-w-3xl mx-auto px-4 py-8">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-semibold text-gray-800">{{ __('app.tasks.wizard_title') }}</h1>
            <span class="text-sm text-gray-500">{{ __('app.tasks.step_n', ['current' => $step, 'total' => 5]) }}</span>
        </div>

        <div class="flex gap-1 mb-8">
            @for ($i = 1; $i <= 5; $i++)
                <button type="button" wire:click="go({{ $i }})"
                        class="flex-1 h-1.5 rounded-full transition {{ $i <= $step ? 'bg-blue-600' : 'bg-gray-200' }}"></button>
            @endfor
        </div>

        @if (session('status'))
            <div class="mb-4 px-4 py-2 bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg">{{ session('status') }}</div>
        @endif

        @if ($step === 1)
            <h2 class="text-lg font-medium text-gray-800 mb-2">{{ __('app.tasks.step1_title') }}</h2>
            <p class="text-sm text-gray-500 mb-4">{{ __('app.tasks.step1_subtitle') }}</p>

            <x-input-label :value="__('app.tasks.fields.type')" />
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 mt-1">
                @foreach ($taskTypes as $taskType)
                    <label class="flex flex-col items-start gap-1 px-3 py-3 border rounded-lg cursor-pointer text-sm {{ $type === $taskType->value ? 'border-blue-500 bg-blue-50 text-blue-700' : 'border-gray-200 text-gray-700' }}">
                        <input type="radio" wire:model.live="type" value="{{ $taskType->value }}" class="sr-only" />
                        <span class="font-medium">{{ $taskType->label() }}</span>
                        <span class="text-xs text-gray-500">{{ __('app.tasks.type_hint.'.$taskType->value) }}</span>
                    </label>
                @endforeach
            </div>
            <x-input-error :messages="$errors->get('type')" class="mt-2" />

            <div class="mt-6">
                <x-input-label :value="__('app.tasks.fields.marketplaces')" />
                <div class="mt-1 grid grid-cols-2 sm:grid-cols-3 gap-2">
                    @foreach ($marketplaces as $marketplace)
                        <label class="flex items-center gap-2 px-3 py-2 border rounded-lg cursor-pointer text-sm {{ in_array($marketplace->id, $marketplaceIds) ? 'border-blue-500 bg-blue-50 text-blue-700' : 'border-gray-200 text-gray-700' }}">
                            <input type="checkbox" wire:model.live="marketplaceIds" value="{{ $marketplace->id }}" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
                            {{ $marketplace->name() }}
                        </label>
                    @endforeach
                </div>
                <x-input-error :messages="$errors->get('marketplaceIds')" class="mt-2" />
            </div>
        @endif

        @if ($step === 2)
            <h2 class="text-lg font-medium text-gray-800 mb-2">{{ __('app.tasks.step2_title') }}</h2>
            <p class="text-sm text-gray-500 mb-4">{{ __('app.tasks.step2_subtitle') }}</p>

            <div>
                <x-input-label :value="__('app.tasks.fields.title')" />
                <x-text-input wire:model="title" type="text" class="mt-1 block w-full" maxlength="160" />
                <x-input-error :messages="$errors->get('title')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label :value="__('app.tasks.fields.category')" />
                <select wire:model.live="categoryId" class="mt-1 block w-full border-gray-300 rounded-md">
                    <option value="">—</option>
                    @foreach ($rootCategories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name() }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('categoryId')" class="mt-2" />
            </div>

            @if ($subcategories->count() > 0)
                <div class="mt-4">
                    <x-input-label :value="__('app.tasks.fields.subcategory')" />
                    <select wire:model.live="subcategoryId" class="mt-1 block w-full border-gray-300 rounded-md">
                        <option value="">—</option>
                        @foreach ($subcategories as $sub)
                            <option value="{{ $sub->id }}">{{ $sub->name() }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            @if ($specializations->count() > 0)
                <div class="mt-4">
                    <x-input-label :value="__('app.tasks.fields.specialization')" />
                    <select wire:model="specializationId" class="mt-1 block w-full border-gray-300 rounded-md">
                        <option value="">—</option>
                        @foreach ($specializations as $spec)
                            <option value="{{ $spec->id }}">{{ $spec->name() }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <x-input-label :value="__('app.tasks.fields.volume_amount')" />
                    <x-text-input wire:model="volumeAmount" type="number" min="1" class="mt-1 block w-full" />
                </div>
                <div>
                    <x-input-label :value="__('app.tasks.fields.volume_unit')" />
                    <x-text-input wire:model="volumeUnit" type="text" class="mt-1 block w-full" placeholder="карточек / часов / кампаний" />
                </div>
            </div>
        @endif

        @if ($step === 3)
            <h2 class="text-lg font-medium text-gray-800 mb-2">{{ __('app.tasks.step3_title') }}</h2>
            <p class="text-sm text-gray-500 mb-4">{{ __('app.tasks.step3_subtitle') }}</p>

            <div>
                <x-input-label :value="__('app.tasks.fields.description')" />
                <textarea wire:model="description" rows="10" class="mt-1 block w-full border-gray-300 rounded-md focus:border-blue-500 focus:ring-blue-500 font-mono text-sm" placeholder="**markdown**"></textarea>
                <p class="text-xs text-gray-500 mt-1">{{ __('app.tasks.markdown_hint') }}</p>
                <x-input-error :messages="$errors->get('description')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label :value="__('app.tasks.fields.attachments')" />
                <input type="file" wire:model="newAttachments" multiple class="mt-1 block w-full text-sm text-gray-700" />
                <p class="text-xs text-gray-500 mt-1">{{ __('app.tasks.attachments_hint') }}</p>
                <x-input-error :messages="$errors->get('newAttachments')" class="mt-2" />
                <x-input-error :messages="$errors->get('newAttachments.*')" class="mt-2" />

                @if ($task && $task->attachments->count() > 0)
                    <ul class="mt-3 space-y-1">
                        @foreach ($task->attachments as $att)
                            <li class="flex items-center justify-between text-sm bg-gray-50 px-3 py-2 rounded">
                                <span>{{ $att->filename }} <span class="text-gray-400">({{ number_format($att->size_bytes / 1024, 0) }} KB)</span></span>
                                <button type="button" wire:click="deleteAttachment({{ $att->id }})" class="text-red-500 hover:underline">{{ __('app.actions.delete') }}</button>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        @endif

        @if ($step === 4)
            <h2 class="text-lg font-medium text-gray-800 mb-2">{{ __('app.tasks.step4_title') }}</h2>
            <p class="text-sm text-gray-500 mb-4">{{ __('app.tasks.step4_subtitle') }}</p>

            <div>
                <x-input-label :value="__('app.tasks.fields.budget_type')" />
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 mt-1">
                    @foreach ($budgetTypes as $bt)
                        <label class="flex items-center gap-2 px-3 py-2 border rounded-lg cursor-pointer text-sm {{ $budgetType === $bt->value ? 'border-blue-500 bg-blue-50 text-blue-700' : 'border-gray-200 text-gray-700' }}">
                            <input type="radio" wire:model.live="budgetType" value="{{ $bt->value }}" class="sr-only" />
                            {{ $bt->label() }}
                        </label>
                    @endforeach
                </div>
            </div>

            @if ($budgetType !== 'negotiable')
                <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <x-input-label :value="__('app.tasks.fields.budget_min')" />
                        <x-text-input wire:model="budgetMin" type="number" min="0" step="0.01" class="mt-1 block w-full" />
                        <x-input-error :messages="$errors->get('budgetMin')" class="mt-2" />
                    </div>
                    @if ($budgetType === 'range')
                        <div>
                            <x-input-label :value="__('app.tasks.fields.budget_max')" />
                            <x-text-input wire:model="budgetMax" type="number" min="0" step="0.01" class="mt-1 block w-full" />
                            <x-input-error :messages="$errors->get('budgetMax')" class="mt-2" />
                        </div>
                    @endif
                    <div>
                        <x-input-label :value="__('app.tasks.fields.currency')" />
                        <select wire:model="currency" class="mt-1 block w-full border-gray-300 rounded-md">
                            <option value="UZS">UZS</option>
                            <option value="RUB">RUB</option>
                            <option value="KZT">KZT</option>
                            <option value="USD">USD</option>
                        </select>
                    </div>
                </div>
            @endif

            <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <x-input-label :value="__('app.tasks.fields.deadline_at')" />
                    <x-text-input wire:model="deadlineAt" type="date" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('deadlineAt')" class="mt-2" />
                </div>
                <div>
                    <x-input-label :value="__('app.tasks.fields.estimated_duration_days')" />
                    <x-text-input wire:model="estimatedDurationDays" type="number" min="1" max="365" class="mt-1 block w-full" />
                </div>
            </div>

            <div class="mt-4">
                <x-input-label :value="__('app.tasks.fields.required_seller_level')" />
                <div class="mt-1 grid grid-cols-1 sm:grid-cols-4 gap-2">
                    <label class="flex items-center justify-center px-3 py-2 border rounded-lg cursor-pointer text-sm {{ $requiredSellerLevel === null || $requiredSellerLevel === '' ? 'border-blue-500 bg-blue-50 text-blue-700' : 'border-gray-200 text-gray-700' }}">
                        <input type="radio" wire:model.live="requiredSellerLevel" value="" class="sr-only" />
                        {{ __('app.tasks.level_any') }}
                    </label>
                    @foreach ($sellerLevels as $level)
                        <label class="flex items-center justify-center px-3 py-2 border rounded-lg cursor-pointer text-sm {{ $requiredSellerLevel === $level->value ? 'border-blue-500 bg-blue-50 text-blue-700' : 'border-gray-200 text-gray-700' }}">
                            <input type="radio" wire:model.live="requiredSellerLevel" value="{{ $level->value }}" class="sr-only" />
                            {{ $level->label() }}+
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="mt-4 flex items-center gap-2">
                <input type="checkbox" wire:model="requireVerifiedSeller" id="rv" class="rounded border-gray-300 text-blue-600" />
                <label for="rv" class="text-sm text-gray-700">{{ __('app.tasks.fields.require_verified_seller') }}</label>
            </div>
        @endif

        @if ($step === 5)
            <h2 class="text-lg font-medium text-gray-800 mb-2">{{ __('app.tasks.step5_title') }}</h2>
            <p class="text-sm text-gray-500 mb-4">{{ __('app.tasks.step5_subtitle') }}</p>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                <label class="flex items-start gap-2 px-3 py-3 border rounded-lg cursor-pointer text-sm {{ ! $invitationOnly ? 'border-blue-500 bg-blue-50 text-blue-700' : 'border-gray-200 text-gray-700' }}">
                    <input type="radio" wire:model.live="invitationOnly" value="0" class="mt-0.5" />
                    <div>
                        <div class="font-medium">{{ __('app.tasks.publication_open') }}</div>
                        <div class="text-xs text-gray-500">{{ __('app.tasks.publication_open_hint') }}</div>
                    </div>
                </label>
                <label class="flex items-start gap-2 px-3 py-3 border rounded-lg cursor-pointer text-sm {{ $invitationOnly ? 'border-blue-500 bg-blue-50 text-blue-700' : 'border-gray-200 text-gray-700' }}">
                    <input type="radio" wire:model.live="invitationOnly" value="1" class="mt-0.5" />
                    <div>
                        <div class="font-medium">{{ __('app.tasks.publication_invite') }}</div>
                        <div class="text-xs text-gray-500">{{ __('app.tasks.publication_invite_hint') }}</div>
                    </div>
                </label>
            </div>

            <div class="mt-6 p-4 border border-gray-100 rounded-lg bg-gray-50">
                <h3 class="font-medium text-gray-800 mb-2">{{ __('app.tasks.preview') }}</h3>
                <div class="text-sm text-gray-700 space-y-1">
                    <div><strong>{{ __('app.tasks.fields.title') }}:</strong> {{ $title }}</div>
                    <div><strong>{{ __('app.tasks.fields.type') }}:</strong> {{ \App\Enums\TaskType::from($type)->label() }}</div>
                    <div><strong>{{ __('app.tasks.fields.marketplaces') }}:</strong> {{ count($marketplaceIds) }}</div>
                    <div><strong>{{ __('app.tasks.fields.budget_type') }}:</strong> {{ \App\Enums\BudgetType::from($budgetType)->label() }} {{ $budgetMin ? '(от '.number_format((float) $budgetMin, 0, '.', ' ').' '.$currency.')' : '' }}</div>
                    @if ($deadlineAt)<div><strong>{{ __('app.tasks.fields.deadline_at') }}:</strong> {{ $deadlineAt }}</div>@endif
                </div>
            </div>
        @endif

        <div class="flex items-center justify-between mt-8 pt-6 border-t border-gray-100">
            <button type="button" wire:click="back" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900 {{ $step === 1 ? 'invisible' : '' }}">
                {{ __('app.actions.back') }}
            </button>

            <div class="flex items-center gap-2">
                <button type="button" wire:click="saveDraft" class="px-4 py-2 text-sm text-blue-600 hover:underline">
                    {{ __('app.tasks.save_as_draft') }}
                </button>

                @if ($step < 5)
                    <button type="button" wire:click="next" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg">
                        {{ __('app.actions.next') }}
                    </button>
                @else
                    <button type="button" wire:click="publish" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg">
                        {{ __('app.tasks.actions.publish') }}
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>
