<?php

namespace Tests\Unit\Services;

use App\Services\ContentAutoLinker;
use PHPUnit\Framework\TestCase;

class ContentAutoLinkerTest extends TestCase
{
    private ContentAutoLinker $linker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->linker = new ContentAutoLinker;
    }

    public function test_strips_markdown_links_to_internal_corpus_paths(): void
    {
        $input = 'Ver [00-proyecto/nota-indicios.md](../../00-proyecto/nota-indicios.md) para más detalles.';
        $result = $this->linker->linkContent($input, 'current-page');

        $this->assertSame('Ver 00-proyecto/nota-indicios.md para más detalles.', $result);
    }

    public function test_strips_existing_links_to_nota_indicios(): void
    {
        $input = 'Consultar [nota de indicios](/pages/nota-indicios) en el proyecto.';
        $result = $this->linker->linkContent($input, 'current-page');

        $this->assertSame('Consultar nota de indicios en el proyecto.', $result);
    }

    public function test_resolves_known_slug_typo_aliases(): void
    {
        $input = 'Visitar la [Playa de Regla](/pages/playa-de-relga) y sus fiestas.';
        $result = $this->linker->linkContent($input, 'current-page', [], ['playa-de-regla']);

        $this->assertSame('Visitar la [Playa de Regla](/pages/playa-de-regla) y sus fiestas.', $result);
    }

    public function test_removes_link_if_target_slug_does_not_exist_in_valid_slugs(): void
    {
        $input = 'Enlace a [Página Inexistente](/pages/slug-que-no-existe) en el texto.';
        $result = $this->linker->linkContent($input, 'current-page', [], ['playa-de-regla', 'faro-de-chipiona']);

        $this->assertSame('Enlace a Página Inexistente en el texto.', $result);
    }

    public function test_converts_relative_md_link_to_page_route_when_slug_is_valid(): void
    {
        $input = 'Ver el [Faro de Chipiona](../monumentos/faro-de-chipiona.md) histórico.';
        $result = $this->linker->linkContent($input, 'current-page', [], ['faro-de-chipiona']);

        $this->assertSame('Ver el [Faro de Chipiona](/pages/faro-de-chipiona) histórico.', $result);
    }
}
