<?php

namespace App\Console\Commands;

use App\Models\Page;
use App\Services\ContentAutoLinker;
use Illuminate\Console\Command;

class SanitizeContentCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chipiona:sanitize-content {--dry-run : Ejecuta el saneamiento sin persistir cambios en la base de datos}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sanea el contenido de las páginas eliminando notas de investigación, indicios internos y enlaces rotos.';

    /**
     * Execute the console command.
     */
    public function handle(ContentAutoLinker $autoLinker): int
    {
        $dryRun = (bool) $this->option('dry-run');

        $this->info('Iniciando saneamiento de contenidos...');
        if ($dryRun) {
            $this->warn('Modo DRY-RUN activado: no se aplicarán cambios a la base de datos.');
        }

        $pages = Page::all();
        $validSlugs = Page::pluck('slug')->all();
        $modifiedCount = 0;
        $modifiedSlugs = [];

        foreach ($pages as $page) {
            $changed = false;
            $updatedContent = [];
            $updatedExcerpt = [];

            foreach (['es', 'en'] as $locale) {
                $rawContent = (string) $page->getTranslation('content', $locale);
                if ($rawContent !== '') {
                    $cleaned = Page::sanitizeContentBody($rawContent);
                    $cleaned = (string) preg_replace(
                        '/\[Muscat Festival\]\(\/pages\/festival\b(?!\-del-moscatel)/u',
                        '[Muscat Festival](/pages/festival-del-moscatel)**, established in August **1966** as a major musical and wine showcase where figures such as Rocío Jurado, Raphael, Lola Flores, Julio Iglesias, Massiel, Antonio Machín, and José Mercé have performed. The third is the lyrical and popular talent of the **[Chipiona Carnival](/pages/carnaval)** (revived in 1984), which mobilizes dozens of musical groups every year. And the fourth is the **processional, choral, and rociera music** linked to the brotherhoods and the Muscat Fair, along with the broadcasting work of **Radio Chipiona (107.1 FM)** and **RTV Chipiona**.',
                        $cleaned
                    );
                    $cleaned = $autoLinker->linkContent($cleaned, $page->slug, [], $validSlugs);

                    if ($cleaned !== $rawContent) {
                        $changed = true;
                    }
                    $updatedContent[$locale] = $cleaned;
                } else {
                    $updatedContent[$locale] = $rawContent;
                }

                $rawExcerpt = (string) $page->getTranslation('excerpt', $locale);
                if ($rawExcerpt !== '') {
                    $cleanedExcerpt = Page::sanitizeContentBody($rawExcerpt);
                    $cleanedExcerpt = $autoLinker->linkContent($cleanedExcerpt, $page->slug, [], $validSlugs);

                    if ($cleanedExcerpt !== $rawExcerpt) {
                        $changed = true;
                    }
                    $updatedExcerpt[$locale] = $cleanedExcerpt;
                } else {
                    $updatedExcerpt[$locale] = $rawExcerpt;
                }
            }

            if ($changed) {
                $modifiedCount++;
                $modifiedSlugs[] = $page->slug;

                if (! $dryRun) {
                    $page->setTranslations('content', $updatedContent);
                    $page->setTranslations('excerpt', $updatedExcerpt);
                    $page->saveQuietly();
                }
            }
        }

        $this->info(sprintf(
            '✓ Saneamiento completado: %d de %d páginas %s.',
            $modifiedCount,
            $pages->count(),
            $dryRun ? 'requieren saneamiento' : 'fueron saneadas con éxito'
        ));

        if (! empty($modifiedSlugs)) {
            $this->line('Páginas afectadas: '.implode(', ', array_slice($modifiedSlugs, 0, 15)).($modifiedCount > 15 ? sprintf(' ...y %d más', $modifiedCount - 15) : ''));
        }

        return Command::SUCCESS;
    }
}
