<?php

namespace App\Services;

use App\Models\Page;

/**
 * Servicio para enlazar automáticamente términos y referencias entre artículos
 * enciclopédicos (estilo Wikipedia: enlaza solo la primera aparición por artículo,
 * respetando encabezados, enlaces ya existentes y bloques de código).
 */
class ContentAutoLinker
{
    /** @var array<string, string> Mapeo de erratas comunes o slugs en inglés a slugs válidos */
    protected const SLUG_ALIASES = [
        'playa-de-relga' => 'playa-de-regla',
        'fiestas-patronales-de-relga' => 'fiestas-patronales-de-regla',
        'alqueria-bererber-y-origen-de-los-corrales' => 'alqueria-bereber-y-origen-de-los-corrales',
        'chameleon-comun' => 'camaleon-comun',
        'puebla-carta-1477' => 'carta-puebla-1477',
        'ermita-de-nostra-senora-del-pinar' => 'ermita-de-nuestra-senora-del-pinar',
        'quarries-romanas-de-las-canteras' => 'canteras-romanas-de-las-canteras',
        'intangible-heritage' => 'patrimonio-inmaterial',
        'parish-of-our-senora-de-la-o' => 'parroquia-de-nuestra-senora-de-la-o',
        'common-chameleon' => 'camaleon-comun',
        'broom-white' => 'retama-blanca',
        'ocellated-lizard' => 'lagarto-ocelado',
        'berber-piracy-and-coastal-defences' => 'pirateria-berberisca-y-defensas-costeras',
        'sanatorium-of-santa-clara' => 'sanatorio-de-santa-clara',
        'fishing-and-sports-port' => 'puerto-pesquero-y-deportivo',
        'sea-cross' => 'cruz-del-mar',
        'ingots-romanos-de-chipiona' => 'lingotes-romanos-de-chipiona',
        'sea-lily' => 'azucena-de-mar',
        'maritime-thistle' => 'cardo-maritimo',
        'rabbit' => 'conejo-de-monte',
        'snow plover' => 'chorlitejo-patinegro',
        'snow-plover' => 'chorlitejo-patinegro',
        'striped plover' => 'chorlitejo-patinegro',
        'striped-plover' => 'chorlitejo-patinegro',
        'festival' => 'festival-del-moscatel',
    ];

