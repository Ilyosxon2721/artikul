<?php

declare(strict_types=1);

use App\Enums\ContractStatus;
use App\Enums\Currency;
use App\Enums\TaskType;
use App\Livewire\Disputes\OpenDispute;
use App\Models\Contract;
use App\Models\Dispute;
use App\Models\Task;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->buyer = User::factory()->create();
    $this->seller = User::factory()->create();

    $this->task = Task::create([
        'buyer_id' => $this->buyer->id,
        'type' => TaskType::Gig,
        'status' => 'submitted',
        'title' => 'X',
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
        'status' => ContractStatus::Submitted,
        'contract_type' => TaskType::Gig,
        'agreed_amount' => 100,
        'currency' => Currency::USD,
        'started_at' => now()->subDay(),
    ]);
});

it('opens a dispute via the Livewire form', function () {
    $this->actingAs($this->buyer);

    Livewire::test(OpenDispute::class, ['contract' => $this->contract])
        ->set('reasonCode', 'quality')
        ->set('description', str_repeat('Quality issues across multiple deliverables. ', 3))
        ->call('open')
        ->assertHasNoErrors()
        ->assertRedirect();

    expect(Dispute::where('contract_id', $this->contract->id)->count())->toBe(1);
});

it('strangers cannot access the form', function () {
    $stranger = User::factory()->create();
    $this->actingAs($stranger);

    Livewire::test(OpenDispute::class, ['contract' => $this->contract])->assertForbidden();
});
