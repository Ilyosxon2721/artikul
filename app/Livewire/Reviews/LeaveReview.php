<?php

declare(strict_types=1);

namespace App\Livewire\Reviews;

use App\Enums\ContractStatus;
use App\Models\Contract;
use App\Services\Reviews\ReviewService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use RuntimeException;

#[Layout('layouts.app')]
class LeaveReview extends Component
{
    public Contract $contract;

    public int $ratingOverall = 5;

    public int $ratingQuality = 5;

    public int $ratingCommunication = 5;

    public int $ratingDeadline = 5;

    public int $ratingProfessionalism = 5;

    public int $ratingClarity = 5;

    public int $ratingPaymentTimeliness = 5;

    public string $comment = '';

    public string $wouldWorkAgain = 'yes';

    public function mount(Contract $contract): void
    {
        abort_unless(in_array(Auth::id(), [$contract->buyer_id, $contract->seller_id], true), 403);
        abort_unless($contract->status === ContractStatus::Completed, 422, __('app.reviews.contract_not_completed'));
        $this->contract = $contract;
    }

    public function save(ReviewService $service): void
    {
        $this->validate([
            'ratingOverall' => ['required', 'integer', 'between:1,5'],
            'ratingQuality' => ['required', 'integer', 'between:1,5'],
            'ratingCommunication' => ['required', 'integer', 'between:1,5'],
            'ratingDeadline' => ['required', 'integer', 'between:1,5'],
            'comment' => ['required', 'string', 'min:30', 'max:1500'],
            'wouldWorkAgain' => ['required', 'in:yes,no,maybe'],
        ]);

        try {
            $service->leave($this->contract, Auth::user(), [
                'rating_overall' => $this->ratingOverall,
                'rating_quality' => $this->ratingQuality,
                'rating_communication' => $this->ratingCommunication,
                'rating_deadline' => $this->ratingDeadline,
                'rating_professionalism' => $this->ratingProfessionalism,
                'rating_clarity' => $this->ratingClarity,
                'rating_payment_timeliness' => $this->ratingPaymentTimeliness,
                'comment' => $this->comment,
                'would_work_again' => $this->wouldWorkAgain,
            ]);
            session()->flash('status', __('app.reviews.saved'));
            $this->redirect(route('contracts.show', $this->contract), navigate: true);
        } catch (RuntimeException $e) {
            $this->addError('comment', $e->getMessage());
        }
    }

    public function render(): View
    {
        $isBuyer = Auth::id() === $this->contract->buyer_id;

        return view('livewire.reviews.leave-review', [
            'isBuyer' => $isBuyer,
        ]);
    }
}
