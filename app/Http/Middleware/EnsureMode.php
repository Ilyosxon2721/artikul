<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\UserMode;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class EnsureMode
{
    public function handle(Request $request, Closure $next, string $mode): Response
    {
        $required = UserMode::tryFrom($mode);
        $user = $request->user();

        abort_unless($user !== null, 403);
        abort_unless($required !== null, 500, 'Unknown mode: '.$mode);

        if ($user->current_mode !== $required) {
            return redirect()
                ->route('mode.required', ['mode' => $required->value])
                ->with('mode_required_intent', $request->fullUrl());
        }

        return $next($request);
    }
}
