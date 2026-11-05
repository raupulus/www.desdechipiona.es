<?php

namespace App\Services;

use App\Models\News;
use App\Models\Page;
use App\Models\Post;
use Illuminate\Support\Str;

/**
 * Servicio para la gestión dinámica de meta tags SEO y Schema.org JSON-LD.
 * Acumula los valores SEO que se inyectan en el layout head.blade.php.
 *
 * Uso: $seo->setTitle('Mi título')->setDescription('Mi descripción')
 */
class SeoService
{
    /** @var string Título de la página */
    private string $title = '';

    /** @var string Titular limpio sin sufijo de marca para Schema.org headline */
    private string $headline = '';

    /** @var string Descripción de la página */
    private string $description = '';

    /** @var string Palabras clave para meta keywords */
    private string $keywords = '';

    /** @var string URL base de la página (sin parámetros de idioma) */
    private string $url = '';

    /** @var string Tipo de contenido para Open Graph (website, article, etc.) */
    private string $type = 'website';

    /** @var string URL de la imagen para Open Graph y Twitter Cards */
    private string $image = '';

    /** @var string Directiva para meta robots (index,follow / noindex,follow...) */
    private string $robots = 'index,follow';

    /** @var string|null Nombre del autor del contenido */
    private ?string $author = null;

    /** @var string|null Fecha de publicación en formato ISO 8601 */
    private ?string $publishedAt = null;

    /** @var string|null Fecha de última modificación en formato ISO 8601 */
    private ?string $modifiedAt = null;

    /** @var array<int, array{name: string, url: string}> Migas de pan para BreadcrumbList JSON-LD */
    private array $breadcrumbs = [];

    /** @var array{latitude: float, longitude: float, name?: string|null}|null Coordenadas geográficas para Schema.org */
    private ?array $geo = null;

    /** @var string Subtipo de artículo para Schema.org (Article, NewsArticle, BlogPosting) */
    private string $schemaType = 'BlogPosting';

    /**
     * Establece el título de la página.
     */
    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Establece la descripción meta de la página.
     */
    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Establece las palabras clave meta de la página.
     */
    public function setKeywords(string $keywords): static
    {
        $this->keywords = $keywords;

        return $this;
    }

    /**
     * Establece la URL base de la página.
     */
    public function setUrl(string $url): static
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Establece el tipo de contenido Open Graph (website, article, product...).
     */
    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Establece la imagen para Open Graph y Twitter Cards.
     */
    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Establece la directiva meta robots para la página.
     */
    public function setRobots(string $robots): static
    {
        $this->robots = $robots;

        return $this;
    }

    /**
     * Establece el nombre del autor del contenido.
     */
    public function setAuthor(?string $author): static
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Establece la fecha de publicación en formato ISO 8601.
     */
    public function setPublishedAt(?string $publishedAt): static
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    /**
     * Establece la fecha de última modificación en formato ISO 8601.
     */
    public function setModifiedAt(?string $modifiedAt): static
    {
        $this->modifiedAt = $modifiedAt;

        return $this;
    }

    /**
     * Define las migas de pan para generar BreadcrumbList en JSON-LD.
     *
     * @param  array<int, array{name: string, url: string}>  $breadcrumbs
     */
    public function setBreadcrumbs(array $breadcrumbs): static
    {
        $this->breadcrumbs = $breadcrumbs;

        return $this;
    }

    /**
     * Establece las coordenadas geográficas para Schema.org.
     */
    public function setGeo(float $latitude, float $longitude, ?string $name = null): static
    {
        $this->geo = [
            'latitude' => $latitude,
            'longitude' => $longitude,
            'name' => $name,
        ];

        return $this;
    }

