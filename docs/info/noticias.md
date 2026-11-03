# Módulo de Noticias y Hallazgos Documentales (`news`)

*Última actualización: 2026-11-03*

## 1. Resumen del Módulo
Gestiona en un CRUD independiente al de páginas enciclopédicas las noticias, novedades editoriales y nuevos legajos documentales de **Desde Chipiona**, con soporte de Markdown, nivel de evidencia, coordenadas GPS, vídeo de YouTube, páginas enlazadas y sugerencias de corrección.

## 2. Archivos Principales Involucrados

| Capa / Tipo | Ruta del Archivo | Responsabilidad Principal |
|-------------|------------------|---------------------------|
| **Modelo** | `app/Models/News.php` | Modelo Eloquent con traducciones, relaciones con categorías, páginas, correcciones y comentarios |
| **Migración** | `database/migrations/2026_11_03_000020_create_news_table.php` | Tablas `news`, `category_news` y `news_page` |
| **Factory** | `database/factories/NewsFactory.php` | Factoría con estados `published`, `draft`, `inactive` |
| **Filament Resource** | `app/Filament/Admin/Resources/NewsResource.php` | CRUD independiente con `MarkdownEditor` y `CorrectionRequestsRelationManager` |
| **Policy** | `app/Policies/NewsPolicy.php` | Autorización para administradores |
| **Controlador Web** | `app/Http/Controllers/Web/NewsController.php` | Listado (`/news`) y detalle (`/news/{slug}`) |
| **Vistas Blade** | `resources/views/news/index.blade.php`, `resources/views/news/show.blade.php` | Vistas públicas con mapa, páginas enlazadas y comentarios |

## 3. Esquema de Base de Datos
**Tabla**: `news` (con tablas pivote `category_news` y `news_page`).

| Campo | Tipo | Nullable | Default | Descripción |
|-------|------|:--------:|:-------:|-------------|
| `id` | `bigint unsigned` | No | Auto | Clave primaria |
| `slug` | `string(255)` | No | — | Slug único para URLs |
| `title`, `content`, `excerpt` | `jsonb` | No/Sí | — | Campos traducibles (`es`, `en`) en Markdown/HTML |
| `evidence_level` | `string(30)` | No | `'documentado'` | Nivel de rigor documental (`EvidenceLevel`) |
| `source_references` | `jsonb` | Sí | `NULL` | Códigos de fuente `F-XXXX` |
| `image`, `social_image` | `string(512)` | Sí | `NULL` | Imagen 16:9 e imagen social 1200x630 |
| `youtube_url` | `string(512)` | Sí | `NULL` | Vídeo opcional de YouTube |
| `latitude`, `longitude`, `location_name` | `decimal` / `string` | Sí | `NULL` | Coordenadas GPS del lugar en Chipiona |

## 4. Relaciones, Scopes y Métodos Relevantes
- `categories(): BelongsToMany<Category>`
- `relatedPages(): BelongsToMany<Page>` (`news_page`)
- `correctionRequests(): MorphMany<CorrectionRequest>`
- `comments(): MorphMany<Comment>`

## 5. Rutas Web
- `GET /news` (`news.index`)
- `GET /news/{slug}` (`news.show`)

## 6. Configuración en Filament (`/admin`)
- **Resource**: `App\Filament\Admin\Resources\NewsResource`
- **RelationManager**: `CorrectionRequestsRelationManager` (pestaña «Sugerencias del contenido»).

## 7. Contenido Gestionable
- Título, resumen y cuerpo en Markdown, imágenes 16:9 y 1200x630, coordenadas GPS, vídeo de YouTube, categorías y páginas enlazadas.
