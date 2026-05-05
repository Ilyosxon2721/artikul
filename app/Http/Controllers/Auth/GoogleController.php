<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Enums\UserMode;
use App\Http\Controllers\Controller;
use App\Models\BuyerProfile;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirectResponse;

class GoogleController extends Controller
{
    public function redirect(): SymfonyRedirectResponse
    {
        return Socialite::driver('google')
            ->scopes(['openid', 'profile', 'email'])
            ->redirect();
    }

    public function callback(): RedirectResponse
    {
        $oauth = Socialite::driver('google')->user();

        $user = DB::transaction(function () use ($oauth): User {
            $user = User::query()->where('email', $oauth->getEmail())->first();

            if (! $user) {
                $user = User::create([
                    'name' => $oauth->getName() ?: ($oauth->getNickname() ?: 'User'),
                    'email' => $oauth->getEmail(),
                    'password' => Hash::make(Str::random(40)),
                    'avatar_url' => $oauth->getAvatar(),
                    'email_verified_at' => now(),
                    'current_mode' => UserMode::Buyer,
                    'referral_code' => Str::upper(Str::random(8)),
                    'locale' => app()->getLocale(),
                ]);

                UserProfile::firstOrCreate(['user_id' => $user->id]);
                BuyerProfile::firstOrCreate(['user_id' => $user->id]);
            } elseif (! $user->email_verified_at) {
                $user->email_verified_at = now();
                $user->save();
            }

            return $user;
        });

        Auth::login($user, remember: true);

        return redirect()->intended(route('dashboard', absolute: false));
    }
}
