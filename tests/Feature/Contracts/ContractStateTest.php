<?php

declare(strict_types=1);

use App\Enums\ContractStatus;
use App\Enums\Currency;
use App\Enums\TaskStatus;
use App\Enums\TaskType;
use App\Models\Contract;
use App\Models\Task;
use App\Models\User;
use App\Services\Contracts\ContractService;

beforeEach(function () {
    $this->buyer = User::factory()->create();
    $this->seller = User::factory()->create();

    $this->task = Task::create([
        'buyer_id' => $this->buyer->id,
        'type' => TaskType::Gig,
        'status' => TaskStatus::InProgress,
        'title' => 'Test task',
        'description' => str_repeat('Body. ', 10),
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
        'contract_type' => TaskType::Gig,
        'agreed_amount' => 500,
        'currency' => Currency::USD,
        'started_at' => now(),
        'revisions_limit' => 3,
    ]);
});

it('seller can submit work', function () {
    app(ContractService::class)->submit($this->contract, $this->seller);

    expect($this->contract->fresh()->status)->toBe(ContractStatus::Submitted);
});

it('buyer cannot submit work for the seller', function () {
    app(ContractService::class)->submit($this->contract, $this->buyer);
})->throws(RuntimeException::class, 'Only the seller');

it('buyer can approve and complete the contract', function () {
    $service = app(ContractService::class);
    $service->submit($this->contract, $this->seller);
    $service->approve($this->contract->fresh(), $this->buyer);

    $this->contract->refresh();
    expect($this->contract->status)->toBe(ContractStatus::Completed);
    expect($this->task->fresh()->status)->toBe(TaskStatus::Completed);
});

it('buyer can request a revision but not beyond the limit', function () {
    $service = app(ContractService::class);
    $service->submit($this->contract, $this->seller);
    $service->requestRevision($this->contract->fresh(), $this->buyer);

    $contract = $this->contract->fresh();
    expect($contract->status)->toBe(ContractStatus::Revision);
    expect($contract->revisions_used)->toBe(1);
});
