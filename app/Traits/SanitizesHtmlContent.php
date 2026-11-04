<?php

namespace App\Traits;

/**
 * Trait para sanear en servidor el contenido HTML enriquecido (RichEditor)
 * antes de persistirlo en columnas JSONB traducibles.
 */
trait SanitizesHtmlContent
{
    /**
     * Registra el evento saving para limpiar etiquetas y atributos peligrosos (XSS).
     */
    public static function bootSanitizesHtmlContent(): void
    {
        static::saving(function ($model): void {
            if (! method_exists($model, 'getTranslations')) {
                return;
            }

            foreach (['content'] as $field) {
                $translations = $model->getTranslations($field);
                if (empty($translations)) {
                    continue;
                }

                $sanitized = [];
                foreach ($translations as $locale => $html) {
                    $sanitized[$locale] = static::sanitizeContentValue($html);
                }

                $model->setTranslations($field, $sanitized);
            }
        });
    }

    /**
     * Sanea recursivamente un valor traducible (string o array de bloques).
     */
    protected static function sanitizeContentValue(mixed $value): mixed
    {
        if (is_string($value)) {
            return static::sanitizeHtmlString($value);
        }

        if (is_array($value)) {
            $result = [];
            foreach ($value as $k => $v) {
                if ($k === 'button_url' && is_string($v)) {
                    $result[$k] = static::sanitizeUrlString($v);
                } else {
                    $result[$k] = static::sanitizeContentValue($v);
                }
            }

            return $result;
        }

        return $value;
    }

    /**
     * Elimina etiquetas ejecutables (<script>, <iframe>, <object>, <embed>, <style>)
     * y atributos de eventos inline (on*) o esquemas javascript:/data:.
     */
    public static function sanitizeHtmlString(string $html): string
    {
        // Eliminar bloques completos de etiquetas peligrosas
        $clean = preg_replace(
            '#<(script|iframe|object|embed|applet|meta|link|style|form)\b[^>]*>.*?</\1>#is',
            '',
            $html
        ) ?? $html;

        // Eliminar etiquetas peligrosas huérfanas o auto-cerradas
        $clean = preg_replace(
            '#</?(script|iframe|object|embed|applet|meta|link|style|form|input|button)\b[^>]*>#is',
            '',
            $clean
        ) ?? $clean;

        // Eliminar manejadores de eventos inline (onload, onerror, onclick, etc.)
        $clean = preg_replace(
            '#\s+on[a-z]+\s*=\s*(?:"[^"]*"|\'[^\']*\'|[^\s>]+)#i',
            '',
            $clean
        ) ?? $clean;

        // Neutralizar protocolos javascript:, vbscript: o data: (incluso ofuscados con entidades HTML o caracteres de control)
        $clean = preg_replace_callback(
            '#\b(href|src|action|formaction|xlink:href)\s*=\s*(?:"([^"]*)"|\'([^\']*)\'|([^\s"\'=<>`]+))#i',
            function (array $matches): string {
                $attr = $matches[1];
                $rawVal = ($matches[2] ?? '') !== '' ? $matches[2] : (($matches[3] ?? '') !== '' ? $matches[3] : ($matches[4] ?? ''));
                $safeVal = static::sanitizeUrlString($rawVal);

                return $attr.'="'.htmlspecialchars($safeVal, ENT_QUOTES, 'UTF-8').'"';
            },
            $clean
        ) ?? $clean;

        return $clean;
    }

    /**
     * Valida y limpia una URL para impedir esquemas ejecutables (javascript:, vbscript:, data:).
     */
    public static function sanitizeUrlString(string $url): string
    {
        $decoded = html_entity_decode($url, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $normalized = preg_replace('/[\x00-\x20]+/', '', $decoded) ?? '';

        if (preg_match('/^(?:javascript|vbscript|data):/i', $normalized)) {
            return '#';
        }

        return trim($url);
    }
}
