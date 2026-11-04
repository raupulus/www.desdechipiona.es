<?php

namespace Tests\Feature\Web;

use App\Enums\ContentGroup;
use App\Enums\EvidenceLevel;
use App\Enums\HistoricalEra;
use App\Enums\SuggestionStatus;
use App\Models\News;
use App\Models\OfficialPhone;
use App\Models\Page;
use App\Models\Proverb;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests integrales de las funcionalidades públicas de Desde Chipiona:
 * - Buscador y filtrado multicriterio (por apartado, época, años, evidencia y mapa).
 * - Páginas enciclopédicas con páginas enlazadas, vídeo de YouTube y coordenadas.
 * - Sección independiente de Noticias (/news).
 * - Refranero popular (/proverbs) y envío de refranes.
 * - Formulario de sugerencias de contenido (/suggestions), correcciones (/corrections) y comentarios (/comments).
 * - Identificación social ligera con Facebook/Google sin cuentas locales.
 * - El Tiempo y Mareas (AEMET) (/weather), Teléfonos Oficiales (/phones) y Aviso Legal (/legal).
 */
class ChipionaFeaturesTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_explorer_filters_by_group_era_evidence_and_map(): void
    {
        $faro = Page::factory()->published()->withCoordinates()->create([
            'slug' => 'faro-de-chipiona',
            'title' => ['es' => 'El Faro de Chipiona', 'en' => 'Chipiona Lighthouse'],
            'content_group' => ContentGroup::Monumentos,
            'historical_era' => HistoricalEra::Contemporary,
            'year_start' => 1863,
            'year_end' => 1867,
            'evidence_level' => EvidenceLevel::Consolidated,
        ]);

        $leyenda = Page::factory()->published()->create([
            'slug' => 'leyenda-virgen-regla',
            'title' => ['es' => 'Leyenda de la Virgen de Regla', 'en' => 'Legend of Regla'],
            'content_group' => ContentGroup::Historia,
            'historical_era' => HistoricalEra::MiddleAges,
            'year_start' => 1330,
            'evidence_level' => EvidenceLevel::Tradition,
            'latitude' => null,
            'longitude' => null,
        ]);

        // Filtro por grupo 'monumentos' (en idioma español)
        $this->withSession(['locale' => 'es'])
            ->get(route('home', ['group' => 'monumentos']))
            ->assertStatus(200)
            ->assertSee('El Faro de Chipiona')
            ->assertDontSee('Leyenda de la Virgen de Regla');

        // Filtro por nivel de evidencia 'tradicion'
        $this->withSession(['locale' => 'es'])
            ->get(route('home', ['evidence' => 'tradicion']))
            ->assertStatus(200)
            ->assertSee('Leyenda de la Virgen de Regla')
            ->assertDontSee('El Faro de Chipiona');

        // Filtro por época 'contemporanea' y con coordenadas GPS
        $this->withSession(['locale' => 'es'])
            ->get(route('home', ['era' => 'contemporanea', 'map' => 1]))
            ->assertStatus(200)
            ->assertSee('El Faro de Chipiona')
            ->assertDontSee('Leyenda de la Virgen de Regla');
    }

    public function test_home_renders_bilingual_labels_in_spanish_and_english(): void
    {
        $page = Page::factory()->published()->create([
            'slug' => 'faro-de-chipiona',
            'title' => ['es' => 'El Faro de Chipiona', 'en' => 'Chipiona Lighthouse'],
            'content_group' => ContentGroup::Monumentos,
            'evidence_level' => EvidenceLevel::Consolidated,
        ]);

        $related = Page::factory()->published()->create([
            'slug' => 'las-canteras',
            'title' => ['es' => 'Playa de Las Canteras', 'en' => 'Las Canteras Beach'],
        ]);
        $page->relatedPages()->attach($related->id);

        News::factory()->published()->create([
            'title' => ['es' => 'Nueva excavación', 'en' => 'New excavation'],
            'content' => ['es' => 'Detalle', 'en' => 'Detail'],
        ]);

        // Versión en español
        $this->withSession(['locale' => 'es'])
            ->get(route('home'))
            ->assertStatus(200)
            ->assertSee('Explorador de Contenidos de Chipiona')
            ->assertSee('Nuestro compromiso con la verdad histórica:')
            ->assertSee('Época histórica o siglo:')
            ->assertSee('Rigor documental:')
            ->assertSee('Rango cronológico (años):')
            ->assertSee('Páginas enlazadas:')
            ->assertSee('Leer reportaje completo')
            ->assertSee('Ver todas las noticias');

        // Versión en inglés
        $this->withSession(['locale' => 'en'])
            ->get(route('home'))
            ->assertStatus(200)
            ->assertSee('Chipiona Content Explorer')
            ->assertSee('Our commitment to historical truth:')
            ->assertSee('Historical era or century:')
            ->assertSee('Documentary rigor:')
            ->assertSee('Year range:')
            ->assertSee('Linked pages:')
            ->assertSee('Read full story')
            ->assertSee('View all news');
    }

    public function test_page_show_renders_linked_pages_coordinates_youtube_and_evidence_banner(): void
    {
        $salmedina = Page::factory()->published()->create([
            'slug' => 'bajo-de-salmedina',
            'title' => ['es' => 'El Bajo de Salmedina', 'en' => 'Salmedina Reef'],
        ]);

        $faro = Page::factory()->published()->withCoordinates()->create([
            'slug' => 'faro-de-chipiona',
            'title' => ['es' => 'El Faro de Chipiona', 'en' => 'Chipiona Lighthouse'],
            'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'source_references' => [
                ['id' => 'F-0071', 'title' => 'Archivo Histórico Provincial de Cádiz', 'author' => 'Ministerio de Fomento', 'year' => 1867],
            ],
        ]);

        $faro->relatedPages()->attach($salmedina->id);

        $this->withSession(['locale' => 'es'])
            ->get(route('pages.show', $faro->slug))
            ->assertStatus(200)
            ->assertSee('El Faro de Chipiona')
            ->assertSee('El Bajo de Salmedina')
            ->assertSee('Archivo Histórico Provincial de Cádiz')
            ->assertSee('fuente oficial verificada')
            ->assertSee('maps.google.com');
    }

    public function test_independent_news_section_and_detail_work(): void
    {
        $news = News::factory()->published()->create([
            'slug' => 'nuevo-legajo-carta-puebla',
            'title' => ['es' => 'Digitalizado el legajo de la Carta Puebla', 'en' => 'Charter digitized'],
        ]);

        $this->withSession(['locale' => 'es'])
            ->get(route('news.index'))
            ->assertStatus(200)
            ->assertSee('Digitalizado el legajo de la Carta Puebla');

        $this->withSession(['locale' => 'es'])
            ->get(route('news.show', $news->slug))
            ->assertStatus(200)
            ->assertSee('Digitalizado el legajo de la Carta Puebla');
    }

    public function test_unauthenticated_visitor_cannot_submit_proverb_suggestion_correction_or_comment(): void
    {
        $page = Page::factory()->published()->create(['slug' => 'corrales-de-pesca']);

        // 1. Subir refrán sin OAuth debe fallar con error 'auth'
        $this->post(route('proverbs.store'), [
            'text' => 'Con Levante en la punta, el pescado se junta.',
            'meaning' => 'Cuando sopla Levante templado entra pescado al corral.',
            'category' => 'mar',
            'privacy' => '1',
            'cf-turnstile-response' => 'test-token',
        ])->assertSessionHasErrors('auth');

        $this->assertDatabaseCount('proverbs', 0);

        // 2. Enviar sugerencia sin OAuth debe fallar con error 'auth'
        $this->post(route('suggestions.store'), [
            'content_group' => ContentGroup::Gastronomia->value,
            'title' => 'Receta tradicional de la telera en sarmientos',
            'body' => 'Se prepara con sarmientos de las viñas de moscatel y pescado fresco de roca.',
            'privacy' => '1',
            'cf-turnstile-response' => 'test-token',
        ])->assertSessionHasErrors('auth');

        $this->assertDatabaseCount('content_suggestions', 0);

        // 3. Enviar corrección sin OAuth debe fallar con error 'auth'
        $this->post(route('corrections.store'), [
            'correctable_type' => 'page',
            'correctable_id' => $page->id,
            'proposed_correction' => 'El dato documental exacto figura en el pleito de 1536.',
            'privacy' => '1',
            'cf-turnstile-response' => 'test-token',
        ])->assertSessionHasErrors('auth');

        $this->assertDatabaseCount('correction_requests', 0);

        // 4. Enviar comentario sin OAuth debe fallar con error 'auth'
        $this->post(route('comments.store'), [
            'commentable_type' => 'page',
            'commentable_id' => $page->id,
            'body' => 'Qué gran recuerdo de los corrales con mi abuelo.',
            'privacy' => '1',
            'cf-turnstile-response' => 'test-token',
        ])->assertSessionHasErrors('auth');

        $this->assertDatabaseCount('comments', 0);
    }

    public function test_visitor_can_submit_correction_comment_content_suggestion_and_proverb(): void
    {
        $page = Page::factory()->published()->create(['slug' => 'corrales-de-pesca']);

        // 1. Identificación social ligera simulada
        $this->get(route('social.redirect', ['provider' => 'facebook']))
            ->assertRedirect();
        $this->assertNotNull(session('public_social_identity'));

        // 2. Enviar propuesta de corrección sobre una página
        $this->post(route('corrections.store'), [
            'correctable_type' => 'page',
            'correctable_id' => $page->id,
            'quoted_text' => 'Dato antiguo',
            'proposed_correction' => 'El dato documental exacto figura en el pleito de 1536.',
            'sources_info' => 'Archivo Ducal, F-0042',
            'privacy' => '1',
            'cf-turnstile-response' => 'test-token',
        ])->assertRedirect();

        $this->assertDatabaseHas('correction_requests', [
            'correctable_id' => $page->id,
            'oauth_provider' => 'facebook',
            'status' => SuggestionStatus::Pending->value,
        ]);

        // 3. Enviar comentario en la página (queda pendiente de moderación: is_approved = false)
        $this->post(route('comments.store'), [
            'commentable_type' => 'page',
            'commentable_id' => $page->id,
            'body' => 'Qué gran recuerdo de los corrales con mi abuelo.',
            'privacy' => '1',
            'cf-turnstile-response' => 'test-token',
        ])->assertRedirect();

        $this->assertDatabaseHas('comments', [
            'commentable_id' => $page->id,
            'oauth_provider' => 'facebook',
            'is_approved' => false,
        ]);

        // 4. Enviar sugerencia de nuevo contenido
        $this->post(route('suggestions.store'), [
            'content_group' => ContentGroup::Gastronomia->value,
            'title' => 'Receta tradicional de la telera en sarmientos',
            'related_page_id' => $page->id,
            'body' => 'Se prepara con sarmientos de las viñas de moscatel y pescado fresco de roca.',
            'privacy' => '1',
            'cf-turnstile-response' => 'test-token',
        ])->assertRedirect(route('suggestions.create'));

        $this->assertDatabaseHas('content_suggestions', [
            'title' => 'Receta tradicional de la telera en sarmientos',
            'content_group' => ContentGroup::Gastronomia->value,
        ]);

        // 5. Subir un refrán al refranero popular
        $this->post(route('proverbs.store'), [
            'text' => 'Con Levante en la punta, el pescado se junta.',
            'meaning' => 'Cuando sopla Levante templado entra pescado al corral.',
            'category' => 'mar',
            'privacy' => '1',
            'cf-turnstile-response' => 'test-token',
        ])->assertRedirect(route('proverbs.index'));

        $this->assertDatabaseHas('proverbs', [
            'text' => 'Con Levante en la punta, el pescado se junta.',
            'status' => SuggestionStatus::Pending->value,
        ]);

        // 6. Desconectar la identidad social ligera mediante POST
        $this->post(route('social.disconnect'))->assertRedirect();
        $this->assertNull(session('public_social_identity'));
    }

    public function test_utility_and_legal_pages_are_accessible(): void
    {
        OfficialPhone::factory()->create(['name' => 'Policía Local de Chipiona', 'phone' => '956 92 90 60']);
        Proverb::factory()->approved()->create(['text' => 'En Chipiona el sol se esconde en la mar.']);

        $this->withSession(['locale' => 'es'])
            ->get(route('weather.index'))
            ->assertStatus(200)
            ->assertSee('AEMET')
            ->assertSee('Tabla de Mareas');

        $this->withSession(['locale' => 'en'])
            ->get(route('weather.index'))
            ->assertStatus(200)
            ->assertSee('AEMET')
            ->assertSee('Tide Table');

        $this->withSession(['locale' => 'es'])
            ->get(route('phones.index'))
            ->assertStatus(200)
            ->assertSee('Policía Local de Chipiona')
            ->assertSee('tel:956929060');

        $this->withSession(['locale' => 'es'])
            ->get(route('proverbs.index'))
            ->assertStatus(200)
            ->assertSee('En Chipiona el sol se esconde en la mar.');

        $this->withSession(['locale' => 'es'])
            ->get(route('suggestions.create'))
            ->assertStatus(200);

        $this->withSession(['locale' => 'es'])
            ->get(route('legal'))
            ->assertStatus(200)
            ->assertSee('Raúl Caro Pastorino')
            ->assertSee('raupulus.dev')
            ->assertSee('AGPLv3');
    }
}
