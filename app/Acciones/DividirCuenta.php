<?php

namespace App\Acciones;

use App\Models\Cuenta;
use App\Models\Mesa;
use App\Models\Pedido;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use LogicException;

/**
 * Crea (o rehace) la cuenta de una mesa y la divide en partes, una por persona.
 * Es el ÚNICO sitio donde se crean cuentas y partes.
 *
 * Dos formas de dividir:
 *  - 'iguales': el total entre N personas. El céntimo que sobra va a la primera.
 *  - 'platos':  cada línea de pedido se asigna entera a una persona, y cada
 *               persona paga la suma de sus líneas.
 *
 * Uso:
 *   (new DividirCuenta)->ejecutar($mesa, 'iguales', 3);
 *   (new DividirCuenta)->ejecutar($mesa, 'platos', 0, [idLinea => persona, ...]);
 */
class DividirCuenta
{
    /**
     * Reparte un total en N partes lo más iguales posible, en céntimos.
     * La suma de las partes es SIEMPRE exactamente el total.
     *
     * Ejemplo: repartirAPartesIguales(3100, 3) → [1034, 1033, 1033]
     *   3100 / 3 = 1033 (división entera) y sobra 3100 % 3 = 1 céntimo,
     *   que va a la primera parte (regla de CLAUDE.md).
     *
     * @return array<int, int>
     */
    public static function repartirAPartesIguales(int $totalCentimos, int $personas): array
    {
        if ($personas < 1) {
            throw new LogicException('Hay que dividir entre al menos 1 persona.');
        }

        // intdiv es la división entera: 3100 / 3 → 1033, sin decimales.
        $base = intdiv($totalCentimos, $personas);
        // El resto (%) son los céntimos que no se han podido repartir.
        $sobra = $totalCentimos % $personas;

        $partes = array_fill(0, $personas, $base);
        $partes[0] += $sobra;

        return $partes;
    }

    /**
     * Pedidos que entran en la cuenta de la mesa: los que no tienen cuenta
     * todavía y, si ya hay una cuenta abierta, también los suyos. Filtra a
     * mano por la mesa (sin el filtro del trait), porque lo usa el cliente
     * sin iniciar sesión.
     */
    public static function pedidosDeLaMesa(Mesa $mesa, ?Cuenta $cuentaAbierta): Collection
    {
        return Pedido::withoutGlobalScope('restaurante')
            ->where('mesa_id', $mesa->id)
            ->where(function ($consulta) use ($cuentaAbierta) {
                $consulta->whereNull('cuenta_id');
                if ($cuentaAbierta) {
                    $consulta->orWhere('cuenta_id', $cuentaAbierta->id);
                }
            })
            ->with(['lineas.plato' => fn ($consulta) => $consulta->withoutGlobalScope('restaurante')])
            ->orderBy('id')
            ->get();
    }

    /**
     * Cuenta abierta de la mesa, o null si no hay.
     */
    public static function cuentaAbierta(Mesa $mesa): ?Cuenta
    {
        return Cuenta::withoutGlobalScope('restaurante')
            ->where('mesa_id', $mesa->id)
            ->where('estado', 'abierta')
            ->latest('id')
            ->first();
    }

    /**
     * @param  string  $modo  'iguales' o 'platos'.
     * @param  int  $personas  Número de personas (solo para 'iguales').
     * @param  array<int, int>  $asignaciones  { idLinea: persona } (solo para 'platos').
     *
     * @throws ValidationException si no hay nada que cobrar, ya hay pagos o
     *                             faltan platos por asignar.
     */
    public function ejecutar(Mesa $mesa, string $modo, int $personas = 1, array $asignaciones = []): Cuenta
    {
        // Transacción: o se guarda la cuenta con todas sus partes, o nada.
        return DB::transaction(function () use ($mesa, $modo, $personas, $asignaciones) {
            $cuenta = self::cuentaAbierta($mesa);

            // Si alguien ya ha pagado su parte (ramas 11 y 12), no se puede
            // rehacer el reparto: cambiaría lo que ya se ha cobrado.
            if ($cuenta && $cuenta->tienePagos()) {
                throw ValidationException::withMessages([
                    'cuenta' => 'La cuenta ya tiene pagos y no se puede volver a dividir.',
                ]);
            }

            $pedidos = self::pedidosDeLaMesa($mesa, $cuenta);
            $lineas = $pedidos->flatMap->lineas;

            if ($lineas->isEmpty()) {
                throw ValidationException::withMessages([
                    'cuenta' => 'Esta mesa todavía no tiene nada que pagar.',
                ]);
            }

            $total = $pedidos->sum(fn (Pedido $pedido) => $pedido->totalCentimos());

            // Importes por persona: [persona => céntimos].
            $importes = $modo === 'platos'
                ? $this->importesPorPlatos($lineas, $asignaciones)
                : array_combine(range(1, $personas), self::repartirAPartesIguales($total, $personas));

            // Comprobación de seguridad (regla de CLAUDE.md): la suma de las
            // partes tiene que ser EXACTAMENTE el total. Si no, no se guarda.
            if (array_sum($importes) !== $total) {
                throw new LogicException('La suma de las partes no coincide con el total de la cuenta.');
            }

            // Creamos la cuenta o actualizamos la abierta con el total nuevo.
            if ($cuenta) {
                $cuenta->update(['total_centimos' => $total]);
                // Borramos el reparto anterior para crear el nuevo.
                $cuenta->partes()->delete();
            } else {
                $cuenta = Cuenta::create([
                    'restaurante_id' => $mesa->restaurante_id,
                    'mesa_id' => $mesa->id,
                    'total_centimos' => $total,
                    'estado' => 'abierta',
                ]);
            }

            // Los pedidos quedan apuntados en esta cuenta.
            Pedido::withoutGlobalScope('restaurante')
                ->whereIn('id', $pedidos->pluck('id'))
                ->update(['cuenta_id' => $cuenta->id]);

            foreach ($importes as $persona => $importe) {
                $cuenta->partes()->create([
                    'persona' => $persona,
                    'importe_centimos' => $importe,
                    'estado' => 'pendiente',
                ]);
            }

            return $cuenta->load('partes');
        });
    }

    /**
     * Reparto por platos: cada línea va entera a una persona.
     * Las personas se renumeran 1, 2, 3... en orden, para que no queden huecos
     * (si se asigna a las personas 1 y 3, quedan como 1 y 2).
     *
     * @return array<int, int> [persona => céntimos]
     */
    private function importesPorPlatos($lineas, array $asignaciones): array
    {
        $porPersona = [];

        foreach ($lineas as $linea) {
            if (! isset($asignaciones[$linea->id])) {
                throw ValidationException::withMessages([
                    'asignaciones' => 'Asigna cada plato a una persona.',
                ]);
            }
            $persona = (int) $asignaciones[$linea->id];
            $porPersona[$persona] = ($porPersona[$persona] ?? 0) + $linea->subtotalCentimos();
        }

        ksort($porPersona);

        // array_values quita los números originales; luego numeramos desde 1.
        $importes = array_values($porPersona);

        return array_combine(range(1, count($importes)), $importes);
    }
}
