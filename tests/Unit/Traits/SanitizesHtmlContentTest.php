<?php

namespace Tests\Unit\Traits;

use App\Enums\ContentStatus;
use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SanitizesHtmlContentTest extends TestCase
{
    use RefreshDatabase;

    public function test_sanitizes_html_string_strips_entity_encoded_javascript_urls(): void
    {
        $dirty = '<p><a href="javascript&#58;alert(1)">Click 1</a> <a href="&#x6a;avascript:alert(2)">Click 2</a> <a href="java&#9;script:alert(3)">Click 3</a> <a href="https://desdechipiona.es">Safe</a></p>';

        $clean = Page::sanitizeHtmlString($dirty);

        $this->assertStringNotContainsString('alert(1)', $clean);
        $this->assertStringNotContainsString('alert(2)', $clean);
        $this->assertStringNotContainsString('alert(3)', $clean);
        $this->assertStringContainsString('href="#"', $clean);
        $this->assertStringContainsString('href="https://desdechipiona.es"', $clean);
    }

    public function test_sanitizes_page_content_blocks_recursively_on_save(): void
    {
        $page = Page::create([
            'title' => ['es' => 'Ficha con Bloques'],
            'slug' => 'ficha-con-bloques',
            'status' => ContentStatus::Published,
            'is_active' => true,
            'content' => [
                'es' => [
                    [
                        'type' => 'text',
                        'data' => [
                            'content' => '<p>Texto seguro</p><script>alert("xss")</script><a href="javascript&#58;evil()">Enlace</a>',
                        ],
                    ],
                    [
                        'type' => 'cta',
                        'data' => [
                            'title' => 'Botón',
                            'button_url' => 'javascript:alert(1)',
                        ],
                    ],
                ],
            ],
        ]);

        $blocks = $page->fresh()->getTranslation('content', 'es');

        $this->assertIsArray($blocks);
        $this->assertStringNotContainsString('<script>', $blocks[0]['data']['content']);
        $this->assertStringNotContainsString('evil()', $blocks[0]['data']['content']);
        $this->assertEquals('#', $blocks[1]['data']['button_url']);
    }
}
