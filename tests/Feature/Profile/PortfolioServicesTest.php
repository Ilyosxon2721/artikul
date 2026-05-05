<?php

declare(strict_types=1);

use App\Livewire\Profile\PortfolioManager;
use App\Livewire\Profile\ServicesManager;
use App\Models\Portfolio;
use App\Models\SellerProfile;
use App\Models\Service;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
    SellerProfile::create(['user_id' => $this->user->id]);
    $this->actingAs($this->user);
});

it('saves a portfolio item', function () {
    Livewire::test(PortfolioManager::class)
        ->set('title', 'Wildberries product cards')
        ->set('description', 'Designed 10 cards with infographics.')
        ->call('save')
        ->assertHasNoErrors();

    $profile = SellerProfile::where('user_id', $this->user->id)->firstOrFail();

    expect(Portfolio::where('seller_profile_id', $profile->id)->count())->toBe(1);
});

it('saves a service / gig', function () {
    Livewire::test(ServicesManager::class)
        ->set('title', '10 product cards for WB')
        ->set('description', 'Layout + infographics, source files included.')
        ->set('price', 200.0)
        ->set('currency', 'USD')
        ->set('deliveryDays', 5)
        ->call('save')
        ->assertHasNoErrors();

    $profile = SellerProfile::where('user_id', $this->user->id)->firstOrFail();
    $service = Service::where('seller_profile_id', $profile->id)->first();

    expect($service)->not->toBeNull();
    expect((float) $service->price)->toBe(200.0);
    expect($service->delivery_days)->toBe(5);
});
