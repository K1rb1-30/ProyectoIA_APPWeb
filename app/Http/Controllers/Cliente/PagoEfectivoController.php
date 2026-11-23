<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\Mesa;
use App\Models\ParteCuenta;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * El cliente elige pagar su parte en efectivo (rama 12). Pantallas públicas,
 * sin login.
 *
 * El cliente SOLO puede llevar su parte de "pendiente" a "pendiente_efectivo".
 * Marcarla como "pagado" lo hace el camarero cuando recibe el dinero
 * (Camarero\DetalleMesaController::cobrar). Aquí no hay ninguna ruta para eso.
 */
class PagoEfectivoController extends Controller
{
    /**
     * POST: "Pagaré en efectivo". pendiente → pendiente_efectivo.
     */
    public function elegir(string $slug, int $numero, int $parte): RedirectResponse
    {
        $mesa = Mesa::delQr($slug, $numero);
        // deLaMesa filtra a mano: la parte tiene que ser de ESTA mesa y restaurante.
        $parte = ParteCuenta::deLaMesa($mesa, $parte) ?? abort(404);
        $rutaParte = ['slug' => $slug, 'numero' => $numero, 'parte' => $parte->id];

        if ($parte->cuenta->estado !== 'abierta') {
            return $this->volverALaCuenta($slug, $numero, 'Esta cuenta ya está cerrada.');
        }

        // Si ya la había elegido (doble clic, volver atrás...), solo enseñamos la pantalla.
        if ($parte->estado === 'pendiente_efectivo') {
            return redirect()->route('cliente.pago.efectivo', $rutaParte);
        }

        // pedirEfectivo() solo funciona desde "pendiente". Si devuelve false,
        // la parte ya estaba pagada.
        if (! $parte->pedirEfectivo()) {
            return $this->volverALaCuenta($slug, $numero, 'Esta parte ya está pagada.');
        }

        return redirect()->route('cliente.pago.efectivo', $rutaParte);
    }

    /**
     * GET: pantalla "Avisa al camarero, vendrá a cobrarte" (maqueta cliente/pago-efectivo).
     */
    public function show(string $slug, int $numero, int $parte): View|RedirectResponse
    {
        $mesa = Mesa::delQr($slug, $numero);
        $parte = ParteCuenta::deLaMesa($mesa, $parte) ?? abort(404);

        if ($parte->estaPagada()) {
            return $this->volverALaCuenta($slug, $numero, 'Esta parte ya está pagada.');
        }

        // Si todavía no ha elegido efectivo, que lo elija en la cuenta.
        if ($parte->estado !== 'pendiente_efectivo') {
            return $this->volverALaCuenta($slug, $numero, 'Elige cómo quieres pagar tu parte.');
        }

        return view('cliente.pago-efectivo', [
            'restaurante' => $mesa->restaurante,
            'mesa' => $mesa,
            'parte' => $parte,
            'cuenta' => $parte->cuenta,
        ]);
    }

    private function volverALaCuenta(string $slug, int $numero, string $aviso): RedirectResponse
    {
        return redirect()->route('cliente.cuenta', ['slug' => $slug, 'numero' => $numero])
            ->with('estado', $aviso);
    }
}
