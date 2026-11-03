<?php

namespace App\Filament\Admin\Resources\RelationManagers;

use App\Enums\SuggestionStatus;
use App\Models\CorrectionRequest;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

/**
 * RelationManager polimórfico que muestra la pestaña «Sugerencias del contenido»
 * dentro de PageResource y NewsResource para aprobar, rechazar o dejar en espera
 * rápidamente las propuestas de corrección sobre esa ficha.
 */
class CorrectionRequestsRelationManager extends RelationManager
{
    protected static string $relationship = 'correctionRequests';

    /**
     * Título traducido de la pestaña en la vista de edición del contenido.
     */
    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('filament.content_suggestions_tab');
    }

    /**
     * Badge con el número de sugerencias pendientes sobre este contenido.
     */
    public static function getBadge(Model $ownerRecord, string $pageClass): ?string
    {
        $pending = $ownerRecord->correctionRequests()
            ->where('status', SuggestionStatus::Pending)
            ->count();

        return $pending > 0 ? (string) $pending : null;
    }

    /**
     * Color del badge de pendientes.
     */
    public static function getBadgeColor(Model $ownerRecord, string $pageClass): ?string
    {
        return 'warning';
    }

    /**
     * Formulario de edición / inspección de la solicitud de corrección.
     */
    public function form(Schema $form): Schema
    {
        return $form->schema([
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
                ->label(__('filament.oauth_provider'))
                ->disabled(),
            Forms\Components\Textarea::make('quoted_text')
                ->label(__('filament.quoted_text'))
                ->rows(2)
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
     * Tabla de sugerencias de corrección con acciones rápidas de estado.
     */
    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('author_name')
            ->columns([
                Tables\Columns\TextColumn::make('status')
                    ->label(__('filament.status'))
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('quoted_text')
                    ->label(__('filament.quoted_text'))
                    ->limit(45)
                    ->wrap(),
                Tables\Columns\TextColumn::make('proposed_correction')
                    ->label(__('filament.proposed_correction'))
                    ->limit(65)
                    ->wrap()
                    ->searchable(),
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
            ]);
    }
}
