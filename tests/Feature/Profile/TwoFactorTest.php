<?php

declare(strict_types=1);

use App\Livewire\Profile\TwoFactorSetup;
use App\Models\User;
use App\Services\TwoFactor\TwoFactorService;
use Livewire\Livewire;
use PragmaRX\Google2FA\Google2FA;

it('enables two factor with a valid code', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $component = Livewire::test(TwoFactorSetup::class)
        ->call('start')
        ->assertSet('pendingSecret', fn ($v) => is_string($v) && strlen($v) > 8);

    $secret = $component->get('pendingSecret');
    $code = (new Google2FA)->getCurrentOtp($secret);

    $component->set('code', $code)->call('confirm')->assertHasNoErrors();

    $user->refresh();
    expect($user->hasTwoFactorEnabled())->toBeTrue();
    expect($user->two_factor_recovery_codes)->toBeArray();
});

it('rejects invalid codes', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Livewire::test(TwoFactorSetup::class)
        ->call('start')
        ->set('code', '000000')
        ->call('confirm')
        ->assertHasErrors('code');

    expect($user->fresh()->hasTwoFactorEnabled())->toBeFalse();
});

it('consumes a recovery code only once', function () {
    $user = User::factory()->create([
        'two_factor_secret' => 'JBSWY3DPEHPK3PXP',
        'two_factor_recovery_codes' => ['abcde-fghij', 'klmno-pqrst'],
        'two_factor_confirmed_at' => now(),
    ]);

    $svc = app(TwoFactorService::class);

    expect($svc->consumeRecoveryCode($user, 'abcde-fghij'))->toBeTrue();
    expect($svc->consumeRecoveryCode($user->fresh(), 'abcde-fghij'))->toBeFalse();
    expect($user->fresh()->two_factor_recovery_codes)->toEqual(['klmno-pqrst']);
});
