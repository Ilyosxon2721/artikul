<?php

declare(strict_types=1);

use App\Enums\Currency;
use App\Livewire\Profile\EditSellerProfile;
use App\Models\Marketplace;
use App\Models\SellerProfile;
use App\Models\Specialization;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create(['username' => 'seller_'.uniqid()]);
    $this->actingAs($this->user);

    $this->marketplace = Marketplace::query()->firstOrCreate(
        ['code' => 'WB'],
        [
            'name_ru' => 'Wildberries',
            'name_uz' => 'Wildberries',
            'name_en' => 'Wildberries',
            'country_code' => 'RU',
            'is_active' => true,
            'sort_order' => 1,
        ],
    );

    $this->specialization = Specialization::query()->firstOrCreate(
        ['slug' => 'wb-manager'],
        [
            'name_ru' => 'Менеджер WB',
            'name_uz' => 'WB menejeri',
            'name_en' => 'WB manager',
            'is_active' => true,
            'sort_order' => 1,
        ],
    );
});

it('saves seller profile fields', function () {
    Livewire::test(EditSellerProfile::class)
        ->set('headline', 'WB и Ozon, 4 года')
        ->set('bio', 'Опытный менеджер маркетплейсов.')
        ->set('hourlyRate', 25.00)
        ->set('currency', 'USD')
        ->set('specializationIds', [$this->specialization->id])
        ->set('marketplaceLevels.'.$this->marketplace->id, 'senior')
        ->set('languages', ['ru', 'en'])
        ->call('save')
        ->assertHasNoErrors();

    $profile = SellerProfile::where('user_id', $this->user->id)->first();

    expect($profile->headline)->toBe('WB и Ozon, 4 года');
    expect((float) $profile->hourly_rate)->toBe(25.00);
    expect($profile->currency)->toBe(Currency::USD);
    expect($profile->specializations->pluck('id')->all())->toEqual([$this->specialization->id]);
    expect($profile->marketplaces->first()->pivot->level)->toBe('senior');
});

it('public seller profile page is accessible', function () {
    SellerProfile::create([
        'user_id' => $this->user->id,
        'headline' => 'Hello',
    ]);

    $this->get(route('public.seller', $this->user->username))
        ->assertOk()
        ->assertSee('Hello');
});
