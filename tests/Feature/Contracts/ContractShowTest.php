<?php

declare(strict_types=1);

use App\Enums\ContractStatus;
use App\Enums\Currency;
use App\Enums\TaskStatus;
use App\Enums\TaskType;
use App\Livewire\Contracts\ContractShow;
use App\Models\Contract;
use App\Models\Task;
use App\Models\TaskMilestone;
use App\Models\TimeLog;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->buyer = User::factory()->create();
    $this->seller = User::factory()->create();

    $this->task = Task::create([
        'buyer_id' => $this->buyer->id,
        'type' => TaskType::Project,
        'status' => TaskStatus::InProgress,
        'title' => 'Test',
        'description' => str_repeat('x', 30),
        'marketplace_ids' => [],
        'budget_type' => 'fixed',
        'budget_min' => 100,
        'currency' => Currency::USD,
    ]);

    $this->contract = Contract::create([
        'task_id' => $this->task->id,
        'buyer_id' => $this->buyer->id,
        'seller_id' => $this->seller->id,
        'status' => ContractStatus::InProgress,
        'contract_type' => TaskType::Project,
        'agreed_amount' => 500,
        'currency' => Currency::USD,
        'started_at' => now(),
        'revisions_limit' => 3,
    ]);
});

it('only contract parties can open it', function () {
    $stranger = User::factory()->create();
    $this->actingAs($stranger);

    Livewire::test(ContractShow::class, ['contract' => $this->contract])->assertForbidden();
});

it('buyer can add a milestone', function () {
    $this->actingAs($this->buyer);

    Livewire::test(ContractShow::class, ['contract' => $this->contract])
        ->set('newMilestoneTitle', 'Design phase')
        ->set('newMilestoneAmount', 250)
        ->call('addMilestone')
        ->assertHasNoErrors();

    expect(TaskMilestone::where('contract_id', $this->contract->id)->count())->toBe(1);
});

it('seller can log hours on hourly contract', function () {
    $hourly = Contract::create([
        'task_id' => $this->task->id,
        'buyer_id' => $this->buyer->id,
        'seller_id' => $this->seller->id,
        'status' => ContractStatus::InProgress,
        'contract_type' => TaskType::Hourly,
        'agreed_amount' => 50,
        'currency' => Currency::USD,
        'started_at' => now(),
        'revisions_limit' => 3,
    ]);

    $this->actingAs($this->seller);

    Livewire::test(ContractShow::class, ['contract' => $hourly])
        ->set('newMinutes', 90)
        ->set('newWorkDescription', 'Designed two product cards.')
        ->call('addTimeLog')
        ->assertHasNoErrors();

    expect(TimeLog::where('contract_id', $hourly->id)->count())->toBe(1);
});
