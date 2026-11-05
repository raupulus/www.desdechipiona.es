<?php

namespace Tests\Feature\Web;

use App\Enums\ContentGroup;
use App\Enums\ContentStatus;
use App\Enums\EvidenceLevel;
use App\Enums\HistoricalEra;
use App\Enums\SuggestionStatus;
use App\Models\News;
use App\Models\OfficialPhone;
use App\Models\Page;
use App\Models\Post;
use App\Models\Proverb;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Suite de auditoría integral de SEO técnico, metadatos dinámicos, JSON-LD,
 * internacionalización (ES/EN) y garantías de diseño responsive mobile-first.
 */
class SeoAndResponsiveAuditTest extends TestCase
{
    use RefreshDatabase;

    private Page $page;

    private News $news;

    private Post $post;

    protected function setUp(): void
    {
        parent::setUp();

        $author = User::factory()->create(['name' => 'Raúl Caro Pastorino']);

        $this->page = Page::create([
            'slug' => 'faro-de-chipiona-test',
            'title' => [
                'es' => 'El Faro de Chipiona',
                'en' => 'The Chipiona Lighthouse',
            ],
            'excerpt' => [
                'es' => 'El faro más alto de España sobre la restinga del Perro en Chipiona.',
                'en' => 'The tallest lighthouse in Spain on the Restinga del Perro reef in Chipiona.',
            ],
            'content' => [
                'es' => '<p>Proyectado por Jaime Font y encendido en 1867.</p>',
                'en' => '<p>Designed by Jaime Font and lit in 1867.</p>',
            ],
            'status' => ContentStatus::Published,
            'is_active' => true,
            'published_at' => now()->subDay(),
            'content_group' => ContentGroup::Monumentos,
            'historical_era' => HistoricalEra::Contemporary,
            'evidence_level' => EvidenceLevel::Consolidated,
            'latitude' => 36.7378,
            'longitude' => -6.4414,
            'location_name' => 'Punta del Perro, Chipiona',
        ]);

        $this->news = News::create([
            'slug' => 'hallazgo-archivo-chipiona',
            'title' => [
                'es' => 'Nuevo hallazgo documental sobre la Carta Puebla de 1477',
                'en' => 'New documentary finding on the 1477 Town Charter',
            ],
            'excerpt' => [
                'es' => 'Investigación de archivo sobre la repoblación de Chipiona en el siglo XV.',
                'en' => 'Archival research on the 15th-century repopulation of Chipiona.',
            ],
            'content' => [
                'es' => '<p>Texto íntegro del hallazgo documental.</p>',
                'en' => '<p>Full text of the documentary finding.</p>',
            ],
            'status' => ContentStatus::Published,
            'is_active' => true,
            'published_at' => now()->subHours(6),
            'evidence_level' => EvidenceLevel::Consolidated,
        ]);

        $this->post = Post::create([
            'user_id' => $author->id,
            'slug' => 'cronica-corrales-chipiona',
            'title' => [
                'es' => 'Crónica de los Corrales de Pesca',
                'en' => 'Chronicle of the Fishing Corrals',
            ],
            'excerpt' => [
                'es' => ' Arte milenario de piedra ostionera en la costa de Chipiona.',
                'en' => 'Ancient oyster-stone fishing traps on the coast of Chipiona.',
            ],
            'content' => [
                'es' => '<p>Contenido del artículo de blog.</p>',
                'en' => '<p>Blog post content.</p>',
            ],
            'status' => ContentStatus::Published,
            'is_active' => true,
            'published_at' => now()->subDays(2),
        ]);

        Proverb::create([
            'text' => 'Cuando el Faro relumbra en claro, viento de Poniente al canto.',
            'meaning' => 'Indica que la visibilidad nítida hacia poniente anuncia brisa suave.',
            'category' => 'mar',
            'status' => SuggestionStatus::Approved,
            'featured' => true,
        ]);

        OfficialPhone::create([
            'name' => 'Policía Local de Chipiona',
            'category' => 'emergencias',
            'phone' => '956 92 90 60',
            'is_emergency' => true,
            'is_active' => true,
            'order' => 1,
        ]);
    }

