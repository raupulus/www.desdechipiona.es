<?php

namespace App\Console\Commands;

use App\Enums\ContentGroup;
use App\Enums\ContentStatus;
use App\Enums\EvidenceLevel;
use App\Enums\HistoricalEra;
use App\Enums\SuggestionStatus;
use App\Models\Category;
use App\Models\News;
use App\Models\Page;
use App\Models\Proverb;
use App\Models\User;
use App\Services\ContentAutoLinker;
use Database\Seeders\ChipionaStructureSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Yaml\Yaml;

/**
 * Comando Artisan para importar localmente el corpus de investigación de Chipiona
 * hacia la base de datos PostgreSQL (`desdechipiona`), categorizando, enlazando
 * y relacionando automáticamente todas las fichas temáticas y panoramas históricos.
 */
class ImportChipionaCorpus extends Command
{
    /**
     * Firma del comando en consola.
     *
     * @var string
     */
    protected $signature = 'chipiona:import-corpus
                            {--path= : Ruta al directorio externo de investigación o archivo de datos local}
                            {--clean : Limpia páginas previas importadas antes de sincronizar}';

    /**
     * Descripción del comando.
     *
     * @var string
     */
    protected $description = 'Importa, categoriza, limpia y enlaza el corpus completo de Chipiona desde el repositorio de investigación';

    /**
     * Coordenadas GPS de referencia para lugares emblemáticos de Chipiona.
     *
     * @var array<string, array{float, float}>
     */
    protected array $coordinatesMap = [
        'faro-de-chipiona' => [36.7378, -6.4425],
        'piedra-de-salmedina' => [36.7410, -6.4680],
        'santuario-de-nuestra-senora-de-regla' => [36.7302, -6.4401],
        'castillo-de-chipiona' => [36.7398, -6.4391],
        'parroquia-de-nuestra-senora-de-la-o' => [36.7389, -6.4383],
        'ermita-del-cristo-de-las-misericordias' => [36.7389, -6.4385],
        'humilladero' => [36.7298, -6.4404],
        'sanatorio-de-santa-clara' => [36.7295, -6.4398],
        'corrales-de-pesca' => [36.7294, -6.4408],
        'playa-de-regla' => [36.7278, -6.4392],
        'playa-cruz-del-mar' => [36.7405, -6.4380],
        'playa-las-canteras' => [36.7392, -6.4412],
        'playa-de-camaron' => [36.7160, -6.4310],
        'playa-de-la-laguna' => [36.7110, -6.4280],
        'playa-las-tres-piedras' => [36.7020, -6.4180],
        'playa-de-montijo' => [36.7510, -6.4180],
        'playa-micaela' => [36.7440, -6.4310],
        'playa-nino-de-oro' => [36.7470, -6.4250],
        'playa-de-la-ballena' => [36.6950, -6.4090],
        'pinar-de-la-villa' => [36.7350, -6.4020],
        'pinar-de-peritanda' => [36.7310, -6.3980],
        'centro-de-interpretacion-el-camaleon' => [36.7165, -6.4312],
        'puerto-pesquero-y-deportivo' => [36.7429, -6.4334],
        'museo-del-moscatel' => [36.7351, -6.4330],
        'mausoleo-de-rocio-jurado' => [36.7324, -6.4278],
        'via-verde-entre-rios' => [36.7310, -6.4250],
        'turris-caepionis' => [36.7365, -6.4470],
        'alfar-romano-de-el-olivar-y-salazones' => [36.7242, -6.4131],
        'alqueria-bereber-y-origen-de-los-corrales' => [36.7405, -6.4402],
    ];

