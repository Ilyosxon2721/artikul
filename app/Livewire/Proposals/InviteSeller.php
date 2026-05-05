<?php

declare(strict_types=1);

namespace App\Livewire\Proposals;

use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;
use App\Notifications\InvitationReceivedNotification;
use App\Services\Contracts\ProposalService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use RuntimeException;

class InviteSeller extends Component
{
    public User $seller;

    public bool $open = false;

    public ?int $taskId = null;

    public string $message = '';

    public function mount(User $seller): void
    {
        $this->seller = $seller;
    }

    public function show(): void
    {
        $this->open = true;
    }

    public function close(): void
    {
        $this->open = false;
    }

    public function send(ProposalService $service): void
    {
        $user = Auth::user();
        abort_unless($user !== null, 403);

        $this->validate([
            'taskId' => ['required', 'integer'],
            'message' => ['nullable', 'string', 'max:2000'],
        ]);

        $task = Task::query()
            ->where('id', $this->taskId)
            ->where('buyer_id', $user->id)
            ->whereIn('status', [TaskStatus::Draft->value, TaskStatus::Published->value])
            ->firstOrFail();

        try {
            $proposal = $service->create($task, $this->seller, [
                'cover_letter' => $this->message ?: __('app.proposals.invitation_default_message'),
                'via_invitation' => true,
            ]);
            $this->seller->notify(new InvitationReceivedNotification($proposal));
            $this->open = false;
            session()->flash('status', __('app.proposals.invitation_sent'));
        } catch (RuntimeException $e) {
            $this->addError('taskId', $e->getMessage());
        }
    }

    public function render(): View
    {
        $tasks = Auth::check()
            ? Task::query()
                ->where('buyer_id', Auth::id())
                ->whereIn('status', [TaskStatus::Draft->value, TaskStatus::Published->value])
                ->orderByDesc('created_at')
                ->get()
            : collect();

        return view('livewire.proposals.invite-seller', compact('tasks'));
    }
}
