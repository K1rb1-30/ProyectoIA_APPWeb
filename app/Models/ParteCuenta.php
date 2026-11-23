<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// Lo que paga cada persona de una cuenta (rama 10).
//
// Ojo: NO usa el trait PerteneceARestaurante porque la tabla partes_cuenta no
// tiene restaurante_id (igual que LineaPedido). Queda aislada a través de su
// cuenta: a las partes se llega siempre desde $cuenta->partes.
#[Fillable(['cuenta_id', 'persona', 'importe_centimos', 'metodo', 'estado'])]
class ParteCuenta extends Model
{
    // Sin esto, Laravel buscaría la tabla "parte_cuentas".
    protected $table = 'partes_cuenta';

    protected function casts(): array
    {
        return [
            'persona' => 'integer',
            'importe_centimos' => 'integer',
        ];
    }

    public function cuenta(): BelongsTo
    {
        return $this->belongsTo(Cuenta::class);
    }
}
