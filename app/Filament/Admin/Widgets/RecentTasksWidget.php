<?php

declare(strict_types=1);

namespace App\Filament\Admin\Widgets;

use App\Models\Task;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentTasksWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(Task::query()->latest()->limit(10))
            ->heading('Последние задачи')
            ->columns([
                Tables\Columns\TextColumn::make('title')->limit(60),
                Tables\Columns\TextColumn::make('buyer.name')->label('Заказчик'),
                Tables\Columns\BadgeColumn::make('status'),
                Tables\Columns\TextColumn::make('created_at')->dateTime('d.m.Y H:i'),
            ]);
    }
}
