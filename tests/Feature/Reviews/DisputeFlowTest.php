<?php

declare(strict_types=1);

use App\Enums\ContractStatus;
use App\Enums\Currency;
use App\Enums\DisputeStatus;
use App\Enums\TaskType;
use App\Models\Contract;
use App\Models\Dispute;
use App\Models\Task;
use App\Models\User;
use App\Services\Reviews\DisputeService;

beforeEach(function () {
    $this->buyer = User::factory()->create();
    $this->seller = User::factory()->create();
    $this->admin = User::factory()->create(['is_admin' => true]);

    $this->task = Task::create([
        'buyer_id' => $this->buyer->id,
        'type' => TaskType::Gig,
        'status' => 'in_progress',
        'title' => 'Disputable task',
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
        'status' => ContractStatus::Submitted,
        'contract_type' => TaskType::Gig,
        'agreed_amount' => 100,
        'currency' => Currency::USD,
        'started_at' => now()->subDay(),
    ]);
});

it('opens a dispute and flips the contract status', function () {
    /** @var DisputeService $service */
    $service = app(DisputeService::class);

    $dispute = $service->open($this->contract, $this->buyer, [
        'reason_code' => 'quality',
        'description' => str_repeat('The work has multiple issues. ', 3),
    ]);

    expect($dispute)->toBeInstanceOf(Dispute::class);
    expect($dispute->status)->toBe(DisputeStatus::Open);
    expect($this->contract->fresh()->status)->toBe(ContractStatus::Disputed);
    expect($this->contract->fresh()->has_dispute)->toBeTrue();
});

it('admins can resolve a dispute', function () {
    $service = app(DisputeService::class);
    $dispute = $service->open($this->contract, $this->buyer, [
        'reason_code' => 'deadline',
        'description' => str_repeat('Missed deadline by weeks. ', 3),
    ]);

    $resolved = $service->resolve($dispute, $this->admin, DisputeStatus::ResolvedBuyer, [
        'summary' => 'Buyer was right.',
    ]);

    expect($resolved->status)->toBe(DisputeStatus::ResolvedBuyer);
    expect($this->contract->fresh()->has_dispute)->toBeFalse();
});

it('only admins can resolve disputes', function () {
    $service = app(DisputeService::class);
    $dispute = $service->open($this->contract, $this->buyer, [
        'reason_code' => 'quality',
        'description' => str_repeat('Bad. ', 10),
    ]);

    $service->resolve($dispute, $this->seller, DisputeStatus::ResolvedSeller);
})->throws(RuntimeException::class, 'admins');
