<?php

namespace App\Models;

use App\Models\Concerns\PerteneceARestaurante;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

// Pedido (comanda) de una mesa. Usa PerteneceARestaurante: el personal solo
// ve los pedidos de su restaurante.
//
// Para crear un pedido no se usa Pedido::create() directamente: se usa la
// clase App\Acciones\CrearPedido, que valida los platos y crea las líneas
// dentro de una transacción.
#[Fillable(['restaurante_id', 'mesa_id', 'user_id', 'estado'])]
class Pedido extends Model
{
    use PerteneceARestaurante;

    protected $table = 'pedidos';

    /**
     * Estados posibles, en el orden en que tiene que pasar el pedido.
     */
    public const ESTADOS = ['pendiente', 'preparando', 'listo', 'servido'];

    public function mesa(): BelongsTo
    {
        return $this->belongsTo(Mesa::class);
    }

    /**
     * Camarero que tomó la comanda (null si la hizo el cliente desde el QR).
     * Se llama "usuario" porque la columna es user_id (de Laravel).
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function lineas(): HasMany
    {
        return $this->hasMany(LineaPedido::class);
    }

    /**
     * Total del pedido en céntimos: suma de cantidad × precio de cada línea.
     */
    public function totalCentimos(): int
    {
        return $this->lineas->sum(fn (LineaPedido $linea) => $linea->subtotalCentimos());
    }
}
