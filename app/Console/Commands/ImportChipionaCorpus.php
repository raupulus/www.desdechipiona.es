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
use Database\Seeders\ChipionaStructureSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Yaml\Yaml;

/**
 * Comando Artisan para importar localmente el corpus privado de investigación
 * de Chipiona hacia la base de datos PostgreSQL (`desdechipiona`), categorizando
 * y relacionando automáticamente las fichas sin incluir nunca los textos privados
 * en el repositorio de código AGPLv3.
 */
class ImportChipionaCorpus extends Command
{
    /**
     * Firma del comando en consola.
     *
     * @var string
     */
    protected $signature = 'chipiona:import-corpus
                            {--path= : Ruta al directorio externo de investigación o archivo de datos local (excluido de git)}
                            {--clean : Limpia páginas previas importadas antes de sincronizar}';

    /**
     * Descripción del comando.
     *
     * @var string
     */
    protected $description = 'Importa, categoriza y relaciona el corpus privado de investigación de Chipiona desde fuera del repositorio git';

    /**
     * Ejecuta la importación del corpus privado.
     */
    public function handle(): int
    {
        $this->info('=== Importador del Corpus Privado de Desde Chipiona ===');

        // 1. Asegurar categorías, estructura base y teléfonos oficiales
        $this->call(ChipionaStructureSeeder::class);

        $author = User::where('email', 'public@raupulus.dev')->first()
            ?? User::first();

        $authorId = $author?->id ?? 1;
        $categories = Category::all()->keyBy('slug');

        if ($this->option('clean')) {
            if ($this->laravel->isProduction() && ! $this->confirm('⚠ Estás en producción. ¿Confirmas eliminar permanentemente las páginas previas antes de reimportar?', false)) {
                $this->warn('→ Limpieza previa cancelada; se realizará actualización incremental (updateOrCreate).');
            } else {
                $this->warn('→ Limpiando páginas enciclopédicas previas...');
                Page::whereNotNull('content_group')->forceDelete();
            }
        }

        // 2. Comprobar si existe un paquete local curado (gitignored) en database/seeders/data/
        $localCuratedFile = database_path('seeders/data/chipiona_corpus_pages.php');

        $pagesBySlug = DB::transaction(function () use ($localCuratedFile, $authorId, $categories): array {
            $pages = [];

            if (File::exists($localCuratedFile)) {
                $this->line('→ Importando catálogo curado local (excluido de git)...');
                /** @var array<int, array<string, mixed>> $curatedPages */
                $curatedPages = require $localCuratedFile;
                $pages = $this->importCuratedArray($curatedPages, $authorId, $categories->all());
            }

            // 3. Si se indica ruta a directorio Markdown externo (o variable services.chipiona.corpus_path), importar fichas .md
            $externalPath = $this->option('path') ?: config('services.chipiona.corpus_path');
            if (is_string($externalPath) && $externalPath !== '' && File::isDirectory($externalPath)) {
                $this->line("→ Escaneando fichas Markdown en directorio externo: {$externalPath}");
                $mdPages = $this->importMarkdownDirectory($externalPath, $authorId, $categories->all());
                $pages = array_merge($pages, $mdPages);
            }

            return $pages;
        });

        if (empty($pagesBySlug)) {
            $this->warn('⚠ No se encontró archivo local en database/seeders/data/ ni directorio externo en --path.');

            return Command::SUCCESS;
        }

        $this->info(sprintf('✓ Corpus importado y relacionado con éxito (%d páginas activas).', count($pagesBySlug)));

        return Command::SUCCESS;
    }

