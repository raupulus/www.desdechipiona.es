<?php

namespace App\Models;

use App\Enums\SuggestionStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo del refranero popular y dichos marineros/agrícolas de Chipiona.
 * Permite tanto los refranes curados desde el archivo como los enviados por vecinos.
 *
 * @property int $id Identificador único del refrán
 * @property string $text Texto literal del refrán o dicho popular
 * @property string|null $meaning Explicación de su significado y contexto chipionero
 * @property string $category Categoría temática (mar, campo, vientos, fiestas, vida)
 * @property string|null $source_code Código de fuente documental (ej. F-0082) o tradición oral
 * @property string $author_name Nombre del vecino o fuente que lo aporta
 * @property string|null $author_email Correo electrónico opcional del vecino
 * @property string|null $oauth_provider Proveedor OAuth ligero (facebook, google)
 * @property string|null $oauth_id Identificador externo OAuth
 * @property SuggestionStatus $status Estado de moderación (pending, approved, rejected)
 * @property bool $featured Si el refrán es destacado
 * @property int|null $related_page_id ID de página enciclopédica relacionada
 * @property Carbon $created_at Fecha de creación
 * @property Carbon $updated_at Fecha de modificación
 * @property-read Page|null $relatedPage Página relacionada con el refrán
 *
 * @method static Builder<Proverb> approved() Filtra refranes aprobados para la vista pública
 * @method static Builder<Proverb> pending() Filtra refranes pendientes de moderación
 */
class Proverb extends Model
{
    use HasFactory;

    /** @var array<string> Campos asignables en masa */
    protected $fillable = [
        'text',
        'meaning',
        'category',
        'source_code',
        'author_name',
        'author_email',
        'oauth_provider',
        'oauth_id',
        'status',
        'featured',
        'related_page_id',
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
            'status' => SuggestionStatus::class,
            'featured' => 'boolean',
        ];
    }

    // =========================================================
    // SCOPES
    // =========================================================

    /**
     * Filtra refranes aprobados para su visualización pública.
     *
     * @param  Builder<Proverb>  $query
     * @return Builder<Proverb>
     */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', SuggestionStatus::Approved);
    }

    /**
     * Filtra refranes pendientes de moderación.
     *
     * @param  Builder<Proverb>  $query
     * @return Builder<Proverb>
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', SuggestionStatus::Pending);
    }

    // =========================================================
    // RELACIONES
    // =========================================================

    /**
     * Página enciclopédica vinculada al refrán (ej. Corrales de Pesca, Moscatel).
     *
     * @return BelongsTo<Page, $this>
     */
    public function relatedPage(): BelongsTo
    {
        return $this->belongsTo(Page::class, 'related_page_id');
    }
}
