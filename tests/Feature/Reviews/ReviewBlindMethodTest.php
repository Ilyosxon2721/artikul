<?php

declare(strict_types=1);

use App\Enums\ContractStatus;
use App\Enums\Currency;
use App\Enums\TaskType;
use App\Models\BuyerProfile;
use App\Models\Contract;
use App\Models\SellerProfile;
use App\Models\Task;
use App\Models\User;
use App\Services\Reviews\ReviewService;

beforeEach(function () {
    $this->buyer = User::factory()->create();
    BuyerProfile::create(['user_id' => $this->buyer->id]);
    $this->seller = User::factory()->create();
    SellerProfile::create(['user_id' => $this->seller->id]);

    $this->task = Task::create([
        'buyer_id' => $this->buyer->id,
        'type' => TaskType::Gig,
        'status' => 'completed',
        'title' => 'Reviewable task',
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
        'status' => ContractStatus::Completed,
        'contract_type' => TaskType::Gig,
        'agreed_amount' => 100,
        'currency' => Currency::USD,
        'started_at' => now()->subDays(2),
        'completed_at' => now(),
    ]);
});

it('keeps reviews hidden until both sides submit', function () {
    /** @var ReviewService $service */
    $service = app(ReviewService::class);

    $review = $service->leave($this->contract, $this->buyer, [
        'rating_overall' => 5,
        'comment' => str_repeat('Excellent work all around. ', 3),
    ]);

    expect($review->is_visible)->toBeFalse();
    expect($this->seller->fresh()->sellerProfile->rating_avg)->toEqual('0.00');
});

it('publishes both reviews when the second one arrives and updates ratings', function () {
    $service = app(ReviewService::class);

    $service->leave($this->contract, $this->buyer, [
        'rating_overall' => 5,
        'comment' => str_repeat('Excellent work all around. ', 3),
    ]);
    $service->leave($this->contract->fresh(), $this->seller, [
        'rating_overall' => 4,
        'comment' => str_repeat('Good buyer to work with. ', 3),
    ]);

    expect((float) $this->seller->fresh()->sellerProfile->rating_avg)->toBe(5.0);
    expect((float) $this->buyer->fresh()->buyerProfile->rating_avg)->toBe(4.0);
});

it('rejects double reviews from the same author', function () {
    $service = app(ReviewService::class);

    $service->leave($this->contract, $this->buyer, [
        'rating_overall' => 5,
        'comment' => str_repeat('Excellent. ', 5),
    ]);

    $service->leave($this->contract, $this->buyer, [
        'rating_overall' => 4,
        'comment' => str_repeat('Wait, again. ', 5),
    ]);
})->throws(RuntimeException::class, 'already reviewed');
