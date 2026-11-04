<?php

namespace App\Models;

use App\Enums\ContentGroup;
use App\Enums\ContentStatus;
use App\Enums\EvidenceLevel;
use App\Enums\HistoricalEra;
use App\Traits\BelongsToUser;
use App\Traits\CleansUpStorageFiles;
use App\Traits\HasSeo;
use App\Traits\HasSlug;
use App\Traits\HasStatus;
use App\Traits\SanitizesHtmlContent;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Translatable\HasTranslations;

/**
 * Modelo de página enciclopédica o estática editable desde el backend.
 * Soporta traducciones (es/en), estados, SEO, categorías, agrupación temática,
 * época histórica, nivel de evidencia documental, coordenadas GPS, vídeo de YouTube,
 * páginas enlazadas y participación ciudadana (correcciones y comentarios).
 *
 * @property int $id Identificador único de la página
 * @property string $slug Slug único para URLs
 * @property array<string, string> $title Título traducible (es, en)
 * @property array<string, mixed>|null $content Contenido traducible en Markdown/HTML o bloques (es, en)
 * @property array<string, string>|null $excerpt Extracto traducible (es, en)
 * @property array<string, string>|null $seo_title Título SEO traducible (es, en)
 * @property array<string, string>|null $seo_description Descripción SEO traducible (es, en)
 * @property array<string, string>|null $seo_keywords Palabras clave SEO traducibles (es, en)
 * @property ContentGroup|null $content_group Grupo temático principal
 * @property HistoricalEra|null $historical_era Época histórica
 * @property string|null $period_label Etiqueta legible del periodo o fecha
 * @property int|null $year_start Año inicial del hito o periodo (negativo para a. C.)
 * @property int|null $year_end Año final del hito o periodo
 * @property EvidenceLevel|null $evidence_level Nivel de evidencia documental
 * @property array<string, string>|null $evidence_note Nota aclaratoria sobre fuentes o leyendas (es, en)
 * @property array<int, string>|null $source_references Lista de identificadores de fuentes F-XXXX
 * @property string|null $image Ruta de la imagen principal (16:9)
 * @property string|null $social_image Ruta de la imagen para redes sociales (1200x630)
 * @property string|null $youtube_url URL opcional de vídeo de YouTube
 * @property float|null $latitude Latitud GPS en grados decimales
 * @property float|null $longitude Longitud GPS en grados decimales
 * @property string|null $location_name Nombre descriptivo de la ubicación en Chipiona
 * @property ContentStatus $status Estado (draft, published)
 * @property bool $is_active Si la página está activa
 * @property bool $featured Si la página está destacada
 * @property int|null $user_id ID del usuario autor
 * @property Carbon|null $published_at Fecha de publicación
 * @property Carbon $created_at Fecha de creación
 * @property Carbon $updated_at Fecha de modificación
 * @property Carbon|null $deleted_at Fecha de eliminación lógica
 * @property-read string|null $image_url URL pública dinámica de la imagen principal
 * @property-read string|null $social_image_url URL pública dinámica de la imagen social
 * @property-read string|null $youtube_embed_url URL embebida segura de YouTube
 * @property-read string $rendered_content Contenido renderizado en HTML a partir de Markdown o HTML
 * @property-read User|null $user Usuario autor
 * @property-read Collection<int, Category> $categories Categorías asignadas
 * @property-read Collection<int, Page> $relatedPages Páginas enlazadas desde esta ficha
 * @property-read Collection<int, Page> $linkedFromPages Páginas que enlazan hacia esta ficha
 * @property-read Collection<int, News> $news Noticias relacionadas con esta página
 * @property-read Collection<int, CorrectionRequest> $correctionRequests Solicitudes de corrección recibidas
 * @property-read Collection<int, Comment> $comments Comentarios asociados a la página
 *
 * @method static \Illuminate\Database\Eloquent\Builder<Page> published() Filtra páginas publicadas y activas
 * @method static \Illuminate\Database\Eloquent\Builder<Page> draft() Filtra páginas en borrador
 */
class Page extends Model
{
    use BelongsToUser, CleansUpStorageFiles, HasFactory, HasSeo, HasSlug, HasStatus, HasTranslations, SanitizesHtmlContent, SoftDeletes;

    /** @var array<string> Campos traducibles usando spatie/laravel-translatable */
    public array $translatable = [
        'title',
        'content',
        'excerpt',
        'seo_title',
        'seo_description',
        'seo_keywords',
        'evidence_note',
    ];

    /** @var array<int, string> Archivos en disco public a limpiar al reemplazar o borrar */
    protected array $storageAttributes = ['image', 'social_image'];

    /** @var array<string> Campos asignables en masa */
    protected $fillable = [
        'slug',
        'title',
        'content',
        'excerpt',
        'content_group',
        'historical_era',
        'period_label',
        'year_start',
        'year_end',
        'evidence_level',
        'evidence_note',
        'source_references',
        'image',
        'social_image',
        'youtube_url',
        'latitude',
        'longitude',
        'location_name',
        'status',
        'is_active',
        'featured',
        'user_id',
        'seo_title',
        'seo_description',
        'seo_keywords',
        'published_at',
    ];

    /**
     * Definición de casts de atributos.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'featured' => 'boolean',
            'published_at' => 'datetime',
            'status' => ContentStatus::class,
            'content_group' => ContentGroup::class,
            'historical_era' => HistoricalEra::class,
            'evidence_level' => EvidenceLevel::class,
            'source_references' => 'array',
            'year_start' => 'integer',
            'year_end' => 'integer',
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    // =========================================================
    // ACCESSORS & HELPERS
    // =========================================================

    /**
     * Devuelve la URL dinámica y pública de la imagen principal de la página.
     */
    public function getImageUrlAttribute(): ?string
    {
        if (empty($this->image)) {
            return null;
        }

        if (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://')) {
            return $this->image;
        }

        if (str_starts_with($this->image, 'images/')) {
            return asset($this->image);
        }

        return Storage::disk('public')->url($this->image);
    }

