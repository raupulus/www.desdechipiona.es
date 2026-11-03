<?php

namespace App\Models;

use App\Enums\ContentGroup;
use App\Enums\SuggestionStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo de sugerencia ciudadana para añadir nuevos contenidos a Desde Chipiona.
 * Permite a los visitantes escribir texto extenso, seleccionar el tipo de apartado
 * (historia, refranes, monumentos, recetas...) y vincularlo opcionalmente a una página existente.
 *
 * @property int $id Identificador único de la sugerencia
 * @property ContentGroup $content_group Apartado o tipo de contenido sugerido
 * @property string $title Título propuesto para el nuevo contenido
 * @property int|null $related_page_id ID de página existente relacionada (opcional)
 * @property string $body Desarrollo extenso del relato, receta, monumento o historia
 * @property string|null $sources_info Fuentes, recortes o procedencia oral del aporte
 * @property string|null $location_name Ubicación orientativa en Chipiona
 * @property float|null $latitude Latitud GPS opcional
 * @property float|null $longitude Longitud GPS opcional
 * @property string $author_name Nombre del colaborador
 * @property string|null $author_email Correo electrónico de contacto
 * @property string|null $oauth_provider Proveedor OAuth ligero (facebook, google)
 * @property string|null $oauth_id ID externo del usuario en el proveedor OAuth
 * @property string|null $author_avatar Avatar del colaborador
 * @property SuggestionStatus $status Estado de revisión (pending, approved, rejected)
 * @property string|null $admin_notes Notas internas del equipo editorial
 * @property Carbon $created_at Fecha de envío
 * @property Carbon $updated_at Fecha de modificación
 * @property-read Page|null $relatedPage Página publicada con la que se relaciona la sugerencia
 *
 * @method static Builder<ContentSuggestion> pending() Filtra sugerencias en espera
 * @method static Builder<ContentSuggestion> approved() Filtra sugerencias aprobadas
 */
class ContentSuggestion extends Model
{
    use HasFactory;

    /** @var array<string> Campos asignables en masa */
    protected $fillable = [
        'content_group',
        'title',
        'related_page_id',
        'body',
        'sources_info',
        'location_name',
        'latitude',
        'longitude',
        'author_name',
        'author_email',
        'oauth_provider',
        'oauth_id',
        'author_avatar',
        'status',
        'admin_notes',
    ];

    /** @var array<string> Campos ocultos en la serialización JSON por privacidad (RGPD) */
    protected $hidden = [
        'author_email',
        'oauth_id',
    ];

    /**
     * Definición de casts de atributos.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'content_group' => ContentGroup::class,
            'status' => SuggestionStatus::class,
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    // =========================================================
    // SCOPES
    // =========================================================

    /**
     * Filtra sugerencias en espera de revisión.
     *
     * @param  Builder<ContentSuggestion>  $query
     * @return Builder<ContentSuggestion>
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', SuggestionStatus::Pending);
    }

    /**
     * Filtra sugerencias aprobadas.
     *
     * @param  Builder<ContentSuggestion>  $query
     * @return Builder<ContentSuggestion>
     */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', SuggestionStatus::Approved);
    }

    // =========================================================
    // RELACIONES
    // =========================================================

    /**
     * Página existente con la que el visitante ha vinculado su aportación.
     *
     * @return BelongsTo<Page, $this>
     */
    public function relatedPage(): BelongsTo
    {
        return $this->belongsTo(Page::class, 'related_page_id');
    }
}
