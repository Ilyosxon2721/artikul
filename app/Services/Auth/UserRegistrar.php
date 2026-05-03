<?php

declare(strict_types=1);

namespace App\Services\Auth;

use App\Enums\UserMode;
use App\Models\BuyerProfile;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

final class UserRegistrar
{
    /**
     * @param  array{name:string, email?:?string, phone?:?string, password:string, locale?:string, country_code?:?string, referral_code?:?string}  $attributes
     */
    public function register(array $attributes, UserMode $mode = UserMode::Buyer): User
    {
        return DB::transaction(function () use ($attributes, $mode): User {
            $referrer = ! empty($attributes['referral_code'])
                ? User::query()->where('referral_code', $attributes['referral_code'])->first()
                : null;

            $user = User::create([
                'name' => $attributes['name'],
                'email' => $attributes['email'] ?? null,
                'phone' => $attributes['phone'] ?? null,
                'password' => Hash::make($attributes['password']),
                'locale' => $attributes['locale'] ?? config('app.locale'),
                'country_code' => $attributes['country_code'] ?? null,
                'current_mode' => $mode,
                'referral_code' => $this->generateReferralCode(),
                'referred_by' => $referrer?->id,
                'password_changed_at' => now(),
            ]);

            UserProfile::firstOrCreate(['user_id' => $user->id]);
            BuyerProfile::firstOrCreate(['user_id' => $user->id]);

            return $user->fresh();
        });
    }

    private function generateReferralCode(): string
    {
        do {
            $code = Str::upper(Str::random(8));
        } while (User::query()->where('referral_code', $code)->exists());

        return $code;
    }
}
