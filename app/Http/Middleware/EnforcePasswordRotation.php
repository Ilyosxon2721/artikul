<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\Response;

final class EnforcePasswordRotation
{
    private const ADMIN_MAX_AGE_DAYS = 180;

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null || ! $user->is_admin) {
            return $next($request);
        }

        /** @var Carbon|null $changed */
        $changed = $user->password_changed_at;
        $age = $changed instanceof Carbon ? $changed->diffInDays(now()) : PHP_INT_MAX;

        if ($age < self::ADMIN_MAX_AGE_DAYS) {
            return $next($request);
        }

        if ($request->routeIs('password.update', 'password.expired', 'logout', 'verification.*')) {
            return $next($request);
        }

        return redirect()->route('password.expired');
    }
}