    /**
     * Importa un array curado local de páginas, noticias y refranes (excluido de git).
     *
     * @param  array<int, array<string, mixed>>  $items
     * @param  array<string, Category>  $categories
     * @return array<string, Page>
     */
    protected function importCuratedArray(array $items, int $userId, array $categories): array
    {
        $pagesBySlug = [];

        foreach ($items as $item) {
            $slug = (string) $item['slug'];
            /** @var ContentGroup $contentGroup */
            $contentGroup = $item['content_group'];
            $groupKey = $contentGroup->value;

            $page = Page::updateOrCreate(
                ['slug' => $slug],
                [
                    'title' => ['es' => $item['title_es'], 'en' => $item['title_en'] ?? $item['title_es']],
                    'excerpt' => ['es' => $item['excerpt_es'], 'en' => $item['excerpt_en'] ?? $item['excerpt_es']],
                    'content' => ['es' => $item['content_es'], 'en' => $item['content_es']],
                    'seo_title' => ['es' => $item['title_es'].' | Desde Chipiona', 'en' => ($item['title_en'] ?? $item['title_es']).' | Desde Chipiona'],
                    'seo_description' => ['es' => $item['excerpt_es'], 'en' => $item['excerpt_en'] ?? $item['excerpt_es']],
                    'seo_keywords' => ['es' => $item['keywords'] ?? 'Chipiona', 'en' => $item['keywords'] ?? 'Chipiona'],
                    'content_group' => $contentGroup,
                    'historical_era' => $item['historical_era'],
                    'period_label' => $item['period_label'] ?? null,
                    'year_start' => $item['year_start'] ?? null,
                    'year_end' => $item['year_end'] ?? null,
                    'evidence_level' => $item['evidence_level'] ?? EvidenceLevel::Documented,
                    'evidence_note' => ['es' => $item['evidence_note_es'] ?? '', 'en' => $item['evidence_note_es'] ?? ''],
                    'source_references' => $item['source_references'] ?? [],
                    'image' => "images/pages/{$slug}-header.webp",
                    'social_image' => "images/pages/{$slug}-social.webp",
                    'youtube_url' => $item['youtube_url'] ?? null,
                    'latitude' => $item['latitude'] ?? null,
                    'longitude' => $item['longitude'] ?? null,
                    'location_name' => $item['location_name'] ?? 'Chipiona (Cádiz)',
                    'status' => ContentStatus::Published,
                    'is_active' => true,
                    'featured' => (bool) ($item['featured'] ?? false),
                    'user_id' => $userId,
                    'published_at' => now()->subDays(rand(1, 25)),
                ]
            );

            if (isset($categories[$groupKey])) {
                $page->categories()->sync([$categories[$groupKey]->id]);
            }

            $pagesBySlug[$slug] = $page;
        }

        // Enlazar páginas relacionadas si existe archivo local de relaciones o metadatos
        $extraFile = database_path('seeders/data/chipiona_corpus_extras.php');
        if (File::exists($extraFile)) {
            $extras = require $extraFile;
            $this->importExtras($extras, $userId, $categories, $pagesBySlug);
        }

        return $pagesBySlug;
    }

