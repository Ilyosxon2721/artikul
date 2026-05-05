<?php

declare(strict_types=1);

use App\Filament\Admin\Resources\UserResource;
use App\Filament\Admin\Resources\UserResource\Pages\ListUsers;
use App\Models\User;
use Filament\Facades\Filament;
use Livewire\Livewire;

beforeEach(function () {
    $this->admin = User::factory()->create(['is_admin' => true]);
    $this->actingAs($this->admin);
    Filament::setCurrentPanel(Filament::getPanel('admin'));
});

it('admins can list users', function () {
    User::factory()->count(3)->create();

    $this->get(UserResource::getUrl('index'))->assertOk();
});

it('ban action sets is_banned and banned_at', function () {
    $target = User::factory()->create(['is_banned' => false]);

    Livewire::test(ListUsers::class)
        ->callTableAction('ban', $target);

    $target->refresh();
    expect($target->is_banned)->toBeTrue();
    expect($target->banned_at)->not->toBeNull();
});

it('unban action clears the flag', function () {
    $target = User::factory()->create(['is_banned' => true, 'banned_at' => now()]);

    Livewire::test(ListUsers::class)
        ->callTableAction('unban', $target);

    expect($target->fresh()->is_banned)->toBeFalse();
});
