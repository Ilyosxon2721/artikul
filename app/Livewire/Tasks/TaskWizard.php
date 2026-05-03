<?php

declare(strict_types=1);

namespace App\Livewire\Tasks;

use App\Enums\BudgetType;
use App\Enums\Currency;
use App\Enums\SellerLevel;
use App\Enums\TaskStatus;
use App\Enums\TaskType;
use App\Models\Category;
use App\Models\Marketplace;
use App\Models\Specialization;
use App\Models\Task;
use App\Models\TaskAttachment;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
class TaskWizard extends Component
{
    use WithFileUploads;

    public ?int $taskId = null;

    public int $step = 1;

    /** Step 1: type + marketplaces */
    public string $type = 'gig';

    /** @var array<int> */
    public array $marketplaceIds = [];

    /** Step 2: category + subcategory + specialization */
    public ?int $categoryId = null;

    public ?int $subcategoryId = null;

    public ?int $specializationId = null;

    public string $title = '';

    public ?int $volumeAmount = null;

    public string $volumeUnit = '';

    /** Step 3: description + attachments */
    public string $description = '';

    /** @var array<int, mixed> */
    public array $newAttachments = [];

    /** Step 4: budget + deadline + requirements */
    public string $budgetType = 'fixed';

    public ?float $budgetMin = null;

    public ?float $budgetMax = null;

    public string $currency = 'UZS';

    public ?string $deadlineAt = null;

    public ?int $estimatedDurationDays = null;

    public ?string $requiredSellerLevel = null;

    public bool $requireVerifiedSeller = false;

    /** @var array<string> */
    public array $requiredLanguages = [];

    public string $requiredCountry = '';

    /** Step 5: publication */
    public bool $invitationOnly = false;

    /** @var array<int> */
    public array $invitedSellerIds = [];

    public function mount(?string $slug = null): void
    {
        $user = Auth::user();
        abort_unless($user !== null, 403);

        if ($slug) {
            $task = Task::query()
                ->where('slug', $slug)
                ->where('buyer_id', $user->id)
                ->firstOrFail();

            $this->loadFromTask($task);
        }
    }

    public function loadFromTask(Task $task): void
    {
        $this->taskId = $task->id;
        $this->type = $task->type->value ?? 'gig';
        $this->marketplaceIds = (array) ($task->marketplace_ids ?? []);
        $this->categoryId = $task->category_id;
        $this->subcategoryId = $task->subcategory_id;
        $this->specializationId = $task->specialization_id;
        $this->title = $task->title ?? '';
        $this->volumeAmount = $task->volume_amount;
        $this->volumeUnit = $task->volume_unit ?? '';
        $this->description = $task->description ?? '';
        $this->budgetType = $task->budget_type->value ?? 'fixed';
        $this->budgetMin = $task->budget_min ? (float) $task->budget_min : null;
        $this->budgetMax = $task->budget_max ? (float) $task->budget_max : null;
        $this->currency = $task->currency->value ?? 'UZS';
        $this->deadlineAt = $task->deadline_at?->format('Y-m-d');
        $this->estimatedDurationDays = $task->estimated_duration_days;
        $this->requiredSellerLevel = $task->required_seller_level?->value;
        $this->requireVerifiedSeller = (bool) $task->require_verified_seller;
        $this->requiredLanguages = (array) ($task->required_languages ?? []);
        $this->requiredCountry = $task->required_country ?? '';
        $this->invitationOnly = (bool) $task->invitation_only;
        $this->invitedSellerIds = (array) ($task->invited_seller_ids ?? []);
    }

    public function next(): void
    {
        $this->validateStep($this->step);
        $this->saveDraft();
        $this->step = min(5, $this->step + 1);
    }

    public function back(): void
    {
        $this->step = max(1, $this->step - 1);
    }

    public function go(int $step): void
    {
        if ($step < $this->step) {
            $this->step = max(1, $step);
        }
    }

    public function saveDraft(): void
    {
        $task = $this->persist(TaskStatus::Draft);

        $this->processNewAttachments($task);

        session()->flash('status', __('app.tasks.draft_saved'));
    }

    public function publish(): void
    {
        foreach ([1, 2, 3, 4, 5] as $s) {
            $this->validateStep($s);
        }

        $task = $this->persist(TaskStatus::Published);
        $task->forceFill(['published_at' => $task->published_at ?? now()])->save();

        $this->processNewAttachments($task);

        session()->flash('status', __('app.tasks.published'));
        $this->redirect(route('tasks.show', $task->slug), navigate: true);
    }

    public function deleteAttachment(int $attachmentId): void
    {
        $task = $this->task();
        if ($task === null) {
            return;
        }
        $task->attachments()->whereKey($attachmentId)->delete();
    }

