<?php

declare(strict_types=1);

namespace App\Filament\Admin\Widgets;

use App\Models\User;
use Filament\Widgets\ChartWidget;

class SignupsChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Регистрации (7 дней)';

    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        $start = now()->subDays(6)->startOfDay();

        $rows = User::query()
            ->selectRaw('DATE(created_at) as day, COUNT(*) as count')
            ->where('created_at', '>=', $start)
            ->groupBy('day')
            ->pluck('count', 'day')
            ->all();

        $labels = [];
        $values = [];
        for ($i = 0; $i < 7; $i++) {
            $date = $start->copy()->addDays($i);
            $key = $date->toDateString();
            $labels[] = $date->format('d.m');
            $values[] = (int) ($rows[$key] ?? 0);
        }

        return [
            'datasets' => [[
                'label' => 'Регистрации',
                'data' => $values,
                'borderColor' => '#2563EB',
                'backgroundColor' => 'rgba(37, 99, 235, 0.1)',
                'fill' => true,
                'tension' => 0.3,
            ]],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
