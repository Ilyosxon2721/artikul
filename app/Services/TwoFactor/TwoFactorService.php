<?php

declare(strict_types=1);

namespace App\Services\TwoFactor;

use App\Models\User;
use Illuminate\Support\Str;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorService
{
    public function __construct(private readonly Google2FA $engine = new Google2FA) {}

    public function generateSecret(): string
    {
        return $this->engine->generateSecretKey();
    }

    public function qrUrl(User $user, string $secret): string
    {
        return $this->engine->getQRCodeUrl(
            (string) config('app.name', 'Artikul'),
            (string) ($user->email ?? $user->phone ?? $user->id),
            $secret,
        );
    }

    public function verify(string $secret, string $code): bool
    {
        return $this->engine->verifyKey($secret, preg_replace('/\s+/', '', $code) ?? $code);
    }

    /**
     * @return array<int, string>
     */
    public function generateRecoveryCodes(int $count = 8): array
    {
        return collect(range(1, $count))
            ->map(fn () => Str::lower(Str::random(5)).'-'.Str::lower(Str::random(5)))
            ->all();
    }

    public function consumeRecoveryCode(User $user, string $code): bool
    {
        $codes = (array) ($user->two_factor_recovery_codes ?? []);
        $code = Str::lower(trim($code));

        if (! in_array($code, $codes, true)) {
            return false;
        }

        $remaining = array_values(array_filter($codes, fn ($c) => $c !== $code));
        $user->forceFill(['two_factor_recovery_codes' => $remaining])->save();

        return true;
    }
}
