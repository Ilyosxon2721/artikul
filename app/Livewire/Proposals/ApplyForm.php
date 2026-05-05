<?php

declare(strict_types=1);

namespace App\Livewire\Proposals;

use App\Models\Task;
use App\Notifications\NewProposalNotification;
use App\Services\Contracts\ProposalService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use RuntimeException;

class ApplyForm extends Component
{
    public Task $task;

    public bool $open = false;

    public string $coverLetter = '';

    public ?float $proposedAmount = null;

    public ?int $proposedDeadlineDays = null;

    public string $rateType = 'fixed';

    public string $currency = 'UZS';

    public function mount(Task $task): void
    {
        $this->task = $task;
        $this->currency = $task->currency?->value ?? 'UZS';
    }

    public function show(): void
    {
        $this->open = true;
    }

    public function close(): void
    {
        $this->open = false;
    }

    public function submit(ProposalService $service): void
    {
        $user = Auth::user();
        abort_unless($user !== null, 403);

        $this->validate([
            'coverLetter' => ['required', 'string', 'min:30', 'max:2000'],
            'proposedAmount' => ['nullable', 'numeric', 'min:0'],
            'proposedDeadlineDays' => ['nullable', 'integer', 'min:1', 'max:365'],
            'rateType' => ['required', 'in:fixed,hourly'],
            'currency' => ['required', 'in:UZS,RUB,KZT,USD'],
        ]);

        try {
            $proposal = $service->create($this->task, $user, [
                'cover_letter' => $this->coverLetter,
                'proposed_amount' => $this->proposedAmount,
                'currency' => $this->currency,
                'rate_type' => $this->rateType,
                'proposed_deadline_days' => $this->proposedDeadlineDays,
            ]);
        } catch (RuntimeException $e) {
            $this->addError('coverLetter', $e->getMessage());

            return;
        }

        $this->task->buyer?->notify(new NewProposalNotification($proposal));

        $this->reset(['coverLetter', 'proposedAmount', 'proposedDeadlineDays']);
        $this->open = false;
        session()->flash('status', __('app.proposals.submitted'));
        $this->redirect(route('tasks.show', $this->task->slug), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.proposals.apply-form');
    }
}
