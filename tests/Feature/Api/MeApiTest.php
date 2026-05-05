<?php

declare(strict_types=1);

use App\Models\User;
use Laravel\Sanctum\Sanctum;

it('returns the current user via api', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $this->getJson('/api/me')
        ->assertOk()
        ->assertJsonPath('data.email', $user->email);
});

it('updates the current user via api', function () {
    $user = User::factory()->create(['name' => 'Old Name']);
    Sanctum::actingAs($user);

    $this->putJson('/api/me', ['name' => 'New Name', 'locale' => 'en'])
        ->assertOk()
        ->assertJsonPath('data.name', 'New Name')
        ->assertJsonPath('data.locale', 'en');
});

it('switches mode via api', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $this->postJson('/api/me/mode', ['mode' => 'seller'])
        ->assertOk()
        ->assertJsonPath('data.current_mode', 'seller');
});

it('register endpoint issues a token', function () {
    $this->postJson('/api/auth/register', [
        'name' => 'API User',
        'email' => 'api@example.com',
        'password' => 'password1',
        'password_confirmation' => 'password1',
    ])
        ->assertCreated()
        ->assertJsonStructure(['user' => ['id', 'email'], 'token']);
});

it('login endpoint issues a token', function () {
    $user = User::factory()->create();

    $this->postJson('/api/auth/login', [
        'identifier' => $user->email,
        'password' => 'password',
    ])
        ->assertOk()
        ->assertJsonStructure(['user' => ['id', 'email'], 'token']);
});
