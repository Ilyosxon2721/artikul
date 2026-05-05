<?php

declare(strict_types=1);

namespace App\Livewire\Tasks;

use App\Enums\UserMode;
use App\Models\Marketplace;
use App\Models\Task;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class TaskShow extends Component
{
    public Task $task;

    public function mount(string $slug): void
    {
        $this->task = Task::query()
            ->with(['buyer.buyerProfile', 'category', 'subcategory', 'specialization', 'attachments'])
            ->where('slug', $slug)
            ->firstOrFail();

        if ($this->task->buyer_id !== Auth::id()) {
            $this->task->increment('views_count');
        }
    }

    public function render(): View
    {
        $marketplaces = Marketplace::query()
            ->whereIn('id', (array) ($this->task->marketplace_ids ?? []))
            ->get();

        return view('livewire.tasks.task-show', [
            'marketplaces' => $marketplaces,
            'canEdit' => Auth::id() === $this->task->buyer_id,
            'canRespond' => Auth::user()?->current_mode === UserMode::Seller && Auth::id() !== $this->task->buyer_id,
        ]);
    }
}
