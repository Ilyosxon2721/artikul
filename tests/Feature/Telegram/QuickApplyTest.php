<?php

declare(strict_types=1);

use App\Enums\BudgetType;
use App\Enums\Currency;
use App\Enums\TaskStatus;
use App\Enums\TaskType;
use App\Models\Proposal;
use App\Models\SellerProfile;
use App\Models\Task;
use App\Models\User;
use App\Services\Telegram\TelegramConversationState;

beforeEach(function () {
    $this->buyer = User::factory()->create();
    $this->seller = User::factory()->create(['telegram_chat_id' => '123456']);
    SellerProfile::create(['user_id' => $this->seller->id, 'is_verified' => true]);

    $this->task = Task::create([
        'buyer_id' => $this->buyer->id,
        'type' => TaskType::Gig,
        'status' => TaskStatus::Published,
        'title' => 'Looking for a WB designer',
        'description' => str_repeat('Need product card design. ', 5),
        'marketplace_ids' => [],
        'budget_type' => BudgetType::Fixed,
        'budget_min' => 1000,
        'currency' => Currency::USD,
        'published_at' => now(),
    ]);
});

it('starts the apply conversation on callback_query', function () {
    $this->postJson('/telegram/webhook', [
        'callback_query' => [
            'message' => ['chat' => ['id' => '123456']],
            'data' => 'apply:'.$this->task->id,
        ],
    ])->assertOk();

    $state = app(TelegramConversationState::class)->get('123456');

    expect($state)->not->toBeNull();
    expect($state['flow'])->toBe('apply');
    expect($state['task_id'])->toBe($this->task->id);
    expect($state['step'])->toBe('amount');
});

it('walks through the conversation and creates a proposal via_telegram', function () {
    $state = app(TelegramConversationState::class);
    $state->put('123456', ['flow' => 'apply', 'task_id' => $this->task->id, 'step' => 'amount']);

    $this->postJson('/telegram/webhook', [
        'message' => ['chat' => ['id' => '123456'], 'text' => '900'],
    ])->assertOk();

    expect($state->get('123456')['step'])->toBe('days');
    expect($state->get('123456')['amount'])->toBe(900.0);

    $this->postJson('/telegram/webhook', [
        'message' => ['chat' => ['id' => '123456'], 'text' => '7'],
    ])->assertOk();

    expect($state->get('123456')['step'])->toBe('cover');
    expect($state->get('123456')['days'])->toBe(7);

    $this->postJson('/telegram/webhook', [
        'message' => ['chat' => ['id' => '123456'], 'text' => str_repeat('Experienced with WB cards. ', 3)],
    ])->assertOk();

    $proposal = Proposal::query()->where('task_id', $this->task->id)->where('seller_id', $this->seller->id)->first();

    expect($proposal)->not->toBeNull();
    expect((bool) $proposal->via_telegram)->toBeTrue();
    expect((float) $proposal->proposed_amount)->toBe(900.0);
    expect($proposal->proposed_deadline_days)->toBe(7);
});

it('cancel command clears the conversation', function () {
    $state = app(TelegramConversationState::class);
    $state->put('123456', ['flow' => 'apply', 'task_id' => $this->task->id, 'step' => 'days']);

    $this->postJson('/telegram/webhook', [
        'message' => ['chat' => ['id' => '123456'], 'text' => '/cancel'],
    ])->assertOk();

    expect($state->get('123456'))->toBeNull();
});
