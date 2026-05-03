<?php

declare(strict_types=1);

namespace App\Filament\Admin\Widgets;

use App\Enums\VerificationStatus;
use App\Models\Verification;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentVerificationsWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(Verification::query()
                ->where('status', VerificationStatus::Pending)
                ->latest()
                ->limit(10))
            ->heading('Ожидают верификации')
            ->columns([
                Tables\Columns\TextColumn::make('user.name')->label('Пользователь'),
                Tables\Columns\TextColumn::make('user.email')->label('Email'),
                Tables\Columns\TextColumn::make('created_at')->dateTime('d.m.Y H:i')->label('Подал'),
            ]);
    }
}
