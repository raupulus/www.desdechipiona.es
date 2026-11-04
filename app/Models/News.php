<?php

namespace App\Models;

use App\Enums\ContentStatus;
use App\Enums\EvidenceLevel;
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
 * Modelo de noticia o novedad editorial de Desde Chipiona.
 * Gestionado en un CRUD independiente al de páginas enciclopédicas, con soporte
 * de Markdown, coordenadas GPS, vídeo de YouTube, páginas enlazadas, comentarios
 * y solicitudes de corrección.
 *
 * @property int $id Identificador único de la noticia
 * @property string $slug Slug único para URLs
 * @property array<string, string> $title Título traducible (es, en)
 * @property array<string, string>|null $excerpt Extracto traducible (es, en)
 * @property array<string, string>|null $content Contenido traducible en Markdown/HTML (es, en)
 * @property string|null $image Ruta de la imagen principal (16:9)
 * @property string|null $social_image Ruta de la imagen social (1200x630)
 * @property string|null $youtube_url URL opcional de vídeo de YouTube
 * @property float|null $latitude Latitud GPS en grados decimales
 * @property float|null $longitude Longitud GPS en grados decimales
 * @property string|null $location_name Nombre descriptivo de la ubicación
 * @property EvidenceLevel $evidence_level Nivel de evidencia documental
 * @property ContentStatus $status Estado de publicación (draft, published)
 * @property bool $is_active Si la noticia está activa
 * @property bool $featured Si la noticia está destacada
 * @property int|null $user_id ID del autor
 * @property array<string, string>|null $seo_title Título SEO traducible (es, en)
 * @property array<string, string>|null $seo_description Descripción SEO traducible (es, en)
 * @property array<string, string>|null $seo_keywords Palabras clave SEO traducibles (es, en)
 * @property Carbon|null $published_at Fecha de publicación
 * @property Carbon $created_at Fecha de creación
 * @property Carbon $updated_at Fecha de modificación
 * @property Carbon|null $deleted_at Fecha de eliminación lógica
 * @property-read string|null $image_url URL pública dinámica de la imagen principal
 * @property-read string|null $social_image_url URL pública dinámica de la imagen social
 * @property-read string|null $youtube_embed_url URL embebida segura de YouTube
 * @property-read string $rendered_content Contenido renderizado en HTML a partir de Markdown o HTML
 * @property-read User|null $user Autor de la noticia
 * @property-read Collection<int, Category> $categories Categorías asignadas
 * @property-read Collection<int, Page> $relatedPages Páginas enciclopédicas enlazadas
 * @property-read Collection<int, CorrectionRequest> $correctionRequests Solicitudes de corrección recibidas
 * @property-read Collection<int, Comment> $comments Comentarios asociados a la noticia
 *
 * @method static \Illuminate\Database\Eloquent\Builder<News> published() Filtra noticias publicadas y activas
 * @method static \Illuminate\Database\Eloquent\Builder<News> draft() Filtra noticias en borrador
 */
class News extends Model
{
    use BelongsToUser, CleansUpStorageFiles, HasFactory, HasSeo, HasSlug, HasStatus, HasTranslations, SanitizesHtmlContent, SoftDeletes;

    /** @var string Nombre de la tabla en la base de datos */
    protected $table = 'news';

    /** @var array<string> Campos traducibles usando spatie/laravel-translatable */
    public array $translatable = [
        'title',
        'excerpt',
        'content',
        'seo_title',
        'seo_description',
        'seo_keywords',
    ];

    /** @var array<int, string> Archivos en disco public a limpiar al reemplazar o borrar */
    protected array $storageAttributes = ['image', 'social_image'];

    /** @var array<string> Campos asignables en masa */
    protected $fillable = [
        'slug',
        'title',
        'excerpt',
        'content',
        'image',
        'social_image',
        'youtube_url',
        'latitude',
        'longitude',
        'location_name',
        'evidence_level',
        'source_references',
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
            'evidence_level' => EvidenceLevel::class,
            'source_references' => 'array',
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    // =========================================================
    // ACCESSORS & HELPERS
    // =========================================================

    /**
     * Devuelve la URL dinámica y pública de la imagen principal de la noticia.
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
     * Determina si la noticia dispone de coordenadas GPS válidas.
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

        if (preg_match('/<(p|div|section|article|h2|h3|ul|ol|table)\b/i', $text)) {
            $html = static::sanitizeHtmlString($text);
        } else {
            $html = static::sanitizeHtmlString(Str::markdown($text, [
                'html_input' => 'strip',
                'allow_unsafe_links' => false,
            ]));
        }

        return Page::wrapTablesInResponsiveContainer($html);
    }

    // =========================================================
    // RELACIONES
    // =========================================================

    /**
     * Relación muchos-a-muchos con categorías (tabla pivot category_news).
     *
     * @return BelongsToMany<Category, $this>
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_news');
    }

    /**
     * Páginas enciclopédicas relacionadas con esta noticia (tabla pivot news_page).
     *
     * @return BelongsToMany<Page, $this>
     */
    public function relatedPages(): BelongsToMany
    {
        return $this->belongsToMany(Page::class, 'news_page');
    }

    /**
     * Solicitudes de corrección propuestas por visitantes sobre esta noticia.
     *
     * @return MorphMany<CorrectionRequest, $this>
     */
    public function correctionRequests(): MorphMany
    {
        return $this->morphMany(CorrectionRequest::class, 'correctable')->latest();
    }

    /**
     * Comentarios públicos enviados a esta noticia.
     *
     * @return MorphMany<Comment, $this>
     */
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable')->latest();
    }
}
