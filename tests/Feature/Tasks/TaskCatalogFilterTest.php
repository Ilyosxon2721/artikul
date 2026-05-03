<?php

declare(strict_types=1);

use App\Enums\BudgetType;
use App\Enums\Currency;
use App\Enums\TaskStatus;
use App\Enums\TaskType;
use App\Livewire\Tasks\TaskCatalog;
use App\Models\Category;
use App\Models\Marketplace;
use App\Models\Task;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $buyer = User::factory()->create();

    $this->wb = Marketplace::query()->firstOrCreate(['code' => 'WB'], [
        'name_ru' => 'Wildberries', 'name_uz' => 'Wildberries', 'name_en' => 'Wildberries',
        'country_code' => 'RU', 'is_active' => true, 'sort_order' => 1,
    ]);
    $this->ozon = Marketplace::query()->firstOrCreate(['code' => 'OZON'], [
        'name_ru' => 'Ozon', 'name_uz' => 'Ozon', 'name_en' => 'Ozon',
        'country_code' => 'RU', 'is_active' => true, 'sort_order' => 2,
    ]);

    $this->design = Category::query()->firstOrCreate(['slug' => 'design'], [
        'name_ru' => 'Дизайн', 'name_uz' => 'Dizayn', 'name_en' => 'Design',
        'is_active' => true, 'sort_order' => 1,
    ]);
    $this->ads = Category::query()->firstOrCreate(['slug' => 'ads'], [
        'name_ru' => 'Реклама', 'name_uz' => 'Reklama', 'name_en' => 'Ads',
        'is_active' => true, 'sort_order' => 2,
    ]);

    Task::create([
        'buyer_id' => $buyer->id, 'type' => TaskType::Gig, 'status' => TaskStatus::Published,
        'title' => 'WB infographic 10 cards', 'description' => str_repeat('Need design for WB. ', 5),
        'marketplace_ids' => [$this->wb->id], 'category_id' => $this->design->id,
        'budget_type' => BudgetType::Fixed, 'budget_min' => 1000000, 'currency' => Currency::UZS,
        'published_at' => now()->subDay(),
    ]);

    Task::create([
        'buyer_id' => $buyer->id, 'type' => TaskType::Project, 'status' => TaskStatus::Published,
        'title' => 'Ozon ads campaign', 'description' => str_repeat('Need ad manager. ', 5),
        'marketplace_ids' => [$this->ozon->id], 'category_id' => $this->ads->id,
        'budget_type' => BudgetType::Range, 'budget_min' => 5000000, 'budget_max' => 10000000, 'currency' => Currency::UZS,
        'published_at' => now()->subHours(2),
    ]);

    Task::create([
        'buyer_id' => $buyer->id, 'type' => TaskType::Gig, 'status' => TaskStatus::Draft,
        'title' => 'Hidden draft', 'description' => str_repeat('Draft. ', 10),
        'marketplace_ids' => [$this->wb->id], 'category_id' => $this->design->id,
        'budget_type' => BudgetType::Negotiable, 'currency' => Currency::UZS,
    ]);
});

it('only shows published tasks', function () {
    Livewire::test(TaskCatalog::class)
        ->assertSee('WB infographic 10 cards')
        ->assertSee('Ozon ads campaign')
        ->assertDontSee('Hidden draft');
});

it('filters by marketplace', function () {
    Livewire::test(TaskCatalog::class)
        ->set('marketplaceIds', [$this->ozon->id])
        ->assertSee('Ozon ads campaign')
        ->assertDontSee('WB infographic 10 cards');
});

it('filters by category', function () {
    Livewire::test(TaskCatalog::class)
        ->set('categoryId', $this->ads->id)
        ->assertSee('Ozon ads campaign')
        ->assertDontSee('WB infographic 10 cards');
});

it('filters by task type', function () {
    Livewire::test(TaskCatalog::class)
        ->set('taskType', 'project')
        ->assertSee('Ozon ads campaign')
        ->assertDontSee('WB infographic 10 cards');
});

it('searches by title text', function () {
    Livewire::test(TaskCatalog::class)
        ->set('search', 'infographic')
        ->assertSee('WB infographic 10 cards')
        ->assertDontSee('Ozon ads campaign');
});

it('sorts by budget', function () {
    Livewire::test(TaskCatalog::class)
        ->set('sort', 'budget')
        ->assertSeeInOrder(['Ozon ads campaign', 'WB infographic 10 cards']);
});
