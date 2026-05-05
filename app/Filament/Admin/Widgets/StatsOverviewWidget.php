<?php

declare(strict_types=1);

namespace App\Filament\Admin\Widgets;

use App\Enums\TaskStatus;
use App\Enums\VerificationStatus;
use App\Models\Contract;
use App\Models\Task;
use App\Models\User;
use App\Models\Verification;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Активных пользователей (30д)', User::query()->where('last_active_at', '>=', now()->subDays(30))->count())
                ->color('primary'),
            Stat::make('Регистраций сегодня', User::query()->whereDate('created_at', today())->count())
                ->color('success'),
            Stat::make('Опубликованных задач', Task::query()->where('status', TaskStatus::Published)->count())
                ->description('Сегодня: '.Task::query()->whereDate('published_at', today())->count())
                ->color('info'),
            Stat::make('Открытых сделок', Contract::query()->whereIn('status', ['in_progress', 'submitted', 'in_review', 'revision'])->count())
                ->color('warning'),
            Stat::make('Заявок на верификацию', Verification::query()->where('status', VerificationStatus::Pending)->count())
                ->color('danger'),
        ];
    }
}
