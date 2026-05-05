<?php

declare(strict_types=1);

use App\Enums\UserMode;
use App\Livewire\ModeSwitcher;
use App\Models\SellerProfile;
use App\Models\User;
use Livewire\Livewire;

it('redirects to seller onboarding when no seller profile exists', function () {
    $user = User::factory()->create(['current_mode' => UserMode::Buyer]);
    $this->actingAs($user);

    Livewire::test(ModeSwitcher::class)
        ->call('switch', 'seller')
        ->assertRedirect(route('onboarding.seller'));
});

it('switches to seller when seller profile already exists', function () {
    $user = User::factory()->create(['current_mode' => UserMode::Buyer]);
    SellerProfile::create(['user_id' => $user->id]);
    $this->actingAs($user);

    Livewire::test(ModeSwitcher::class)
        ->call('switch', 'seller')
        ->assertRedirect(route('dashboard'));

    expect($user->fresh()->current_mode)->toBe(UserMode::Seller);
});

it('switches back to buyer freely', function () {
    $user = User::factory()->create(['current_mode' => UserMode::Seller]);
    $this->actingAs($user);

    Livewire::test(ModeSwitcher::class)
        ->call('switch', 'buyer')
        ->assertRedirect(route('dashboard'));

    expect($user->fresh()->current_mode)->toBe(UserMode::Buyer);
});
