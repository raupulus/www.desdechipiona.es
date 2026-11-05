<?php

namespace App\Services;

use App\Models\News;
use App\Models\OfficialPhone;
use App\Models\Page;
use App\Models\Post;
use App\Models\Proverb;
use Illuminate\Support\Carbon;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

/**
 * Servicio para la generación del sitemap XML del sitio web.
 * Genera un sitemap con todas las URLs públicas indexables del sitio:
 * páginas estáticas, posts publicados y páginas dinámicas (excluyendo legales con noindex).
 * Incluye alternates hreflang para multi-idioma (es/en) y x-default.
 */
class SitemapService
{
    /**
     * Slugs de páginas legales que llevan directiva noindex,follow y no deben incluirse en el sitemap.
     *
     * @var array<int, string>
     */
    private const NOINDEX_PAGE_SLUGS = [
        'privacy',
        'cookies',
        'terms',
        'legal-notice',
    ];

    /**
     * Genera el archivo sitemap.xml con todas las URLs públicas indexables del sitio.
     */
    public function generate(?string $outputPath = null): void
    {
        $sitemap = Sitemap::create();

        // Añadir URLs estáticas del sitio
        $this->addStaticUrls($sitemap);

        // Módulo de Blog (Posts) desactivado temporalmente en el frontend:
        // $this->addPosts($sitemap);

        // Añadir páginas dinámicas publicadas (excluyendo páginas legales noindex)
        $this->addPages($sitemap);

        // Añadir noticias publicadas
        $this->addNews($sitemap);

        // Guardar el sitemap en el directorio público (o en ruta de test si estamos en entorno testing)
        $targetPath = $outputPath ?? (app()->environment('testing')
            ? storage_path('framework/testing/sitemap.xml')
            : public_path('sitemap.xml'));

        if (! is_dir(dirname($targetPath))) {
            mkdir(dirname($targetPath), 0755, true);
        }

        $sitemap->writeToFile($targetPath);
    }

    /**
     * Añade alternates hreflang multi-idioma y x-default a una URL.
     */
    private function addHreflangAlternates(Url $url, string $baseUrl): Url
    {
        foreach (config('locales.available', ['es', 'en']) as $locale) {
            $url->addAlternate(
                url: $baseUrl.'?lang='.$locale,
                locale: $locale
            );
        }

        $url->addAlternate(
            url: $baseUrl,
            locale: 'x-default'
        );

        return $url;
    }

    /**
     * Añade las URLs estáticas del sitio al sitemap con lastmod real por módulo.
     */
    private function addStaticUrls(Sitemap $sitemap): void
    {
        $latestPageDate = Page::publishedAndActive()->max('updated_at');
        $latestNewsDate = News::publishedAndActive()->max('updated_at');
        $latestProverbDate = Proverb::approved()->max('updated_at');
        $latestPhoneDate = OfficialPhone::active()->max('updated_at');

        $pageMod = $latestPageDate ? Carbon::parse($latestPageDate) : now();
        $newsMod = $latestNewsDate ? Carbon::parse($latestNewsDate) : $pageMod;
        $proverbMod = $latestProverbDate ? Carbon::parse($latestProverbDate) : $pageMod;
        $phoneMod = $latestPhoneDate ? Carbon::parse($latestPhoneDate) : $pageMod;
        $homeMod = collect([$pageMod, $newsMod, $proverbMod])->max() ?? now();

        $homeUrl = url('/');
        $sitemap->add(
            $this->addHreflangAlternates(
                Url::create($homeUrl)
                    ->setLastModificationDate($homeMod)
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                    ->setPriority(1.0),
                $homeUrl
            )
        );

        $aboutUrl = route('about');
        $sitemap->add(
            $this->addHreflangAlternates(
                Url::create($aboutUrl)
                    ->setLastModificationDate($pageMod)
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                    ->setPriority(0.5),
                $aboutUrl
            )
        );

        $newsUrl = route('news.index');
        $sitemap->add(
            $this->addHreflangAlternates(
                Url::create($newsUrl)
                    ->setLastModificationDate($newsMod)
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                    ->setPriority(0.8),
                $newsUrl
            )
        );

        $proverbsUrl = route('proverbs.index');
        $sitemap->add(
            $this->addHreflangAlternates(
                Url::create($proverbsUrl)
                    ->setLastModificationDate($proverbMod)
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                    ->setPriority(0.7),
                $proverbsUrl
            )
        );

        $weatherUrl = route('weather.index');
        $sitemap->add(
            $this->addHreflangAlternates(
                Url::create($weatherUrl)
                    ->setLastModificationDate(now())
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_HOURLY)
                    ->setPriority(0.8),
                $weatherUrl
            )
        );

