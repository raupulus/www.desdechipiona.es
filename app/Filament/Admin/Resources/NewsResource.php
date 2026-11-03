<?php

namespace App\Filament\Admin\Resources;

use App\Enums\ContentStatus;
use App\Enums\EvidenceLevel;
use App\Filament\Admin\Resources\RelationManagers\CorrectionRequestsRelationManager;
use App\Filament\Components\ImageCropField;
use App\Models\News;
use App\Models\Page;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Validation\Rules\Unique;

/**
 * Recurso Filament independiente para gestionar las Noticias (News) de Desde Chipiona.
 * Dispone de editor Markdown, coordenadas GPS, vídeo de YouTube, páginas enlazadas y
 * pestaña «Sugerencias del contenido».
 */
class NewsResource extends Resource
{
    protected static ?string $model = News::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-megaphone';

    protected static ?int $navigationSort = 4;

    protected static ?string $recordTitleAttribute = 'slug';

    /**
     * Atributos para la búsqueda global de Filament.
     *
     * @return array<string>
     */
    public static function getGloballySearchableAttributes(): array
    {
        return ['slug', 'title', 'location_name'];
    }

    /**
     * Devuelve el label del modelo traducido.
     */
    public static function getModelLabel(): string
    {
        return __('filament.news_singular');
    }

    /**
     * Devuelve el label plural del modelo traducido.
     */
    public static function getPluralModelLabel(): string
    {
        return __('filament.news_plural');
    }

    /**
     * Devuelve el grupo de navegación traducido.
     */
    public static function getNavigationGroup(): ?string
    {
        return __('filament.nav_content');
    }

    /**
     * Permite acceder y restaurar registros con borrado lógico.
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    /**
     * Define el formulario del recurso Noticia.
     */
    public static function form(Schema $form): Schema
    {
        return $form->schema([
            ImageCropField::make16x9('image', 'news'),
            ImageCropField::make16x9('social_image', 'news/social')
                ->label(__('filament.social_image')),

            Tabs::make(__('filament.translations'))->tabs([
                Tab::make(__('filament.tab_spanish'))->schema([
                    Forms\Components\TextInput::make('title.es')
                        ->label(__('filament.title').' (ES)')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\MarkdownEditor::make('content.es')
                        ->label(__('filament.content').' (ES)')
                        ->fileAttachmentsDisk('public')
                        ->fileAttachmentsDirectory('news/attachments'),
                    Forms\Components\Textarea::make('excerpt.es')
                        ->label(__('filament.excerpt').' (ES)')
                        ->rows(3)
                        ->maxLength(1000),
                    Forms\Components\TextInput::make('seo_title.es')
                        ->label(__('filament.seo_title').' (ES)')
                        ->maxLength(160),
                    Forms\Components\Textarea::make('seo_description.es')
                        ->label(__('filament.seo_description').' (ES)')
                        ->maxLength(320),
                    Forms\Components\TextInput::make('seo_keywords.es')
                        ->label(__('filament.seo_keywords').' (ES)')
                        ->maxLength(255),
                ]),
                Tab::make(__('filament.tab_english'))->schema([
                    Forms\Components\TextInput::make('title.en')
                        ->label(__('filament.title').' (EN)')
                        ->maxLength(255),
                    Forms\Components\MarkdownEditor::make('content.en')
                        ->label(__('filament.content').' (EN)')
                        ->fileAttachmentsDisk('public')
                        ->fileAttachmentsDirectory('news/attachments'),
                    Forms\Components\Textarea::make('excerpt.en')
                        ->label(__('filament.excerpt').' (EN)')
                        ->rows(3)
                        ->maxLength(1000),
                    Forms\Components\TextInput::make('seo_title.en')
                        ->label(__('filament.seo_title').' (EN)')
                        ->maxLength(160),
                    Forms\Components\Textarea::make('seo_description.en')
                        ->label(__('filament.seo_description').' (EN)')
                        ->maxLength(320),
                    Forms\Components\TextInput::make('seo_keywords.en')
                        ->label(__('filament.seo_keywords').' (EN)')
                        ->maxLength(255),
                ]),
            ])->columnSpanFull(),

            Forms\Components\TextInput::make('slug')
                ->label(__('filament.slug'))
                ->maxLength(255)
                ->unique(ignoreRecord: true, modifyRuleUsing: fn (Unique $rule) => $rule->whereNull('deleted_at'))
                ->columnSpanFull(),

            Section::make(__('filament.section_media_map'))
                ->columns(3)
                ->schema([
                    Forms\Components\Select::make('evidence_level')
                        ->label(__('filament.evidence_level'))
                        ->options(EvidenceLevel::class)
                        ->default(EvidenceLevel::Documented->value)
                        ->required(),
                    Forms\Components\TextInput::make('location_name')
                        ->label(__('filament.location_name'))
                        ->maxLength(255),
                    Forms\Components\TextInput::make('youtube_url')
                        ->label(__('filament.youtube_url'))
                        ->url()
                        ->maxLength(512),
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
                    Forms\Components\TagsInput::make('source_references')
                        ->label(__('filament.source_references'))
                        ->placeholder('F-0031'),
                    Forms\Components\Select::make('relatedPages')
                        ->label(__('filament.related_pages'))
                        ->relationship(
                            name: 'relatedPages',
                            titleAttribute: 'slug',
                            modifyQueryUsing: fn ($query) => $query->reorder('slug')
                        )
                        ->getOptionLabelFromRecordUsing(fn (Page $record) => $record->getTranslation('title', app()->getLocale()).' ('.$record->slug.')')
                        ->multiple()
                        ->preload()
                        ->searchable()
                        ->columnSpanFull(),
                ])
                ->columnSpanFull(),

            Forms\Components\Select::make('status')
                ->label(__('filament.status'))
                ->options(ContentStatus::class)
                ->required()
                ->default(ContentStatus::Draft->value),
            Forms\Components\Select::make('categories')
                ->label(__('filament.categories'))
                ->relationship(
                    name: 'categories',
                    titleAttribute: 'name',
                    modifyQueryUsing: fn ($query) => $query->reorder()
                )
                ->getOptionLabelFromRecordUsing(fn ($record) => $record->getTranslation('name', app()->getLocale()))
                ->multiple()
                ->preload(),

            Forms\Components\Toggle::make('is_active')
                ->label(__('filament.is_active'))
                ->default(true),
            Forms\Components\Toggle::make('featured')
                ->label(__('filament.featured')),
            Forms\Components\DateTimePicker::make('published_at')
                ->label(__('filament.published_at')),
        ]);
    }

