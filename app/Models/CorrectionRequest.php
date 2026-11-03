<?php

namespace App\Models;

use App\Enums\SuggestionStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Modelo de solicitud de corrección documental enviada por un visitante.
 * Vinculado polimórficamente a una página (Page) o noticia (News).
 *
 * @property int $id Identificador único de la solicitud
 * @property string $correctable_type Clase del modelo objetivo (Page, News)
 * @property int $correctable_id ID del contenido objetivo
 * @property string|null $quoted_text Fragmento o dato actual que se propone corregir
 * @property string $proposed_correction Corrección propuesta por el visitante
 * @property string|null $sources_info Fuentes o justificación aportada
 * @property string $author_name Nombre del remitente
 * @property string|null $author_email Correo electrónico del remitente
 * @property string|null $oauth_provider Proveedor OAuth ligero (facebook, google)
 * @property string|null $oauth_id Identificador del remitente en el proveedor OAuth
 * @property string|null $author_avatar URL del avatar del remitente
 * @property SuggestionStatus $status Estado de revisión (pending, approved, rejected)
 * @property string|null $admin_notes Notas internas o respuesta del editor
 * @property int|null $reviewed_by ID del administrador que revisó la propuesta
 * @property Carbon|null $reviewed_at Fecha y hora de revisión
 * @property Carbon $created_at Fecha de creación
 * @property Carbon $updated_at Fecha de modificación
 * @property-read Model|Page|News|null $correctable Contenido al que se refiere la corrección
 * @property-read User|null $reviewer Administrador que revisó la solicitud
 *
 * @method static Builder<CorrectionRequest> pending() Filtra correcciones en espera
 * @method static Builder<CorrectionRequest> approved() Filtra correcciones aprobadas
 * @method static Builder<CorrectionRequest> rejected() Filtra correcciones rechazadas
 */
class CorrectionRequest extends Model
{
    use HasFactory;

    /** @var array<string> Campos asignables en masa */
    protected $fillable = [
        'correctable_type',
        'correctable_id',
        'quoted_text',
        'proposed_correction',
        'sources_info',
        'author_name',
        'author_email',
        'oauth_provider',
        'oauth_id',
        'author_avatar',
        'status',
        'admin_notes',
        'reviewed_by',
        'reviewed_at',
    ];

    /**
     * Definición de casts de atributos.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => SuggestionStatus::class,
            'reviewed_at' => 'datetime',
        ];
    }

    // =========================================================
    // SCOPES
    // =========================================================

    /**
     * Filtra solicitudes pendientes («En espera»).
     *
     * @param  Builder<CorrectionRequest>  $query
     * @return Builder<CorrectionRequest>
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', SuggestionStatus::Pending);
    }

    /**
     * Filtra solicitudes aprobadas.
     *
     * @param  Builder<CorrectionRequest>  $query
     * @return Builder<CorrectionRequest>
     */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', SuggestionStatus::Approved);
    }

    /**
     * Filtra solicitudes rechazadas.
     *
     * @param  Builder<CorrectionRequest>  $query
     * @return Builder<CorrectionRequest>
     */
    public function scopeRejected(Builder $query): Builder
    {
        return $query->where('status', SuggestionStatus::Rejected);
    }

    // =========================================================
    // RELACIONES
    // =========================================================

    /**
     * Contenido polimórfico al que aplica la solicitud de corrección (Page o News).
     *
     * @return MorphTo<Model, $this>
     */
    public function correctable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Administrador que revisó la propuesta de corrección.
     *
     * @return BelongsTo<User, $this>
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