        $phonesUrl = route('phones.index');
        $sitemap->add(
            $this->addHreflangAlternates(
                Url::create($phonesUrl)
                    ->setLastModificationDate($phoneMod)
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                    ->setPriority(0.8),
                $phonesUrl
            )
        );

        $suggestionsUrl = route('suggestions.create');
        $sitemap->add(
            $this->addHreflangAlternates(
                Url::create($suggestionsUrl)
                    ->setLastModificationDate($pageMod)
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                    ->setPriority(0.6),
                $suggestionsUrl
            )
        );

        $legalUrl = route('legal');
        $sitemap->add(
            $this->addHreflangAlternates(
                Url::create($legalUrl)
                    ->setLastModificationDate($pageMod)
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_YEARLY)
                    ->setPriority(0.4),
                $legalUrl
            )
        );

        $contactUrl = route('contact');
        $sitemap->add(
            $this->addHreflangAlternates(
                Url::create($contactUrl)
                    ->setLastModificationDate($pageMod)
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                    ->setPriority(0.4),
                $contactUrl
            )
        );
    }

    /**
     * Añade todos los posts publicados al sitemap usando chunking para eficiencia en memoria ([BDB-04]).
     */
    private function addPosts(Sitemap $sitemap): void
    {
        Post::publishedAndActive()
            ->select(['id', 'slug', 'updated_at', 'published_at'])
            ->orderByDesc('published_at')
            ->chunk(200, function ($posts) use ($sitemap): void {
                foreach ($posts as $post) {
                    $postUrl = route('blog.show', $post->slug);
                    $sitemap->add(
                        $this->addHreflangAlternates(
                            Url::create($postUrl)
                                ->setLastModificationDate($post->updated_at ?? now())
                                ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                                ->setPriority(0.7),
                            $postUrl
                        )
                    );
                }
            });
    }

    /**
     * Añade todas las páginas dinámicas publicadas al sitemap (excluyendo páginas con noindex).
     */
    private function addPages(Sitemap $sitemap): void
    {
        Page::publishedAndActive()
            ->select(['id', 'slug', 'updated_at'])
            ->whereNotIn('slug', self::NOINDEX_PAGE_SLUGS)
            ->chunk(200, function ($pages) use ($sitemap): void {
                foreach ($pages as $page) {
                    $pageUrl = route('pages.show', $page->slug);
                    $sitemap->add(
                        $this->addHreflangAlternates(
                            Url::create($pageUrl)
                                ->setLastModificationDate($page->updated_at ?? now())
                                ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                                ->setPriority(0.8),
                            $pageUrl
                        )
                    );
                }
            });
    }

    /**
     * Añade todas las noticias publicadas al sitemap.
     */
    private function addNews(Sitemap $sitemap): void
    {
        News::publishedAndActive()
            ->select(['id', 'slug', 'updated_at', 'published_at'])
            ->orderByDesc('published_at')
            ->chunk(200, function ($newsItems) use ($sitemap): void {
                foreach ($newsItems as $news) {
                    $newsUrl = route('news.show', $news->slug);
                    $sitemap->add(
                        $this->addHreflangAlternates(
                            Url::create($newsUrl)
                                ->setLastModificationDate($news->updated_at ?? now())
                                ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                                ->setPriority(0.7),
                            $newsUrl
                        )
                    );
                }
            });
    }
}
