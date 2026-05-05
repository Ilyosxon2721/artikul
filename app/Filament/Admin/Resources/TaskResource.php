<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Enums\TaskStatus;
use App\Enums\TaskType;
use App\Filament\Admin\Resources\TaskResource\Pages;
use App\Models\Task;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $navigationGroup = 'Контент';

    protected static ?int $navigationSort = 20;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('title')->required()->maxLength(160),
            Forms\Components\Select::make('status')
                ->options(collect(TaskStatus::cases())->mapWithKeys(fn ($s) => [$s->value => $s->label()]))
                ->required(),
            Forms\Components\Select::make('type')
                ->options(collect(TaskType::cases())->mapWithKeys(fn ($t) => [$t->value => $t->label()]))
                ->required(),
            Forms\Components\Textarea::make('description')->rows(8)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('title')->searchable()->limit(50),
                Tables\Columns\TextColumn::make('buyer.name')->label('Заказчик')->searchable(),
                Tables\Columns\BadgeColumn::make('status'),
                Tables\Columns\TextColumn::make('type'),
                Tables\Columns\TextColumn::make('proposals_count')->label('Откликов'),
                Tables\Columns\TextColumn::make('views_count')->label('Просмотры')->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('published_at')->dateTime('d.m.Y')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(collect(TaskStatus::cases())->mapWithKeys(fn ($s) => [$s->value => $s->label()])),
                Tables\Filters\SelectFilter::make('type')
                    ->options(collect(TaskType::cases())->mapWithKeys(fn ($t) => [$t->value => $t->label()])),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('hide')
                    ->label('Скрыть')
                    ->icon('heroicon-o-eye-slash')
                    ->requiresConfirmation()
                    ->visible(fn (Task $t) => $t->status === TaskStatus::Published)
                    ->action(fn (Task $t) => $t->forceFill(['status' => TaskStatus::Cancelled])->save()),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTasks::route('/'),
            'edit' => Pages\EditTask::route('/{record}/edit'),
        ];
    }
}