    /**
     * Devuelve la URL dinámica y pública de la imagen para redes sociales (1200x630).
     */
    public function getSocialImageUrlAttribute(): ?string
    {
        if (empty($this->social_image)) {
            return $this->image_url;
        }

        if (str_starts_with($this->social_image, 'http://') || str_starts_with($this->social_image, 'https://')) {
            return $this->social_image;
        }

        if (str_starts_with($this->social_image, 'images/')) {
            return asset($this->social_image);
        }

        return Storage::disk('public')->url($this->social_image);
    }

    /**
     * Convierte la URL de YouTube en una URL de embed segura (youtube-nocookie.com).
     */
    public function getYoutubeEmbedUrlAttribute(): ?string
    {
        if (empty($this->youtube_url)) {
            return null;
        }

        if (preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/|shorts\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $this->youtube_url, $matches)) {
            return 'https://www.youtube-nocookie.com/embed/'.$matches[1];
        }

        return null;
    }

    /**
     * Determina si la página dispone de coordenadas GPS válidas.
     */
    public function hasCoordinates(): bool
    {
        return $this->latitude !== null && $this->longitude !== null;
    }

    /**
     * Renderiza el contenido del idioma activo a HTML seguro (soporta Markdown y HTML)
     * y envuelve tablas de datos en un contenedor accesible con scroll horizontal.
     */
    public function getRenderedContentAttribute(): string
    {
        $raw = $this->getTranslation('content', app()->getLocale());

        if (is_array($raw)) {
            return '';
        }

        $text = (string) $raw;
        if ($text === '') {
            return '';
        }

        // Eliminar H1 inicial en Markdown (# Título) e imágenes/enlaces relativos al corpus privado (04-recursos / .jpg)
        $text = (string) preg_replace('/^\s*#\s+[^\r\n]+\R*/u', '', $text);
        $text = (string) preg_replace('/!\[[^\]]*\]\((?:[^)]*04-recursos[^)]*|[^)]*\.(?:jpe?g|png|gif))(?:\s+"[^"]*")?\)\R*/iu', '', $text);
        $text = (string) preg_replace('/\[([^\]]+)\]\([^)]*04-recursos[^)]*\)/iu', '$1', $text);

        // Si ya contiene bloques HTML principales, devolver saneado directamente
        if (preg_match('/<(p|div|section|article|h2|h3|ul|ol|table)\b/i', $text)) {
            $html = static::sanitizeHtmlString($text);
        } else {
            $html = static::sanitizeHtmlString(Str::markdown($text, [
                'html_input' => 'strip',
                'allow_unsafe_links' => false,
            ]));
        }

        // Garantizar que ningún bloque interno del cuerpo genere un segundo <h1> en la vista
        $html = (string) preg_replace('/<h1(\b[^>]*)>(.*?)<\/h1>/is', '<h2$1>$2</h2>', $html);

        return static::wrapTablesInResponsiveContainer($html);
    }

    /**
     * Envuelve tablas de datos en un contenedor accesible con scroll horizontal.
     */
    public static function wrapTablesInResponsiveContainer(string $html): string
    {
        if (! str_contains($html, '<table')) {
            return $html;
        }

        return (string) preg_replace_callback(
            '#(<div\s+class="[^"]*table-responsive-wrapper[^"]*"[^>]*>\s*)?<table\b([^>]*)>(.*?)<\/table>(\s*<\/div>)?#si',
            function (array $matches): string {
                if (! empty($matches[1]) && ! empty($matches[4])) {
                    return $matches[0];
                }

                $attrs = $matches[2];
                $inner = $matches[3];
                $label = e(__('messages.table_data_label'));

                return '<div class="table-responsive-wrapper" tabindex="0" role="region" aria-label="'.$label.'"><table'.$attrs.'>'.$inner.'</table></div>';
            },
            $html
        );
    }

    // =========================================================
    // RELACIONES
    // =========================================================

    /**
     * Relación muchos-a-muchos con categorías (tabla pivot category_page).
     *
     * @return BelongsToMany<Category, $this>
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    /**
     * Páginas enlazadas directamente desde esta ficha (tabla pivot page_relations).
     *
     * @return BelongsToMany<Page, $this>
     */
    public function relatedPages(): BelongsToMany
    {
        return $this->belongsToMany(
            self::class,
            'page_relations',
            'page_id',
            'related_page_id'
        );
    }

    /**
     * Páginas que enlazan hacia esta ficha como referencia inversa.
     *
     * @return BelongsToMany<Page, $this>
     */
    public function linkedFromPages(): BelongsToMany
    {
        return $this->belongsToMany(
            self::class,
            'page_relations',
            'related_page_id',
            'page_id'
        );
    }

    /**
     * Noticias relacionadas con esta página (tabla pivot news_page).
     *
     * @return BelongsToMany<News, $this>
     */
    public function news(): BelongsToMany
    {
        return $this->belongsToMany(News::class, 'news_page');
    }

    /**
     * Solicitudes de corrección propuestas por visitantes sobre esta página.
     *
     * @return MorphMany<CorrectionRequest, $this>
     */
    public function correctionRequests(): MorphMany
    {
        return $this->morphMany(CorrectionRequest::class, 'correctable')->latest();
    }

    /**
     * Comentarios públicos enviados a esta página.
     *
     * @return MorphMany<Comment, $this>
     */
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable')->latest();
    }
}