    /**
     * Ejecuta la importación del corpus completo.
     */
    public function handle(ContentAutoLinker $autoLinker): int
    {
        $this->info('=== Importador del Corpus Privado de Desde Chipiona ===');

        // 1. Asegurar categorías, estructura base y teléfonos oficiales
        $this->call(ChipionaStructureSeeder::class);

        $author = User::where('email', 'public@raupulus.dev')->first()
            ?? User::first();

        $authorId = $author?->id ?? 1;
        $categories = Category::all()->keyBy('slug');

        if ($this->option('clean')) {
            if ($this->laravel->isProduction() && ! $this->confirm('Estás en producción. ¿Confirmas limpiar páginas previas?', false)) {
                $this->warn('→ Limpieza previa cancelada; se realizará actualización incremental.');
            } else {
                $this->warn('→ Limpiando páginas enciclopédicas previas...');
                Page::whereNotNull('content_group')->forceDelete();
            }
        }

        $corpusPath = $this->resolveCorpusPath();
        if (! $corpusPath || ! File::isDirectory($corpusPath)) {
            $this->error("No se encontró el directorio del corpus en: {$corpusPath}");

            return Command::FAILURE;
        }

        $this->line("→ Escaneando corpus en: {$corpusPath}");

        // 2. Cargar y filtrar fuentes oficiales gubernamentales y académicas públicas
        $officialSources = $this->loadOfficialSources($corpusPath);
        $this->line(sprintf('→ Fuentes oficiales / gubernamentales catalogadas: %d', count($officialSources)));

        // 3. Importar todas las fichas Markdown (.md)
        $rawPagesData = $this->parseMarkdownFiles($corpusPath, $officialSources, $authorId, $categories->all());
        $this->line(sprintf('→ Fichas procesadas del corpus: %d', count($rawPagesData)));

        // 4. Guardar páginas y registrar mapa de títulos para auto-enlazado
        $termsMap = [];
        $pagesBySlug = [];
        $pendingRelations = [];

        DB::transaction(function () use ($rawPagesData, &$termsMap, &$pagesBySlug, &$pendingRelations, $categories) {
            foreach ($rawPagesData as $data) {
                $slug = $data['slug'];
                $pendingRelations[$slug] = $data['related_slugs'];
                unset($data['related_slugs']);

                $contentGroup = $data['content_group'];

                /** @var Page $page */
                $page = Page::updateOrCreate(['slug' => $slug], $data);
                if (isset($categories[$contentGroup->value])) {
                    $page->categories()->sync([$categories[$contentGroup->value]->id]);
                }

                $pagesBySlug[$slug] = $page;
                $titleEs = $page->getTranslation('title', 'es');
                if (! empty($titleEs) && mb_strlen($titleEs) >= 4) {
                    $termsMap[$titleEs] = $slug;
                }
            }

            // Eliminar páginas antiguas que ya no forman parte del corpus real
            if (! empty($pagesBySlug)) {
                Page::whereNotNull('content_group')
                    ->whereNotIn('slug', array_keys($pagesBySlug))
                    ->forceDelete();
            }
        });

        // 5. Aplicar auto-enlazador enciclopédico sobre el contenido de cada página
        $this->line('→ Aplicando auto-enlazado enciclopédico inteligente...');
        DB::transaction(function () use ($pagesBySlug, $termsMap, $autoLinker) {
            foreach ($pagesBySlug as $slug => $page) {
                $contentEs = $page->getTranslation('content', 'es');
                $linkedContentEs = $autoLinker->linkContent($contentEs, $slug, $termsMap);

                $contentEn = $page->getTranslation('content', 'en');
                $linkedContentEn = $autoLinker->linkContent($contentEn, $slug, $termsMap);

                $page->update([
                    'content' => ['es' => $linkedContentEs, 'en' => $linkedContentEn],
                ]);
            }
        });

        // 6. Sincronizar relaciones cruzadas entre páginas
        $this->line('→ Sincronizando red de conocimiento (páginas relacionadas)...');
        DB::transaction(function () use ($pagesBySlug, $pendingRelations) {
            foreach ($pendingRelations as $slug => $relSlugs) {
                if (! isset($pagesBySlug[$slug])) {
                    continue;
                }
                $relIds = [];
                foreach ($relSlugs as $tSlug) {
                    if (isset($pagesBySlug[$tSlug])) {
                        $relIds[] = $pagesBySlug[$tSlug]->id;
                    }
                }
                if (! empty($relIds)) {
                    $pagesBySlug[$slug]->relatedPages()->sync($relIds);
                }
            }
        });

        // 7. Cargar noticias y refranes extras si existen
        $extraFile = database_path('seeders/data/chipiona_corpus_extras.php');
        if (File::exists($extraFile)) {
            $this->line('→ Sincronizando noticias y refranes del catálogo...');
            $extras = require $extraFile;
            $this->importExtras($extras, $authorId, $categories->all(), $pagesBySlug, $officialSources);
        }

        // 8. Asegurar que las noticias tampoco tengan vídeos ajenos
        News::query()->update(['youtube_url' => null]);

        $this->info(sprintf('✓ Corpus completo importado con éxito: %d entradas activas.', Page::where('is_active', true)->count()));

        return Command::SUCCESS;
    }

