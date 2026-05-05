<?php

declare(strict_types=1);

namespace App\Livewire\Contracts;

use App\Enums\Currency;
use App\Enums\MilestoneStatus;
use App\Enums\TaskType;
use App\Models\Contract;
use App\Models\Conversation;
use App\Models\TaskMilestone;
use App\Models\TimeLog;
use App\Notifications\MilestoneSubmittedNotification;
use App\Services\Chat\MessagingService;
use App\Services\Contracts\ContractService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use RuntimeException;

#[Layout('layouts.app')]
class ContractShow extends Component
{
    public Contract $contract;

    public string $tab = 'overview';

    public string $chatBody = '';

    public string $newMilestoneTitle = '';

    public string $newMilestoneDescription = '';

    public ?float $newMilestoneAmount = null;

    public ?string $newMilestoneDeadline = null;

    public ?string $newWorkDate = null;

    public ?int $newMinutes = null;

    public string $newWorkDescription = '';

    public function mount(Contract $contract): void
    {
        abort_unless(in_array(Auth::id(), [$contract->buyer_id, $contract->seller_id], true), 403);
        $this->contract = $contract;
        $this->newWorkDate = now()->format('Y-m-d');
    }

    public function setTab(string $tab): void
    {
        $this->tab = in_array($tab, ['overview', 'milestones', 'hours', 'chat', 'history'], true) ? $tab : 'overview';
    }

    public function sendChatMessage(MessagingService $messaging): void
    {
        $user = Auth::user();
        abort_unless($user !== null, 403);

        $this->validate([
            'chatBody' => ['required', 'string', 'min:1', 'max:4000'],
        ]);

        $conversation = $this->contractConversation();
        if ($conversation === null) {
            return;
        }

        $messaging->send($conversation, $user, $this->chatBody);
        $this->chatBody = '';
    }

    private function contractConversation(): ?Conversation
    {
        $messaging = app(MessagingService::class);

        return $messaging->openDirect(
            $this->contract->buyer,
            $this->contract->seller,
            $this->contract->task,
            $this->contract->id,
        );
    }

    public function submit(ContractService $service): void
    {
        try {
            $service->submit($this->contract, Auth::user());
            session()->flash('status', __('app.contracts.submitted'));
        } catch (RuntimeException $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function approve(ContractService $service): void
    {
        try {
            $service->approve($this->contract, Auth::user());
            session()->flash('status', __('app.contracts.approved'));
        } catch (RuntimeException $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function requestRevision(ContractService $service): void
    {
        try {
            $service->requestRevision($this->contract, Auth::user());
            session()->flash('status', __('app.contracts.revision_requested'));
        } catch (RuntimeException $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function cancel(ContractService $service): void
    {
        try {
            $service->cancel($this->contract, Auth::user());
            session()->flash('status', __('app.contracts.cancelled'));
        } catch (RuntimeException $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function addMilestone(): void
    {
        abort_unless(Auth::id() === $this->contract->buyer_id, 403);

        $this->validate([
            'newMilestoneTitle' => ['required', 'string', 'min:3', 'max:160'],
            'newMilestoneDescription' => ['nullable', 'string', 'max:2000'],
            'newMilestoneAmount' => ['required', 'numeric', 'min:0'],
            'newMilestoneDeadline' => ['nullable', 'date', 'after:today'],
        ]);

        $maxOrder = (int) $this->contract->milestones()->max('order_index');

        TaskMilestone::create([
            'task_id' => $this->contract->task_id,
            'contract_id' => $this->contract->id,
            'order_index' => $maxOrder + 1,
            'title' => $this->newMilestoneTitle,
            'description' => $this->newMilestoneDescription ?: null,
            'amount' => $this->newMilestoneAmount,
            'currency' => $this->contract->currency ?? Currency::UZS,
            'deadline_at' => $this->newMilestoneDeadline ?: null,
            'status' => MilestoneStatus::Pending,
        ]);

        $this->reset(['newMilestoneTitle', 'newMilestoneDescription', 'newMilestoneAmount', 'newMilestoneDeadline']);
        session()->flash('status', __('app.contracts.milestone_added'));
    }

    public function submitMilestone(int $milestoneId): void
    {
        abort_unless(Auth::id() === $this->contract->seller_id, 403);

        $milestone = $this->contract->milestones()->whereKey($milestoneId)->firstOrFail();
        $milestone->forceFill([
            'status' => MilestoneStatus::Submitted,
            'submitted_at' => now(),
        ])->save();

        $this->contract->buyer?->notify(new MilestoneSubmittedNotification($milestone));
        session()->flash('status', __('app.contracts.milestone_submitted'));
    }

    public function approveMilestone(int $milestoneId): void
    {
        abort_unless(Auth::id() === $this->contract->buyer_id, 403);

        $milestone = $this->contract->milestones()->whereKey($milestoneId)->firstOrFail();
        $milestone->forceFill([
            'status' => MilestoneStatus::Approved,
            'approved_at' => now(),
        ])->save();
    }

    public function addTimeLog(): void
    {
        abort_unless(Auth::id() === $this->contract->seller_id, 403);

        $this->validate([
            'newWorkDate' => ['required', 'date'],
            'newMinutes' => ['required', 'integer', 'min:1', 'max:1440'],
            'newWorkDescription' => ['required', 'string', 'min:5', 'max:500'],
        ]);

        TimeLog::create([
            'contract_id' => $this->contract->id,
            'seller_id' => $this->contract->seller_id,
            'work_date' => $this->newWorkDate,
            'minutes' => $this->newMinutes,
            'description' => $this->newWorkDescription,
        ]);

        $this->reset(['newMinutes', 'newWorkDescription']);
        session()->flash('status', __('app.contracts.time_log_added'));
    }

    public function disputeTimeLog(int $timeLogId, string $reason): void
    {
        abort_unless(Auth::id() === $this->contract->buyer_id, 403);

        $log = $this->contract->timeLogs()->whereKey($timeLogId)->firstOrFail();
        $log->forceFill([
            'is_disputed' => true,
            'dispute_reason' => $reason,
            'disputed_at' => now(),
        ])->save();
    }

    public function render(): View
    {
        $conversation = $this->tab === 'chat' ? $this->contractConversation() : null;
        $messages = $conversation !== null
            ? $conversation->messages()->latest()->limit(100)->get()->reverse()->values()
            : collect();

        return view('livewire.contracts.contract-show', [
            'milestones' => $this->contract->milestones()->get(),
            'timeLogs' => $this->contract->timeLogs()->orderByDesc('work_date')->get(),
            'conversation' => $conversation,
            'messages' => $messages,
            'me' => Auth::user(),
            'isBuyer' => Auth::id() === $this->contract->buyer_id,
            'isSeller' => Auth::id() === $this->contract->seller_id,
            'isProject' => $this->contract->contract_type === TaskType::Project,
            'isHourly' => $this->contract->contract_type === TaskType::Hourly,
        ]);
    }
}
