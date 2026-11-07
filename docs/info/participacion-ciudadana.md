# Módulo de Participación Ciudadana y Refranero (`correction_requests`, `content_suggestions`, `proverbs`, `comments`)

*Última actualización: 2026-11-07*

## 1. Resumen del Módulo
Gestiona la participación de los vecinos y lectores de **Desde Chipiona** sin cuentas ni contraseñas locales: propuestas de corrección sobre páginas y noticias, sugerencias extensas de nuevos contenidos, refranero popular y comentarios públicos, exigiendo en todos los formularios **identificación previa obligatoria en un clic mediante Google OAuth** (`session('public_social_identity')`), moderación previa y protección Cloudflare Turnstile contra el spam.

## 2. Archivos Principales Involucrados

| Capa / Tipo | Ruta del Archivo | Responsabilidad Principal |
|-------------|------------------|---------------------------|
| **Modelos** | `app/Models/CorrectionRequest.php`, `ContentSuggestion.php`, `Proverb.php`, `Comment.php` | Modelos de participación y refranero |
| **Migración** | `database/migrations/2026_11_03_000030_create_chipiona_participation_and_utility_tables.php` | Esquema de las tablas de participación |
| **Filament Resources** | `CorrectionRequestResource`, `ContentSuggestionResource`, `ProverbResource`, `CommentResource` | Moderación y aprobación rápida en `/admin` |
| **RelationManager** | `app/Filament/Admin/Resources/RelationManagers/CorrectionRequestsRelationManager.php` | Pestaña «Sugerencias del contenido» en `PageResource` y `NewsResource` |
| **Controladores Web** | `SocialIdentityController`, `CorrectionRequestController`, `ContentSuggestionController`, `ProverbController`, `CommentController` | Recepción pública con `throttle:10,1` y `Turnstile` |
| **Regla y Componentes** | `app/Rules/Turnstile.php`, `resources/views/components/turnstile-widget.blade.php`, `social-identity-banner.blade.php` | Seguridad Cloudflare Turnstile y sesión OAuth ligera |

## 3. Esquema de Base de Datos
- **`correction_requests`**: `correctable_type`, `correctable_id`, `quoted_text`, `proposed_correction`, `sources_info`, `author_name`, `oauth_provider`, `status` (`pending`, `approved`, `rejected`).
- **`content_suggestions`**: `content_group`, `related_page_id`, `title`, `body`, `sources_info`, `location_name`, `latitude`, `longitude`, `status`.
- **`proverbs`**: `text`, `meaning`, `category`, `source_code`, `related_page_id`, `author_name`, `status`, `featured`.
- **`comments`**: `commentable_type`, `commentable_id`, `body`, `author_name`, `oauth_provider`, `is_approved`.

## 4. Relaciones y Scopes
- `CorrectionRequest::correctable()` y `Comment::commentable()`: Relaciones polimórficas hacia `Page` y `News`.
- `Proverb::scopeApproved()` y `Comment::scopeApproved()`: Filtran registros visibles en el frontend.

## 5. Rutas Web (`routes/web.php`)
- `GET /proverbs` (`proverbs.index`), `POST /proverbs` (`proverbs.store`)
- `GET /suggestions` (`suggestions.create`), `POST /suggestions` (`suggestions.store`)
- `POST /corrections` (`corrections.store`)
- `POST /comments` (`comments.store`)
- `GET /auth/social/{provider}/redirect` (`social.redirect`), `GET /auth/social/{provider}/callback` (`social.callback`), `GET /auth/social/disconnect` (`social.disconnect`)

## 6. Configuración en Filament (`/admin`)
- Grupo de navegación `__('filament.nav_participation')` con contadores de elementos pendientes y acciones de un clic para marcar `Aprobado`, `Rechazado` o `En espera`.

## 7. Contenido Gestionable
- Revisión de correcciones propuestas tanto desde el listado global como desde la pestaña «Sugerencias del contenido» de cada página/noticia, sugerencias de nuevos temas, refranes y comentarios.