    public function test_all_public_routes_render_complete_seo_metatags_single_h1_and_valid_json_ld_in_es_and_en(): void
    {
        $routes = [
            ['url' => route('home'), 'is_interior' => false, 'is_article' => false],
            ['url' => route('pages.show', $this->page->slug), 'is_interior' => true, 'is_article' => true],
            ['url' => route('news.index'), 'is_interior' => true, 'is_article' => false],
            ['url' => route('news.show', $this->news->slug), 'is_interior' => true, 'is_article' => true],
            ['url' => route('proverbs.index'), 'is_interior' => true, 'is_article' => false],
            ['url' => route('weather.index'), 'is_interior' => true, 'is_article' => false],
            ['url' => route('phones.index'), 'is_interior' => true, 'is_article' => false],
            ['url' => route('suggestions.create'), 'is_interior' => true, 'is_article' => false],
            ['url' => route('contact'), 'is_interior' => true, 'is_article' => false],
            ['url' => route('about'), 'is_interior' => true, 'is_article' => false],
            ['url' => route('legal'), 'is_interior' => true, 'is_article' => false],
            ['url' => route('privacy'), 'is_interior' => true, 'is_article' => false],
            ['url' => route('cookies'), 'is_interior' => true, 'is_article' => false],
        ];

        foreach (['es', 'en'] as $locale) {
            foreach ($routes as $item) {
                $url = $item['url'].'?lang='.$locale;
                $response = $this->get($url);
                $response->assertOk();

                $html = $response->getContent();
                $this->assertIsString($html);

                // 1. Exactamente un único <h1> por vista pública
                $h1Count = preg_match_all('/<h1\b[^>]*>/i', $html);
                $this->assertSame(1, $h1Count, "La ruta {$url} debe contener exactamente un único <h1> (encontrados: {$h1Count}).");

                // 2. <title> y <meta name="description"> no vacíos y sin saltos de línea crudos
                $this->assertMatchesRegularExpression('/<title>[^<]{10,}<\/title>/u', $html, "Falta <title> válido en {$url}");
                $this->assertMatchesRegularExpression('/<meta\s+name="description"\s+content="[^"\r\n]{20,}"\s*\/>/u', $html, "Falta <meta name=\"description\"> limpio en {$url}");

                // 3. Canonical y hreflang bidireccional (es, en, x-default)
                $this->assertMatchesRegularExpression('/<link\s+rel="canonical"\s+href="https?:\/\/[^"]+"\s*\/>/i', $html);
                $this->assertStringContainsString('rel="alternate" hreflang="es"', $html);
                $this->assertStringContainsString('rel="alternate" hreflang="en"', $html);
                $this->assertStringContainsString('rel="alternate" hreflang="x-default"', $html);

                // 4. Open Graph y Twitter Cards con imagen WebP obligatoria
                $this->assertStringContainsString('<meta property="og:image:type" content="image/webp" />', $html);
                $this->assertStringContainsString('<meta property="og:image:width" content="1200" />', $html);
                $this->assertStringContainsString('<meta property="og:image:height" content="630" />', $html);
                $this->assertMatchesRegularExpression('/<meta\s+property="og:image"\s+content="https?:\/\/[^"]+\.webp"\s*\/>/i', $html, "og:image debe ser URL absoluta .webp en {$url}");
                $this->assertStringContainsString('<meta name="twitter:card" content="summary_large_image" />', $html);

                // 5. Extracción y validación sintáctica de JSON-LD Schema.org
                $hasJsonLd = preg_match('/<script type="application\/ld\+json">\s*(\{.*?\})\s*<\/script>/s', $html, $matches);
                $this->assertSame(1, $hasJsonLd, "Falta bloque JSON-LD en {$url}");

                $jsonLd = json_decode($matches[1], true);
                $this->assertIsArray($jsonLd, "JSON-LD inválido en {$url}");
                $this->assertSame('https://schema.org', $jsonLd['@context'] ?? null);
                $this->assertIsArray($jsonLd['@graph'] ?? null);

                $types = array_column($jsonLd['@graph'], '@type');
                $this->assertContains('Organization', $types, "Falta Organization en JSON-LD de {$url}");
                $this->assertContains('WebSite', $types, "Falta WebSite en JSON-LD de {$url}");

                // Verificar foundingDate = 2008 en Organization
                $orgNode = collect($jsonLd['@graph'])->firstWhere('@type', 'Organization');
                $this->assertSame('2008', $orgNode['foundingDate'] ?? null);

                // Verificar BreadcrumbList en todas las páginas interiores
                if ($item['is_interior']) {
                    $this->assertContains('BreadcrumbList', $types, "Falta BreadcrumbList en la ruta interior {$url}");
                }
            }
        }
    }

    public function test_geolocated_page_emits_place_and_geocoordinates_in_json_ld(): void
    {
        $response = $this->get(route('pages.show', $this->page->slug));
        $response->assertOk();

        preg_match('/<script type="application\/ld\+json">\s*(\{.*?\})\s*<\/script>/s', (string) $response->getContent(), $matches);
        $jsonLd = json_decode($matches[1], true);

        $placeNode = collect($jsonLd['@graph'])->firstWhere('@type', 'Place');
        $this->assertNotNull($placeNode);
        $this->assertSame('Punta del Perro, Chipiona', $placeNode['name']);
        $this->assertSame(36.7378, $placeNode['geo']['latitude']);
        $this->assertSame(-6.4414, $placeNode['geo']['longitude']);
    }