    /**
     * Resuelve la ruta al directorio del corpus de investigación.
     */
    protected function resolveCorpusPath(): ?string
    {
        $explicit = $this->option('path') ?: config('services.chipiona.corpus_path');
        if (is_string($explicit) && $explicit !== '' && File::isDirectory($explicit)) {
            return $explicit;
        }

        $candidates = [
            base_path('../Historia Chipiona'),
        ];

        foreach ($candidates as $cand) {
            if (File::isDirectory($cand)) {
                return $cand;
            }
        }

        return null;
    }

    /**
     * Lee y filtra el registro maestro de fuentes para quedarse únicamente
     * con fuentes oficiales, gubernamentales, archivos históricos y académicas públicas.
     *
     * @return array<string, array{id: string, title: string, author: string, year: string, url: string|null}>
     */
    protected function loadOfficialSources(string $corpusDir): array
    {
        $registryFile = rtrim($corpusDir, '/').'/01-fuentes/registro-fuentes.md';
        if (! File::exists($registryFile)) {
            return [];
        }

        $officialKeywords = [
            'ayuntamiento', 'turismo de chipiona', 'iaph', 'junta de andalucía', 'junta de andalucia',
            'universidad', 'instituto', 'ministerio', 'boe', 'boja', 'bop', 'real academia',
            'catastro', 'archivo', 'pares', 'autoridad portuaria', 'csic', 'aemet', 'instituto hidrográfico',
            'instituto hidrografico', 'iala', 'pueblos mágicos', 'pueblos magicos', 'diputación', 'diputacion',
            'patronato', 'mancomunidad', 'consejería', 'consejeria', 'delegación', 'delegacion',
            'anuario del veraneante', 'anuario', 'carta arqueológica', 'carta arqueologica',
        ];

        $sources = [];
        $lines = file($registryFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            $line = trim($line);
            if (! str_starts_with($line, '| F-')) {
                continue;
            }

            $cols = array_map('trim', explode('|', trim($line, '|')));
            if (count($cols) < 10) {
                continue;
            }

            [$id, $title, $author, $year, $type, , , $license, , $location] = $cols;
            $fullText = mb_strtolower("{$author} {$title} {$location} {$license} {$type}");

            $isOfficial = false;
            foreach ($officialKeywords as $kw) {
                if (str_contains($fullText, $kw)) {
                    $isOfficial = true;
                    break;
                }
            }

            // Descartar blogs privados o redes no institucionales
            if (str_contains($location, 'blogspot') || str_contains($location, 'wordpress') || str_contains($location, 'facebook')) {
                if (! str_contains($fullText, 'iaph') && ! str_contains($fullText, 'ayto')) {
                    $isOfficial = false;
                }
            }

            if (! $isOfficial) {
                continue;
            }

            $url = str_starts_with($location, 'http') ? $location : null;

            $sources[$id] = [
                'id' => $id,
                'title' => $title,
                'author' => $author,
                'year' => $year,
                'url' => $url,
            ];
        }

        return $sources;
    }

