<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Updates the user's last_active_at and last_ip on authenticated requests,
 * but rate-limited to once per 5 minutes per user to avoid excessive writes.
 */
final class TrackLastActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user !== null) {
            $cutoff = now()->subMinutes(5);
            $lastActive = $user->last_active_at;

            if ($lastActive === null || $lastActive < $cutoff) {
                $user->forceFill([
                    'last_active_at' => now(),
                    'last_ip' => $request->ip(),
                ])->saveQuietly();
            }
        }

        return $next($request);
    }
}
