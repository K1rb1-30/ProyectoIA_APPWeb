<?php

namespace App\Models;

use App\Models\Concerns\PerteneceARestaurante;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

// Cuenta de una mesa (rama 10). Usa PerteneceARestaurante: el personal solo
// ve las cuentas de su restaurante.
//
// Para crearla o dividirla no se usa Cuenta::create() directamente: se usa
// App\Acciones\DividirCuenta, que calcula las partes dentro de una transacción.
// Para cerrarla (rama 12) se usa cerrar(), y solo desde aquí.
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
     * Entonces el reparto ya no se puede rehacer (solo se divide lo nuevo).
     */
    public function tienePagos(): bool
    {
        return $this->partes()->where('estado', '!=', 'pendiente')->exists();
    }

    /**
     * True si la mesa tiene pedidos que todavía no están en ninguna cuenta
     * (los ha hecho después de dividir).
     */
    public function hayPedidosFuera(): bool
    {
        return Pedido::withoutGlobalScope('restaurante')
            ->where('mesa_id', $this->mesa_id)
            ->whereNull('cuenta_id')
            ->exists();
    }

    /**
     * Lo que falta para poder cerrar, en frases para el camarero.
     * Lista vacía = todo cobrado.
     *
     * @return array<int, string>
     */
    public function faltaPorCobrar(): array
    {
        $falta = [];

        foreach ($this->partes()->where('estado', '!=', 'pagado')->get() as $parte) {
            $importe = number_format($parte->importe_centimos / 100, 2, ',', '.').' €';
            $motivo = $parte->estado === 'pendiente_efectivo' ? 'efectivo sin confirmar' : 'sin pagar';
            $falta[] = "Persona {$parte->persona}: {$importe} ({$motivo}).";
        }

        if ($this->hayPedidosFuera()) {
            $falta[] = 'Hay pedidos hechos después de dividir la cuenta que no están en ninguna parte. El cliente tiene que dividirlos en «Cuenta».';
        }

        return $falta;
    }

    /**
     * Cierra la cuenta y deja la mesa libre, SOLO si todo está cobrado.
     *
     * @throws ValidationException con la lista de lo que falta.
     */
    public function cerrar(): void
    {
        DB::transaction(function () {
            // lockForUpdate: si mientras tanto alguien paga o pide, espera.
            $cuenta = static::withoutGlobalScope('restaurante')->lockForUpdate()->findOrFail($this->id);

            if ($cuenta->estado !== 'abierta') {
                throw ValidationException::withMessages(['cuenta' => 'Esta cuenta ya está cerrada.']);
            }

            $falta = $cuenta->faltaPorCobrar();
            if ($falta !== []) {
                throw ValidationException::withMessages(['cuenta' => $falta]);
            }

            $cuenta->update(['estado' => 'cerrada']);

            Mesa::withoutGlobalScope('restaurante')
                ->whereKey($cuenta->mesa_id)
                ->update(['estado' => 'libre']);
        });

        $this->refresh();
    }
}
