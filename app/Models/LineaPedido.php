<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// Línea de un pedido (un plato con su cantidad, precio y notas).
//
// Ojo: NO usa el trait PerteneceARestaurante porque la tabla lineas_pedido
// no tiene columna restaurante_id (el filtro daría error de SQL). Queda
// aislada a través de su pedido: a las líneas se llega siempre desde
// $pedido->lineas, y el pedido sí está filtrado por restaurante.
#[Fillable(['pedido_id', 'plato_id', 'cantidad', 'precio_centimos', 'notas'])]
class LineaPedido extends Model
{
    // Sin esto, Laravel buscaría la tabla "linea_pedidos".
    protected $table = 'lineas_pedido';

    protected function casts(): array
    {
        return [
            'cantidad' => 'integer',
            'precio_centimos' => 'integer',
        ];
    }

    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class);
    }

    public function plato(): BelongsTo
    {
        return $this->belongsTo(Plato::class);
    }

    /**
     * Importe de la línea en céntimos: cantidad × precio unitario.
     */
    public function subtotalCentimos(): int
    {
        return $this->cantidad * $this->precio_centimos;
    }
}
