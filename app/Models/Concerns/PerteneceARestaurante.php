<?php

namespace App\Models\Concerns;

use App\Models\Restaurante;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

/**
 * Aísla los datos de cada restaurante.
 *
 * Todos los modelos de negocio (Plato, Mesa, Pedido...) usan este trait:
 *   class Plato extends Model { use PerteneceARestaurante; }
 *
 * Hace dos cosas, siempre que haya un usuario logueado:
 *  1. Al CONSULTAR, añade "where restaurante_id = <el del usuario>", así un
 *     restaurante nunca ve los datos de otro aunque el controlador se olvide.
 *  2. Al CREAR, rellena restaurante_id con el del usuario si no viene puesto.
 *
 * Sin usuario logueado (por ejemplo, la carta pública del QR) no filtra nada:
 * en esas pantallas hay que filtrar a mano por el restaurante del slug.
 */
trait PerteneceARestaurante
{
    /**
     * Laravel llama solo a los métodos "boot" + nombre del trait cuando
     * arranca el modelo. Aquí registramos el filtro y el evento de creación.
     */
    protected static function bootPerteneceARestaurante(): void
    {
        // 1. Filtro global para todas las consultas del modelo.
        static::addGlobalScope('restaurante', function (Builder $consulta) {
            if (Auth::check()) {
                // Ponemos el nombre de la tabla delante de la columna para que
                // no sea ambigua si la consulta hace un join con otra tabla.
                $tabla = $consulta->getModel()->getTable();
                $consulta->where($tabla.'.restaurante_id', Auth::user()->restaurante_id);
            }
        });

        // 2. Antes de guardar un registro nuevo, le ponemos el restaurante.
        static::creating(function ($modelo) {
            if (Auth::check() && empty($modelo->restaurante_id)) {
                $modelo->restaurante_id = Auth::user()->restaurante_id;
            }
        });
    }

    /**
     * Restaurante al que pertenece el registro.
     */
    public function restaurante(): BelongsTo
    {
        return $this->belongsTo(Restaurante::class);
    }
}
