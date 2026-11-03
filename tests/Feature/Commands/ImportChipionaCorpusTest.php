<?php

namespace Tests\Feature\Commands;

use App\Models\Category;
use App\Models\OfficialPhone;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

/**
 * Tests para el seeder lógico ChipionaStructureSeeder y el comando importador chipiona:import-corpus.
 */
class ImportChipionaCorpusTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Comprueba que el comando importa la estructura base y fichas Markdown externas sin requerir archivos en git.
     */
    public function test_import_chipiona_corpus_command_imports_external_markdown_files(): void
    {
        $tempDir = storage_path('framework/testing/temp-corpus');
        $mdDir = $tempDir.'/03-actualidad/monumentos';
        File::ensureDirectoryExists($mdDir);

        $sampleMd = <<<'MD'
---
titulo: "Monumento de Prueba en Chipiona"
tipo: monumento
estado: revisado
nivel_evidencia: consolidado
fuentes: [F-0001, F-0037]
actualizado: 2026-11-03
slug: monumento-prueba-chipiona
categoria: patrimonio
periodo: siglo-xix
siglos: [XIX]
anios_clave: [1867]
lugar: "Punta del Perro, Chipiona"
---

# Monumento de Prueba en Chipiona

Contenido de prueba para validar el importador externo de fichas Markdown.
MD;

        File::put($mdDir.'/monumento-prueba-chipiona.md', $sampleMd);

        $this->artisan('chipiona:import-corpus', ['--path' => $tempDir])
            ->expectsOutputToContain('Importador del Corpus Privado de Desde Chipiona')
            ->assertSuccessful();

        File::deleteDirectory($tempDir);

        $this->assertGreaterThanOrEqual(8, Category::count());
        $this->assertGreaterThanOrEqual(15, OfficialPhone::count());
        $this->assertSame('Desde Chipiona', Setting::where('key', 'site_title')->value('value'));
        $this->assertDatabaseHas('pages', [
            'slug' => 'monumento-prueba-chipiona',
            'content_group' => 'monumentos',
        ]);
    }
}
