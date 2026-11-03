<?php

namespace Tests\Feature\Api;

use App\Enums\ContentStatus;
use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_published_and_active_pages(): void
    {
        Page::create([
            'title' => ['es' => 'Página Pública', 'en' => 'Public Page'],
            'slug' => 'pagina-publica',
            'status' => ContentStatus::Published,
            'is_active' => true,
            'published_at' => now()->subDay(),
        ]);

        Page::create([
            'title' => ['es' => 'Página Borrador', 'en' => 'Draft Page'],
            'slug' => 'pagina-borrador',
            'status' => ContentStatus::Draft,
            'is_active' => true,
            'published_at' => now()->subDay(),
        ]);

        $response = $this->getJson('/api/v1/pages');

        $response->assertOk();
        $response->assertJsonPath('total', 1);
        $response->assertJsonFragment(['slug' => 'pagina-publica']);
        $response->assertJsonMissing(['slug' => 'pagina-borrador']);
    }

    public function test_show_returns_page_with_encyclopedic_metadata(): void
    {
        Page::create([
            'title' => ['es' => 'Corrales de Pesca', 'en' => 'Fishing Corrales'],
            'slug' => 'corrales-de-pesca',
            'content' => ['es' => 'Muros de piedra ostionera...', 'en' => 'Stone walls...'],
            'status' => ContentStatus::Published,
            'is_active' => true,
            'content_group' => 'monumentos',
            'historical_era' => 'antigua',
            'period_label' => 'Siglos I a.C. - Actualidad',
            'year_start' => -100,
            'year_end' => 2026,
            'latitude' => 36.7412,
            'longitude' => -6.4421,
            'location_name' => 'Playa de Las Canteras',
            'published_at' => now()->subDay(),
        ]);

        $response = $this->getJson('/api/v1/pages/corrales-de-pesca');

        $response->assertOk();
        $response->assertJsonFragment(['slug' => 'corrales-de-pesca']);
        $response->assertJsonFragment(['content_group' => 'monumentos']);
        $response->assertJsonFragment(['historical_era' => 'antigua']);
        $response->assertJsonFragment(['location_name' => 'Playa de Las Canteras']);
        $response->assertJsonStructure([
            'id',
            'title',
            'slug',
            'content',
            'content_group',
            'historical_era',
            'period_label',
            'year_start',
            'year_end',
            'evidence_level',
            'evidence_note',
            'source_references',
            'latitude',
            'longitude',
            'location_name',
            'categories',
        ]);
    }

    public function test_show_returns_404_for_draft_page(): void
    {
        Page::create([
            'title' => ['es' => 'Página Privada'],
            'slug' => 'pagina-privada',
            'status' => ContentStatus::Draft,
            'is_active' => true,
        ]);

        $this->getJson('/api/v1/pages/pagina-privada')->assertNotFound();
    }
}
