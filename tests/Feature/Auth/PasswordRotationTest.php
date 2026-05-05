<?php

declare(strict_types=1);

use App\Models\User;

it('redirects admins to password expired page when password is older than 180 days', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
        'password_changed_at' => now()->subDays(200),
        'email_verified_at' => now(),
    ]);

    $this->actingAs($admin);
    $this->get('/dashboard')->assertRedirect(route('password.expired'));
});

it('does not redirect non-admins', function () {
    $user = User::factory()->create([
        'is_admin' => false,
        'password_changed_at' => now()->subDays(500),
        'email_verified_at' => now(),
    ]);

    $this->actingAs($user);
    $this->get('/dashboard')->assertOk();
});

it('does not redirect admins when their password is fresh', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
        'password_changed_at' => now()->subDays(30),
        'email_verified_at' => now(),
    ]);

    $this->actingAs($admin);
    $this->get('/dashboard')->assertOk();
});
