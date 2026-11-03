<?php

namespace App\Filament\Admin\Resources;

use App\Enums\ContentGroup;
use App\Enums\SuggestionStatus;
use App\Models\ContentSuggestion;
use App\Models\Page;
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
 * Recurso Filament para gestionar las sugerencias ciudadanas de nuevos contenidos.
 */
class ContentSuggestionResource extends Resource
{
    protected static ?string $model = ContentSuggestion::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-light-bulb';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'title';

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
        return __('filament.content_suggestion');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.content_suggestions');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament.nav_participation');
    }

    public static function form(Schema $form): Schema
    {
        return $form->schema([
            Forms\Components\Select::make('content_group')
                ->label(__('filament.content_group'))
                ->options(ContentGroup::class)
                ->required()
                ->default(ContentGroup::Historia->value),
            Forms\Components\Select::make('status')
                ->label(__('filament.status'))
                ->options(SuggestionStatus::class)
                ->required()
                ->default(SuggestionStatus::Pending->value),
            Forms\Components\TextInput::make('title')
                ->label(__('filament.title'))
                ->required()
                ->maxLength(255)
                ->columnSpanFull(),
            Forms\Components\Select::make('related_page_id')
                ->label(__('filament.related_pages'))
                ->relationship('relatedPage', 'slug')
                ->getOptionLabelFromRecordUsing(fn (Page $record) => $record->getTranslation('title', app()->getLocale()).' ('.$record->slug.')')
                ->searchable()
                ->preload()
                ->columnSpanFull(),
            Forms\Components\Textarea::make('body')
                ->label(__('filament.content'))
                ->required()
                ->rows(6)
                ->columnSpanFull(),
            Forms\Components\Textarea::make('sources_info')
                ->label(__('filament.sources_info'))
                ->rows(3)
                ->columnSpanFull(),
            Forms\Components\TextInput::make('location_name')
                ->label(__('filament.location_name'))
                ->maxLength(255),
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
            Forms\Components\TextInput::make('author_name')
                ->label(__('filament.author_name'))
                ->required()
                ->maxLength(150),
            Forms\Components\TextInput::make('author_email')
                ->label(__('filament.author_email'))
                ->email()
                ->maxLength(191),
            Forms\Components\TextInput::make('oauth_provider')
                ->label(__('filament.oauth_provider'))
                ->maxLength(30),
            Forms\Components\Textarea::make('admin_notes')
                ->label(__('filament.admin_notes'))
                ->rows(3)
                ->maxLength(5000)
                ->columnSpanFull(),
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
                Tables\Columns\TextColumn::make('content_group')
                    ->label(__('filament.content_group'))
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->label(__('filament.title'))
                    ->searchable()
                    ->limit(45),
                Tables\Columns\TextColumn::make('author_name')
                    ->label(__('filament.author_name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('filament.created_at'))
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('filament.status'))
                    ->options(SuggestionStatus::class),
                Tables\Filters\SelectFilter::make('content_group')
                    ->label(__('filament.content_group'))
                    ->options(ContentGroup::class),
            ])
            ->actions([
                Action::make('approve')
                    ->label(__('filament.mark_approved'))
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (ContentSuggestion $record): bool => $record->status !== SuggestionStatus::Approved)
                    ->action(function (ContentSuggestion $record): void {
                        $record->update(['status' => SuggestionStatus::Approved]);
                        Notification::make()->title(__('filament.status_updated'))->success()->send();
                    }),
                Action::make('reject')
                    ->label(__('filament.mark_rejected'))
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (ContentSuggestion $record): bool => $record->status !== SuggestionStatus::Rejected)
                    ->action(function (ContentSuggestion $record): void {
                        $record->update(['status' => SuggestionStatus::Rejected]);
                        Notification::make()->title(__('filament.status_updated'))->warning()->send();
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
            'index' => ContentSuggestionResource\Pages\ListContentSuggestions::route('/'),
            'create' => ContentSuggestionResource\Pages\CreateContentSuggestion::route('/create'),
            'edit' => ContentSuggestionResource\Pages\EditContentSuggestion::route('/{record}/edit'),
        ];
    }
}
