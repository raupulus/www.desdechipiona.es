<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo del directorio de teléfonos oficiales y servicios públicos de Chipiona.
 *
 * @property int $id Identificador único del teléfono oficial
 * @property string $name Nombre del servicio o dependencia municipal
 * @property string $category Categoría (emergencias, salud, ayuntamiento, transporte, cultura)
 * @property string $phone Número de teléfono principal
 * @property string|null $secondary_phone Número de teléfono secundario
 * @property string|null $address Dirección física en Chipiona
 * @property string|null $schedule Horario de atención al público
 * @property string|null $description Notas o información adicional del servicio
 * @property float|null $latitude Latitud GPS de la dependencia
 * @property float|null $longitude Longitud GPS de la dependencia
 * @property bool $is_emergency Indica si es un teléfono prioritario de urgencias 24h
 * @property bool $is_active Indica si el registro está activo y visible
 * @property int $sort_order Orden de visualización
 * @property Carbon $created_at Fecha de creación
 * @property Carbon $updated_at Fecha de modificación
 * @property-read string $tel_href Enlace limpio para el atributo href="tel:..."
 *
 * @method static Builder<OfficialPhone> active() Filtra teléfonos activos
 * @method static Builder<OfficialPhone> ordered() Ordena por urgencia y sort_order
 */
class OfficialPhone extends Model
{
    use HasFactory;

    /** @var array<string> Campos asignables en masa */
    protected $fillable = [
        'name',
        'category',
        'phone',
        'secondary_phone',
        'address',
        'schedule',
        'description',
        'latitude',
        'longitude',
        'is_emergency',
        'is_active',
        'sort_order',
    ];

    /**
     * Definición de casts de atributos.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_emergency' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    // =========================================================
    // ACCESSORS & SCOPES
    // =========================================================

    /**
     * Devuelve el número limpio para enlaces `tel:`.
     */
    public function getTelHrefAttribute(): string
    {
        return preg_replace('/[^0-9+]/', '', $this->phone) ?: $this->phone;
    }

    /**
     * Filtra registros activos.
     *
     * @param  Builder<OfficialPhone>  $query
     * @return Builder<OfficialPhone>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Ordena primero emergencias y después por orden configurado.
     *
     * @param  Builder<OfficialPhone>  $query
     * @return Builder<OfficialPhone>
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderByDesc('is_emergency')->orderBy('sort_order')->orderBy('name');
    }
}
