<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Enums\VerificationStatus;
use App\Filament\Admin\Resources\VerificationResource\Pages;
use App\Models\User;
use App\Models\Verification;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class VerificationResource extends Resource
{
    protected static ?string $model = Verification::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationGroup = 'Модерация';

    protected static ?int $navigationSort = 50;

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::query()->where('status', VerificationStatus::Pending)->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('status')
                ->options(collect(VerificationStatus::cases())->mapWithKeys(fn ($s) => [$s->value => $s->name]))
                ->required(),
            Forms\Components\Textarea::make('admin_comment')->rows(3),
            Forms\Components\Textarea::make('rejection_reason')->rows(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('user.name')->label('Пользователь')->searchable(),
                Tables\Columns\BadgeColumn::make('status')->colors([
                    'warning' => VerificationStatus::Pending->value,
                    'success' => VerificationStatus::Approved->value,
                    'danger' => VerificationStatus::Rejected->value,
                ]),
                Tables\Columns\TextColumn::make('reviewer.name')->label('Рассмотрел'),
                Tables\Columns\TextColumn::make('created_at')->dateTime('d.m.Y H:i')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(collect(VerificationStatus::cases())->mapWithKeys(fn ($s) => [$s->value => $s->name])),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('approve')
                    ->label('Одобрить')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Verification $v) => $v->status === VerificationStatus::Pending)
                    ->action(function (Verification $v): void {
                        $v->forceFill([
                            'status' => VerificationStatus::Approved,
                            'reviewed_by' => auth()->id(),
                            'reviewed_at' => now(),
                        ])->save();

                        /** @var User|null $user */
                        $user = $v->user;
                        $user?->sellerProfile?->forceFill([
                            'is_verified' => true,
                            'verified_at' => now(),
                        ])->save();
                    }),
                Tables\Actions\Action::make('reject')
                    ->label('Отклонить')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Verification $v) => $v->status === VerificationStatus::Pending)
                    ->form([Forms\Components\Textarea::make('rejection_reason')->required()->rows(3)])
                    ->action(function (Verification $v, array $data): void {
                        $v->forceFill([
                            'status' => VerificationStatus::Rejected,
                            'rejection_reason' => $data['rejection_reason'],
                            'reviewed_by' => auth()->id(),
                            'reviewed_at' => now(),
                        ])->save();
                    }),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVerifications::route('/'),
            'edit' => Pages\EditVerification::route('/{record}/edit'),
        ];
    }
}
