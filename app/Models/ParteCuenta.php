<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

// Lo que paga cada persona de una cuenta (rama 10).
//
// Ojo: NO usa el trait PerteneceARestaurante porque la tabla partes_cuenta no
// tiene restaurante_id (igual que LineaPedido). Queda aislada a través de su
// cuenta: a las partes se llega desde $cuenta->partes o con deLaMesa().
//
// Los cambios de estado (rama 12) se hacen SOLO con pedirEfectivo(),
// confirmarEfectivo(), cobrarEfectivoEnMesa(), pagarConTarjeta() y
// pagarConTpv(). Nunca con update(['estado' => ...]).
#[Fillable(['cuenta_id', 'pedido_id', 'persona', 'importe_centimos', 'metodo', 'estado'])]
class ParteCuenta extends Model
{
    // Sin esto, Laravel buscaría la tabla "parte_cuentas".
    protected $table = 'partes_cuenta';

    /**
     * Únicos saltos de estado permitidos (regla de CLAUDE.md):
     *   efectivo: pendiente → pendiente_efectivo → pagado
     *   tarjeta:  pendiente → pagado
     * "pagado" no está como clave porque es el final: no se puede volver a cobrar.
     */
    public const TRANSICIONES = [
        'pendiente' => ['pendiente_efectivo', 'pagado'],
        'pendiente_efectivo' => ['pagado'],
    ];

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

    /**
     * Pagos de esta parte (rama 11). Normalmente uno solo.
     */
    public function pagos(): HasMany
    {
        return $this->hasMany(Pago::class);
    }

    public function estaPagada(): bool
    {
        return $this->estado === 'pagado';
    }

