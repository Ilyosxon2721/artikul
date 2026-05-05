<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Пользователи';

    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->required()->maxLength(120),
            Forms\Components\TextInput::make('username')->maxLength(32),
            Forms\Components\TextInput::make('email')->email()->unique(ignoreRecord: true),
            Forms\Components\TextInput::make('phone')->tel()->unique(ignoreRecord: true),
            Forms\Components\Select::make('locale')->options(['ru' => 'Русский', 'uz' => "O'zbekcha", 'en' => 'English'])->default('ru'),
            Forms\Components\TextInput::make('country_code')->maxLength(2),
            Forms\Components\TextInput::make('city')->maxLength(120),
            Forms\Components\Toggle::make('is_admin'),
            Forms\Components\Toggle::make('is_super_admin'),
            Forms\Components\Toggle::make('is_banned'),
            Forms\Components\Textarea::make('banned_reason')->rows(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\ImageColumn::make('avatar_url')->circular()->size(32),
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('email')->searchable()->copyable(),
                Tables\Columns\TextColumn::make('phone')->searchable(),
                Tables\Columns\BadgeColumn::make('current_mode')->colors(['primary' => 'buyer', 'success' => 'seller']),
                Tables\Columns\IconColumn::make('is_admin')->boolean(),
                Tables\Columns\IconColumn::make('is_banned')->boolean()->trueColor('danger'),
                Tables\Columns\TextColumn::make('created_at')->dateTime('d.m.Y H:i')->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_admin')->label('Админы'),
                Tables\Filters\TernaryFilter::make('is_banned')->label('Забанены'),
                Tables\Filters\SelectFilter::make('current_mode')->options(['buyer' => 'Заказчик', 'seller' => 'Исполнитель']),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('ban')
                    ->label('Бан')
                    ->icon('heroicon-o-no-symbol')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (User $u) => ! $u->is_banned)
                    ->action(fn (User $u) => $u->forceFill(['is_banned' => true, 'banned_at' => now()])->save()),
                Tables\Actions\Action::make('unban')
                    ->label('Разбанить')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (User $u) => $u->is_banned)
                    ->action(fn (User $u) => $u->forceFill(['is_banned' => false, 'banned_at' => null])->save()),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