    public function render(): View
    {
        $rootCategories = Category::query()
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $subcategories = $this->categoryId
            ? Category::query()->where('parent_id', $this->categoryId)->orderBy('sort_order')->get()
            : new Collection;

        $specializations = $this->subcategoryId
            ? Specialization::query()->where('category_id', $this->subcategoryId)->where('is_active', true)->orderBy('sort_order')->get()
            : ($this->categoryId
                ? Specialization::query()->where('category_id', $this->categoryId)->where('is_active', true)->orderBy('sort_order')->get()
                : new Collection);

        return view('livewire.tasks.task-wizard', [
            'marketplaces' => Marketplace::query()->where('is_active', true)->orderBy('sort_order')->get(),
            'rootCategories' => $rootCategories,
            'subcategories' => $subcategories,
            'specializations' => $specializations,
            'sellerLevels' => SellerLevel::cases(),
            'taskTypes' => TaskType::cases(),
            'budgetTypes' => BudgetType::cases(),
            'task' => $this->task(),
        ]);
    }

    private function task(): ?Task
    {
        return $this->taskId ? Task::query()->find($this->taskId) : null;
    }

    private function validateStep(int $step): void
    {
        match ($step) {
            1 => $this->validate([
                'type' => ['required', 'in:gig,project,hourly'],
                'marketplaceIds' => ['required', 'array', 'min:1'],
                'marketplaceIds.*' => ['integer', 'exists:marketplaces,id'],
            ]),
            2 => $this->validate([
                'categoryId' => ['required', 'integer', 'exists:categories,id'],
                'subcategoryId' => ['nullable', 'integer', 'exists:categories,id'],
                'specializationId' => ['nullable', 'integer', 'exists:specializations,id'],
                'title' => ['required', 'string', 'min:8', 'max:160'],
                'volumeUnit' => ['nullable', 'string', 'max:32'],
                'volumeAmount' => ['nullable', 'integer', 'min:1'],
            ]),
            3 => $this->validate([
                'description' => ['required', 'string', 'min:30', 'max:10000'],
                'newAttachments' => ['nullable', 'array', 'max:10'],
                'newAttachments.*' => ['file', 'max:5120'],
            ]),
            4 => $this->validate([
                'budgetType' => ['required', 'in:fixed,range,negotiable'],
                'budgetMin' => ['nullable', 'numeric', 'min:0', 'required_unless:budgetType,negotiable'],
                'budgetMax' => ['nullable', 'numeric', 'gte:budgetMin', 'required_if:budgetType,range'],
                'currency' => ['required', 'in:UZS,RUB,KZT,USD'],
                'deadlineAt' => ['nullable', 'date', 'after:today'],
                'estimatedDurationDays' => ['nullable', 'integer', 'min:1', 'max:365'],
                'requiredSellerLevel' => ['nullable', 'in:junior,middle,senior'],
                'requireVerifiedSeller' => ['boolean'],
                'requiredLanguages' => ['array'],
                'requiredLanguages.*' => ['string', 'in:ru,uz,en,kk'],
                'requiredCountry' => ['nullable', 'string', 'size:2'],
            ]),
            5 => $this->validate([
                'invitationOnly' => ['boolean'],
                'invitedSellerIds' => ['array'],
                'invitedSellerIds.*' => ['integer', 'exists:users,id'],
            ]),
            default => null,
        };
    }

    private function persist(TaskStatus $status): Task
    {
        $user = Auth::user();
        abort_unless($user !== null, 403);

        return DB::transaction(function () use ($status, $user): Task {
            $payload = [
                'buyer_id' => $user->id,
                'type' => TaskType::from($this->type),
                'status' => $status,
                'title' => $this->title ?: __('app.tasks.untitled'),
                'description' => $this->description ?: null,
                'marketplace_ids' => $this->marketplaceIds,
                'category_id' => $this->categoryId,
                'subcategory_id' => $this->subcategoryId,
                'specialization_id' => $this->specializationId,
                'volume_unit' => $this->volumeUnit ?: null,
                'volume_amount' => $this->volumeAmount,
                'budget_type' => BudgetType::from($this->budgetType),
                'budget_min' => $this->budgetMin,
                'budget_max' => $this->budgetMax,
                'currency' => Currency::from($this->currency),
                'deadline_at' => $this->deadlineAt ?: null,
                'estimated_duration_days' => $this->estimatedDurationDays,
                'required_seller_level' => $this->requiredSellerLevel
                    ? SellerLevel::from($this->requiredSellerLevel)
                    : null,
                'require_verified_seller' => $this->requireVerifiedSeller,
                'required_languages' => $this->requiredLanguages,
                'required_country' => $this->requiredCountry !== '' ? mb_strtoupper($this->requiredCountry) : null,
                'invitation_only' => $this->invitationOnly,
                'invited_seller_ids' => $this->invitedSellerIds,
            ];

            if ($this->taskId) {
                $task = Task::query()->where('id', $this->taskId)->where('buyer_id', $user->id)->firstOrFail();
                $task->fill($payload)->save();
            } else {
                $task = Task::create($payload);
                $this->taskId = $task->id;
            }

            return $task;
        });
    }

    private function processNewAttachments(Task $task): void
    {
        if (empty($this->newAttachments)) {
            return;
        }

        foreach ($this->newAttachments as $upload) {
            $path = $upload->store("tasks/{$task->id}", 'public');
            TaskAttachment::create([
                'task_id' => $task->id,
                'filename' => $upload->getClientOriginalName(),
                'mime_type' => $upload->getMimeType(),
                'size_bytes' => $upload->getSize(),
                'disk' => 'public',
                'path' => $path,
            ]);
        }
        $this->newAttachments = [];
    }
}
