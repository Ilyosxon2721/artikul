<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Throwable;

class HealthController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $checks = [
            'app' => true,
            'db' => $this->check(fn () => DB::connection()->getPdo() !== null),
            'cache' => $this->check(function (): bool {
                Cache::put('health.ping', 'ok', 5);

                return Cache::get('health.ping') === 'ok';
            }),
            'meilisearch' => $this->checkMeilisearch(),
        ];

        $ok = ! in_array(false, $checks, true);

        return response()->json([
            'status' => $ok ? 'ok' : 'degraded',
            'checks' => $checks,
            'timestamp' => now()->toIso8601String(),
            'environment' => app()->environment(),
        ], $ok ? 200 : 503);
    }

    private function check(\Closure $callback): bool
    {
        try {
            return (bool) $callback();
        } catch (Throwable) {
            return false;
        }
    }

    private function checkMeilisearch(): bool
    {
        $host = config('scout.meilisearch.host');
        if (! is_string($host) || $host === '') {
            return true;
        }

        try {
            return Http::timeout(2)->get($host.'/health')->successful();
        } catch (Throwable) {
            return false;
        }
    }
}
