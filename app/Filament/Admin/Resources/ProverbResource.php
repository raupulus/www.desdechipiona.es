<?php

namespace App\Filament\Admin\Resources;

use App\Enums\SuggestionStatus;
use App\Models\Page;
use App\Models\Proverb;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

/**
 * Recurso Filament para gestionar el Refranero Popular de Chipiona.
 */
class ProverbResource extends Resource
{
    protected static ?string $model = Proverb::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-bottom-center-text';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'text';

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::where('status', SuggestionStatus::Pending)->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function getModelLabel(): string
    {
        return __('filament.proverb');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.proverbs');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament.nav_participation');
    }

    public static function form(Schema $form): Schema
    {
        return $form->schema([
            Forms\Components\Textarea::make('text')
                ->label(__('filament.proverb_text'))
                ->required()
                ->rows(3)
                ->columnSpanFull(),
            Forms\Components\Textarea::make('meaning')
                ->label(__('filament.proverb_meaning'))
                ->rows(3)
                ->columnSpanFull(),
            Forms\Components\Select::make('category')
                ->label(__('filament.category'))
                ->options([
                    'mar' => 'La Mar, Mareas y Corrales',
                    'campo' => 'El Campo, Huerta y Moscatel',
                    'vientos' => 'Vientos, Levante y Poniente',
                    'fiestas' => 'Tradiciones y Fiestas',
                    'vida' => 'Vida Cotidiana en el Pueblo',
                ])
                ->required()
                ->default('mar'),
            Forms\Components\Select::make('status')
                ->label(__('filament.status'))
                ->options(SuggestionStatus::class)
                ->required()
                ->default(SuggestionStatus::Approved->value),
            Forms\Components\TextInput::make('source_code')
                ->label(__('filament.source_code'))
                ->maxLength(150),
            Forms\Components\Select::make('related_page_id')
                ->label(__('filament.related_pages'))
                ->relationship('relatedPage', 'slug')
                ->getOptionLabelFromRecordUsing(fn (Page $record) => $record->getTranslation('title', app()->getLocale()).' ('.$record->slug.')')
                ->searchable()
                ->preload(),
            Forms\Components\TextInput::make('author_name')
                ->label(__('filament.author_name'))
                ->required()
                ->maxLength(150)
                ->default('Archivo Oral Desde Chipiona'),
            Forms\Components\TextInput::make('author_email')
                ->label(__('filament.author_email'))
                ->email()
                ->maxLength(191),
            Forms\Components\Toggle::make('featured')
                ->label(__('filament.featured')),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('status')
                    ->label(__('filament.status'))
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('text')
                    ->label(__('filament.proverb_text'))
                    ->searchable()
                    ->limit(60)
                    ->wrap(),
                Tables\Columns\TextColumn::make('category')
                    ->label(__('filament.category'))
                    ->badge(),
                Tables\Columns\TextColumn::make('author_name')
                    ->label(__('filament.author_name'))
                    ->searchable(),
                Tables\Columns\IconColumn::make('featured')
                    ->label(__('filament.featured'))
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('filament.status'))
                    ->options(SuggestionStatus::class),
            ])
            ->actions([
                Action::make('approve')
                    ->label(__('filament.mark_approved'))
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Proverb $record): bool => $record->status !== SuggestionStatus::Approved)
                    ->action(function (Proverb $record): void {
                        $record->update(['status' => SuggestionStatus::Approved]);
                        Notification::make()->title(__('filament.status_updated'))->success()->send();
                    }),
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
            'index' => ProverbResource\Pages\ListProverbs::route('/'),
            'create' => ProverbResource\Pages\CreateProverb::route('/create'),
            'edit' => ProverbResource\Pages\EditProverb::route('/{record}/edit'),
        ];
    }
}
