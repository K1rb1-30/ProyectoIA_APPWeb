<?php

namespace App\Http\Controllers\Camarero;

use App\Http\Controllers\Controller;
use App\Models\Pago;
use App\Models\ParteCuenta;
use Illuminate\View\View;

/**
 * Pestaña «Cobros» del camarero (rama 12). Sin maqueta propia: sigue el estilo
 * de camarero/mesas y camarero/detalle-mesa.
 *
 * Aislamiento: ni partes_cuenta ni pagos tienen restaurante_id. Se filtran con
 * whereHas sobre Cuenta, que lleva el global scope del trait: el camarero solo
 * ve lo de su restaurante.
 */
class CobroController extends Controller
{
    public function index(): View
    {
        // Partes que esperan el cobro en efectivo, ordenadas por mesa y persona.
        $esperando = ParteCuenta::esperandoEfectivo()
            ->with('cuenta.mesa')
            ->get()
            ->sortBy([fn ($parte) => $parte->cuenta->mesa->numero, 'persona'])
            ->groupBy(fn ($parte) => $parte->cuenta->mesa->numero);

        // Pagos de hoy del restaurante, del más nuevo al más viejo.
        $pagosHoy = Pago::whereDate('created_at', today())
            ->whereHas('parteCuenta.cuenta')
            ->with('parteCuenta.cuenta.mesa')
            ->latest('id')
            ->get();

        return view('camarero.cobros', [
            'esperando' => $esperando,
            'pagosHoy' => $pagosHoy,
            // Totales de hoy. Tarjeta con referencia = Stripe; sin ella = TPV.
            'totalEfectivo' => $pagosHoy->where('metodo', 'efectivo')->sum('importe_centimos'),
            'totalStripe' => $pagosHoy->where('metodo', 'tarjeta')->whereNotNull('referencia_stripe')->sum('importe_centimos'),
            'totalTpv' => $pagosHoy->where('metodo', 'tarjeta')->whereNull('referencia_stripe')->sum('importe_centimos'),
        ]);
    }
}
