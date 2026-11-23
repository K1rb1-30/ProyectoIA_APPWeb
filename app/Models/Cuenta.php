<?php

namespace App\Models;

use App\Models\Concerns\PerteneceARestaurante;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

// Cuenta de una mesa (rama 10). Usa PerteneceARestaurante: el personal solo
// ve las cuentas de su restaurante.
//
// Para crearla o dividirla no se usa Cuenta::create() directamente: se usa
// App\Acciones\DividirCuenta, que calcula las partes dentro de una transacción.
#[Fillable(['restaurante_id', 'mesa_id', 'total_centimos', 'estado'])]
class Cuenta extends Model
{
    use PerteneceARestaurante;

    protected $table = 'cuentas';

    protected function casts(): array
    {
        return ['total_centimos' => 'integer'];
    }

    public function mesa(): BelongsTo
    {
        return $this->belongsTo(Mesa::class);
    }

    /**
     * Partes de la cuenta, ordenadas por persona (1, 2, 3...).
     */
    public function partes(): HasMany
    {
        return $this->hasMany(ParteCuenta::class)->orderBy('persona');
    }

    public function pedidos(): HasMany
    {
        return $this->hasMany(Pedido::class);
    }

    /**
     * True si alguna parte ya está pagada o pendiente de cobro en efectivo.
     * Entonces ya no se puede volver a dividir.
     */
    public function tienePagos(): bool
    {
        return $this->partes()->where('estado', '!=', 'pendiente')->exists();
    }
}
