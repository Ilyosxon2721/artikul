<?php

declare(strict_types=1);

use App\Services\Chat\ContactFilter;

it('detects email addresses', function () {
    $result = app(ContactFilter::class)->inspect('Contact me at john@example.com');
    expect($result['matched'])->toBeTrue();
    expect($result['reason'])->toBe('email');
    expect($result['masked'])->not->toContain('john@example.com');
});

it('detects phone numbers', function () {
    $result = app(ContactFilter::class)->inspect('My phone is +998 90 123 45 67');
    expect($result['matched'])->toBeTrue();
});

it('detects telegram links', function () {
    $result = app(ContactFilter::class)->inspect('Add me on t.me/myname');
    expect($result['matched'])->toBeTrue();
    expect($result['reason'])->toBe('link');
});

it('passes clean text', function () {
    $result = app(ContactFilter::class)->inspect('Just a normal message about the task.');
    expect($result['matched'])->toBeFalse();
});
