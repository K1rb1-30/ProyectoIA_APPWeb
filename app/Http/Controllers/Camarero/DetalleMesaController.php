<?php

namespace App\Http\Controllers\Camarero;

use App\Acciones\DividirCuenta;
use App\Http\Controllers\Controller;
use App\Models\Mesa;
use App\Models\ParteCuenta;
use App\Models\Pedido;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Detalle de una mesa para el camarero (rama 12): pedidos, cuenta, cobro en
 * efectivo y cierre de la mesa.
 *
 * Aislamiento: {mesa} de la URL se busca con el global scope del trait
 * PerteneceARestaurante, así que la mesa de otro restaurante da 404. Las
 * partes no tienen restaurante_id: se buscan con ParteCuenta::deLaMesa(),
 * que exige que sean de una cuenta de ESTA mesa.
 */
class DetalleMesaController extends Controller
{
    public function show(Mesa $mesa): View
    {
        // Cuenta abierta (o null si el cliente aún no ha pedido la cuenta) y
        // los pedidos que entran: los de la cuenta y los hechos después.
        $cuenta = DividirCuenta::cuentaAbierta($mesa)?->load('partes.pagos');
        $pedidos = DividirCuenta::pedidosDeLaMesa($mesa, $cuenta);

        return view('camarero.detalle-mesa', [
            'mesa' => $mesa,
            'cuenta' => $cuenta,
            'pedidos' => $pedidos,
            'totalPedidosCentimos' => $pedidos->sum(fn (Pedido $pedido) => $pedido->totalCentimos()),
            // Frases de lo que falta para cerrar (vacío = se puede cerrar).
            'falta' => $cuenta?->faltaPorCobrar() ?? [],
            // Hay pedidos sin cuenta: el camarero puede dividirlos él mismo.
            'puedeDividir' => $pedidos->contains(fn (Pedido $pedido) => $pedido->cuenta_id === null),
            // Si ya hay pagos, solo se divide lo nuevo (DividirCuenta).
            'soloLoNuevo' => $cuenta?->tienePagos() ?? false,
        ]);
    }

    /**
     * El camarero divide la cuenta a partes iguales cuando nadie la ha pedido
     * desde el QR, o divide lo pedido después si ya hay pagos. Usa el MISMO
     * código que el cliente (DividirCuenta): no hay otro reparto.
     */
    public function dividir(Request $request, Mesa $mesa, DividirCuenta $dividirCuenta): RedirectResponse
    {
        $datos = $request->validate(
            ['personas' => ['required', 'integer', 'min:1', 'max:20']],
            [],
            ['personas' => 'número de personas'],
        );

        try {
            $dividirCuenta->ejecutar($mesa, 'iguales', (int) $datos['personas']);
        } catch (ValidationException $error) {
            return $this->volverAlDetalle($mesa)->withErrors($error->errors());
        }

        return $this->volverAlDetalle($mesa)->with('estado', 'Cuenta dividida entre '.$datos['personas'].'.');
    }

    /**
     * Cobrar una parte. Tres formas (campo "metodo"):
     *  - confirmar: el cliente eligió efectivo en el móvil y el camarero recibe
     *               el dinero. pendiente_efectivo → pagado. (Es lo que se hace
     *               si no llega "metodo".)
     *  - efectivo:  el cliente paga en efectivo sin haber elegido nada en el
     *               móvil. pendiente → pendiente_efectivo → pagado.
     *  - tpv:       el cliente paga con el datáfono del bar. pendiente → pagado.
     * Cada método del modelo solo funciona desde su estado, así que una parte
     * pagada nunca se vuelve a cobrar.
     */
    public function cobrar(Request $request, Mesa $mesa, int $parte): RedirectResponse
    {
        $metodo = $request->validate([
            'metodo' => ['nullable', 'in:confirmar,efectivo,tpv'],
        ])['metodo'] ?? 'confirmar';

        $parte = ParteCuenta::deLaMesa($mesa, $parte) ?? abort(404);

        $cobrado = match ($metodo) {
            'efectivo' => $parte->cobrarEfectivoEnMesa(),
            'tpv' => $parte->pagarConTpv(),
            default => $parte->confirmarEfectivo(),
        };

        if (! $cobrado) {
            return $this->volverAlDetalle($mesa)->withErrors(['cobro' => $this->motivoNoCobrado($parte, $metodo)]);
        }

        $importe = number_format($parte->importe_centimos / 100, 2, ',', '.');
        $como = $metodo === 'tpv' ? 'con TPV' : 'en efectivo';

        return $this->volverAlDetalle($mesa)
            ->with('estado', "Cobrados {$importe} € {$como} a la persona {$parte->persona}.");
    }

    /**
     * Mensaje para el camarero cuando no se ha podido cobrar.
     */
    private function motivoNoCobrado(ParteCuenta $parte, string $metodo): string
    {
        $persona = 'La persona '.$parte->persona;

        return match (true) {
            $parte->estaPagada() => "{$persona} ya había pagado.",
            $metodo === 'confirmar' => "{$persona} no ha elegido pagar en efectivo.",
            $metodo === 'tpv' => "{$persona} ha elegido pagar en efectivo: cóbrale en efectivo.",
            default => "No se ha podido cobrar a {$persona}.",
        };
    }

    /**
     * Cierra la cuenta y deja la mesa libre. Cuenta::cerrar() comprueba que
     * todo esté cobrado; si no, lanza un error con lo que falta.
     */
    public function cerrar(Mesa $mesa): RedirectResponse
    {
        $cuenta = DividirCuenta::cuentaAbierta($mesa);

        if (! $cuenta) {
            return $this->volverAlDetalle($mesa)->withErrors([
                'cuenta' => 'Esta mesa no tiene ninguna cuenta abierta. El cliente tiene que pedir la cuenta y dividirla.',
            ]);
        }

        try {
            $cuenta->cerrar();
        } catch (ValidationException $error) {
            // Volvemos al detalle con la lista de lo que falta.
            return $this->volverAlDetalle($mesa)->withErrors($error->errors());
        }

        return redirect()->route('camarero.mesas')
            ->with('estado', "Mesa {$mesa->numero} cerrada. Ya está libre.");
    }

    private function volverAlDetalle(Mesa $mesa): RedirectResponse
    {
        return redirect()->route('camarero.mesas.detalle', $mesa);
    }
}
