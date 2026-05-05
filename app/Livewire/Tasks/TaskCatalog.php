<?php

declare(strict_types=1);

namespace App\Livewire\Tasks;

use App\Enums\TaskStatus;
use App\Models\Category;
use App\Models\Marketplace;
use App\Models\Task;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class TaskCatalog extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    /** @var array<int> */
    #[Url(as: 'mp')]
    public array $marketplaceIds = [];

    #[Url(as: 'cat')]
    public ?int $categoryId = null;

    #[Url(as: 'type')]
    public string $taskType = '';

    #[Url(as: 'budget_min')]
    public ?int $budgetMin = null;

    #[Url(as: 'budget_max')]
    public ?int $budgetMax = null;

    #[Url(as: 'verified')]
    public bool $verifiedBuyerOnly = false;

    #[Url(as: 'sort')]
    public string $sort = 'newest';

    public function updating(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'marketplaceIds', 'categoryId', 'taskType', 'budgetMin', 'budgetMax', 'verifiedBuyerOnly']);
    }

    public function render(): View
    {
        return view('livewire.tasks.task-catalog', [
            'tasks' => $this->results(),
            'marketplaces' => Marketplace::query()->where('is_active', true)->orderBy('sort_order')->get(),
            'categories' => Category::query()->whereNull('parent_id')->where('is_active', true)->orderBy('sort_order')->get(),
        ]);
    }

    /**
     * @return LengthAwarePaginator<int, Task>
     */
    private function results(): LengthAwarePaginator
    {
        $query = Task::query()
            ->with(['buyer', 'category'])
            ->where('status', TaskStatus::Published->value);

        if ($this->search !== '') {
            $term = '%'.str_replace(['%', '_'], ['\%', '\_'], $this->search).'%';
            $query->where(function ($q) use ($term): void {
                $q->where('title', 'like', $term)
                    ->orWhere('description', 'like', $term);
            });
        }

        if ($this->categoryId) {
            $query->where(function ($q): void {
                $q->where('category_id', $this->categoryId)
                    ->orWhere('subcategory_id', $this->categoryId);
            });
        }

        if ($this->taskType !== '') {
            $query->where('type', $this->taskType);
        }

        if ($this->budgetMin !== null) {
            $query->where('budget_min', '>=', $this->budgetMin);
        }

        if ($this->budgetMax !== null) {
            $query->where(function ($q): void {
                $q->where('budget_max', '<=', $this->budgetMax)
                    ->orWhere('budget_min', '<=', $this->budgetMax);
            });
        }

        if (! empty($this->marketplaceIds)) {
            $query->where(function ($q): void {
                foreach ($this->marketplaceIds as $id) {
                    $q->orWhereJsonContains('marketplace_ids', (int) $id);
                }
            });
        }

        if ($this->verifiedBuyerOnly) {
            $query->whereHas('buyer.buyerProfile', fn ($q) => $q->where('rating_avg', '>=', 4));
        }

        $query = match ($this->sort) {
            'budget' => $query->orderByDesc('budget_max')->orderByDesc('budget_min'),
            'deadline' => $query->orderBy('deadline_at'),
            default => $query->orderByDesc('published_at'),
        };

        return $query->paginate(20);
    }
}
