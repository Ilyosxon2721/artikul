<?php

declare(strict_types=1);

namespace App\Livewire\Proposals;

use App\Models\Proposal;
use App\Models\Task;
use App\Services\Contracts\ProposalService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use RuntimeException;

#[Layout('layouts.app')]
class TaskProposalsList extends Component
{
    public Task $task;

    public string $sort = 'newest';

    public function mount(string $slug): void
    {
        $task = Task::query()->where('slug', $slug)->firstOrFail();
        abort_unless($task->buyer_id === Auth::id(), 403);
        $this->task = $task;
    }

    public function accept(int $proposalId, ProposalService $service): void
    {
        $proposal = Proposal::query()
            ->where('id', $proposalId)
            ->where('task_id', $this->task->id)
            ->firstOrFail();

        try {
            $contract = $service->accept($proposal);
            $this->redirect(route('contracts.show', $contract), navigate: true);
        } catch (RuntimeException $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function reject(int $proposalId, ProposalService $service): void
    {
        $proposal = Proposal::query()
            ->where('id', $proposalId)
            ->where('task_id', $this->task->id)
            ->firstOrFail();

        $service->reject($proposal);
        session()->flash('status', __('app.proposals.rejected'));
    }

    public function render(): View
    {
        $query = Proposal::query()
            ->with('seller.sellerProfile')
            ->where('task_id', $this->task->id);

        $query = match ($this->sort) {
            'rating' => $query->join('seller_profiles', 'seller_profiles.user_id', '=', 'proposals.seller_id')->orderByDesc('seller_profiles.rating_avg'),
            'price' => $query->orderBy('proposed_amount'),
            default => $query->latest(),
        };

        return view('livewire.proposals.task-proposals-list', [
            'proposals' => $query->paginate(20),
        ]);
    }
}
