<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

final class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $available = config('app.available_locales', ['ru', 'uz', 'en']);
        $fallback = config('app.fallback_locale', 'en');
        $default = config('app.locale', 'ru');

        $locale = $this->resolveLocale($request, $available)
            ?? $default
            ?? $fallback;

        App::setLocale($locale);
        $request->session()->put('locale', $locale);

        return $next($request);
    }

    private function resolveLocale(Request $request, array $available): ?string
    {
        $candidates = [
            $request->query('lang'),
            $request->session()->get('locale'),
            $request->user()?->locale,
            $request->getPreferredLanguage($available),
        ];

        foreach ($candidates as $candidate) {
            if (is_string($candidate) && in_array($candidate, $available, true)) {
                return $candidate;
            }
        }

        return null;
    }
}
