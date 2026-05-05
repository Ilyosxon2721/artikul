<?php

declare(strict_types=1);

use App\Enums\BudgetType;
use App\Enums\ContractStatus;
use App\Enums\Currency;
use App\Enums\TaskStatus;
use App\Enums\TaskType;
use App\Models\Contract;
use App\Models\Proposal;
use App\Models\SellerProfile;
use App\Models\Task;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->buyer = User::factory()->create();
    $this->seller = User::factory()->create();
    SellerProfile::create(['user_id' => $this->seller->id, 'is_verified' => true]);

    $this->task = Task::create([
        'buyer_id' => $this->buyer->id,
        'type' => TaskType::Gig,
        'status' => TaskStatus::Published,
        'title' => 'Need WB designer api',
        'description' => str_repeat('Need card design. ', 5),
        'marketplace_ids' => [],
        'budget_type' => BudgetType::Fixed,
        'budget_min' => 1000,
        'currency' => Currency::USD,
        'published_at' => now(),
    ]);
});

it('seller can create a proposal via API', function () {
    Sanctum::actingAs($this->seller);

    $response = $this->postJson("/api/tasks/{$this->task->slug}/proposals", [
        'cover_letter' => str_repeat('I am the right person. ', 3),
        'proposed_amount' => 800,
        'currency' => 'USD',
        'rate_type' => 'fixed',
        'proposed_deadline_days' => 7,
    ]);

    $response->assertSuccessful()->assertJsonPath('data.task_id', $this->task->id);
});

it('buyer can accept the proposal via API', function () {
    Sanctum::actingAs($this->seller);
    $this->postJson("/api/tasks/{$this->task->slug}/proposals", [
        'cover_letter' => str_repeat('Pick me. ', 5),
        'proposed_amount' => 800,
    ])->assertSuccessful();

    $proposalId = Proposal::query()->where('task_id', $this->task->id)->value('id');

    Sanctum::actingAs($this->buyer);
    $this->postJson("/api/proposals/{$proposalId}/accept")
        ->assertSuccessful()
        ->assertJsonPath('data.status', ContractStatus::InProgress->value);
});

it('seller can submit, buyer can approve via API', function () {
    $contract = Contract::create([
        'task_id' => $this->task->id,
        'buyer_id' => $this->buyer->id,
        'seller_id' => $this->seller->id,
        'status' => ContractStatus::InProgress,
        'contract_type' => TaskType::Gig,
        'agreed_amount' => 1000,
        'currency' => Currency::USD,
        'started_at' => now(),
        'revisions_limit' => 3,
    ]);

    Sanctum::actingAs($this->seller);
    $this->postJson("/api/contracts/{$contract->id}/submit")
        ->assertOk()
        ->assertJsonPath('data.status', ContractStatus::Submitted->value);

    Sanctum::actingAs($this->buyer);
    $this->postJson("/api/contracts/{$contract->id}/approve")
        ->assertOk()
        ->assertJsonPath('data.status', ContractStatus::Completed->value);
});
