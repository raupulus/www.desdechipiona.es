<?php

namespace App\Traits;

use Illuminate\Support\Str;

/**
 * Trait para gestionar campos SEO en modelos de contenido.
 * Proporciona métodos helper que obtienen el valor SEO del idioma actual
 * con fallbacks jerárquicos: SEO field → title/excerpt → string vacío.
 */
trait HasSeo
{
    /**
     * Obtiene el título SEO optimizado para el idioma actual.
     * Prioridad: seo_title → title → string vacío.
     *
     * @return string El título SEO o el título del modelo como fallback
     */
    public function getSeoTitleText(): string
    {
        $seoTitle = $this->seo_title;
        $fallback = (string) config('app.fallback_locale', 'es');

        // Si es array de traducciones, obtener el idioma actual o fallback
        if (is_array($seoTitle)) {
            $seoTitle = $seoTitle[app()->getLocale()] ?? $seoTitle[$fallback] ?? $seoTitle['es'] ?? '';
        }

        // Si el seo_title está vacío, usar el título del modelo
        if (! empty($seoTitle)) {
            return Str::limit(trim((string) $seoTitle), 64, '…');
        }

        $title = $this->title;
        if (is_array($title)) {
            $title = $title[app()->getLocale()] ?? $title[$fallback] ?? $title['es'] ?? '';
        }

        $rawTitle = (string) ($title ?? '');
        $siteTitle = (string) setting('site_title', config('app.name'));
        if ($rawTitle !== '' && $siteTitle !== '' && ! str_contains($rawTitle, $siteTitle)) {
            $withBrand = $rawTitle.' — '.$siteTitle;
            if (mb_strlen($withBrand) <= 65) {
                return $withBrand;
            }
        }

        return Str::limit($rawTitle, 64, '…');
    }

    /**
     * Obtiene la descripción SEO optimizada para el idioma actual.
     * Prioridad: seo_description → excerpt → string vacío.
     *
     * @return string La descripción SEO o el extracto del modelo como fallback
     */
    public function getSeoDescriptionText(): string
    {
        $fallback = (string) config('app.fallback_locale', 'es');
        $seoDesc = $this->seo_description;

        if (is_array($seoDesc)) {
            $seoDesc = $seoDesc[app()->getLocale()] ?? $seoDesc[$fallback] ?? $seoDesc['es'] ?? '';
        }

        if (! empty($seoDesc)) {
            return $this->cleanSeoText((string) $seoDesc);
        }

        // Fallback al excerpt si existe en el modelo
        if (isset($this->excerpt)) {
            $excerpt = $this->excerpt;
            if (is_array($excerpt)) {
                $excerpt = $excerpt[app()->getLocale()] ?? $excerpt[$fallback] ?? $excerpt['es'] ?? '';
            }

            if (! empty($excerpt)) {
                return $this->cleanSeoText((string) $excerpt);
            }
        }

        // Último fallback: primeros 155 caracteres del contenido sin etiquetas HTML
        if (isset($this->content)) {
            $content = $this->content;
            if (is_array($content)) {
                $content = $content[app()->getLocale()] ?? $content[$fallback] ?? $content['es'] ?? '';
            }

            if (! empty($content)) {
                return $this->cleanSeoText((string) $content, 155);
            }
        }

        return '';
    }

    /**
     * Normaliza el texto para meta description eliminando HTML, saltos de línea y espacios múltiples.
     */
    private function cleanSeoText(string $text, int $limit = 160): string
    {
        $normalized = preg_replace('/\s+/u', ' ', trim(strip_tags($text))) ?? '';
        if (mb_strlen($normalized) <= $limit) {
            return $normalized;
        }

        return Str::limit($normalized, max(1, $limit - 1), '…');
    }

    /**
     * Obtiene las keywords SEO para el idioma actual.
     * Devuelve string vacío si no hay keywords definidas.
     *
     * @return string Las keywords SEO o string vacío
     */
    public function getSeoKeywordsText(): string
    {
        $keywords = $this->seo_keywords;

        if (is_array($keywords)) {
            return $keywords[app()->getLocale()] ?? $keywords[config('app.fallback_locale')] ?? '';
        }

        return $keywords ?? '';
    }
}
