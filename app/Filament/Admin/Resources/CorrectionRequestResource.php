<?php

namespace App\Filament\Admin\Resources;

use App\Enums\SuggestionStatus;
use App\Models\CorrectionRequest;
use App\Models\News;
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
 * Recurso Filament para gestionar la lista global de peticiones de corrección en la intranet.
 * Enlaza directamente al contenido (Page o News) que se sugiere cambiar y permite
 * marcarlas rápidamente como Aprobado, Rechazado o En espera.
 */
class CorrectionRequestResource extends Resource
{
    protected static ?string $model = CorrectionRequest::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-check-badge';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'author_name';

    /**
     * Badge con el número de solicitudes en espera.
     */
    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::where('status', SuggestionStatus::Pending)->count();

        return $count > 0 ? (string) $count : null;
    }

    /**
     * Color del badge de navegación.
     */
    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    /**
     * Devuelve el label del modelo traducido.
     */
    public static function getModelLabel(): string
    {
        return __('filament.correction_request');
    }

    /**
     * Devuelve el label plural del modelo traducido.
     */
    public static function getPluralModelLabel(): string
    {
        return __('filament.correction_requests');
    }

    /**
     * Devuelve el grupo de navegación traducido.
     */
    public static function getNavigationGroup(): ?string
    {
        return __('filament.nav_participation');
    }

    /**
     * Define el formulario del recurso.
     */
    public static function form(Schema $form): Schema
    {
        return $form->schema([
            Forms\Components\MorphToSelect::make('correctable')
                ->label(__('filament.target_content'))
                ->types([
                    Forms\Components\MorphToSelect\Type::make(Page::class)
                        ->titleAttribute('slug')
                        ->label(__('filament.page')),
                    Forms\Components\MorphToSelect\Type::make(News::class)
                        ->titleAttribute('slug')
                        ->label(__('filament.news_singular')),
                ])
                ->required()
                ->columnSpanFull(),
            Forms\Components\TextInput::make('author_name')
                ->label(__('filament.author_name'))
                ->required()
                ->maxLength(150),
            Forms\Components\TextInput::make('author_email')
                ->label(__('filament.author_email'))
                ->email()
                ->maxLength(191),
            Forms\Components\Select::make('status')
                ->label(__('filament.status'))
                ->options(SuggestionStatus::class)
                ->required()
                ->default(SuggestionStatus::Pending->value),
            Forms\Components\TextInput::make('oauth_provider')
                ->label(__('filament.oauth_provider')),
            Forms\Components\Textarea::make('quoted_text')
                ->label(__('filament.quoted_text'))
                ->rows(3)
                ->columnSpanFull(),
            Forms\Components\Textarea::make('proposed_correction')
                ->label(__('filament.proposed_correction'))
                ->required()
                ->rows(4)
                ->columnSpanFull(),
            Forms\Components\Textarea::make('sources_info')
                ->label(__('filament.sources_info'))
                ->rows(2)
                ->columnSpanFull(),
            Forms\Components\Textarea::make('admin_notes')
                ->label(__('filament.admin_notes'))
                ->rows(3)
                ->columnSpanFull(),
        ]);
    }

    /**
     * Define la tabla del recurso con enlace directo al contenido que se sugiere cambiar.
     */
    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('status')
                    ->label(__('filament.status'))
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('correctable_link')
                    ->label(__('filament.target_content'))
                    ->state(function (CorrectionRequest $record): string {
                        $target = $record->correctable;
                        if (! $target) {
                            return '—';
                        }

                        $title = method_exists($target, 'getTranslation')
                            ? $target->getTranslation('title', app()->getLocale())
                            : ($target->slug ?? '#'.$target->id);

                        return (string) $title;
                    })
                    ->url(function (CorrectionRequest $record): ?string {
                        $target = $record->correctable;
                        if ($target instanceof Page) {
                            return PageResource::getUrl('edit', ['record' => $target]);
                        }
                        if ($target instanceof News) {
                            return NewsResource::getUrl('edit', ['record' => $target]);
                        }

                        return null;
                    })
                    ->color('primary')
                    ->limit(40),
                Tables\Columns\TextColumn::make('proposed_correction')
                    ->label(__('filament.proposed_correction'))
                    ->searchable()
                    ->limit(55)
                    ->wrap(),
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
            ])
            ->actions([
                Action::make('approve')
                    ->label(__('filament.mark_approved'))
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (CorrectionRequest $record): bool => $record->status !== SuggestionStatus::Approved)
                    ->action(function (CorrectionRequest $record): void {
                        $record->update([
                            'status' => SuggestionStatus::Approved,
                            'reviewed_by' => auth()->id(),
                            'reviewed_at' => now(),
                        ]);
                        Notification::make()->title(__('filament.status_updated'))->success()->send();
                    }),
                Action::make('reject')
                    ->label(__('filament.mark_rejected'))
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (CorrectionRequest $record): bool => $record->status !== SuggestionStatus::Rejected)
                    ->action(function (CorrectionRequest $record): void {
                        $record->update([
                            'status' => SuggestionStatus::Rejected,
                            'reviewed_by' => auth()->id(),
                            'reviewed_at' => now(),
                        ]);
                        Notification::make()->title(__('filament.status_updated'))->warning()->send();
                    }),
                Action::make('pending')
                    ->label(__('filament.mark_pending'))
                    ->icon('heroicon-o-clock')
                    ->color('warning')
                    ->visible(fn (CorrectionRequest $record): bool => $record->status !== SuggestionStatus::Pending)
                    ->action(function (CorrectionRequest $record): void {
                        $record->update([
                            'status' => SuggestionStatus::Pending,
                            'reviewed_by' => auth()->id(),
                            'reviewed_at' => now(),
                        ]);
                        Notification::make()->title(__('filament.status_updated'))->info()->send();
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
     * Define las páginas del recurso.
     *
     * @return array<string, PageRegistration>
     */
    public static function getPages(): array
    {
        return [
            'index' => CorrectionRequestResource\Pages\ListCorrectionRequests::route('/'),
            'create' => CorrectionRequestResource\Pages\CreateCorrectionRequest::route('/create'),
            'edit' => CorrectionRequestResource\Pages\EditCorrectionRequest::route('/{record}/edit'),
        ];
    }
}