    /**
     * Define la tabla del recurso Noticia.
     */
    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('slug')
                ->label(__('filament.slug'))
                ->searchable()
                ->sortable()
                ->limit(35),
            Tables\Columns\TextColumn::make('title')
                ->label(__('filament.title'))
                ->searchable()
                ->limit(45),
            Tables\Columns\TextColumn::make('evidence_level')
                ->label(__('filament.evidence_level'))
                ->badge()
                ->toggleable(),
            Tables\Columns\TextColumn::make('status')
                ->label(__('filament.status'))
                ->badge(),
            Tables\Columns\IconColumn::make('featured')
                ->label(__('filament.featured'))
                ->boolean()
                ->toggleable(),
            Tables\Columns\TextColumn::make('published_at')
                ->label(__('filament.published_at'))
                ->dateTime('d/m/Y')
                ->sortable(),
        ])->filters([
            Tables\Filters\SelectFilter::make('status')
                ->label(__('filament.status'))
                ->options(ContentStatus::class),
            Tables\Filters\TrashedFilter::make(),
        ])->actions([
            ActionGroup::make([
                EditAction::make(),
                DeleteAction::make(),
                RestoreAction::make(),
                ForceDeleteAction::make(),
            ]),
        ])->bulkActions([
            DeleteBulkAction::make(),
            RestoreBulkAction::make(),
            ForceDeleteBulkAction::make(),
        ]);
    }

    /**
     * Registra la pestaña «Sugerencias del contenido».
     *
     * @return array<class-string>
     */
    public static function getRelations(): array
    {
        return [
            CorrectionRequestsRelationManager::class,
        ];
    }

    /**
     * Define las páginas del recurso Noticia.
     *
     * @return array<string, PageRegistration>
     */
    public static function getPages(): array
    {
        return [
            'index' => NewsResource\Pages\ListNews::route('/'),
            'create' => NewsResource\Pages\CreateNews::route('/create'),
            'edit' => NewsResource\Pages\EditNews::route('/{record}/edit'),
        ];
    }
}
