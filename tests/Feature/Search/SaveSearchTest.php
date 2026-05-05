<?php

declare(strict_types=1);

use App\Livewire\Search\SaveSearch;
use App\Models\SavedSearch;
use App\Models\User;
use Livewire\Livewire;

it('persists a saved search via the modal', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Livewire::test(SaveSearch::class, [
        'filters' => ['marketplace_ids' => [1, 2], 'category_id' => 3],
        'query' => 'wb manager',
    ])
        ->call('show')
        ->set('name', 'WB managers')
        ->set('frequency', 'daily')
        ->set('notifyVia', ['email', 'telegram'])
        ->call('save')
        ->assertHasNoErrors();

    $saved = SavedSearch::query()->where('user_id', $user->id)->first();

    expect($saved)->not->toBeNull();
    expect($saved->name)->toBe('WB managers');
    expect($saved->frequency)->toBe('daily');
    expect($saved->notify_via)->toEqual(['email', 'telegram']);
    expect($saved->query)->toBe('wb manager');
});
