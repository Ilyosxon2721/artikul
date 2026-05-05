<?php

declare(strict_types=1);

use App\Models\Marketplace;

it('renders the home page', function () {
    $this->get('/')->assertOk()->assertSee('Artikul');
});

it('renders the about page', function () {
    $this->get('/about')->assertOk();
});

it('renders the how-it-works page', function () {
    $this->get('/how-it-works')->assertOk();
});

it('renders the terms page', function () {
    $this->get('/terms')->assertOk();
});

it('renders the privacy page', function () {
    $this->get('/privacy')->assertOk();
});

it('renders the for-sellers landing', function () {
    $this->get('/for-sellers')->assertOk();
});

it('renders the for-buyers landing', function () {
    $this->get('/for-buyers')->assertOk();
});

it('renders a per-marketplace landing', function () {
    Marketplace::query()->firstOrCreate(['code' => 'WB'], [
        'name_ru' => 'Wildberries', 'name_uz' => 'Wildberries', 'name_en' => 'Wildberries',
        'country_code' => 'RU', 'is_active' => true, 'sort_order' => 1,
    ]);

    $this->get('/marketplaces/WB')->assertOk()->assertSee('Wildberries');
});

it('returns a healthy /health JSON', function () {
    $this->get('/health')
        ->assertStatus(200)
        ->assertJsonPath('status', 'ok');
});

it('returns sitemap.xml', function () {
    $this->get('/sitemap.xml')
        ->assertStatus(200)
        ->assertHeader('Content-Type', 'application/xml');
});

it('renders the tasks catalog', function () {
    $this->get('/tasks')->assertOk();
});

it('renders the sellers catalog', function () {
    $this->get('/sellers')->assertOk();
});