    /**
     * Pedido que el cliente pagó al hacerlo (rama 15). Null en las partes
     * normales, las que salen de dividir la cuenta.
     */
    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class);
    }

    /**
     * Cómo se llama la parte en la web: «Persona 3» o, si es un pedido pagado
     * al hacerlo (rama 15), «Pedido #12 · pagado al pedir».
     */
    public function nombre(): string
    {
        return $this->pedido_id
            ? "Pedido #{$this->pedido_id} · pagado al pedir"
            : "Persona {$this->persona}";
    }

    /**
     * Texto del estado para enseñarlo en la web.
     */
    public function textoEstado(): string
    {
        return match ($this->estado) {
            'pagado' => $this->metodo === 'efectivo' ? 'Pagado en efectivo' : 'Pagado con tarjeta',
            // pendiente_efectivo: el cliente ha elegido efectivo en su móvil.
            'pendiente_efectivo' => 'Efectivo: esperando al camarero',
            default => 'Pendiente de pago',
        };
    }

    /**
     * Busca la parte $id solo si es de una cuenta de ESTA mesa y de SU
     * restaurante. Si no, devuelve null (el controlador da 404).
     *
     * Filtra a mano y sin el global scope del trait, porque lo usan tanto el
     * cliente (sin login, ramas 11 y 12) como el camarero (rama 12).
     */
    public static function deLaMesa(Mesa $mesa, int $id): ?self
    {
        $soloEstaMesa = fn ($consulta) => $consulta->withoutGlobalScope('restaurante')
            ->where('mesa_id', $mesa->id)
            ->where('restaurante_id', $mesa->restaurante_id);

        return static::whereKey($id)
            ->whereHas('cuenta', $soloEstaMesa)
            ->with(['cuenta' => $soloEstaMesa])
            ->first();
    }

    /**
     * Partes que esperan a que el camarero cobre el efectivo, en cuentas
     * abiertas. Para la pestaña «Cobros».
     *
     * Solo se usa con el camarero logueado: el whereHas sobre Cuenta lleva el
     * global scope del trait, así que solo salen partes de SU restaurante.
     */
    public static function esperandoEfectivo(): Builder
    {
        return static::where('estado', 'pendiente_efectivo')
            ->whereHas('cuenta', fn ($consulta) => $consulta->where('estado', 'abierta'));
    }

    /**
     * Cliente: "pagaré en efectivo". pendiente → pendiente_efectivo.
     * Todavía NO está pagada: falta que el camarero confirme el cobro.
     */
    public function pedirEfectivo(): bool
    {
        return $this->saltar('pendiente', 'pendiente_efectivo', ['metodo' => 'efectivo']);
    }

    /**
     * Camarero: "ya he cobrado en efectivo". pendiente_efectivo → pagado, y se
     * guarda el Pago (sin referencia de Stripe).
     *
     * Desde "pendiente" devuelve false: por la vía del efectivo hay que pasar
     * antes por "pendiente_efectivo".
     */
    public function confirmarEfectivo(): bool
    {
        // Transacción: o cambia el estado Y se crea el pago, o no pasa nada.
        return DB::transaction(function () {
            if (! $this->saltar('pendiente_efectivo', 'pagado')) {
                return false;
            }

            $this->pagos()->create([
                'importe_centimos' => $this->importe_centimos,
                'metodo' => 'efectivo',
            ]);

            return true;
        });
    }

    /**
     * Stripe ha confirmado el pago (rama 11). pendiente → pagado, y se guarda
     * el Pago con el id de la sesión de Stripe.
     */
    public function pagarConTarjeta(string $idSesion): bool
    {
        return DB::transaction(function () use ($idSesion) {
            if (! $this->saltar('pendiente', 'pagado', ['metodo' => 'tarjeta'])) {
                return false;
            }

            $this->pagos()->create([
                'importe_centimos' => $this->importe_centimos,
                'metodo' => 'tarjeta',
                'referencia_stripe' => $idSesion,
            ]);

            return true;
        });
    }

    /**
     * Camarero en la mesa: el cliente le da el dinero sin haber elegido nada
     * en el móvil. Hace los DOS pasos del efectivo seguidos (pendiente →
     * pendiente_efectivo → pagado), así que la regla no se salta.
     */
    public function cobrarEfectivoEnMesa(): bool
    {
        // Si el segundo paso fallara, la transacción deshace también el primero.
        return DB::transaction(fn () => $this->pedirEfectivo() && $this->confirmarEfectivo());
    }

    /**
     * Camarero: cobro con el datáfono (TPV) del bar. pendiente → pagado con
     * tarjeta. El Pago va sin referencia_stripe: así se distingue de Stripe.
     * Solo desde "pendiente" (CLAUDE.md: la tarjeta va de pendiente a pagado).
     */
    public function pagarConTpv(): bool
    {
        return DB::transaction(function () {
            if (! $this->saltar('pendiente', 'pagado', ['metodo' => 'tarjeta'])) {
                return false;
            }

            $this->pagos()->create([
                'importe_centimos' => $this->importe_centimos,
                'metodo' => 'tarjeta',
            ]);

            return true;
        });
    }

    /**
     * Cambia el estado de $desde a $hasta SOLO si el salto está en TRANSICIONES
     * y la parte sigue en $desde. Devuelve false si no se ha cambiado nada.
     *
     * Igual que Pedido::cambiarEstado(): el update lleva "where estado = $desde".
     * Si dos personas pulsan a la vez, la segunda ya no encuentra la fila en
     * $desde, el update cambia 0 filas y no se cobra dos veces.
     */
    private function saltar(string $desde, string $hasta, array $otrosCampos = []): bool
    {
        if ($this->estado !== $desde || ! in_array($hasta, self::TRANSICIONES[$desde] ?? [], true)) {
            return false;
        }

        $cambios = ['estado' => $hasta] + $otrosCampos;

        $filasCambiadas = static::whereKey($this->id)
            ->where('estado', $desde)
            ->update($cambios);

        if ($filasCambiadas === 0) {
            return false;
        }

        // Actualizamos también el objeto que tenemos en memoria.
        $this->forceFill($cambios)->syncOriginal();

        return true;
    }
}