    /**
     * Convierte enlaces Markdown relativos (*.md) a rutas web /pages/{slug} válidas
     * y enlaza la primera aparición de otros artículos del catálogo.
     *
     * @param  array<string, string>  $termsMap  [Título o sinónimo => slug]
     * @param  array<string>|null  $validSlugs  Lista exhaustiva de slugs válidos del catálogo
     */
    public function linkContent(string $markdown, string $currentSlug, array $termsMap = [], ?array $validSlugs = null): string
    {
        // 0. Si no se proporciona lista explícita de slugs válidos, inferirla de termsMap o Page si la BD está conectada
        if ($validSlugs === null) {
            if (! empty($termsMap)) {
                $validSlugs = array_values($termsMap);
            } elseif (class_exists(Page::class) && Page::getConnectionResolver() !== null) {
                try {
                    $validSlugs = Page::pluck('slug')->all();
                } catch (\Throwable) {
                    $validSlugs = null;
                }
            }
        }

        // 1. Limpiar enlaces a carpetas de trabajo interno del repositorio o archivos privados (00-proyecto, nota-indicios, etc.)
        $markdown = (string) preg_replace(
            '/\[([^\]]+)\]\([^)]*?(?:00-proyecto|01-fuentes|04-recursos|99-archivo|nota-indicios)[^)]*?\)/iu',
            '$1',
            $markdown
        );

        // 2. Corregir enlaces rotos truncados conocidos (ej: festival-del-mosc)
        $markdown = (string) preg_replace('/\[Muscat Festival\]\(\/pages\/festival-del-mosc\b/u', '[Muscat Festival](/pages/festival-del-moscatel)', $markdown);

        // 3. Convertir enlaces relativos .md a rutas dinámicas /pages/{slug}, validando existencia y saneando alias
        $markdown = (string) preg_replace_callback(
            '/\[([^\]]+)\]\(([^)]*?([a-zA-Z0-9_-]+)\.md)\)/',
            function (array $matches) use ($validSlugs): string {
                $text = $matches[1];
                $url = $matches[2];
                $rawSlug = $matches[3];

                if (preg_match('/(?:00-proyecto|01-fuentes|04-recursos|99-archivo|nota-indicios)/i', $url)) {
                    return $text;
                }

                $targetSlug = self::SLUG_ALIASES[$rawSlug] ?? $rawSlug;

                if (is_array($validSlugs) && ! empty($validSlugs) && ! in_array($targetSlug, $validSlugs, true)) {
                    return $text;
                }

                return "[{$text}](/pages/{$targetSlug})";
            },
            $markdown
        );

        // 4. Sanear enlaces /pages/{slug} ya existentes: resolver alias y evitar enlaces 404
        $markdown = (string) preg_replace_callback(
            '/\[([^\]]+)\]\(\/pages\/([a-zA-Z0-9_ -]+)\)/',
            function (array $matches) use ($validSlugs): string {
                $text = $matches[1];
                $rawSlug = trim($matches[2]);

                if ($rawSlug === 'nota-indicios') {
                    return $text;
                }

                $targetSlug = self::SLUG_ALIASES[$rawSlug] ?? $rawSlug;

                if (is_array($validSlugs) && ! empty($validSlugs) && ! in_array($targetSlug, $validSlugs, true)) {
                    return $text;
                }

                return "[{$text}](/pages/{$targetSlug})";
            },
            $markdown
        );

        if (empty($termsMap)) {
            return $markdown;
        }

        // 5. Ordenar términos por longitud descendente para evitar colisiones de subcadenas
        uksort($termsMap, fn (string $a, string $b) => mb_strlen($b) <=> mb_strlen($a));

        $linkedSlugs = [];
        $lines = explode("\n", $markdown);
        $resultLines = [];
        $inCodeBlock = false;

        foreach ($lines as $line) {
            $trimmed = trim($line);

            // Manejo de bloques de código
            if (str_starts_with($trimmed, '```')) {
                $inCodeBlock = ! $inCodeBlock;
                $resultLines[] = $line;

                continue;
            }

            if ($inCodeBlock) {
                $resultLines[] = $line;

                continue;
            }

            // Ignorar encabezados (# Título) y filas de tablas para no ensuciar estructura
            if (preg_match('/^#{1,6}\s+/', $trimmed) || str_starts_with($trimmed, '|')) {
                $resultLines[] = $line;

                continue;
            }

            foreach ($termsMap as $term => $targetSlug) {
                if ($targetSlug === $currentSlug || isset($linkedSlugs[$targetSlug])) {
                    continue;
                }

                // Evitar enlazar si el término ya está enlazado en la línea o forma parte de un enlace [..](..)
                $quotedTerm = preg_quote($term, '/');
                $pattern = '/(?<!\[)(?<!\/|\w)\b('.$quotedTerm.')\b(?!\w)(?![^\[]*\])(?![^<]*>)/iu';

                if (preg_match($pattern, $line, $matches, PREG_OFFSET_CAPTURE)) {
                    $offset = $matches[0][1];
                    $matchText = $matches[0][0];
                    $replacement = "[{$matchText}](/pages/{$targetSlug})";

                    // Verificar que no estemos dentro de un paréntesis de enlace previo
                    $prefix = substr($line, 0, $offset);
                    $openBrackets = substr_count($prefix, '[') - substr_count($prefix, ']');
                    $openParens = substr_count($prefix, '(') - substr_count($prefix, ')');

                    if ($openBrackets === 0 && $openParens === 0) {
                        $line = substr_replace($line, $replacement, $offset, strlen($matchText));
                        $linkedSlugs[$targetSlug] = true;
                    }
                }
            }

            $resultLines[] = $line;
        }

        return implode("\n", $resultLines);
    }
}
