<?php

declare(strict_types=1);

use App\Models\User;
use App\Services\Chat\MessagingService;

it('opens a 1:1 conversation idempotently', function () {
    $a = User::factory()->create();
    $b = User::factory()->create();

    /** @var MessagingService $service */
    $service = app(MessagingService::class);

    $first = $service->openDirect($a, $b);
    $second = $service->openDirect($a, $b);

    expect($first->id)->toBe($second->id);
});

it('filters contacts before the deal starts', function () {
    $a = User::factory()->create();
    $b = User::factory()->create();
    $service = app(MessagingService::class);

    $conv = $service->openDirect($a, $b);
    $message = $service->send($conv, $a, 'Reach me at +998901234567');

    expect($message->is_filtered)->toBeTrue();
    expect($message->filter_reason)->toBe('phone');
    expect($message->body)->not->toContain('+998901234567');
});

it('does not filter once the deal is unlocked', function () {
    $a = User::factory()->create();
    $b = User::factory()->create();
    $service = app(MessagingService::class);

    $conv = $service->openDirect($a, $b);
    $conv->forceFill(['contact_filter_enabled' => false])->save();

    $message = $service->send($conv->fresh(), $a, 'Reach me at +998901234567');

    expect($message->is_filtered)->toBeFalse();
    expect($message->body)->toContain('+998901234567');
});
