<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Enums\DisputeStatus;
use App\Filament\Admin\Resources\DisputeResource\Pages;
use App\Models\Dispute;
use App\Services\Reviews\DisputeService;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DisputeResource extends Resource
{
    protected static ?string $model = Dispute::class;

    protected static ?string $navigationIcon = 'heroicon-o-scale';

    protected static ?string $navigationGroup = 'Модерация';

    protected static ?int $navigationSort = 51;

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::query()->whereIn('status', [DisputeStatus::Open->value, DisputeStatus::InReview->value])->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('contract.task.title')->label('Задача')->limit(40),
                Tables\Columns\TextColumn::make('openedBy.name')->label('Открыл'),
                Tables\Columns\BadgeColumn::make('status')->colors([
                    'warning' => DisputeStatus::Open->value,
                    'info' => DisputeStatus::InReview->value,
                    'success' => [DisputeStatus::ResolvedBuyer->value, DisputeStatus::ResolvedSeller->value, DisputeStatus::ResolvedPartial->value],
                    'gray' => DisputeStatus::Cancelled->value,
                ]),
                Tables\Columns\TextColumn::make('sla_due_at')->dateTime('d.m.Y')->label('SLA'),
                Tables\Columns\TextColumn::make('created_at')->dateTime('d.m.Y H:i')->label('Открыт')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(collect(DisputeStatus::cases())->mapWithKeys(fn ($s) => [$s->value => $s->label()])),
            ])
            ->actions([
                Tables\Actions\Action::make('resolve_buyer')
                    ->label('В пользу заказчика')
                    ->icon('heroicon-o-user')
                    ->color('success')
                    ->visible(fn (Dispute $d) => in_array($d->status, [DisputeStatus::Open, DisputeStatus::InReview], true))
                    ->form([Forms\Components\Textarea::make('summary')->required()->rows(3)])
                    ->action(fn (Dispute $d, array $data) => app(DisputeService::class)->resolve($d, auth()->user(), DisputeStatus::ResolvedBuyer, $data)),
                Tables\Actions\Action::make('resolve_seller')
                    ->label('В пользу исполнителя')
                    ->icon('heroicon-o-briefcase')
                    ->color('success')
                    ->visible(fn (Dispute $d) => in_array($d->status, [DisputeStatus::Open, DisputeStatus::InReview], true))
                    ->form([Forms\Components\Textarea::make('summary')->required()->rows(3)])
                    ->action(fn (Dispute $d, array $data) => app(DisputeService::class)->resolve($d, auth()->user(), DisputeStatus::ResolvedSeller, $data)),
                Tables\Actions\Action::make('resolve_partial')
                    ->label('Частично')
                    ->color('warning')
                    ->visible(fn (Dispute $d) => in_array($d->status, [DisputeStatus::Open, DisputeStatus::InReview], true))
                    ->form([
                        Forms\Components\TextInput::make('buyer_share_percent')->numeric()->required()->minValue(0)->maxValue(100),
                        Forms\Components\TextInput::make('seller_share_percent')->numeric()->required()->minValue(0)->maxValue(100),
                        Forms\Components\Textarea::make('summary')->required()->rows(3),
                    ])
                    ->action(fn (Dispute $d, array $data) => app(DisputeService::class)->resolve($d, auth()->user(), DisputeStatus::ResolvedPartial, $data)),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDisputes::route('/'),
        ];
    }
}
