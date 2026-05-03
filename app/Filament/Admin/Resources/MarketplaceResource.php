<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\MarketplaceResource\Pages;
use App\Models\Marketplace;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MarketplaceResource extends Resource
{
    protected static ?string $model = Marketplace::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationGroup = 'Справочники';

    protected static ?int $navigationSort = 30;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('code')->required()->unique(ignoreRecord: true)->maxLength(16),
            Forms\Components\TextInput::make('name_ru')->required()->maxLength(64),
            Forms\Components\TextInput::make('name_uz')->required()->maxLength(64),
            Forms\Components\TextInput::make('name_en')->required()->maxLength(64),
            Forms\Components\TextInput::make('country_code')->maxLength(2),
            Forms\Components\TextInput::make('color')->maxLength(7),
            Forms\Components\TextInput::make('logo_url')->url()->maxLength(255),
            Forms\Components\TextInput::make('website_url')->url()->maxLength(255),
            Forms\Components\Toggle::make('is_active')->default(true),
            Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')->sortable(),
                Tables\Columns\TextColumn::make('name_ru')->searchable(),
                Tables\Columns\TextColumn::make('country_code'),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
                Tables\Columns\TextColumn::make('sort_order')->sortable(),
            ])
            ->defaultSort('sort_order')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMarketplaces::route('/'),
            'create' => Pages\CreateMarketplace::route('/create'),
            'edit' => Pages\EditMarketplace::route('/{record}/edit'),
        ];
    }
}
