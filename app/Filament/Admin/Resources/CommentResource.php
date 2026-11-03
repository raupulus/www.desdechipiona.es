<?php

namespace App\Filament\Admin\Resources;

use App\Models\Comment;
use App\Models\News;
use App\Models\Page;
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
 * Recurso Filament para moderar los comentarios públicos en páginas y noticias.
 */
class CommentResource extends Resource
{
    protected static ?string $model = Comment::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?int $navigationSort = 4;

    protected static ?string $recordTitleAttribute = 'author_name';

    public static function getModelLabel(): string
    {
        return __('filament.comment');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.comments');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament.nav_participation');
    }

    public static function form(Schema $form): Schema
    {
        return $form->schema([
            Forms\Components\MorphToSelect::make('commentable')
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
            Forms\Components\TextInput::make('oauth_provider')
                ->label(__('filament.oauth_provider')),
            Forms\Components\Toggle::make('is_approved')
                ->label(__('filament.is_approved'))
                ->default(true),
            Forms\Components\Textarea::make('body')
                ->label(__('filament.body'))
                ->required()
                ->rows(4)
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\IconColumn::make('is_approved')
                    ->label(__('filament.is_approved'))
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('author_name')
                    ->label(__('filament.author_name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('body')
                    ->label(__('filament.body'))
                    ->searchable()
                    ->limit(60),
                Tables\Columns\TextColumn::make('oauth_provider')
                    ->label(__('filament.oauth_provider'))
                    ->badge(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('filament.created_at'))
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
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
            'index' => CommentResource\Pages\ListComments::route('/'),
            'create' => CommentResource\Pages\CreateComment::route('/create'),
            'edit' => CommentResource\Pages\EditComment::route('/{record}/edit'),
        ];
    }
}
