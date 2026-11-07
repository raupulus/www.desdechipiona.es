<?php

namespace App\Console\Commands;

use App\Services\SitemapService;
use Illuminate\Console\Command;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate {--force : Regenerar sitemap sin confirmación}';

    protected $description = 'Genera el archivo sitemap.xml';

    public function handle(SitemapService $sitemapService): int
    {
        $this->info('Generando sitemap...');
        $sitemapService->generate();
        $this->info('Sitemap generado en: '.public_path('sitemap.xml'));

        return Command::SUCCESS;
    }
}
