<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// Pago de una parte de la cuenta (rama 11).
//
// Igual que ParteCuenta, NO usa PerteneceARestaurante porque la tabla no tiene
// restaurante_id: queda aislado a través de su parte y la cuenta de esta.
#[Fillable(['parte_cuenta_id', 'user_id', 'importe_centimos', 'metodo', 'referencia_stripe'])]
class Pago extends Model
{
    // Sin esto Laravel acertaría ("pagos"), pero lo ponemos siempre por norma.
    protected $table = 'pagos';

    protected function casts(): array
    {
        return ['importe_centimos' => 'integer'];
    }

    public function parteCuenta(): BelongsTo
    {
        return $this->belongsTo(ParteCuenta::class);
    }

    /**
     * Camarero que cobró el pago (efectivo o TPV). Null si pagó el cliente
     * con Stripe desde su móvil.
     */
    public function cobradoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
