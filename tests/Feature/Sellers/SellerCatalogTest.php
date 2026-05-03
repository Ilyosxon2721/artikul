<?php

declare(strict_types=1);

use App\Enums\Currency;
use App\Livewire\Sellers\SellerCatalog;
use App\Models\Marketplace;
use App\Models\SellerProfile;
use App\Models\Specialization;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->wb = Marketplace::query()->firstOrCreate(['code' => 'WB'], [
        'name_ru' => 'Wildberries', 'name_uz' => 'Wildberries', 'name_en' => 'Wildberries',
        'country_code' => 'RU', 'is_active' => true, 'sort_order' => 1,
    ]);
    $this->ozon = Marketplace::query()->firstOrCreate(['code' => 'OZON'], [
        'name_ru' => 'Ozon', 'name_uz' => 'Ozon', 'name_en' => 'Ozon',
        'country_code' => 'RU', 'is_active' => true, 'sort_order' => 2,
    ]);

    $this->designSpec = Specialization::query()->firstOrCreate(['slug' => 'designer'], [
        'name_ru' => 'Дизайнер', 'name_uz' => 'Dizayner', 'name_en' => 'Designer',
        'is_active' => true, 'sort_order' => 1,
    ]);

    $this->verifiedUser = User::factory()->create(['username' => 'verified_'.uniqid(), 'name' => 'Verified Senior']);
    $verifiedProfile = SellerProfile::create([
        'user_id' => $this->verifiedUser->id,
        'headline' => 'Senior WB designer',
        'hourly_rate' => 50,
        'currency' => Currency::USD,
        'is_verified' => true,
        'rating_avg' => 4.8,
        'reviews_count' => 30,
    ]);
    $verifiedProfile->specializations()->sync([$this->designSpec->id => ['is_primary' => true]]);
    $verifiedProfile->marketplaces()->sync([$this->wb->id => ['level' => 'senior']]);

    $this->newcomer = User::factory()->create(['username' => 'newcomer_'.uniqid(), 'name' => 'Newcomer Junior']);
    $newProfile = SellerProfile::create([
        'user_id' => $this->newcomer->id,
        'headline' => 'Junior Ozon manager',
        'hourly_rate' => 5,
        'currency' => Currency::USD,
        'is_verified' => false,
        'rating_avg' => 0,
    ]);
    $newProfile->marketplaces()->sync([$this->ozon->id => ['level' => 'junior']]);
});

it('shows all searchable sellers', function () {
    Livewire::test(SellerCatalog::class)
        ->assertSee('Verified Senior')
        ->assertSee('Newcomer Junior');
});

it('filters by verified flag', function () {
    Livewire::test(SellerCatalog::class)
        ->set('verifiedOnly', true)
        ->assertSee('Verified Senior')
        ->assertDontSee('Newcomer Junior');
});

it('filters by specialization', function () {
    Livewire::test(SellerCatalog::class)
        ->set('specializationIds', [$this->designSpec->id])
        ->assertSee('Verified Senior')
        ->assertDontSee('Newcomer Junior');
});

it('filters by hourly rate range', function () {
    Livewire::test(SellerCatalog::class)
        ->set('rateMin', 20)
        ->assertSee('Verified Senior')
        ->assertDontSee('Newcomer Junior');
});

it('filters by min rating', function () {
    Livewire::test(SellerCatalog::class)
        ->set('minRating', 4)
        ->assertSee('Verified Senior')
        ->assertDontSee('Newcomer Junior');
});

it('filters by marketplace', function () {
    Livewire::test(SellerCatalog::class)
        ->set('marketplaceIds', [$this->ozon->id])
        ->assertSee('Newcomer Junior')
        ->assertDontSee('Verified Senior');
});
