<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\TaskStatus;
use App\Models\SavedSearch;
use App\Models\Task;
use App\Notifications\SavedSearchMatchNotification;
use Illuminate\Console\Command;

class RunSavedSearchAlerts extends Command
{
    protected $signature = 'artikul:run-saved-search-alerts';

    protected $description = 'Notify users about new tasks matching their saved searches.';

    public function handle(): int
    {
        SavedSearch::query()
            ->where('is_active', true)
            ->where('search_type', 'tasks')
            ->chunkById(50, function ($searches): void {
                foreach ($searches as $search) {
                    $this->processSearch($search);
                }
            });

        return Command::SUCCESS;
    }

    private function processSearch(SavedSearch $search): void
    {
        $cutoff = $search->last_alert_at ?? $search->created_at ?? now()->subWeek();

        $query = Task::query()
            ->where('status', TaskStatus::Published)
            ->where('published_at', '>=', $cutoff);

        $filters = (array) ($search->filters ?? []);
        if (! empty($filters['marketplace_ids'])) {
            $query->where(function ($q) use ($filters): void {
                foreach ($filters['marketplace_ids'] as $id) {
                    $q->orWhereJsonContains('marketplace_ids', (int) $id);
                }
            });
        }
        if (! empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }
        if (! empty($filters['budget_min'])) {
            $query->where('budget_min', '>=', $filters['budget_min']);
        }

        if ($search->query) {
            $like = '%'.str_replace(['%', '_'], ['\%', '\_'], $search->query).'%';
            $query->where(function ($q) use ($like): void {
                $q->where('title', 'like', $like)->orWhere('description', 'like', $like);
            });
        }

        $matches = $query->limit(20)->get();

        if ($matches->isEmpty()) {
            return;
        }

        $search->user?->notify(new SavedSearchMatchNotification($search, $matches));

        $search->forceFill([
            'last_alert_at' => now(),
            'last_match_seen_at' => now(),
        ])->save();
    }
}