    /**
     * Escanea y parsea todos los archivos Markdown de 02-historia y 03-actualidad.
     *
     * @param  array<string, array{id: string, title: string, author: string, year: string, url: string|null}>  $officialSources
     * @param  array<string, Category>  $categories
     * @return array<int, array<string, mixed>>
     */
    protected function parseMarkdownFiles(string $corpusDir, array $officialSources, int $userId, array $categories): array
    {
        $items = [];
        $subdirs = ['02-historia', '03-actualidad'];

        // Cargar diccionario de traducciones al inglés si existe
        $enTranslations = [];
        $enTransFile = database_path('seeders/data/chipiona_pages_translations_en.json');
        if (File::exists($enTransFile)) {
            $rawEn = json_decode(File::get($enTransFile), true);
            if (is_array($rawEn)) {
                foreach ($rawEn as $enItem) {
                    if (isset($enItem['slug'])) {
                        $enTranslations[$enItem['slug']] = $enItem;
                    }
                }
            }
        }

        foreach ($subdirs as $sub) {
            $targetDir = rtrim($corpusDir, '/').'/'.$sub;
            if (! File::isDirectory($targetDir)) {
                continue;
            }

            foreach (File::allFiles($targetDir) as $file) {
                if ($file->getExtension() !== 'md' || strtolower($file->getFilename()) === 'readme.md') {
                    continue;
                }

                $raw = File::get($file->getPathname());
                if (! preg_match('/^---\s*\n(.*?)\n---\s*\n(.*)$/s', $raw, $matches)) {
                    continue;
                }

                try {
                    $meta = Yaml::parse($matches[1]);
                } catch (\Throwable) {
                    continue;
                }

                if (! is_array($meta) || empty($meta['titulo'])) {
                    continue;
                }

                $isPanorama = strtolower($file->getFilename()) === 'panorama.md';
                $slug = (string) ($meta['slug'] ?? ($isPanorama
                    ? 'panorama-'.Str::slug((string) $meta['titulo'])
                    : Str::slug((string) $meta['titulo'])));

                $body = trim($matches[2]);
                $cleanBody = $this->cleanMarkdownBody($body);

                $group = $this->resolveGroupFromMeta($meta, $file->getPathname());
                $era = $this->resolveEraFromMeta($meta, $file->getPathname());
                $evidence = $this->resolveEvidenceFromMeta($meta);

                $years = array_filter((array) ($meta['anios_clave'] ?? []), 'is_numeric');
                $yearStart = ! empty($years) ? (int) min($years) : null;
                $yearEnd = ! empty($years) ? (int) max($years) : null;

                $excerpt = Str::limit(strip_tags(Str::markdown($cleanBody)), 220);

                // Resolver únicamente fuentes oficiales
                $sourceIds = (array) ($meta['fuentes'] ?? []);
                $resolvedSources = [];
                foreach ($sourceIds as $sId) {
                    $sId = trim((string) $sId);
                    if (isset($officialSources[$sId])) {
                        $resolvedSources[] = $officialSources[$sId];
                    }
                }

                // Coordenadas GPS
                $latitude = null;
                $longitude = null;
                if (isset($this->coordinatesMap[$slug])) {
                    [$latitude, $longitude] = $this->coordinatesMap[$slug];
                } elseif (isset($meta['latitud'], $meta['longitud']) && is_numeric($meta['latitud']) && is_numeric($meta['longitud'])) {
                    $latitude = (float) $meta['latitud'];
                    $longitude = (float) $meta['longitud'];
                }

                $cleanImage = $this->resolveCleanImagePath($slug, $group);

                $featuredSlugs = [
                    'faro-de-chipiona',
                    'santuario-nuestra-senora-de-regla',
                    'corrales-de-pesca-panorama',
                    'castillo-de-chipiona',
                    'moscatel',
                    'carnaval-de-chipiona',
                    'rocio-jurado',
                    'camaleon-comun',
                    'pinar-de-la-villa',
                    'playa-de-regla',
                    'arrecife-y-piedra-de-salmedina',
                    'turris-caepionis',
                ];

                $titleEs = (string) $meta['titulo'];
                $titleEn = $enTranslations[$slug]['title_en'] ?? $titleEs;
                $excerptEn = $this->cleanMarkdownBody((string) ($enTranslations[$slug]['excerpt_en'] ?? $excerpt));
                $contentEn = $this->cleanMarkdownBody((string) ($enTranslations[$slug]['content_en'] ?? $cleanBody));
                $evidenceNoteEs = $this->cleanMarkdownBody((string) ($meta['nota_evidencia'] ?? ''));
                $evidenceNoteEn = $this->cleanMarkdownBody((string) ($enTranslations[$slug]['evidence_note_en'] ?? $evidenceNoteEs));

                $items[] = [
                    'slug' => $slug,
                    'title' => ['es' => $titleEs, 'en' => $titleEn],
                    'excerpt' => ['es' => $excerpt, 'en' => $excerptEn],
                    'content' => ['es' => $cleanBody, 'en' => $contentEn],
                    'seo_title' => ['es' => $titleEs.' | Desde Chipiona', 'en' => $titleEn.' | Desde Chipiona'],
                    'seo_description' => ['es' => $excerpt, 'en' => $excerptEn],
                    'seo_keywords' => ['es' => 'Chipiona, '.$titleEs, 'en' => 'Chipiona, '.$titleEn.', history, heritage, nature'],
                    'content_group' => $group,
                    'historical_era' => $era,
                    'period_label' => isset($meta['siglos']) && is_array($meta['siglos']) ? 'Siglos '.implode(', ', $meta['siglos']) : ($meta['periodo'] ?? null),
                    'year_start' => $yearStart,
                    'year_end' => $yearEnd,
                    'evidence_level' => $evidence,
                    'evidence_note' => ['es' => $evidenceNoteEs, 'en' => $evidenceNoteEn],
                    'source_references' => $resolvedSources,
                    'image' => $cleanImage,
                    'social_image' => $cleanImage,
                    'youtube_url' => null, // Vídeos desactivados hasta que el autor publique los propios
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'location_name' => (string) ($meta['lugar'] ?? 'Chipiona (Cádiz)'),
                    'status' => ContentStatus::Published,
                    'is_active' => true,
                    'featured' => in_array($slug, $featuredSlugs, true) || (bool) ($meta['destacado'] ?? false),
                    'user_id' => $userId,
                    'published_at' => now(),
                    'related_slugs' => (array) ($meta['relacionados'] ?? []),
                ];
            }
        }

        return $items;
    }

