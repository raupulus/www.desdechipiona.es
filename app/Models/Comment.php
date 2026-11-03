<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Modelo de comentario público en páginas enciclopédicas y noticias.
 * Permite identificación ligera por sesión social (Facebook / Google) sin cuentas locales.
 *
 * @property int $id Identificador único del comentario
 * @property string $commentable_type Clase del modelo comentado (Page, News)
 * @property int $commentable_id ID del contenido comentado
 * @property string $body Texto del comentario o recuerdo aportado
 * @property string $author_name Nombre público del autor
 * @property string|null $author_email Correo electrónico opcional
 * @property string|null $oauth_provider Proveedor OAuth utilizado (facebook, google)
 * @property string|null $oauth_id Identificador externo en el proveedor OAuth
 * @property string|null $author_avatar URL del avatar del autor
 * @property bool $is_approved Si el comentario está aprobado y visible públicamente
 * @property Carbon $created_at Fecha de publicación
 * @property Carbon $updated_at Fecha de modificación
 * @property-read Model|Page|News|null $commentable Contenido al que pertenece el comentario
 *
 * @method static Builder<Comment> approved() Filtra comentarios aprobados y visibles
 */
class Comment extends Model
{
    use HasFactory;

    /** @var array<string> Campos asignables en masa */
    protected $fillable = [
        'commentable_type',
        'commentable_id',
        'body',
        'author_name',
        'author_email',
        'oauth_provider',
        'oauth_id',
        'author_avatar',
        'is_approved',
    ];

    /**
     * Definición de casts de atributos.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_approved' => 'boolean',
        ];
    }

    // =========================================================
    // SCOPES
    // =========================================================

    /**
     * Filtra comentarios aprobados para el público.
     *
     * @param  Builder<Comment>  $query
     * @return Builder<Comment>
     */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('is_approved', true);
    }

    // =========================================================
    // RELACIONES
    // =========================================================

    /**
     * Contenido polimórfico comentado (Page o News).
     *
     * @return MorphTo<Model, $this>
     */
    public function commentable(): MorphTo
    {
        return $this->morphTo();
    }
}
