<?php

namespace App\Http\Controllers\Admin;

use App\Estadisticas\EstadisticasRestaurante;
use App\Http\Controllers\Controller;
use App\Models\Mesa;
use App\Models\Pedido;
use App\Models\Plato;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Panel principal del administrador (rama 17): un resumen de hoy, las mesas,
 * la carta y los pedidos en curso, con enlaces a cada pantalla.
 *
 * Solo prepara datos; la vista no hace ninguna consulta.
 *
 * Aislamiento: Mesa, Plato y Pedido usan el trait PerteneceARestaurante, así
 * que sus consultas ya vienen filtradas por el restaurante del admin logueado.
 * EstadisticasRestaurante filtra a mano por el restaurante que recibe.
 *
 * Pocas consultas: todo lo que son cifras se pide a MySQL ya contado
 * (COUNT, SUM). Lo único que se carga en una lista son las mesas (un bar
 * tiene unas decenas) y 5 platos.
 */
class PanelController extends Controller
{
    /** Cuántas mesas caben en la cuadrícula del panel. */
    private const MAXIMO_MESAS = 24;

    /** Cuántos platos se enseñan en la carta rápida. */
    private const PLATOS_A_MOSTRAR = 5;

    /**
     * __invoke: el controlador tiene una sola acción, así que en la ruta basta
     * con poner PanelController::class.
     */
    public function __invoke(Request $request): View
    {
        $admin = $request->user();

        return view('admin.panel', [
            'admin' => $admin,
            'restaurante' => $admin->restaurante,
            // Ventas de hoy y de ayer, pedidos de hoy y ticket medio (4 consultas).
            'resumen' => (new EstadisticasRestaurante($admin->restaurante_id))->resumenDeHoy(),
            ...$this->datosMesas(),
            ...$this->datosCarta(),
            'pedidosEnCurso' => $this->pedidosEnCurso(),
        ]);
    }

    /**
     * Mesas: una sola consulta con las columnas justas. Libres y ocupadas se
     * cuentan sobre esa misma lista, sin volver a preguntar a MySQL.
     *
     * @return array{mesas: \Illuminate\Support\Collection, totalMesas: int, mesasOcupadas: int, mesasLibres: int, ocupacion: int}
     */
    private function datosMesas(): array
    {
        $mesas = Mesa::orderBy('numero')->get(['id', 'numero', 'estado']);

        $total = $mesas->count();
        $ocupadas = $mesas->where('estado', 'ocupada')->count();

        return [
            // La cuadrícula solo enseña las primeras; la vista dice cuántas faltan.
            'mesas' => $mesas->take(self::MAXIMO_MESAS),
            'totalMesas' => $total,
            'mesasOcupadas' => $ocupadas,
            'mesasLibres' => $total - $ocupadas,
            // % de mesas ocupadas, redondeado. Sin mesas, 0 (no se divide entre 0).
            'ocupacion' => $total > 0 ? (int) round($ocupadas * 100 / $total) : 0,
        ];
    }

    /**
     * Carta: el total de platos y de agotados en UNA consulta, y unos pocos
     * platos para la carta rápida.
     *
     * @return array{totalPlatos: int, platosAgotados: int, platos: \Illuminate\Database\Eloquent\Collection}
     */
    private function datosCarta(): array
    {
        // SELECT COUNT(*) AS total, SUM(disponible = 0) AS agotados FROM platos
        // WHERE restaurante_id = ? (el WHERE lo pone el global scope).
        // "disponible = 0" vale 1 si el plato está agotado y 0 si no: al
        // sumarlo, salen los agotados.
        $cifras = Plato::selectRaw('COUNT(*) AS total, SUM(disponible = 0) AS agotados')->first();

        $platos = Plato::with('alergenos')
            // disponible = 0 va antes que 1: los agotados salen primero, para
            // poder volver a activarlos desde aquí.
            ->orderBy('disponible')
            ->orderBy('nombre')
            ->limit(self::PLATOS_A_MOSTRAR)
            ->get();

        return [
            // COUNT y SUM llegan de MySQL como texto (y SUM como null si no hay
            // platos): los pasamos a entero.
            'totalPlatos' => (int) $cifras->total,
            'platosAgotados' => (int) $cifras->agotados,
            'platos' => $platos,
        ];
    }

    /**
     * Pedidos que todavía no se han servido, contados por estado en UNA
     * consulta. Son los de cualquier día: un pedido olvidado en "listo" desde
     * ayer también tiene que verse.
     *
     * SELECT estado, COUNT(*) AS numero FROM pedidos
     * WHERE restaurante_id = ? AND estado IN ('pendiente', 'preparando', 'listo')
     * GROUP BY estado
     *
     * @return array{pendiente: int, preparando: int, listo: int}
     */
    private function pedidosEnCurso(): array
    {
        $porEstado = Pedido::whereIn('estado', ['pendiente', 'preparando', 'listo'])
            ->selectRaw('estado, COUNT(*) AS numero')
            ->groupBy('estado')
            // pluck da [estado => numero], por ejemplo ['pendiente' => '2'].
            ->pluck('numero', 'estado');

        // MySQL no devuelve los estados sin pedidos: les ponemos 0.
        return [
            'pendiente' => (int) ($porEstado['pendiente'] ?? 0),
            'preparando' => (int) ($porEstado['preparando'] ?? 0),
            'listo' => (int) ($porEstado['listo'] ?? 0),
        ];
    }
}