    /**
     * Limpia el cuerpo Markdown: elimina marcas [F-XXXX], títulos numerados
     * y secciones internas de trabajo, preservando Cuestiones abiertas.
     */
    protected function cleanMarkdownBody(string $text): string
    {
        // 0. Eliminar el H1 inicial del Markdown (# Título) para no duplicar el <h1> de la cabecera
        $text = (string) preg_replace('/^\s*#\s+[^\r\n]+\R*/u', '', $text);

        // 0b. Eliminar imágenes relativas internas del corpus privado (04-recursos o formatos no WebP) y enlaces a 04-recursos
        $text = (string) preg_replace('/!\[[^\]]*\]\((?:[^)]*04-recursos[^)]*|[^)]*\.(?:jpe?g|png|gif))(?:\s+"[^"]*")?\)\R*/iu', '', $text);
        $text = (string) preg_replace('/\[([^\]]+)\]\([^)]*04-recursos[^)]*\)/iu', '$1', $text);

        // 1. Eliminar secciones de trabajo interno
        $text = (string) preg_replace('/(?ms)\n##\s+Notas de investigación\b.*?(?=\n##\s+|\Z)/u', '', $text);
        $text = (string) preg_replace('/(?ms)\n##\s+Cómo citar esta ficha\b.*?(?=\n##\s+|\Z)/u', '', $text);
        $text = (string) preg_replace('/(?ms)\n##\s+Fuentes\b.*?(?=\n##\s+|\Z)/u', '', $text);

        // 2. Eliminar cualquier marca inline de fuentes: [F-XXXX...], ([F-XXXX...]), `F-XXXX`
        $text = (string) preg_replace('/[\s`\'\"]*\(\s*\[F-\d{4}[^\]]*\]\s*\)[\s`\'\"]*/u', ' ', $text);
        $text = (string) preg_replace('/[\s`\'\"]*\[F-\d{4}[^\]]*\][\s`\'\"]*/u', ' ', $text);
        $text = (string) preg_replace('/\bF-\d{4}\b/u', '', $text);

        // 3. Eliminar numeraciones forzadas en encabezados (ej: ## 1. Resumen -> ## Resumen)
        $text = (string) preg_replace('/(?m)^(#{2,4})\s+\d+(?:\.\d+)*\.?\s+/u', '$1 ', $text);

        // 4. Limpieza de puntuación y espacios duplicados
        $text = (string) preg_replace('/\s+([.,;:!?])/u', '$1', $text);
        $text = (string) preg_replace('/[ \t]+/u', ' ', $text);
        $text = (string) preg_replace('/\n{3,}/u', "\n\n", $text);

        return trim($text);
    }

