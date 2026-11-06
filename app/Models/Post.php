<?php

namespace App\Models;

use App\Enums\ContentStatus;
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
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Translatable\HasTranslations;

/**
 * Modelo de post del blog.
 * Soporta traducciones (es/en), estados (draft/published), categorías,
 * SEO fields y asignación automática de user_id al crear.
 *
 * @property int $id Identificador único del post
 * @property string $slug Slug único para URLs
 * @property array<string, string> $title Título traducible (es, en)
 * @property array<string, string>|null $content Contenido traducible (es, en)
 * @property array<string, string>|null $excerpt Extracto traducible (es, en)
 * @property array<string, string>|null $seo_title Título SEO traducible (es, en)
 * @property array<string, string>|null $seo_description Descripción SEO traducible (es, en)
 * @property array<string, string>|null $seo_keywords Palabras clave SEO traducibles (es, en)
 * @property string|null $image Ruta de la imagen principal
 * @property ContentStatus $status Estado (draft, published)
 * @property bool $is_active Si el post está activo
 * @property bool $featured Si el post está destacado
 * @property int|null $user_id ID del autor
 * @property Carbon|null $published_at Fecha de publicación
 * @property Carbon $created_at Fecha de creación
 * @property Carbon $updated_at Fecha de modificación
 * @property Carbon|null $deleted_at Fecha de eliminación lógica
 * @property-read string|null $image_url URL pública dinámica de la imagen
 * @property-read string $rendered_content Contenido renderizado en HTML sanitizado
 * @property-read User|null $user Autor del post
 * @property-read Collection<int, Category> $categories Categorías asignadas
 *
 * @method static \Illuminate\Database\Eloquent\Builder<Post> published() Filtra posts con estado publicado
 * @method static \Illuminate\Database\Eloquent\Builder<Post> publishedAndActive() Filtra posts publicados, activos y con fecha de publicación válida
 * @method static \Illuminate\Database\Eloquent\Builder<Post> draft() Filtra posts en borrador
 */
class Post extends Model
{
    use BelongsToUser, CleansUpStorageFiles, HasFactory, HasSeo, HasSlug, HasStatus, HasTranslations, SanitizesHtmlContent, SoftDeletes;

    /** @var array<string> Campos traducibles usando spatie/laravel-translatable */
    public array $translatable = ['title', 'content', 'excerpt', 'seo_title', 'seo_description', 'seo_keywords'];

    /** @var array<string> Campos asignables en masa */
    protected $fillable = [
        'slug', 'title', 'content', 'excerpt', 'image',
        'status', 'is_active', 'featured', 'user_id',
        'seo_title', 'seo_description', 'seo_keywords', 'published_at',
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
        ];
    }

    // =========================================================
    // ACCESSORS
    // =========================================================

    /**
     * Devuelve la URL dinámica y pública de la imagen del post.
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
     * Renderiza el contenido del idioma activo a HTML seguro (soporta Markdown y HTML)
     * y envuelve tablas de datos en un contenedor accesible con scroll horizontal.
     */
    public function getRenderedContentAttribute(): string
    {
        $raw = $this->getTranslation('content', app()->getLocale());

        if (empty($raw) || (is_string($raw) && trim($raw) === '')) {
            $raw = $this->getTranslation('content', config('app.fallback_locale', 'es'));
            if (empty($raw) || (is_string($raw) && trim($raw) === '')) {
                $raw = $this->getTranslation('content', 'es');
            }
        }

        if (is_array($raw)) {
            return '';
        }

        $text = (string) $raw;
        if (trim($text) === '') {
            return '';
        }

        $text = (string) preg_replace('/^\s*#\s+[^\r\n]+\R*/u', '', $text);

        if (preg_match('/<(p|div|section|article|h2|h3|ul|ol|table)\b/i', $text)) {
            $html = static::sanitizeHtmlString($text);
        } else {
            $html = static::sanitizeHtmlString(Str::markdown($text, [
                'html_input' => 'strip',
                'allow_unsafe_links' => false,
            ]));
        }

        $html = (string) preg_replace('/<h1(\b[^>]*)>(.*?)<\/h1>/is', '<h2$1>$2</h2>', $html);

        return Page::wrapTablesInResponsiveContainer($html);
    }

    // =========================================================
    // RELACIONES
    // =========================================================

    /**
     * Relación muchos-a-muchos con categorías (tabla pivot category_post).
     *
     * @return BelongsToMany<Category>
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }
}
