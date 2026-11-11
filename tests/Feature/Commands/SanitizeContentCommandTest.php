<?php

namespace Tests\Feature\Commands;

use App\Enums\ContentStatus;
use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SanitizeContentCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_sanitize_content_command_cleans_internal_sections_and_broken_links(): void
    {
        $user = User::factory()->create();

        $page = Page::create([
            'slug' => 'tartessos-y-fenicios',
            'title' => ['es' => 'Tartessos y fenicios'],
            'content' => [
                'es' => "## Resumen\nTexto del resumen.\n\n## Indicios pendientes\nIndicios pendientes de archivo. Ver [00-proyecto/nota-indicios.md](/pages/nota-indicios).\n\n## Cuestiones abiertas\n- [ ] Pregunta 1.",
                'en' => "## Summary\nSummary text.\n\n## Pending clues\nPending archival traces. See [00-proyecto/nota-indicios.md](/pages/nota-indicios).\n\n## Open questions\n- [ ] Question 1.",
            ],
            'status' => ContentStatus::Published,
            'is_active' => true,
            'user_id' => $user->id,
        ]);

        $pageWithLocalConf = Page::create([
            'slug' => 'berza-de-chipiona',
            'title' => ['es' => 'Berza de Chipiona'],
            'content' => [
                'es' => "## Resumen\nPlato típico.\n\n## Confirmación local\nConfirmado el 2 de noviembre de 2026.\n\n## Ficha rápida\nTabla de datos.",
                'en' => "## Summary\nTypical dish.\n\n## Local confirmation\nConfirmed locally.\n\n## Quick Facts\nData table.",
            ],
            'status' => ContentStatus::Published,
            'is_active' => true,
            'user_id' => $user->id,
        ]);

        $this->artisan('chipiona:sanitize-content')
            ->expectsOutputToContain('Saneamiento completado')
            ->assertSuccessful();

        $page->refresh();
        $this->assertStringNotContainsString('Indicios pendientes', $page->getTranslation('content', 'es'));
        $this->assertStringNotContainsString('nota-indicios', $page->getTranslation('content', 'es'));
        $this->assertStringNotContainsString('Pending clues', $page->getTranslation('content', 'en'));
        $this->assertStringNotContainsString('nota-indicios', $page->getTranslation('content', 'en'));
        $this->assertStringContainsString('Cuestiones abiertas', $page->getTranslation('content', 'es'));

        $pageWithLocalConf->refresh();
        $this->assertStringNotContainsString('Confirmación local', $pageWithLocalConf->getTranslation('content', 'es'));
        $this->assertStringNotContainsString('Local confirmation', $pageWithLocalConf->getTranslation('content', 'en'));
        $this->assertStringContainsString('Ficha rápida', $pageWithLocalConf->getTranslation('content', 'es'));
    }

    public function test_page_rendered_content_attribute_strips_internal_sections_on_the_fly(): void
    {
        $user = User::factory()->create();

        $page = Page::create([
            'slug' => 'prueba-indicios',
            'title' => ['es' => 'Prueba Indicios'],
            'content' => [
                'es' => "## Resumen\nTexto público.\n\n## Indicios pendientes\nTexto interno con [00-proyecto/nota-indicios.md](/pages/nota-indicios).",
            ],
            'status' => ContentStatus::Published,
            'is_active' => true,
            'user_id' => $user->id,
        ]);

        $rendered = $page->rendered_content;

        $this->assertStringContainsString('Texto público', $rendered);
        $this->assertStringNotContainsString('Indicios pendientes', $rendered);
        $this->assertStringNotContainsString('nota-indicios', $rendered);
    }
}