    /**
     * Resuelve la ruta de imagen WebP limpia (fotografía real propia o ilustración temática compartida sin texto).
     */
    protected function resolveCleanImagePath(string $slug, ContentGroup $group): string
    {
        if (File::exists(public_path("images/pages/{$slug}.webp"))) {
            return "images/pages/{$slug}.webp";
        }

        $theme = match (true) {
            str_contains($slug, 'faro') => 'faro',
            str_contains($slug, 'salmedina') => 'salmedina',
            str_contains($slug, 'corral') => 'corrales',
            str_contains($slug, 'santuario') || str_contains($slug, 'regla') || str_contains($slug, 'parroquia') || str_contains($slug, 'humilladero') || str_contains($slug, 'ermita') => 'santuario',
            str_contains($slug, 'castillo') || str_contains($slug, 'bateria') || str_contains($slug, 'bunker') => 'castillo',
            str_contains($slug, 'tren') || str_contains($slug, 'ferrocarril') => 'tren',
            str_contains($slug, 'camaleon') || str_contains($slug, 'lagarto') || str_contains($slug, 'conejo') => 'camaleon',
            str_contains($slug, 'chorlitejo') || str_contains($slug, 'aves') || str_contains($slug, 'vencejo') => 'aves',
            str_contains($slug, 'duna') || str_contains($slug, 'retama') || str_contains($slug, 'azucena') || str_contains($slug, 'litoral') => 'dunas',
            str_contains($slug, 'pinar') || str_contains($slug, 'pino') || str_contains($slug, 'flora') => 'pinar',
            str_contains($slug, 'romana') || str_contains($slug, 'caepionis') || str_contains($slug, 'tartessos') || str_contains($slug, 'griegos') => 'romana',
            $group === ContentGroup::Playas => 'playas',
            $group === ContentGroup::Monumentos => 'patrimonio',
            $group === ContentGroup::Fauna => 'camaleon',
            $group === ContentGroup::Flora => 'pinar',
            $group === ContentGroup::Fiestas => 'fiestas',
            $group === ContentGroup::Personajes => 'personajes',
            $group === ContentGroup::Gastronomia => 'corrales',
            default => 'historia',
        };

        return "images/themes/{$theme}.webp";
    }

    /**
     * Determina el ContentGroup a partir del frontmatter y la ruta.
     *
     * @param  array<string, mixed>  $meta
     */
    protected function resolveGroupFromMeta(array $meta, string $path): ContentGroup
    {
        $tipo = (string) ($meta['tipo'] ?? '');
        $cat = (string) ($meta['categoria'] ?? '');
        $sub = (string) ($meta['subcategoria'] ?? '');

        return match (true) {
            $tipo === 'playa' || str_contains($path, '/playas/') => ContentGroup::Playas,
            $tipo === 'monumento' || str_contains($path, '/monumentos/') => ContentGroup::Monumentos,
            $tipo === 'fiesta' || str_contains($path, '/fiestas/') => ContentGroup::Fiestas,
            $tipo === 'personaje' || str_contains($path, '/personajes/') => ContentGroup::Personajes,
            $tipo === 'gastronomia' || str_contains($path, '/gastronomia/') => ContentGroup::Gastronomia,
            $sub === 'fauna' || str_contains($path, '/naturaleza/camaleon') || str_contains($path, '/naturaleza/chorlitejo') || str_contains($path, '/naturaleza/conejo') || str_contains($path, '/naturaleza/lagarto') || str_contains($path, '/naturaleza/vencejo') || str_contains($path, '/naturaleza/aves') => ContentGroup::Fauna,
            $tipo === 'naturaleza' || str_contains($path, '/naturaleza/') => ContentGroup::Flora,
            $cat === 'historia' || str_contains($path, '02-historia') => ContentGroup::Historia,
            default => ContentGroup::Historia,
        };
    }

    /**
     * Determina la época histórica a partir del frontmatter y la ruta.
     *
     * @param  array<string, mixed>  $meta
     */
    protected function resolveEraFromMeta(array $meta, string $path): HistoricalEra
    {
        $periodo = (string) ($meta['periodo'] ?? '');

        return match (true) {
            str_contains($periodo, 'prehistoria') || str_contains($periodo, 'roma') || str_contains($periodo, 'tartessos') || str_contains($path, '01-prehistoria') || str_contains($path, '02-tartessos') || str_contains($path, '03-griegos') || str_contains($path, '04-epoca-romana') => HistoricalEra::PrehistoryAntiquity,
            str_contains($periodo, 'andalus') || str_contains($periodo, 'edad-media') || str_contains($periodo, 'visigodo') || str_contains($path, '05-antiguedad') || str_contains($path, '06-al-andalus') || str_contains($path, '07-baja-edad-media') => HistoricalEra::MiddleAges,
            str_contains($periodo, 'moderna') || str_contains($periodo, 'xviii') || str_contains($path, '08-edad-moderna') || str_contains($path, '09-siglo-xviii') => HistoricalEra::EarlyModern,
            str_contains($periodo, 'xix') || str_contains($periodo, 'xx') || str_contains($path, '10-siglo-xix') || str_contains($path, '11-siglo-xx') => HistoricalEra::Contemporary,
            default => HistoricalEra::PresentNature,
        };
    }