    public function test_robots_directives_protect_crawl_budget_on_faceted_search_and_legal_pages(): void
    {
        // Home limpia: index,follow
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('<meta name="robots" content="index,follow" />', false);

        // Búsqueda con filtros facetados: noindex,follow y canonical hacia la base
        $filtered = $this->get(route('home', ['q' => 'faro', 'era' => 'contemporanea']));
        $filtered->assertOk();
        $filtered->assertSee('<meta name="robots" content="noindex,follow" />', false);
        $filtered->assertSee('<link rel="canonical" href="'.route('home').'" />', false);

        // Páginas de privacidad y cookies: noindex,follow
        $this->get(route('privacy'))
            ->assertOk()
            ->assertSee('<meta name="robots" content="noindex,follow" />', false);

        $this->get(route('cookies'))
            ->assertOk()
            ->assertSee('<meta name="robots" content="noindex,follow" />', false);
    }

    public function test_responsive_layout_guards_prevent_dropdown_clipping_and_horizontal_overflow(): void
    {
        $homeHtml = (string) $this->get(route('home'))->getContent();

        // El hero de Home no debe tener overflow-hidden que recorte los desplegables de <x-searchable-select>
        $this->assertStringContainsString('overflow-x-hidden', $homeHtml);
        $this->assertStringNotContainsString(
            '<section class="relative overflow-hidden bg-gradient-to-br from-slate-950',
            $homeHtml
        );

        // La caja de cita bibliográfica de las páginas tiene protección contra desbordamiento horizontal en móviles de 320px
        $pageHtml = (string) $this->get(route('pages.show', $this->page->slug))->getContent();
        $this->assertStringContainsString('[overflow-wrap:anywhere]', $pageHtml);
    }

    public function test_markdown_h1_demotion_and_broken_corpus_image_stripping(): void
    {
        $corpusPage = Page::create([
            'slug' => 'corpora-test-h1-and-images',
            'title' => [
                'es' => 'Título de Prueba del Corpus',
                'en' => 'Corpus Test Title',
            ],
            'excerpt' => [
                'es' => 'Extracto de prueba para validar la limpieza del contenido Markdown.',
                'en' => 'Test excerpt to validate Markdown content cleaning.',
            ],
            'content' => [
                'es' => "# Título Duplicado\n\nTexto con imagen rota ![Foto](../../04-recursos/imagenes/antigua.jpg) y enlace a [Archivo](../../04-recursos/mapas/mapa.jpg).\n\n# Otra Sección H1\n\nPárrafo final.",
                'en' => "# Duplicate Title\n\nText with broken image ![Photo](../../04-recursos/imagenes/antigua.jpg) and link to [File](../../04-recursos/mapas/mapa.jpg).\n\n# Another H1 Section\n\nFinal paragraph.",
            ],
            'status' => ContentStatus::Published,
            'is_active' => true,
            'published_at' => now()->subDay(),
            'content_group' => ContentGroup::Historia,
            'historical_era' => HistoricalEra::Contemporary,
            'evidence_level' => EvidenceLevel::Consolidated,
        ]);

        $response = $this->get(route('pages.show', $corpusPage->slug).'?lang=es');
        $response->assertOk();

        $html = (string) $response->getContent();
        $h1Count = preg_match_all('/<h1\b[^>]*>/i', $html);
        $this->assertSame(1, $h1Count, 'Debe existir únicamente 1 etiqueta <h1> incluso si el Markdown original contenía encabezados #.');
        $this->assertStringNotContainsString('04-recursos', $html);
        $this->assertStringNotContainsString('antigua.jpg', $html);
        $this->assertStringContainsString('Otra Sección H1', $html);
    }

    public function test_default_locale_query_param_canonicalizes_to_clean_url(): void
    {
        $pageUrl = route('pages.show', $this->page->slug);

        // ?lang=es debe apuntar su canonical a la URL limpia sin ?lang=es
        $esResponse = $this->get($pageUrl.'?lang=es');
        $esResponse->assertOk();
        $esResponse->assertSee('<link rel="canonical" href="'.$pageUrl.'" />', false);

        // ?lang=en debe mantener ?lang=en en su canonical
        $enResponse = $this->get($pageUrl.'?lang=en');
        $enResponse->assertOk();
        $enResponse->assertSee('<link rel="canonical" href="'.$pageUrl.'?lang=en" />', false);
    }
}
