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
#[Fillable(['restaurante_id', 'mesa_id', 'user_id', 'cuenta_id', 'estado'])]
class Pedido extends Model
{
    use PerteneceARestaurante;

    protected $table = 'pedidos';

    /**
     * Estados posibles, en el orden en que tiene que pasar el pedido.
     */
    public const ESTADOS = ['pendiente', 'preparando', 'listo', 'servido'];

    /**
     * Único salto permitido desde cada estado. "servido" no está como clave
     * porque es el final: desde ahí no se puede ir a ningún sitio.
     */
    public const SIGUIENTE_ESTADO = [
        'pendiente' => 'preparando',
        'preparando' => 'listo',
        'listo' => 'servido',
    ];

    /**
     * Estado al que puede pasar el pedido ahora, o null si ya está servido.
     */
    public function siguienteEstado(): ?string
    {
        return self::SIGUIENTE_ESTADO[$this->estado] ?? null;
    }

    /**
     * Pasa el pedido a $nuevoEstado SOLO si es el siguiente del actual
     * (pendiente → preparando → listo → servido).
     *
     * Devuelve true si lo ha cambiado y false si no estaba permitido (un salto
     * como pendiente → listo, volver atrás o tocar un pedido ya servido). Si
     * devuelve false, no se ha cambiado nada.
     */
    public function cambiarEstado(string $nuevoEstado): bool
    {
        $estadoActual = $this->estado;

        if ($this->siguienteEstado() !== $nuevoEstado) {
            return false;
        }

        // Guardamos con "where estado = <el que teníamos>". Si dos cocineros
        // pulsan a la vez, el primero cambia la fila y el segundo ya no la
        // encuentra con el estado antiguo: update() devuelve 0 filas y no se
        // salta ningún paso.
        $filasCambiadas = static::whereKey($this->id)
            ->where('estado', $estadoActual)
            ->update(['estado' => $nuevoEstado]);

        if ($filasCambiadas === 0) {
            return false;
        }

        // Actualizamos también el objeto que tenemos en memoria.
        $this->estado = $nuevoEstado;
        $this->syncOriginal();

        return true;
    }

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

    /**
     * Cuenta en la que se cobra este pedido (null hasta que se divide la cuenta).
     */
    public function cuenta(): BelongsTo
    {
        return $this->belongsTo(Cuenta::class);
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
