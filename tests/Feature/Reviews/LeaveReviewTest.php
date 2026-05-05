<?php

declare(strict_types=1);

use App\Enums\ContractStatus;
use App\Enums\Currency;
use App\Enums\TaskType;
use App\Livewire\Reviews\LeaveReview;
use App\Models\Contract;
use App\Models\Review;
use App\Models\Task;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->buyer = User::factory()->create();
    $this->seller = User::factory()->create();

    $this->task = Task::create([
        'buyer_id' => $this->buyer->id,
        'type' => TaskType::Gig,
        'status' => 'completed',
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
        'status' => ContractStatus::Completed,
        'contract_type' => TaskType::Gig,
        'agreed_amount' => 100,
        'currency' => Currency::USD,
        'started_at' => now()->subDay(),
        'completed_at' => now(),
    ]);
});

it('saves a review through the Livewire form', function () {
    $this->actingAs($this->buyer);

    Livewire::test(LeaveReview::class, ['contract' => $this->contract])
        ->set('ratingOverall', 5)
        ->set('ratingQuality', 5)
        ->set('ratingCommunication', 4)
        ->set('ratingDeadline', 5)
        ->set('comment', str_repeat('Great working with this freelancer. ', 3))
        ->set('wouldWorkAgain', 'yes')
        ->call('save')
        ->assertHasNoErrors();

    expect(Review::where('contract_id', $this->contract->id)->where('author_id', $this->buyer->id)->count())->toBe(1);
});

it('rejects too-short comments', function () {
    $this->actingAs($this->buyer);

    Livewire::test(LeaveReview::class, ['contract' => $this->contract])
        ->set('comment', 'short')
        ->call('save')
        ->assertHasErrors('comment');
});