    /**
     * Determina el nivel de evidencia a partir del frontmatter.
     *
     * @param  array<string, mixed>  $meta
     */
    protected function resolveEvidenceFromMeta(array $meta): EvidenceLevel
    {
        $nivel = (string) ($meta['nivel_evidencia'] ?? '');

        return match ($nivel) {
            'consolidado' => EvidenceLevel::Consolidated,
            'documentado' => EvidenceLevel::Documented,
            'indicios' => EvidenceLevel::Tradition,
            'discutido' => EvidenceLevel::Disputed,
            default => EvidenceLevel::Documented,
        };
    }

    /**
     * Importa relaciones cruzadas, noticias y refranes adicionales.
     *
     * @param  array<string, mixed>  $extras
     * @param  array<string, Category>  $categories
     * @param  array<string, Page>  $pagesBySlug
     * @param  array<string, array{id: string, title: string, author: string, year: string, url: string}>  $officialSources
     */
    protected function importExtras(array $extras, int $userId, array $categories, array $pagesBySlug, array $officialSources = []): void
    {
        foreach ($extras['news'] ?? [] as $nItem) {
            $slug = (string) $nItem['slug'];
            $resolvedNewsSources = [];
            foreach ((array) ($nItem['source_references'] ?? []) as $sRef) {
                if (is_array($sRef)) {
                    $resolvedNewsSources[] = $sRef;
                } elseif (is_string($sRef) && isset($officialSources[trim($sRef)])) {
                    $resolvedNewsSources[] = $officialSources[trim($sRef)];
                }
            }

            $newsImg = (string) ($nItem['image'] ?? 'images/themes/faro.webp');

            $news = News::updateOrCreate(
                ['slug' => $slug],
                [
                    'title' => ['es' => $nItem['title_es'], 'en' => $nItem['title_en'] ?? $nItem['title_es']],
                    'excerpt' => ['es' => $nItem['excerpt_es'], 'en' => $nItem['excerpt_en'] ?? $nItem['excerpt_es']],
                    'content' => ['es' => $nItem['content_es'], 'en' => $nItem['content_en'] ?? $nItem['content_es']],
                    'seo_title' => ['es' => $nItem['title_es'].' | Desde Chipiona', 'en' => ($nItem['title_en'] ?? $nItem['title_es']).' | Desde Chipiona'],
                    'seo_description' => ['es' => $nItem['excerpt_es'], 'en' => $nItem['excerpt_en'] ?? $nItem['excerpt_es']],
                    'image' => $newsImg,
                    'social_image' => (string) ($nItem['social_image'] ?? $newsImg),
                    'latitude' => $nItem['latitude'] ?? null,
                    'longitude' => $nItem['longitude'] ?? null,
                    'location_name' => $nItem['location_name'] ?? 'Chipiona',
                    'evidence_level' => $nItem['evidence_level'] ?? EvidenceLevel::Consolidated,
                    'source_references' => $resolvedNewsSources,
                    'status' => ContentStatus::Published,
                    'is_active' => true,
                    'featured' => (bool) ($nItem['featured'] ?? false),
                    'user_id' => $userId,
                    'published_at' => now()->subDays(rand(1, 10)),
                ]
            );

            if (isset($nItem['category'], $categories[$nItem['category']])) {
                $news->categories()->sync([$categories[$nItem['category']]->id]);
            }

            $relIds = [];
            foreach ($nItem['related_pages'] ?? [] as $pSlug) {
                if (isset($pagesBySlug[$pSlug])) {
                    $relIds[] = $pagesBySlug[$pSlug]->id;
                }
            }
            $news->relatedPages()->sync($relIds);
        }

        foreach ($extras['proverbs'] ?? [] as $pItem) {
            Proverb::updateOrCreate(
                ['text' => $pItem['text']],
                [
                    'meaning' => $pItem['meaning'] ?? null,
                    'category' => $pItem['category'] ?? 'mar',
                    'source_code' => $pItem['source_code'] ?? null,
                    'author_name' => $pItem['author_name'] ?? 'Tradición oral de Chipiona',
                    'status' => SuggestionStatus::Approved,
                    'featured' => (bool) ($pItem['featured'] ?? false),
                    'related_page_id' => isset($pItem['related_slug'], $pagesBySlug[$pItem['related_slug']])
                        ? $pagesBySlug[$pItem['related_slug']]->id
                        : null,
                ]
            );
        }
    }
}
