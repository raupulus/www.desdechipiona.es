<?php

namespace App\Services;

/**
 * Servicio para enlazar automáticamente términos y referencias entre artículos
 * enciclopédicos (estilo Wikipedia: enlaza solo la primera aparición por artículo,
 * respetando encabezados, enlaces ya existentes y bloques de código).
 */
class ContentAutoLinker
{
    /**
     * Convierte enlaces Markdown relativos (*.md) a rutas web /pages/{slug}
     * y enlaza la primera aparición de otros artículos del catálogo.
     *
     * @param  array<string, string>  $termsMap  [Título o sinónimo => slug]
     */
    public function linkContent(string $markdown, string $currentSlug, array $termsMap = []): string
    {
        // 1. Convertir enlaces relativos .md a rutas dinámicas /pages/{slug}
        $markdown = (string) preg_replace(
            '/\[([^\]]+)\]\([^)]*?([a-zA-Z0-9_-]+)\.md\)/',
            '[$1](/pages/$2)',
            $markdown
        );

        if (empty($termsMap)) {
            return $markdown;
        }

        // 2. Ordenar términos por longitud descendente para evitar colisiones de subcadenas
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