    /**
     * Carga automáticamente los datos SEO desde un modelo que use el trait HasSeo.
     */
    public function fromModel(object $model): static
    {
        if (isset($model->title)) {
            $rawTitle = $model->title;
            if (is_array($rawTitle)) {
                $rawTitle = $rawTitle[app()->getLocale()] ?? $rawTitle[config('app.fallback_locale')] ?? '';
            }
            $this->headline = Str::limit(trim(strip_tags((string) $rawTitle)), 110, '');
        }
        if (method_exists($model, 'getSeoTitleText')) {
            $this->title = $model->getSeoTitleText() ?: $this->title;
        }
        if (method_exists($model, 'getSeoDescriptionText')) {
            $this->description = $model->getSeoDescriptionText() ?: $this->description;
        }
        if (method_exists($model, 'getSeoKeywordsText')) {
            $this->keywords = $model->getSeoKeywordsText() ?: $this->keywords;
        }
        if (! empty($model->social_image_url)) {
            $this->image = $model->social_image_url;
        } elseif (! empty($model->image_url)) {
            $this->image = $model->image_url;
        } elseif (! empty($model->image)) {
            $this->image = asset('storage/'.$model->image);
        } elseif (! empty($model->featured_image)) {
            $this->image = $model->featured_image;
        }

        if (isset($model->user) && ! empty($model->user?->name)) {
            $this->author = $model->user->name;
        }

        if (! empty($model->published_at) && method_exists($model->published_at, 'toIso8601String')) {
            $this->publishedAt = $model->published_at->toIso8601String();
        }

        if (! empty($model->updated_at) && method_exists($model->updated_at, 'toIso8601String')) {
            $this->modifiedAt = $model->updated_at->toIso8601String();
        }

        if (isset($model->latitude, $model->longitude) && $model->latitude !== null && $model->longitude !== null) {
            $this->setGeo(
                (float) $model->latitude,
                (float) $model->longitude,
                isset($model->location_name) ? (string) $model->location_name : null
            );
        }

        if ($model instanceof News) {
            $this->schemaType = 'NewsArticle';
        } elseif ($model instanceof Page) {
            $this->schemaType = 'Article';
        } elseif ($model instanceof Post) {
            $this->schemaType = 'BlogPosting';
        }

        return $this;
    }

    /**
     * Convierte los datos SEO a un array para pasar a la vista.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $siteTitle = (string) setting('site_title', config('app.name'));
        $baseUrl = $this->url ?: url()->current();

        $params = [];
        $langParam = request()->query('lang');
        $defaultLocale = (string) config('locales.default', 'es');
        if (
            $langParam
            && $langParam !== $defaultLocale
            && in_array($langParam, config('locales.available', ['es', 'en']), true)
        ) {
            $params['lang'] = $langParam;
        }

        $pageParam = request()->query('page');
        if ($pageParam !== null && is_numeric($pageParam) && (int) $pageParam > 1) {
            $params['page'] = (int) $pageParam;
        }

        $canonicalUrl = ! empty($params)
            ? $baseUrl.'?'.http_build_query($params)
            : $baseUrl;

        $robots = $this->robots;
        $facetParams = ['q', 'group', 'era', 'evidence', 'category', 'year_from', 'year_to', 'map', 'sort'];
        foreach ($facetParams as $facet) {
            $val = request()->query($facet);
            if ($val !== null && $val !== '' && $val !== 'all') {
                $robots = 'noindex,follow';
                break;
            }
        }

        $rawImage = $this->image ?: asset('images/redes-sociales/og-image.webp');
        $imageUrl = str_starts_with($rawImage, 'http') ? $rawImage : url($rawImage);

        $defaultDescription = app()->getLocale() === 'en'
            ? __('messages.about_seo_description')
            : (string) setting('site_description', __('messages.about_seo_description'));

        return [
            'title' => $this->title ?: $siteTitle,
            'headline' => $this->headline ?: ($this->title ?: $siteTitle),
            'description' => $this->description ?: $defaultDescription,
            'siteDescription' => $defaultDescription,
            'keywords' => $this->keywords ?: (string) setting('default_meta_keywords', ''),
            'baseUrl' => $baseUrl,
            'url' => $canonicalUrl,
            'type' => $this->type,
            'schemaType' => $this->schemaType,
            'image' => $imageUrl,
            'robots' => $robots,
            'author' => $this->author ?: 'Raúl Caro Pastorino',
            'publishedAt' => $this->publishedAt,
            'modifiedAt' => $this->modifiedAt,
            'breadcrumbs' => $this->breadcrumbs,
            'geo' => $this->geo,
        ];
    }

    /**
     * Construye el grafo Schema.org JSON-LD completo para la página actual.
     *
     * @return array<string, mixed>
     */
    public function toJsonLd(): array
    {
        $data = $this->toArray();
        $siteTitle = (string) setting('site_title', config('app.name'));
        $siteUrl = url('/');
        $logoUrl = asset('images/logos/logo-180x180.webp');

        $sameAs = array_values(array_filter([
            setting('facebook_url'),
            setting('github_url'),
            setting('gitlab_url'),
            setting('telegram_url'),
            setting('youtube_url'),
        ]));

        $organization = array_filter([
            '@type' => 'Organization',
            '@id' => $siteUrl.'/#organization',
            'name' => $siteTitle,
            'url' => $siteUrl,
            'foundingDate' => '2008',
            'founder' => [
                '@type' => 'Person',
                'name' => 'Raúl Caro Pastorino',
                'url' => 'https://raupulus.dev',
            ],
            'logo' => [
                '@type' => 'ImageObject',
                'url' => $logoUrl,
            ],
            'email' => setting('contact_email') ?: null,
            'telephone' => setting('contact_phone') ?: null,
            'sameAs' => ! empty($sameAs) ? $sameAs : null,
        ]);

        $webSite = array_filter([
            '@type' => 'WebSite',
            '@id' => $siteUrl.'/#website',
            'name' => $siteTitle,
            'description' => $data['siteDescription'] ?? $data['description'],
            'url' => $siteUrl,
            'inLanguage' => app()->getLocale(),
            'publisher' => [
                '@id' => $siteUrl.'/#organization',
            ],
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => [
                    '@type' => 'EntryPoint',
                    'urlTemplate' => $siteUrl.'/?q={search_term_string}',
                ],
                'query-input' => 'required name=search_term_string',
            ],
        ]);

