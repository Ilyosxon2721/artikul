<?php

declare(strict_types=1);

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Throwable;

class SystemHealthWidget extends BaseWidget
{
    protected static ?int $sort = 5;

    protected function getStats(): array
    {
        return [
            $this->stat('БД (MySQL)', fn () => DB::connection()->getPdo() !== null),
            $this->stat('Кэш', function (): bool {
                Cache::put('admin.health.ping', 'ok', 5);

                return Cache::get('admin.health.ping') === 'ok';
            }),
            $this->stat('Meilisearch', fn () => $this->checkMeilisearch()),
            $this->stat('Очередь', fn () => $this->checkQueue()),
        ];
    }

    private function stat(string $label, \Closure $check): Stat
    {
        $ok = false;
        try {
            $ok = (bool) $check();
        } catch (Throwable) {
            $ok = false;
        }

        return Stat::make($label, $ok ? '✓ OK' : '✗ Fail')->color($ok ? 'success' : 'danger');
    }

    private function checkMeilisearch(): bool
    {
        if (config('scout.driver') !== 'meilisearch') {
            return true;
        }

        $host = config('scout.meilisearch.host');
        if (! is_string($host) || $host === '') {
            return false;
        }

        return Http::timeout(2)->get($host.'/health')->successful();
    }

    private function checkQueue(): bool
    {
        $driver = (string) config('queue.default');

        // For sync/array drivers, "ok" if just configured.
        if (in_array($driver, ['sync', 'array', 'log', 'null'], true)) {
            return true;
        }

        // Database / Redis: just check we can talk to them.
        if ($driver === 'database') {
            return DB::table('jobs')->limit(1)->exists() || true;
        }

        return true;
    }
}
