<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migración que amplía la tabla pages con metadatos patrimoniales de Desde Chipiona
 * (grupo temático, época histórica, años, nivel de evidencia, nota de rigor, fuentes,
 * vídeo de YouTube, imagen social y coordenadas GPS) y crea la tabla pivote page_relations.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->string('content_group', 50)->default('historia')->after('slug')->comment('Grupo temático: historia, monumentos, playas, fauna, flora, gastronomia, personajes, fiestas, refranes');
            $table->string('historical_era', 50)->nullable()->after('content_group')->comment('Época histórica: antigua, medieval, moderna, contemporanea, actual');
            $table->string('period_label', 120)->nullable()->after('historical_era')->comment('Etiqueta legible del periodo (ej: 1863-1867 o Siglo XV)');
            $table->smallInteger('year_start')->nullable()->after('period_label')->comment('Año inicial para filtrado cronológico (negativo para a. C.)');
            $table->smallInteger('year_end')->nullable()->after('year_start')->comment('Año final para filtrado cronológico');
            $table->string('evidence_level', 30)->default('consolidado')->after('year_end')->comment('Nivel de rigor documental: consolidado, documentado, tradicion, discutido');
            $table->jsonb('evidence_note')->nullable()->after('evidence_level')->comment('Advertencia traducible sobre hechos contrastados frente a mitos o hipótesis');
            $table->jsonb('source_references')->nullable()->after('evidence_note')->comment('Lista JSON de códigos de fuente documental (ej: F-0071, F-0082)');
            $table->string('social_image', 512)->nullable()->after('image')->comment('Ruta de la imagen 1200x630 para metadatos de redes sociales (OpenGraph/Twitter)');
            $table->string('youtube_url', 512)->nullable()->after('social_image')->comment('URL opcional de vídeo explicativo en YouTube');
            $table->decimal('latitude', 10, 7)->nullable()->after('youtube_url')->comment('Latitud geográfica WGS84 para mostrar ubicación en mapa');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude')->comment('Longitud geográfica WGS84 para mostrar ubicación en mapa');
            $table->string('location_name', 255)->nullable()->after('longitude')->comment('Nombre descriptivo del lugar en Chipiona (ej: Punta del Perro)');

            $table->index('content_group', 'idx_pages_content_group');
            $table->index('historical_era', 'idx_pages_historical_era');
            $table->index('evidence_level', 'idx_pages_evidence_level');
            $table->index('year_start', 'idx_pages_year_start');
        });

        Schema::create('page_relations', function (Blueprint $table) {
            $table->comment('Tabla pivote que enlaza páginas patrimoniales relacionadas entre sí');
            $table->id()->comment('Identificador único del enlace entre páginas');
            $table->foreignId('page_id')->constrained('pages')->cascadeOnDelete()->cascadeOnUpdate()->comment('ID de la página origen');
            $table->foreignId('related_page_id')->constrained('pages')->cascadeOnDelete()->cascadeOnUpdate()->comment('ID de la página enlazada');
            $table->unique(['page_id', 'related_page_id'], 'uq_page_relations');
            $table->index('related_page_id', 'idx_page_relations_related_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_relations');

        Schema::table('pages', function (Blueprint $table) {
            $table->dropIndex('idx_pages_content_group');
            $table->dropIndex('idx_pages_historical_era');
            $table->dropIndex('idx_pages_evidence_level');
            $table->dropIndex('idx_pages_year_start');

            $table->dropColumn([
                'content_group',
                'historical_era',
                'period_label',
                'year_start',
                'year_end',
                'evidence_level',
                'evidence_note',
                'source_references',
                'social_image',
                'youtube_url',
                'latitude',
                'longitude',
                'location_name',
            ]);
        });
    }
};
