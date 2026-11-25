<?php

namespace App\Acciones;

use App\Models\Cuenta;
use App\Models\Mesa;
use App\Models\Pago;
use App\Models\Pedido;
use App\Models\Plato;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Pedido del cliente pagado al hacerlo (rama 15). Dos pasos:
 *
 *  1. calcularTotal(): antes de ir a Stripe, comprueba los platos y calcula
 *     el total con los precios del SERVIDOR (no con los del móvil).
 *  2. confirmar(): cuando Stripe dice que está pagado, en una transacción:
 *       - crea el pedido con CrearPedido (sin usuario) → llega a cocina;
 *       - lo apunta en la cuenta abierta de la mesa (o crea una);
 *       - añade a la cuenta una parte YA PAGADA por el importe del pedido.
 *
 * Gracias a esa parte pagada no hay que tocar ni la división de la cuenta ni
 * el cierre de la mesa: al dividir solo se reparte lo que falta (rama 12) y la
 * mesa se cierra cuando todas las partes están pagadas.
 */
class PagarPedido
{
    public function __construct(private CrearPedido $crearPedido) {}

    /**
     * Total del pedido en céntimos con los precios actuales.
     * Lanza un error de validación si algún plato no se puede pedir (no
     * existe, es de otro restaurante o está agotado).
     *
     * @param  array<int, array{plato_id: int, cantidad: int, notas?: ?string}>  $lineas
     */
    public function calcularTotal(Mesa $mesa, array $lineas): int
    {
        $ids = array_unique(array_map(fn ($linea) => (int) $linea['plato_id'], $lineas));

        // Mismo filtro que CrearPedido: a mano, por el restaurante de la mesa
        // y solo platos disponibles.
        $precios = Plato::withoutGlobalScope('restaurante')
            ->where('restaurante_id', $mesa->restaurante_id)
            ->where('disponible', true)
            ->whereIn('id', $ids)
            ->pluck('precio_centimos', 'id');

        if ($lineas === [] || $precios->count() !== count($ids)) {
            throw ValidationException::withMessages([
                'lineas' => 'Algún plato de tu pedido no está disponible. Revisa tu pedido.',
            ]);
        }

        return array_sum(array_map(
            fn ($linea) => (int) $linea['cantidad'] * $precios[(int) $linea['plato_id']],
            $lineas,
        ));
    }

    /**
     * Crea el pedido pagado. Si la misma sesión de Stripe ya se confirmó
     * (por ejemplo, el cliente recarga la página), devuelve ese pedido y no
     * crea nada nuevo.
     *
     * @param  int  $importePagado  Lo que Stripe dice que se ha cobrado.
     *
     * @throws ValidationException si la carta ha cambiado mientras pagaba.
     */
    public function confirmar(Mesa $mesa, array $lineas, string $idSesionStripe, int $importePagado): Pedido
    {
        return DB::transaction(function () use ($mesa, $lineas, $idSesionStripe, $importePagado) {
            // Bloqueamos la mesa: si llegan dos confirmaciones a la vez (doble
            // clic, recargar), la segunda espera y luego ve el pago ya hecho.
            Mesa::withoutGlobalScope('restaurante')->lockForUpdate()->find($mesa->id);

            $pagoYaHecho = Pago::where('referencia_stripe', $idSesionStripe)->first();
            if ($pagoYaHecho) {
                return $pagoYaHecho->parteCuenta->pedido;
            }

            // Si mientras pagaba se ha agotado un plato o ha cambiado su
            // precio, el total ya no es el cobrado: no creamos el pedido.
            try {
                $total = $this->calcularTotal($mesa, $lineas);
            } catch (ValidationException) {
                $total = null;
            }

            if ($total !== $importePagado) {
                throw ValidationException::withMessages([
                    'pago' => 'La carta ha cambiado mientras pagabas y no hemos podido enviar tu pedido. '
                        .'Enseña esta referencia al camarero para que te devuelva el dinero: '.$idSesionStripe,
                ]);
            }

            // 1. El pedido, sin usuario (lo pide el cliente). Llega a cocina.
            $pedido = $this->crearPedido->ejecutar($mesa, $lineas, null);

            // 2. La cuenta abierta de la mesa, o una nueva.
            $cuenta = DividirCuenta::cuentaAbierta($mesa) ?? Cuenta::create([
                'restaurante_id' => $mesa->restaurante_id,
                'mesa_id' => $mesa->id,
                'total_centimos' => 0,
                'estado' => 'abierta',
            ]);

            $pedido->update(['cuenta_id' => $cuenta->id]);
            $cuenta->update(['total_centimos' => $cuenta->total_centimos + $total]);

            // 3. La parte del pedido. No es una persona de la mesa, así que no
            //    lleva número de persona (null): al dividir el resto, las
            //    personas siguen siendo 1, 2, 3... Se crea pendiente y se paga con pagarConTarjeta() (rama 12):
            //    así respeta la regla pendiente → pagado y crea el Pago con la
            //    referencia de Stripe.
            $parte = $cuenta->partes()->create([
                'pedido_id' => $pedido->id,
                'persona' => null,
                'importe_centimos' => $total,
                'estado' => 'pendiente',
            ]);
            $parte->pagarConTarjeta($idSesionStripe);

            return $pedido;
        });
    }
}
