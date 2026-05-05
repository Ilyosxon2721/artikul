<?php

declare(strict_types=1);

namespace App\Livewire\Forms;

use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Validate;
use Livewire\Form;

class LoginForm extends Form
{
    #[Validate('required|string|max:191')]
    public string $identifier = '';

    #[Validate('required|string')]
    public string $password = '';

    #[Validate('boolean')]
    public bool $remember = false;

    /**
     * Authenticate by email OR phone (E.164 with optional spaces).
     *
     * Returns true if the user is fully authenticated; returns false when a
     * 2FA challenge is required and the user id has been parked in the session.
     *
     * @throws ValidationException
     */
    public function authenticate(): bool
    {
        $this->ensureIsNotRateLimited();

        $field = $this->detectField($this->identifier);
        $value = $field === 'phone'
            ? $this->normalizePhone($this->identifier)
            : Str::lower($this->identifier);

        $user = User::query()->where($field, $value)->first();
        if ($user === null || ! Hash::check($this->password, (string) $user->password)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'form.identifier' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());

        if ($user->hasTwoFactorEnabled()) {
            session()->put('auth.2fa.user_id', $user->id);
            session()->put('auth.2fa.remember', $this->remember);

            return false;
        }

        Auth::login($user, $this->remember);

        return true;
    }

    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'form.identifier' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->identifier).'|'.request()->ip());
    }

    private function detectField(string $value): string
    {
        return str_contains($value, '@') ? 'email' : 'phone';
    }

    private function normalizePhone(string $value): string
    {
        $digits = preg_replace('/\D+/', '', $value) ?? '';

        return $digits === '' ? '' : '+'.$digits;
    }
}
