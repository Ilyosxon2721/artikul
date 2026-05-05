<?php

declare(strict_types=1);

namespace App\Livewire\Proposals;

use App\Enums\ProposalStatus;
use App\Models\Proposal;
use App\Services\Contracts\ProposalService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use RuntimeException;

#[Layout('layouts.app')]
class MyProposals extends Component
{
    use WithPagination;

    #[Url(as: 'tab')]
    public string $tab = 'pending';

    public function setTab(string $tab): void
    {
        $this->tab = in_array($tab, ['pending', 'accepted', 'rejected'], true) ? $tab : 'pending';
        $this->resetPage();
    }

    public function withdraw(int $proposalId, ProposalService $service): void
    {
        $proposal = Proposal::query()
            ->where('id', $proposalId)
            ->where('seller_id', Auth::id())
            ->firstOrFail();

        try {
            $service->withdraw($proposal);
            session()->flash('status', __('app.proposals.withdrawn'));
        } catch (RuntimeException $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render(): View
    {
        $statusMap = [
            'pending' => ProposalStatus::Pending,
            'accepted' => ProposalStatus::Accepted,
            'rejected' => ProposalStatus::Rejected,
        ];

        $proposals = Proposal::query()
            ->with('task')
            ->where('seller_id', Auth::id())
            ->where('status', $statusMap[$this->tab] ?? ProposalStatus::Pending)
            ->latest()
            ->paginate(20);

        return view('livewire.proposals.my-proposals', [
            'proposals' => $proposals,
        ]);
    }
}
