<?php

namespace App\Filament\Admin\Resources;

use App\Models\OfficialPhone;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

/**
 * Recurso Filament para gestionar los Teléfonos Oficiales de Chipiona.
 */
class OfficialPhoneResource extends Resource
{
    protected static ?string $model = OfficialPhone::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-phone';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getModelLabel(): string
    {
        return __('filament.official_phone');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.official_phones');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament.nav_chipiona_utility');
    }

    public static function form(Schema $form): Schema
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->label(__('filament.name'))
                ->required()
                ->maxLength(200),
            Forms\Components\Select::make('category')
                ->label(__('filament.category'))
                ->options([
                    'emergencias' => 'Emergencias y Seguridad 24h',
                    'salud' => 'Salud y Farmacias',
                    'ayuntamiento' => 'Ayuntamiento y Servicios Municipales',
                    'transporte' => 'Transporte, Taxi y Puerto',
                    'cultura' => 'Turismo, Cultura y Patrimonio',
                ])
                ->required()
                ->default('ayuntamiento'),
            Forms\Components\TextInput::make('phone')
                ->label(__('filament.phone'))
                ->required()
                ->maxLength(50),
            Forms\Components\TextInput::make('secondary_phone')
                ->label(__('filament.secondary_phone'))
                ->maxLength(50),
            Forms\Components\TextInput::make('address')
                ->label(__('filament.address'))
                ->maxLength(255),
            Forms\Components\TextInput::make('schedule')
                ->label(__('filament.schedule'))
                ->maxLength(120),
            Forms\Components\TextInput::make('latitude')
                ->label(__('filament.latitude'))
                ->numeric()
                ->step('0.0000001')
                ->minValue(-90)
                ->maxValue(90),
            Forms\Components\TextInput::make('longitude')
                ->label(__('filament.longitude'))
                ->numeric()
                ->step('0.0000001')
                ->minValue(-180)
                ->maxValue(180),
            Forms\Components\TextInput::make('sort_order')
                ->label(__('filament.order'))
                ->numeric()
                ->minValue(0)
                ->maxValue(99999)
                ->default(0),
            Forms\Components\Toggle::make('is_emergency')
                ->label(__('filament.is_emergency'))
                ->default(false),
            Forms\Components\Toggle::make('is_active')
                ->label(__('filament.is_active'))
                ->default(true),
            Forms\Components\Textarea::make('description')
                ->label(__('filament.description'))
                ->rows(3)
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('filament.name'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category')
                    ->label(__('filament.category'))
                    ->badge(),
                Tables\Columns\TextColumn::make('phone')
                    ->label(__('filament.phone'))
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_emergency')
                    ->label(__('filament.is_emergency'))
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('filament.is_active'))
                    ->boolean(),
            ])
            ->actions([
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }

    /**
     * @return array<string, PageRegistration>
     */
    public static function getPages(): array
    {
        return [
            'index' => OfficialPhoneResource\Pages\ListOfficialPhones::route('/'),
            'create' => OfficialPhoneResource\Pages\CreateOfficialPhone::route('/create'),
            'edit' => OfficialPhoneResource\Pages\EditOfficialPhone::route('/{record}/edit'),
        ];
    }
}
