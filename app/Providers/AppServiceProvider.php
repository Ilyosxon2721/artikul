<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\Eskiz\EskizService;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(EskizService::class, function ($app): EskizService {
            $cfg = $app['config']->get('services.eskiz');

            return new EskizService(
                email: (string) ($cfg['email'] ?? ''),
                password: (string) ($cfg['password'] ?? ''),
                senderId: (string) ($cfg['sender_id'] ?? '4546'),
                baseUrl: (string) ($cfg['base_url'] ?? 'https://notify.eskiz.uz/api'),
            );
        });
    }

    public function boot(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        $this->configureRateLimiters();
    }

    private function configureRateLimiters(): void
    {
        RateLimiter::for('login', function (Request $request): Limit {
            $key = $request->input('email', $request->input('phone', '')).'|'.$request->ip();

            return Limit::perMinute(5)->by($key);
        });

        RateLimiter::for('register', fn (Request $request): Limit => Limit::perMinute(5)->by($request->ip()));

        RateLimiter::for('phone-code', fn (Request $request): Limit => Limit::perMinute(3)->by(
            (string) ($request->input('phone', '')).'|'.$request->ip(),
        ));

        RateLimiter::for('api', fn (Request $request): Limit => Limit::perMinute(60)->by(
            $request->user()?->id ?: $request->ip(),
        ));
    }
}
