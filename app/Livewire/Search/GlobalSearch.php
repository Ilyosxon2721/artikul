<?php

declare(strict_types=1);

namespace App\Livewire\Search;

use App\Enums\TaskStatus;
use App\Models\SellerProfile;
use App\Models\Task;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.app')]
class GlobalSearch extends Component
{
    #[Url(as: 'q')]
    public string $query = '';

    #[Url(as: 'tab')]
    public string $tab = 'tasks';

    public function setTab(string $tab): void
    {
        $this->tab = in_array($tab, ['tasks', 'sellers'], true) ? $tab : 'tasks';
    }

    public function render(): View
    {
        $tasks = collect();
        $sellers = collect();

        if ($this->query !== '' && $this->tab === 'tasks') {
            $tasks = $this->safeSearch(Task::class, $this->query, 30, ['buyer', 'category'])
                ->filter(fn (Task $t) => $t->status === TaskStatus::Published);
        }

        if ($this->query !== '' && $this->tab === 'sellers') {
            $sellers = $this->safeSearch(SellerProfile::class, $this->query, 30, ['user', 'specializations']);
        }

        return view('livewire.search.global-search', [
            'tasks' => $tasks,
            'sellers' => $sellers,
        ]);
    }

    /**
     * Search via Scout when configured, fall back to LIKE if the driver is null
     * (helps in local/testing without Meilisearch).
     */
    private function safeSearch(string $model, string $term, int $limit, array $with = []): Collection
    {
        try {
            return $model::search($term)
                ->take($limit)
                ->get()
                ->load($with);
        } catch (\Throwable) {
            $columns = $model === Task::class ? ['title', 'description'] : ['headline', 'bio'];
            $like = '%'.str_replace(['%', '_'], ['\%', '\_'], $term).'%';

            return $model::query()
                ->with($with)
                ->where(function ($q) use ($columns, $like): void {
                    foreach ($columns as $col) {
                        $q->orWhere($col, 'like', $like);
                    }
                })
                ->limit($limit)
                ->get();
        }
    }
}