        $webPageNode = array_filter([
            '@type' => 'WebPage',
            '@id' => $data['url'].'#webpage',
            'name' => $data['title'],
            'description' => $data['description'],
            'url' => $data['url'],
            'inLanguage' => app()->getLocale(),
            'isPartOf' => [
                '@id' => $siteUrl.'/#website',
            ],
            'datePublished' => $data['publishedAt'],
            'dateModified' => $data['modifiedAt'],
        ]);

        $graph = [$organization, $webSite, $webPageNode];

        if ($data['type'] === 'article') {
            $articleNode = [
                '@type' => $this->schemaType,
                '@id' => $data['url'].'#article',
                'mainEntityOfPage' => [
                    '@type' => 'WebPage',
                    '@id' => $data['url'].'#webpage',
                ],
                'headline' => $data['headline'] ?? $data['title'],
                'name' => $data['headline'] ?? $data['title'],
                'description' => $data['description'],
                'image' => [$data['image']],
                'url' => $data['url'],
                'inLanguage' => app()->getLocale(),
                'isAccessibleForFree' => true,
                'license' => 'https://creativecommons.org/licenses/by-nc-sa/4.0/',
                'datePublished' => $data['publishedAt'],
                'dateModified' => $data['modifiedAt'] ?? $data['publishedAt'],
                'author' => [
                    '@type' => 'Person',
                    'name' => $data['author'],
                    'url' => 'https://raupulus.dev',
                ],
                'publisher' => [
                    '@id' => $siteUrl.'/#organization',
                ],
            ];

            if ($this->geo !== null) {
                $articleNode['contentLocation'] = [
                    '@type' => 'Place',
                    'name' => $this->geo['name'] ?? ($data['headline'] ?? $data['title']),
                    'geo' => [
                        '@type' => 'GeoCoordinates',
                        'latitude' => $this->geo['latitude'],
                        'longitude' => $this->geo['longitude'],
                    ],
                ];
            }

            $graph[] = array_filter($articleNode, fn ($v) => $v !== null && $v !== '');
        }

        if ($this->geo !== null) {
            $graph[] = array_filter([
                '@type' => 'Place',
                'name' => $this->geo['name'] ?? $data['title'],
                'geo' => [
                    '@type' => 'GeoCoordinates',
                    'latitude' => $this->geo['latitude'],
                    'longitude' => $this->geo['longitude'],
                ],
            ]);
        }

        if (! empty($this->breadcrumbs)) {
            $items = [];
            foreach ($this->breadcrumbs as $index => $crumb) {
                $items[] = [
                    '@type' => 'ListItem',
                    'position' => $index + 1,
                    'name' => $crumb['name'],
                    'item' => $crumb['url'],
                ];
            }

            $graph[] = [
                '@type' => 'BreadcrumbList',
                '@id' => $data['url'].'#breadcrumb',
                'itemListElement' => $items,
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@graph' => $graph,
        ];
    }
}
