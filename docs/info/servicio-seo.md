# Módulo: SeoService

*Última actualización: 2026-11-04*

> Servicio para la gestión dinámica de meta tags SEO (title, description, OG, Twitter Cards, JSON-LD) con soporte para carga automática desde modelos (`Page`, `News`, `Post`).

---

## Objetivo y funcionalidad

El `SeoService` gestiona dinámicamente todos los meta tags SEO de cada página del sitio web. Su objetivo es que cada URL pública tenga meta tags optimizados para buscadores y redes sociales, sin necesidad de hardcodear valores en las vistas Blade.

El servicio acumula los valores SEO (título, descripción, keywords, URL canónica, tipo Open Graph, imagen, directiva robots) y los expone al layout `head.blade.php`, que genera las etiquetas meta estándar, Open Graph, Twitter Cards, JSON-LD structured data, canonical y hreflang. Cada controlador web configura los meta tags de su página mediante una API fluida con encadenamiento de métodos (method chaining), y el servicio se encarga de los fallbacks a los valores por defecto del sistema (cargados desde los settings).

Para modelos con el trait `HasSeo` (posts, páginas), el método `fromModel()` carga automáticamente los campos SEO del modelo (con fallback al título y extracto si los campos SEO están vacíos), simplificando la configuración en los controladores a una sola línea. Esto garantiza que todo el contenido publicado tenga meta tags SEO completos, incluso si el administrador no los rellena explícitamente.

---

## Resumen

El `SeoService` acumula los valores SEO que se inyectan en el layout `head.blade.php`. Cada controlador configura los meta tags de su página mediante una API fluida (method chaining). Los valores por defecto se cargan desde los settings del sistema (`site_title`, `site_description`, `default_meta_keywords`).

---

## Archivo

`app/Services/SeoService.php`

---

## API del servicio

Todos los setters retornan `static` para encadenar:

| Método | Descripción |
|--------|-------------|
| `setTitle(string)` | Título para `<title>` y `og:title` |
| `setDescription(string)` | Meta description y `og:description` |
| `setKeywords(string)` | Meta keywords |
| `setUrl(string)` | URL canónica |
| `setType(string)` | Tipo Open Graph: `website`, `article`, `product`... |
| `setImage(string)` | URL imagen para OG y Twitter Cards |
| `setRobots(string)` | Directiva meta robots (ej: `noindex,follow`) |
| `setBreadcrumbs(array)` | Define la lista jerárquica de migas de pan para `BreadcrumbList` en JSON-LD |
| `setGeo(float, float, ?string)` | Establece coordenadas GPS (`Place` + `GeoCoordinates`) para fichas geolocalizadas |
| `fromModel(object)` | Carga SEO de un modelo con trait `HasSeo`: llama a `getSeoTitleText()` (con sufijo corporativo automático si usa fallback), `getSeoDescriptionText()` (normalizado en una sola línea), `getSeoKeywordsText()`, coordenadas GPS y resuelve la imagen WebP (`social_image_url` o `image_url`) |
| `toArray()` | Devuelve array con todos los valores SEO para la vista y activa `noindex,follow` automáticamente ante parámetros de búsqueda facetada (`q`, `group`, `era`, `evidence`, `year_from`, `year_to`, `map`, `sort`) |
| `toJsonLd()` | Construye el grafo Schema.org JSON-LD (`@graph`) con `Organization` (`foundingDate: "2008"`, `founder`), `WebSite` (`SearchAction`), `Article`/`NewsArticle`/`BlogPosting`/`WebPage`, `Place` (si tiene coordenadas) y `BreadcrumbList` |

---

## Valores por defecto (fallbacks)

Cuando un campo SEO no se establece explícitamente, se usan estos fallbacks:

| Campo | Fallback |
|-------|----------|
| title | `setting('site_title')` → `config('app.name')` |
| description | `setting('site_description')` (ES) / `__('messages.about_seo_description')` (EN) |
| keywords | `setting('default_meta_keywords')` → `''` |
| url | `url()->current()` |
| type | `website` |
| image | `asset('images/redes-sociales/og-image.webp')` |
| robots | `index,follow` (`noindex,follow` si hay filtros facetados activos) |

---

## Uso en controladores

```php
public function show(string $slug, SeoService $seo): View
{
    $post = Post::where('slug', $slug)->firstOrFail();
    $seo->fromModel($post)->setType('article');
    return view('posts.show', compact('post', 'seo'));
}
```

---

## Integración con el layout

La vista `resources/views/layouts/head.blade.php` recibe `$seo->toArray()` y genera:
- Meta tags básicos (charset, viewport, csrf, title, description, keywords, robots)
- Canonical URL y hreflang diferenciados por idioma (`?lang=es`, `?lang=en` y `x-default` hacia la URL canónica limpia)
- Open Graph completo (locale, title, site_name, type, description, image, url)
- Twitter Cards (`summary_large_image`)
- JSON-LD structured data (`WebSite` / `Article`)
- Favicons y Web Manifest
