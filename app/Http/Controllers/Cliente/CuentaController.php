<?php

namespace App\Http\Controllers\Cliente;

use App\Acciones\DividirCuenta;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cliente\DividirCuentaRequest;
use App\Models\Mesa;
use App\Models\Pedido;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * Pantalla "Cuenta" del cliente (rama 10): ver el total de la mesa y
 * dividirlo a partes iguales o por platos. Pública, sin login.
 * Pagar cada parte llega con las ramas 11 (tarjeta) y 12 (efectivo).
 */
class CuentaController extends Controller
{
    public function show(string $slug, int $numero): View
    {
        $mesa = Mesa::delQr($slug, $numero);
        $cuenta = DividirCuenta::cuentaAbierta($mesa)?->load('partes');
        $pedidos = DividirCuenta::pedidosDeLaMesa($mesa, $cuenta);

        // Rama 12: si ya hay pagos, el formulario solo divide lo pedido
        // después (los pedidos que aún no tienen cuenta).
        $soloLoNuevo = $cuenta?->tienePagos() ?? false;
        $pedidosFormulario = $soloLoNuevo ? $pedidos->whereNull('cuenta_id') : $pedidos;

        return view('cliente.cuenta', [
            'soloLoNuevo' => $soloLoNuevo,
            'lineasFormulario' => $pedidosFormulario->flatMap->lineas->values(),
            'totalFormulario' => $pedidosFormulario->sum(fn (Pedido $pedido) => $pedido->totalCentimos()),
            'restaurante' => $mesa->restaurante,
            'mesa' => $mesa,
            'cuenta' => $cuenta,
            'lineas' => $pedidos->flatMap->lineas->values(),
            // Total de lo que se va a cobrar ahora (incluye pedidos nuevos).
            'totalCentimos' => $pedidos->sum(fn (Pedido $pedido) => $pedido->totalCentimos()),
            // Pedidos hechos después de dividir: hay que volver a dividir.
            'hayPedidosNuevos' => $cuenta !== null && $pedidos->contains(fn (Pedido $pedido) => $pedido->cuenta_id === null),
        ]);
    }

    public function dividir(DividirCuentaRequest $request, string $slug, int $numero, DividirCuenta $dividirCuenta): RedirectResponse
    {
        $mesa = Mesa::delQr($slug, $numero);

        // Si no hay nada que cobrar o ya hay pagos, DividirCuenta lanza un
        // error de validación y Laravel vuelve a la pantalla con el aviso.
        $dividirCuenta->ejecutar(
            $mesa,
            $request->validated('modo'),
            (int) $request->validated('personas', 1),
            array_map('intval', $request->validated('asignaciones') ?? []),
        );

        return redirect()->route('cliente.cuenta', ['slug' => $slug, 'numero' => $numero])
            ->with('estado', 'Cuenta dividida. Cada persona ya puede ver lo que le toca.');
    }
}
