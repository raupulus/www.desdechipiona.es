# Módulo: Frontend público

*Última actualización: 2026-11-05*

> Layout principal, barra de navegación, pie de página, componentes Blade reutilizables y páginas públicas del sitio web con Tailwind CSS y Alpine.js.

---

## Resumen

El frontend público incluye el layout HTML5 con Tailwind CSS v4, la barra de navegación responsive con Alpine.js, el pie de página con redes sociales, componentes Blade reutilizables (carrusel, tarjeta de post, selector de idioma, mensajes flash, secciones dinámicas, script de reCAPTCHA v3, botones, inputs y modales) y las páginas: home, blog, detalle de post, detalle de página, about, contacto, privacidad y cookies.

---

## Archivos de layout

| Archivo | Descripción |
|---------|-------------|
| `resources/views/layouts/app.blade.php` | Layout principal HTML5 con `@vite`, `@stack`, navbar y footer |
| `resources/views/layouts/guest.blade.php` | Layout centrado para páginas de autenticación (`/login`, `/register`, etc.) |
| `resources/views/layouts/head.blade.php` | Meta tags SEO completos (OG, Twitter, JSON-LD, hreflang) |
| `resources/views/layouts/navbar.blade.php` | Barra de navegación responsive con Alpine.js |
| `resources/views/layouts/footer.blade.php` | Pie de página con copyright, enlaces legales y redes sociales |

---

## Componentes Blade

> Para el catálogo completo de componentes de UI (botones, inputs, labels, modales, dropdowns), consulte [COMPONENTS.md](COMPONENTS.md).

| Componente | Archivo | Descripción |
|------------|---------|-------------|
| `<x-flash-messages />` | `components/flash-messages.blade.php` | Mensajes flash (`success`, `error`, `warning`, `info`) con auto-ocultado vía Alpine.js |
| `<x-language-switcher />` | `components/language-switcher.blade.php` | Selector de idioma ES/EN |
| `<x-post-card :post="$post" />` | `components/post-card.blade.php` | Tarjeta de post: imagen, título, extracto, categorías, fecha, enlace |
| `<x-slide-carousel zone="home" />` | `components/slide-carousel.blade.php` | Carrusel de slides por zona con Alpine.js |
| `<x-section-block :section="$section" />` | `components/section-block.blade.php` | Bloque de sección dinámica: título, descripción, contenido, imagen |
| `<x-pagination :paginator="$posts" />` | `components/pagination.blade.php` | Paginación personalizada con Tailwind |
| `<x-recaptcha-script action="..." />` | `components/recaptcha-script.blade.php` | Inyección invisible de Google reCAPTCHA v3 al enviar formularios públicos |
| `<x-turnstile-widget action="..." />` | `components/turnstile-widget.blade.php` | Widget anti-spam de Cloudflare Turnstile para formularios de participación y login |
| `<x-social-identity-banner />` | `components/social-identity-banner.blade.php` | Identificación social ligera en sesión (Facebook / Google) sin cuenta local |
| `<x-searchable-select />` | `components/searchable-select.blade.php` | Selector desplegable accesible (ARIA APG combobox) con búsqueda en vivo |
| `<x-content-builder :blocks="$blocks" />` | `components/content-builder.blade.php` | Renderizador público de bloques modulares de contenido |

---

## Páginas

| Página | Vista | Ruta | Controlador |
|--------|-------|------|-------------|
| Home y Explorador Multicriterio | `home.blade.php` | `/` (`/explore`) | `HomeController@index` |
| Ficha / Página documental | `pages/show.blade.php` | `/pages/{slug}` | `PageController@show` |
| Noticias (listado) | `news/index.blade.php` | `/news` | `NewsController@index` |
| Noticia (detalle) | `news/show.blade.php` | `/news/{slug}` | `NewsController@show` |
| Refranero Popular | `proverbs/index.blade.php` | `/proverbs` | `ProverbController@index/store` |
| El Tiempo y Mareas (AEMET 11016) | `weather/index.blade.php` | `/weather` | `WeatherController@index` |
| Teléfonos Oficiales | `phones/index.blade.php` | `/phones` | `OfficialPhoneController@index` |
| Sugerir y Aportar Contenido | `suggestions/create.blade.php` | `/suggestions` | `ContentSuggestionController@create/store` |
| Autoría y Aviso Legal | `static/legal.blade.php` | `/legal` | `StaticPageController@legal` |
| Blog (listado, *desactivado temporalmente*) | `posts/index.blade.php` | `/blog` *(comentada)* | `PostController@index` |
| Blog (detalle, *desactivado temporalmente*) | `posts/show.blade.php` | `/blog/{slug}` *(comentada)* | `PostController@show` |
| Sobre nosotros | `static/about.blade.php` | `/about` | `StaticPageController@about` |
| Contacto | `contact.blade.php` | `/contact` | `ContactController@show/send` |
| Privacidad | `static/privacy.blade.php` | `/privacy` | `StaticPageController@privacy` |
| Cookies | `static/cookies.blade.php` | `/cookies` | `StaticPageController@cookies` |

### Errores personalizados

| Vista | Descripción |
|-------|-------------|
| `errors/404.blade.php` | Página no encontrada |
| `errors/500.blade.php` | Error interno del servidor |

---

## Controladores Web

| Controlador | Ruta |
|-------------|------|
| `HomeController` | `app/Http/Controllers/Web/HomeController.php` |
| `PageController` | `app/Http/Controllers/Web/PageController.php` |
| `NewsController` | `app/Http/Controllers/Web/NewsController.php` |
| `ProverbController` | `app/Http/Controllers/Web/ProverbController.php` |
| `WeatherController` | `app/Http/Controllers/Web/WeatherController.php` |
| `OfficialPhoneController` | `app/Http/Controllers/Web/OfficialPhoneController.php` |
| `ContentSuggestionController` | `app/Http/Controllers/Web/ContentSuggestionController.php` |
| `CorrectionRequestController` | `app/Http/Controllers/Web/CorrectionRequestController.php` |
| `CommentController` | `app/Http/Controllers/Web/CommentController.php` |
| `SocialIdentityController` | `app/Http/Controllers/Web/SocialIdentityController.php` |
| `PostController` | `app/Http/Controllers/Web/PostController.php` |
| `ContactController` | `app/Http/Controllers/Web/ContactController.php` |
| `StaticPageController` | `app/Http/Controllers/Web/StaticPageController.php` |
| `LanguageController` | `app/Http/Controllers/Web/LanguageController.php` |
| `LogDownloadController` | `app/Http/Controllers/Web/LogDownloadController.php` |

---

## Stack frontend

| Tecnología | Uso |
|------------|-----|
| **Tailwind CSS 4** | Estilos utility-first con `@theme` en `resources/css/app.css` |
| **Alpine.js 3** | Interactividad ligera (menú hamburguesa, carrusel, flash messages, modales) |
| **Vue 3** | Preparado (componentes en `resources/js/components/`), no usado en producción todavía |
| **Vite 8 + pnpm** | Bundler de assets (CSS + JS) gestionado con `pnpm` |
