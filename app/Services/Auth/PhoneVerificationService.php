<?php

declare(strict_types=1);

namespace App\Services\Auth;

use App\Models\PhoneVerificationCode;
use App\Services\Eskiz\EskizService;
use Illuminate\Support\Facades\Hash;

final class PhoneVerificationService
{
    public const PURPOSE_REGISTER = 'register';

    public const PURPOSE_LOGIN = 'login';

    public const CODE_TTL_MINUTES = 10;

    public const MAX_ATTEMPTS = 3;

    public const LOCK_MINUTES = 15;

    public function __construct(private readonly EskizService $eskiz) {}

    public function issue(string $phone, string $purpose = self::PURPOSE_REGISTER, ?string $ip = null): string
    {
        $phone = $this->normalize($phone);
        $code = (string) random_int(100000, 999999);

        PhoneVerificationCode::query()
            ->where('phone', $phone)
            ->where('purpose', $purpose)
            ->whereNull('consumed_at')
            ->delete();

        PhoneVerificationCode::create([
            'phone' => $phone,
            'code_hash' => Hash::make($code),
            'purpose' => $purpose,
            'attempts' => 0,
            'expires_at' => now()->addMinutes(self::CODE_TTL_MINUTES),
            'ip_address' => $ip,
        ]);

        $this->eskiz->send(
            $phone,
            __('auth.sms_code_message', ['code' => $code, 'app' => config('app.name')]),
        );

        return $code;
    }

    /**
     * Verify the latest unconsumed code for the phone.
     * Returns true on success and consumes the record.
     */
    public function verify(string $phone, string $code, string $purpose = self::PURPOSE_REGISTER): bool
    {
        $phone = $this->normalize($phone);

        $record = PhoneVerificationCode::query()
            ->where('phone', $phone)
            ->where('purpose', $purpose)
            ->whereNull('consumed_at')
            ->latest('id')
            ->first();

        if (! $record) {
            return false;
        }

        if ($record->locked_until && $record->locked_until->isFuture()) {
            return false;
        }

        if ($record->expires_at->isPast()) {
            return false;
        }

        if (! Hash::check($code, $record->code_hash)) {
            $record->attempts++;
            if ($record->attempts >= self::MAX_ATTEMPTS) {
                $record->locked_until = now()->addMinutes(self::LOCK_MINUTES);
            }
            $record->save();

            return false;
        }

        $record->consumed_at = now();
        $record->save();

        return true;
    }

    public function isLocked(string $phone, string $purpose = self::PURPOSE_REGISTER): bool
    {
        $record = PhoneVerificationCode::query()
            ->where('phone', $this->normalize($phone))
            ->where('purpose', $purpose)
            ->whereNotNull('locked_until')
            ->latest('id')
            ->first();

        return $record !== null && $record->locked_until->isFuture();
    }

    public function normalize(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone) ?? '';

        return $digits === '' ? '' : '+'.$digits;
    }
}
