<?php

declare(strict_types=1);

use App\Enums\BudgetType;
use App\Enums\ContractStatus;
use App\Enums\Currency;
use App\Enums\ProposalStatus;
use App\Enums\TaskStatus;
use App\Enums\TaskType;
use App\Models\Proposal;
use App\Models\SellerProfile;
use App\Models\Task;
use App\Models\User;
use App\Services\Contracts\ProposalService;

beforeEach(function () {
    $this->buyer = User::factory()->create();
    $this->seller = User::factory()->create();
    SellerProfile::create(['user_id' => $this->seller->id, 'is_verified' => true]);

    $this->task = Task::create([
        'buyer_id' => $this->buyer->id,
        'type' => TaskType::Gig,
        'status' => TaskStatus::Published,
        'title' => 'Need WB designer',
        'description' => str_repeat('Project description. ', 5),
        'marketplace_ids' => [],
        'budget_type' => BudgetType::Fixed,
        'budget_min' => 1000,
        'currency' => Currency::USD,
        'published_at' => now(),
    ]);
});

it('creates a proposal and increments counter', function () {
    /** @var ProposalService $service */
    $service = app(ProposalService::class);

    $proposal = $service->create($this->task, $this->seller, [
        'cover_letter' => str_repeat('I am the right fit. ', 5),
        'proposed_amount' => 800,
        'proposed_deadline_days' => 7,
    ]);

    expect($proposal)->toBeInstanceOf(Proposal::class);
    expect($proposal->status)->toBe(ProposalStatus::Pending);
    expect($this->task->fresh()->proposals_count)->toBe(1);
});

it('rejects duplicate proposals', function () {
    $service = app(ProposalService::class);

    $service->create($this->task, $this->seller, [
        'cover_letter' => str_repeat('First. ', 5),
        'proposed_amount' => 100,
    ]);

    $service->create($this->task, $this->seller, [
        'cover_letter' => str_repeat('Second. ', 5),
        'proposed_amount' => 100,
    ]);
})->throws(RuntimeException::class, 'already applied');

it('accepting creates a contract and rejects siblings', function () {
    $service = app(ProposalService::class);

    $proposalA = $service->create($this->task, $this->seller, [
        'cover_letter' => str_repeat('Pick me. ', 5),
        'proposed_amount' => 800,
    ]);

    $other = User::factory()->create();
    SellerProfile::create(['user_id' => $other->id, 'is_verified' => true]);
    $proposalB = $service->create($this->task, $other, [
        'cover_letter' => str_repeat('Or me. ', 5),
        'proposed_amount' => 700,
    ]);

    $contract = $service->accept($proposalA);

    expect($contract->status)->toBe(ContractStatus::InProgress);
    expect($contract->buyer_id)->toBe($this->buyer->id);
    expect($contract->seller_id)->toBe($this->seller->id);

    expect($proposalA->fresh()->status)->toBe(ProposalStatus::Accepted);
    expect($proposalB->fresh()->status)->toBe(ProposalStatus::Rejected);

    expect($this->task->fresh()->status)->toBe(TaskStatus::InProgress);
});

it('cannot apply to your own task', function () {
    app(ProposalService::class)->create($this->task, $this->buyer, [
        'cover_letter' => str_repeat('Self. ', 5),
        'proposed_amount' => 100,
    ]);
})->throws(RuntimeException::class, 'own task');
