<?php

declare(strict_types=1);

use App\Console\Commands\RunSavedSearchAlerts;
use App\Enums\Currency;
use App\Enums\TaskStatus;
use App\Enums\TaskType;
use App\Models\SavedSearch;
use App\Models\Task;
use App\Models\User;
use App\Notifications\SavedSearchMatchNotification;
use Illuminate\Support\Facades\Notification;

it('notifies the owner about new matching tasks', function () {
    Notification::fake();

    $user = User::factory()->create();
    $buyer = User::factory()->create();

    $search = SavedSearch::create([
        'user_id' => $user->id,
        'name' => 'WB managers',
        'search_type' => 'tasks',
        'filters' => [],
        'query' => 'wildberries',
        'notify_via' => ['email'],
        'frequency' => 'instant',
        'is_active' => true,
    ]);
    // Bypass timestamp guard
    $search->forceFill(['created_at' => now()->subHours(2)])->save();

    Task::create([
        'buyer_id' => $buyer->id,
        'type' => TaskType::Gig,
        'status' => TaskStatus::Published,
        'title' => 'Wildberries manager needed',
        'description' => str_repeat('Wildberries operations. ', 5),
        'marketplace_ids' => [],
        'budget_type' => 'fixed',
        'budget_min' => 100,
        'currency' => Currency::USD,
        'published_at' => now()->subMinutes(5),
    ]);

    $this->artisan(RunSavedSearchAlerts::class)->assertSuccessful();

    Notification::assertSentTo($user, SavedSearchMatchNotification::class);
});
