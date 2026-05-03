<?php

declare(strict_types=1);

use App\Enums\TaskStatus;
use App\Enums\UserMode;
use App\Livewire\Tasks\TaskWizard;
use App\Models\Category;
use App\Models\Marketplace;
use App\Models\Task;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create(['current_mode' => UserMode::Buyer]);
    $this->actingAs($this->user);

    $this->marketplace = Marketplace::query()->firstOrCreate(['code' => 'WB'], [
        'name_ru' => 'Wildberries',
        'name_uz' => 'Wildberries',
        'name_en' => 'Wildberries',
        'country_code' => 'RU',
        'is_active' => true,
        'sort_order' => 1,
    ]);

    $this->category = Category::query()->firstOrCreate(['slug' => 'design'], [
        'name_ru' => 'Дизайн',
        'name_uz' => 'Dizayn',
        'name_en' => 'Design',
        'is_active' => true,
        'sort_order' => 10,
    ]);
});

it('creates a published task through the full wizard', function () {
    Livewire::test(TaskWizard::class)
        ->set('type', 'gig')
        ->set('marketplaceIds', [$this->marketplace->id])
        ->call('next')
        ->assertHasNoErrors()
        ->set('categoryId', $this->category->id)
        ->set('title', 'Design 10 product cards for WB')
        ->call('next')
        ->assertHasNoErrors()
        ->set('description', str_repeat('Need infographics for product cards. ', 5))
        ->call('next')
        ->assertHasNoErrors()
        ->set('budgetType', 'fixed')
        ->set('budgetMin', 1500000)
        ->set('currency', 'UZS')
        ->call('next')
        ->assertHasNoErrors()
        ->call('publish')
        ->assertHasNoErrors();

    $task = Task::query()->where('buyer_id', $this->user->id)->first();
    expect($task)->not->toBeNull();
    expect($task->status)->toBe(TaskStatus::Published);
    expect($task->slug)->not->toBeEmpty();
    expect($task->published_at)->not->toBeNull();
    expect($task->marketplace_ids)->toEqual([$this->marketplace->id]);
});

it('saves a draft and lets the wizard reopen it', function () {
    Livewire::test(TaskWizard::class)
        ->set('type', 'project')
        ->set('marketplaceIds', [$this->marketplace->id])
        ->set('categoryId', $this->category->id)
        ->set('title', 'Long-term WB management')
        ->set('description', str_repeat('Looking for an experienced manager. ', 5))
        ->set('budgetType', 'negotiable')
        ->set('currency', 'UZS')
        ->call('saveDraft');

    $task = Task::query()->where('buyer_id', $this->user->id)->first();
    expect($task)->not->toBeNull();
    expect($task->status)->toBe(TaskStatus::Draft);

    Livewire::test(TaskWizard::class, ['slug' => $task->slug])
        ->assertSet('title', 'Long-term WB management')
        ->assertSet('budgetType', 'negotiable');
});

it('publishing requires all required fields', function () {
    Livewire::test(TaskWizard::class)
        ->set('marketplaceIds', [])
        ->call('publish')
        ->assertHasErrors(['marketplaceIds']);

    expect(Task::query()->count())->toBe(0);
});
