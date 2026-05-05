<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Enums\UserMode;
use App\Models\User;
use Livewire\Volt\Volt;

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response
        ->assertOk()
        ->assertSeeVolt('pages.auth.register');
});

test('new users can register with email', function () {
    $component = Volt::test('pages.auth.register')
        ->set('tab', 'email')
        ->set('name', 'Test User')
        ->set('email', 'test@example.com')
        ->set('password', 'password1')
        ->set('password_confirmation', 'password1')
        ->set('intent', 'buyer');

    $component->call('register');

    $component->assertHasNoErrors()->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticated();

    $user = User::query()->where('email', 'test@example.com')->first();
    expect($user)->not->toBeNull();
    expect($user->referral_code)->not->toBeNull();
    expect($user->current_mode)->toBe(UserMode::Buyer);
});

test('seller intent stores user in seller mode', function () {
    Volt::test('pages.auth.register')
        ->set('tab', 'email')
        ->set('name', 'Seller User')
        ->set('email', 'seller@example.com')
        ->set('password', 'password1')
        ->set('password_confirmation', 'password1')
        ->set('intent', 'seller')
        ->call('register');

    $user = User::query()->where('email', 'seller@example.com')->first();
    expect($user->current_mode)->toBe(UserMode::Seller);
});
