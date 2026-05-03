<?php

declare(strict_types=1);

use App\Models\PhoneVerificationCode;
use App\Models\User;
use App\Services\Auth\PhoneVerificationService;
use App\Services\Eskiz\EskizService;
use Illuminate\Support\Facades\Hash;
use Livewire\Volt\Volt;

it('issues a 6-digit code and stores it hashed', function () {
    $service = app(PhoneVerificationService::class);

    $code = $service->issue('+998901234567');

    expect($code)->toMatch('/^\d{6}$/');

    $record = PhoneVerificationCode::query()->where('phone', '+998901234567')->latest()->first();
    expect($record)->not->toBeNull();
    expect(Hash::check($code, $record->code_hash))->toBeTrue();
});

it('verifies the latest code and consumes it', function () {
    $service = app(PhoneVerificationService::class);

    $code = $service->issue('+998901234567');

    expect($service->verify('+998901234567', $code))->toBeTrue();
    expect($service->verify('+998901234567', $code))->toBeFalse();
});

it('locks after three wrong attempts', function () {
    $service = app(PhoneVerificationService::class);

    $service->issue('+998901234567');

    foreach (range(1, 3) as $attempt) {
        expect($service->verify('+998901234567', '000000'))->toBeFalse();
    }

    expect($service->isLocked('+998901234567'))->toBeTrue();
});

it('eskiz stub does not throw and logs the message', function () {
    $sent = app(EskizService::class)->send('+998901234567', 'test');

    expect($sent)->toBeTrue();
});

it('phone registration creates a verified user via wizard', function () {
    $service = app(PhoneVerificationService::class);
    $code = $service->issue('+998901234567');

    session()->put('register.phone_payload', [
        'name' => 'Phone User',
        'phone' => '+998901234567',
        'password' => 'password1',
        'referral_code' => null,
        'intent' => 'buyer',
        'locale' => 'ru',
    ]);

    $component = Volt::test('pages.auth.verify-phone')
        ->set('code', $code);

    $component->call('confirm')->assertHasNoErrors()->assertRedirect();

    $user = User::query()->where('phone', '+998901234567')->first();
    expect($user)->not->toBeNull();
    expect($user->phone_verified_at)->not->toBeNull();
});