    /**
     * Importa relaciones cruzadas, noticias y refranes desde el archivo local excluido de git.
     *
     * @param  array<string, mixed>  $extras
     * @param  array<string, Category>  $categories
     * @param  array<string, Page>  $pagesBySlug
     */
    protected function importExtras(array $extras, int $userId, array $categories, array $pagesBySlug): void
    {
        foreach ($extras['relations'] ?? [] as $slug => $targetSlugs) {
            if (! isset($pagesBySlug[$slug])) {
                continue;
            }
            $ids = [];
            foreach ($targetSlugs as $tSlug) {
                if (isset($pagesBySlug[$tSlug])) {
                    $ids[] = $pagesBySlug[$tSlug]->id;
                }
            }
            $pagesBySlug[$slug]->relatedPages()->sync($ids);
        }

        foreach ($extras['news'] ?? [] as $nItem) {
            $slug = (string) $nItem['slug'];
            $news = News::updateOrCreate(
                ['slug' => $slug],
                [
                    'title' => ['es' => $nItem['title_es'], 'en' => $nItem['title_en'] ?? $nItem['title_es']],
                    'excerpt' => ['es' => $nItem['excerpt_es'], 'en' => $nItem['excerpt_en'] ?? $nItem['excerpt_es']],
                    'content' => ['es' => $nItem['content_es'], 'en' => $nItem['content_es']],
                    'seo_title' => ['es' => $nItem['title_es'].' | Desde Chipiona', 'en' => ($nItem['title_en'] ?? $nItem['title_es']).' | Desde Chipiona'],
                    'seo_description' => ['es' => $nItem['excerpt_es'], 'en' => $nItem['excerpt_en'] ?? $nItem['excerpt_es']],
                    'image' => "images/news/{$slug}-header.webp",
                    'social_image' => "images/news/{$slug}-social.webp",
                    'latitude' => $nItem['latitude'] ?? null,
                    'longitude' => $nItem['longitude'] ?? null,
                    'location_name' => $nItem['location_name'] ?? 'Chipiona',
                    'evidence_level' => $nItem['evidence_level'] ?? EvidenceLevel::Consolidated,
                    'source_references' => $nItem['source_references'] ?? [],
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

    /**
     * Escanea e importa archivos Markdown con frontmatter YAML desde un directorio externo.
     *
     * @param  array<string, Category>  $categories
     * @return array<string, Page>
     */
    protected function importMarkdownDirectory(string $directory, int $userId, array $categories): array
    {
        $imported = [];
        $subdirs = ['02-historia', '03-actualidad'];

        foreach ($subdirs as $sub) {
            $targetDir = rtrim($directory, '/').'/'.$sub;
            if (! File::isDirectory($targetDir)) {
                continue;
            }

            foreach (File::allFiles($targetDir) as $file) {
                if ($file->getExtension() !== 'md' || strtolower($file->getFilename()) === 'readme.md' || strtolower($file->getFilename()) === 'panorama.md') {
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

                $slug = (string) ($meta['slug'] ?? Str::slug((string) $meta['titulo']));
                if (Page::where('slug', $slug)->exists()) {
                    $imported[$slug] = Page::where('slug', $slug)->first();

                    continue;
                }

                $body = trim($matches[2]);
                $group = $this->resolveGroupFromMeta($meta, $file->getPathname());
                $era = $this->resolveEraFromMeta($meta);
                $evidence = $this->resolveEvidenceFromMeta($meta);

                $years = array_filter((array) ($meta['anios_clave'] ?? []), 'is_numeric');
                $yearStart = ! empty($years) ? (int) min($years) : null;
                $yearEnd = ! empty($years) ? (int) max($years) : null;

                $excerpt = Str::limit(strip_tags(Str::markdown($body)), 210);

                $page = Page::updateOrCreate(
                    ['slug' => $slug],
                    [
                        'title' => ['es' => (string) $meta['titulo'], 'en' => (string) $meta['titulo']],
                        'excerpt' => ['es' => $excerpt, 'en' => $excerpt],
                        'content' => ['es' => $body, 'en' => $body],
                        'seo_title' => ['es' => $meta['titulo'].' | Desde Chipiona', 'en' => $meta['titulo'].' | Desde Chipiona'],
                        'seo_description' => ['es' => $excerpt, 'en' => $excerpt],
                        'content_group' => $group,
                        'historical_era' => $era,
                        'period_label' => isset($meta['siglos']) && is_array($meta['siglos']) ? 'Siglos '.implode(', ', $meta['siglos']) : null,
                        'year_start' => $yearStart,
                        'year_end' => $yearEnd,
                        'evidence_level' => $evidence,
                        'source_references' => array_values((array) ($meta['fuentes'] ?? [])),
                        'location_name' => (string) ($meta['lugar'] ?? 'Chipiona (Cádiz)'),
                        'status' => ContentStatus::Published,
                        'is_active' => true,
                        'featured' => false,
                        'user_id' => $userId,
                        'published_at' => now(),
                    ]
                );

                if (isset($categories[$group->value])) {
                    $page->categories()->sync([$categories[$group->value]->id]);
                }

                $imported[$slug] = $page;
            }
        }

        return $imported;
    }

    /**
     * Determina el ContentGroup a partir del frontmatter de una ficha.
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
            $sub === 'fauna' => ContentGroup::Fauna,
            $tipo === 'naturaleza' || str_contains($path, '/naturaleza/') => ContentGroup::Flora,
            $cat === 'historia' || str_contains($path, '02-historia') => ContentGroup::Historia,
            default => ContentGroup::Historia,
        };
    }

    /**
     * Determina la época histórica a partir del frontmatter.
     *
     * @param  array<string, mixed>  $meta
     */
    protected function resolveEraFromMeta(array $meta): HistoricalEra
    {
        $periodo = (string) ($meta['periodo'] ?? '');

        return match (true) {
            str_contains($periodo, 'prehistoria'), str_contains($periodo, 'roma'), str_contains($periodo, 'tartessos') => HistoricalEra::PrehistoryAntiquity,
            str_contains($periodo, 'andalus'), str_contains($periodo, 'edad-media'), str_contains($periodo, 'visigodo') => HistoricalEra::MiddleAges,
            str_contains($periodo, 'moderna'), str_contains($periodo, 'xviii') => HistoricalEra::EarlyModern,
            str_contains($periodo, 'xix'), str_contains($periodo, 'xx') => HistoricalEra::Contemporary,
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
}
