<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migración que crea las tablas de participación ciudadana y utilidades de Desde Chipiona:
 * - correction_requests: Solicitudes de correcciones sobre páginas o noticias.
 * - content_suggestions: Formulario abierto para sugerir añadir contenido de cualquier tipo.
 * - proverbs: Apartado de refranes populares de Chipiona.
 * - comments: Comentarios públicos en páginas y noticias verificados con Facebook/Google.
 * - official_phones: Directorio de teléfonos de información oficiales de la localidad.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('correction_requests', function (Blueprint $table) {
            $table->comment('Solicitudes de corrección enviadas por visitantes sobre páginas o noticias');
            $table->id()->comment('Identificador único de la solicitud de corrección');
            $table->string('correctable_type', 120)->comment('Clase del modelo al que aplica la corrección (Page o News)');
            $table->unsignedBigInteger('correctable_id')->comment('ID del contenido al que aplica la corrección');
            $table->string('oauth_provider', 30)->nullable()->comment('Proveedor OAuth ligero usado (facebook, google)');
            $table->string('oauth_id', 191)->nullable()->comment('Identificador público del proveedor OAuth');
            $table->string('author_name', 150)->comment('Nombre del visitante que propone la corrección');
            $table->string('author_email', 191)->nullable()->comment('Correo opcional del visitante');
            $table->string('author_avatar', 512)->nullable()->comment('URL de avatar público si está disponible');
            $table->text('quoted_text')->nullable()->comment('Frase o dato concreto que se sugiere corregir');
            $table->text('proposed_correction')->comment('Explicación detallada de la corrección propuesta');
            $table->text('sources_info')->nullable()->comment('Fuente documental o testimonio que respalda la corrección');
            $table->string('status', 20)->default('pending')->comment('Estado de revisión en intranet: pending, approved, rejected');
            $table->text('admin_notes')->nullable()->comment('Notas internas del administrador tras revisar la propuesta');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete()->comment('Usuario administrador que revisó la solicitud');
            $table->timestamp('reviewed_at')->nullable()->comment('Fecha y hora de revisión');
            $table->timestamp('created_at')->nullable()->comment('Fecha de creación del registro');
            $table->timestamp('updated_at')->nullable()->comment('Fecha de última actualización');

            $table->index(['correctable_type', 'correctable_id'], 'idx_correction_requests_morph');
            $table->index('status', 'idx_correction_requests_status');
        });

        Schema::create('content_suggestions', function (Blueprint $table) {
            $table->comment('Propuestas ciudadanas para añadir nuevo contenido a la web');
            $table->id()->comment('Identificador único de la sugerencia de contenido');
            $table->string('content_group', 50)->default('historia')->comment('Tipo seleccionado: historia, refranes, monumentos, gastronomia, playas, fauna, flora, personajes, fiestas');
            $table->foreignId('related_page_id')->nullable()->constrained('pages')->nullOnDelete()->comment('Página publicada con la que se relaciona opcionalmente');
            $table->string('title', 255)->comment('Título propuesto para el nuevo contenido');
            $table->text('body')->comment('Desarrollo extenso del contenido sugerido');
            $table->string('location_name', 255)->nullable()->comment('Lugar o referencia geográfica en Chipiona');
            $table->decimal('latitude', 10, 7)->nullable()->comment('Latitud geográfica opcional');
            $table->decimal('longitude', 10, 7)->nullable()->comment('Longitud geográfica opcional');
            $table->text('sources_info')->nullable()->comment('Origen del dato (archivo, tradición familiar, prensa, receta)');
            $table->string('oauth_provider', 30)->nullable()->comment('Proveedor OAuth ligero usado (facebook, google)');
            $table->string('oauth_id', 191)->nullable()->comment('Identificador del proveedor OAuth');
            $table->string('author_name', 150)->comment('Nombre del vecino o visitante que envía la aportación');
            $table->string('author_email', 191)->nullable()->comment('Correo opcional de contacto');
            $table->string('author_avatar', 512)->nullable()->comment('URL del avatar del colaborador');
            $table->string('status', 20)->default('pending')->comment('Estado en intranet: pending, approved, rejected');
            $table->text('admin_notes')->nullable()->comment('Notas de edición interna');
            $table->timestamp('reviewed_at')->nullable()->comment('Fecha y hora de revisión');
            $table->timestamp('created_at')->nullable()->comment('Fecha de creación del registro');
            $table->timestamp('updated_at')->nullable()->comment('Fecha de última actualización');

            $table->index('content_group', 'idx_content_suggestions_group');
            $table->index('status', 'idx_content_suggestions_status');
        });

        Schema::create('proverbs', function (Blueprint $table) {
            $table->comment('Refranero popular y dichos tradicionales de Chipiona');
            $table->id()->comment('Identificador único del refrán');
            $table->text('text')->comment('Texto literal del refrán o dicho popular');
            $table->text('meaning')->nullable()->comment('Explicación de su significado y contexto en Chipiona');
            $table->string('category', 40)->default('mar')->comment('Ámbito del refrán: mar, campo, vientos, fiestas, vida');
            $table->string('source_code', 150)->nullable()->comment('Código de fuente documental o tradición oral');
            $table->foreignId('related_page_id')->nullable()->constrained('pages')->nullOnDelete()->comment('Página histórica o natural enlazada');
            $table->string('author_name', 150)->default('Archivo Oral Desde Chipiona')->comment('Nombre de la familia o vecino que aportó el refrán');
            $table->string('author_email', 191)->nullable()->comment('Correo opcional del vecino');
            $table->string('oauth_provider', 30)->nullable()->comment('Proveedor OAuth si fue subido desde el formulario público');
            $table->string('oauth_id', 191)->nullable()->comment('ID OAuth del autor');
            $table->string('status', 20)->default('pending')->comment('Estado de moderación: pending, approved, rejected');
            $table->boolean('featured')->default(false)->comment('Indica si se destaca como refrán del día en portada');
            $table->timestamp('created_at')->nullable()->comment('Fecha de creación del registro');
            $table->timestamp('updated_at')->nullable()->comment('Fecha de última actualización');

            $table->index(['status', 'category'], 'idx_proverbs_status_cat');
        });

        Schema::create('comments', function (Blueprint $table) {
            $table->comment('Comentarios públicos en páginas y noticias con identificación OAuth ligera');
            $table->id()->comment('Identificador único del comentario');
            $table->string('commentable_type', 120)->comment('Clase del modelo comentado (Page o News)');
            $table->unsignedBigInteger('commentable_id')->comment('ID del contenido comentado');
            $table->string('oauth_provider', 30)->nullable()->comment('Proveedor OAuth ligero (facebook, google)');
            $table->string('oauth_id', 191)->nullable()->comment('ID OAuth del comentarista');
            $table->string('author_name', 150)->comment('Nombre visible del autor del comentario');
            $table->string('author_email', 191)->nullable()->comment('Correo electrónico opcional del comentarista');
            $table->string('author_avatar', 512)->nullable()->comment('URL de avatar público si está disponible');
            $table->text('body')->comment('Texto del comentario o recuerdo vecinal');
            $table->boolean('is_approved')->default(true)->comment('Indica si el comentario es visible públicamente');
            $table->timestamp('created_at')->nullable()->comment('Fecha de creación del registro');
            $table->timestamp('updated_at')->nullable()->comment('Fecha de última actualización');

            $table->index(['commentable_type', 'commentable_id', 'is_approved'], 'idx_comments_morph_approved');
        });

        Schema::create('official_phones', function (Blueprint $table) {
            $table->comment('Directorio de teléfonos de información oficiales de Chipiona');
            $table->id()->comment('Identificador único del teléfono oficial');
            $table->string('name', 200)->comment('Nombre del servicio, organismo o centro oficial');
            $table->string('category', 50)->default('municipal')->comment('Categoría: emergencias, salud, ayuntamiento, transporte, cultura');
            $table->string('phone', 50)->comment('Número de teléfono principal formateado para visualización y llamada');
            $table->string('secondary_phone', 50)->nullable()->comment('Número secundario o de urgencia');
            $table->text('description')->nullable()->comment('Descripción de las gestiones o emergencias que atiende');
            $table->string('address', 255)->nullable()->comment('Dirección física en Chipiona');
            $table->string('schedule', 120)->nullable()->comment('Horario de atención (ej: 24 horas o L-V Mañanas)');
            $table->decimal('latitude', 10, 7)->nullable()->comment('Latitud geográfica opcional');
            $table->decimal('longitude', 10, 7)->nullable()->comment('Longitud geográfica opcional');
            $table->boolean('is_emergency')->default(false)->comment('Indica si es un teléfono prioritario de emergencias 24h');
            $table->integer('sort_order')->default(0)->comment('Orden de aparición en el directorio');
            $table->boolean('is_active')->default(true)->comment('Indica si el teléfono está activo');
            $table->timestamp('created_at')->nullable()->comment('Fecha de creación del registro');
            $table->timestamp('updated_at')->nullable()->comment('Fecha de última actualización');

            $table->index(['is_active', 'category', 'sort_order'], 'idx_official_phones_active_cat');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('official_phones');
        Schema::dropIfExists('comments');
        Schema::dropIfExists('proverbs');
        Schema::dropIfExists('content_suggestions');
        Schema::dropIfExists('correction_requests');
    }
};
