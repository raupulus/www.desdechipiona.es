<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Migración que crea la tabla news (sección independiente de noticias y hallazgos documentales)
 * junto con sus tablas pivote category_news y news_page (páginas enlazadas).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('news', function (Blueprint $table) {
            $table->comment('Noticias, hallazgos documentales y actualidad cultural de Chipiona');
            $table->id()->comment('Identificador único de la noticia');
            $table->string('slug', 255)->comment('Slug URL único de la noticia');
            $table->jsonb('title')->comment('Título de la noticia (traducible: es, en)');
            $table->jsonb('content')->nullable()->comment('Contenido en Markdown de la noticia (traducible: es, en)');
            $table->jsonb('excerpt')->nullable()->comment('Resumen breve de la noticia (traducible: es, en)');
            $table->string('evidence_level', 30)->default('documentado')->comment('Nivel de rigor: consolidado, documentado, tradicion, discutido');
            $table->jsonb('source_references')->nullable()->comment('Lista JSON de códigos de fuente documental (ej: F-0031)');
            $table->string('image', 512)->nullable()->comment('Ruta de la imagen 16:9 de cabecera');
            $table->string('social_image', 512)->nullable()->comment('Ruta de la imagen 1200x630 para redes sociales (OpenGraph)');
            $table->string('youtube_url', 512)->nullable()->comment('URL opcional de vídeo explicativo en YouTube');
            $table->decimal('latitude', 10, 7)->nullable()->comment('Latitud geográfica WGS84 del lugar de la noticia');
            $table->decimal('longitude', 10, 7)->nullable()->comment('Longitud geográfica WGS84 del lugar de la noticia');
            $table->string('location_name', 255)->nullable()->comment('Nombre descriptivo de la ubicación en Chipiona');
            $table->string('status', 20)->default('draft')->comment('Estado de publicación: draft, published');
            $table->boolean('is_active')->default(true)->comment('Indica si la noticia está activa');
            $table->boolean('featured')->default(false)->comment('Indica si la noticia está destacada');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete()->cascadeOnUpdate()->comment('Usuario autor de la noticia');
            $table->jsonb('seo_title')->nullable()->comment('Título SEO traducible (es, en)');
            $table->jsonb('seo_description')->nullable()->comment('Descripción SEO traducible (es, en)');
            $table->jsonb('seo_keywords')->nullable()->comment('Palabras clave SEO traducibles (es, en)');
            $table->timestamp('published_at')->nullable()->comment('Fecha y hora de publicación');
            $table->timestamp('created_at')->nullable()->comment('Fecha de creación del registro');
            $table->timestamp('updated_at')->nullable()->comment('Fecha de última actualización');
            $table->softDeletes()->comment('Fecha de eliminación lógica');

            $table->index(['status', 'is_active'], 'idx_news_status_active');
            $table->index('published_at', 'idx_news_published_at');
            $table->index('featured', 'idx_news_featured');
        });

        DB::statement('CREATE UNIQUE INDEX news_slug_unique ON news (slug) WHERE deleted_at IS NULL');

        Schema::create('category_news', function (Blueprint $table) {
            $table->comment('Tabla pivote que relaciona categorías con noticias');
            $table->id()->comment('Identificador único de la relación categoría-noticia');
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete()->cascadeOnUpdate()->comment('ID de la categoría');
            $table->foreignId('news_id')->constrained('news')->cascadeOnDelete()->cascadeOnUpdate()->comment('ID de la noticia');
            $table->unique(['category_id', 'news_id'], 'uq_category_news');
        });

        Schema::create('news_page', function (Blueprint $table) {
            $table->comment('Tabla pivote que enlaza noticias con páginas patrimoniales relacionadas');
            $table->id()->comment('Identificador único del enlace noticia-página');
            $table->foreignId('news_id')->constrained('news')->cascadeOnDelete()->cascadeOnUpdate()->comment('ID de la noticia');
            $table->foreignId('page_id')->constrained('pages')->cascadeOnDelete()->cascadeOnUpdate()->comment('ID de la página enlazada');
            $table->unique(['news_id', 'page_id'], 'uq_news_page');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('news_page');
        Schema::dropIfExists('category_news');
        Schema::dropIfExists('news');
    }
};
