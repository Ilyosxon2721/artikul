<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ReviewResource\Pages;
use App\Models\Review;
use App\Services\Reviews\ReviewService;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ReviewResource extends Resource
{
    protected static ?string $model = Review::class;

    protected static ?string $navigationIcon = 'heroicon-o-star';

    protected static ?string $navigationGroup = 'Модерация';

    protected static ?int $navigationSort = 52;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('contract_id')->label('Сделка'),
                Tables\Columns\TextColumn::make('author.name')->label('Автор')->searchable(),
                Tables\Columns\TextColumn::make('target.name')->label('Кому')->searchable(),
                Tables\Columns\TextColumn::make('rating_overall')->label('★'),
                Tables\Columns\IconColumn::make('is_visible')->boolean()->label('Опубликован'),
                Tables\Columns\TextColumn::make('created_at')->dateTime('d.m.Y H:i')->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_visible')->label('Опубликован'),
            ])
            ->actions([
                Tables\Actions\Action::make('hide')
                    ->label('Скрыть')
                    ->icon('heroicon-o-eye-slash')
                    ->color('warning')
                    ->visible(fn (Review $r) => $r->is_visible)
                    ->requiresConfirmation()
                    ->action(function (Review $r): void {
                        $r->forceFill(['is_visible' => false])->save();
                        app(ReviewService::class)->refreshAggregatesFor((int) $r->target_id);
                    }),
                Tables\Actions\Action::make('publish')
                    ->label('Опубликовать')
                    ->icon('heroicon-o-eye')
                    ->color('success')
                    ->visible(fn (Review $r) => ! $r->is_visible)
                    ->action(function (Review $r): void {
                        $r->forceFill(['is_visible' => true, 'published_at' => now()])->save();
                        app(ReviewService::class)->refreshAggregatesFor((int) $r->target_id);
                    }),
                Tables\Actions\DeleteAction::make()
                    ->after(fn (Review $r) => app(ReviewService::class)->refreshAggregatesFor((int) $r->target_id)),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReviews::route('/'),
        ];
    }
}
