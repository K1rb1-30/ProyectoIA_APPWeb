<?php

namespace App\Estadisticas;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Todos los cálculos de la pantalla de estadísticas (rama 13), en un solo sitio.
 * Un método por bloque de la pantalla.
 *
 * Uso: (new EstadisticasRestaurante($user->restaurante_id))->ventasPorDia();
 *
 * Reglas:
 *  - Cada método hace UNA consulta con agregados (SUM, COUNT, GROUP BY): MySQL
 *    devuelve ya las sumas y no cargamos los pagos uno a uno en memoria.
 *  - El dinero sale SIEMPRE en céntimos (enteros). Se pasa a euros en la vista.
 *  - Ventas = pagos. Lo que no se ha cobrado no cuenta como venta.
 *
 * Aislamiento: estas consultas usan DB::table() con joins, y ahí el global
 * scope del trait PerteneceARestaurante NO actúa (solo funciona en consultas
 * Eloquent del modelo que lo usa). Por eso cada consulta lleva escrito a mano
 * su "where ... restaurante_id = ?" con el restaurante que recibe la clase.
 */
class EstadisticasRestaurante
{
    public function __construct(private int $restauranteId) {}

    /**
     * Tarjetas de arriba: ventas de hoy y de ayer, pedidos de hoy, cobros de
     * hoy y ticket medio (ventas de hoy / pedidos de hoy).
     *
     * @return array{ventas_hoy: int, ventas_ayer: int, variacion: int|null, pedidos_hoy: int, cobros_hoy: int, ticket_medio: int}
     */
    public function resumenDeHoy(): array
    {
        $hoy = today();
        $ayer = today()->subDay();

        $ventasHoy = $this->sumaDePagosEntre($hoy, $hoy->copy()->endOfDay());
        $ventasAyer = $this->sumaDePagosEntre($ayer, $ayer->copy()->endOfDay());

        $pedidosHoy = DB::table('pedidos')
            ->where('restaurante_id', $this->restauranteId)
            ->whereBetween('created_at', [$hoy, $hoy->copy()->endOfDay()])
            ->count();

        $cobrosHoy = $this->pagosDelRestaurante()
            ->whereBetween('pagos.created_at', [$hoy, $hoy->copy()->endOfDay()])
            ->count();

        return [
            'ventas_hoy' => $ventasHoy,
            'ventas_ayer' => $ventasAyer,
            // % de cambio respecto a ayer. null si ayer no se vendió nada (no se puede dividir entre 0).
            'variacion' => $ventasAyer > 0 ? (int) round(($ventasHoy - $ventasAyer) * 100 / $ventasAyer) : null,
            'pedidos_hoy' => $pedidosHoy,
            'cobros_hoy' => $cobrosHoy,
            // Redondeado al céntimo: sigue siendo un entero.
            'ticket_medio' => $pedidosHoy > 0 ? (int) round($ventasHoy / $pedidosHoy) : 0,
        ];
    }

    /**
     * Ventas de cada uno de los últimos $dias días (hoy incluido), del más
     * antiguo al más nuevo. Los días sin ventas salen con 0.
     *
     * SQL:
     *   SELECT DATE(pagos.created_at) AS dia, SUM(pagos.importe_centimos), COUNT(*)
     *   FROM pagos JOIN partes_cuenta ... JOIN cuentas ...
     *   WHERE cuentas.restaurante_id = ? AND pagos.created_at >= ?
     *   GROUP BY DATE(pagos.created_at)
     *
     * @return array<int, array{dia: string, total_centimos: int, pagos: int}>
     */
    public function ventasPorDia(int $dias = 30): array
    {
        $desde = $this->primerDia($dias);

        // keyBy('dia'): para buscar luego cada día por su fecha ('2026-11-23').
        $filas = $this->pagosDelRestaurante()
            ->where('pagos.created_at', '>=', $desde)
            ->selectRaw('DATE(pagos.created_at) AS dia, SUM(pagos.importe_centimos) AS total_centimos, COUNT(*) AS pagos')
            ->groupByRaw('DATE(pagos.created_at)')
            ->get()
            ->keyBy('dia');

        // MySQL no devuelve los días sin pagos. Recorremos las $dias fechas
        // (30 vueltas, no registros) y ponemos 0 donde no haya fila.
        $resultado = [];
        for ($i = 0; $i < $dias; $i++) {
            $dia = $desde->copy()->addDays($i)->toDateString();
            $fila = $filas->get($dia);

            $resultado[] = [
                'dia' => $dia,
                // SUM y COUNT llegan como texto desde MySQL: los pasamos a entero.
                'total_centimos' => (int) ($fila->total_centimos ?? 0),
                'pagos' => (int) ($fila->pagos ?? 0),
            ];
        }

        return $resultado;
    }

    /**
     * Los $limite platos más pedidos en los últimos $dias días, por unidades.
     * El importe usa el precio guardado en cada línea (el del momento del pedido).
     * Cuenta todos los pedidos, estén cobrados o no.
     *
     * SQL:
     *   SELECT platos.id, platos.nombre,
     *          SUM(lineas_pedido.cantidad) AS unidades,
     *          SUM(lineas_pedido.cantidad * lineas_pedido.precio_centimos) AS importe_centimos
     *   FROM lineas_pedido JOIN pedidos ... JOIN platos ...
     *   WHERE pedidos.restaurante_id = ? AND pedidos.created_at >= ?
     *   GROUP BY platos.id, platos.nombre
     *   ORDER BY unidades DESC LIMIT 10
     *
     * @return array<int, array{nombre: string, unidades: int, importe_centimos: int}>
     */
    public function platosMasPedidos(int $limite = 10, int $dias = 30): array
    {
        return DB::table('lineas_pedido')
            ->join('pedidos', 'pedidos.id', '=', 'lineas_pedido.pedido_id')
            ->join('platos', 'platos.id', '=', 'lineas_pedido.plato_id')
            // Aislamiento: pedidos sí tiene restaurante_id.
            ->where('pedidos.restaurante_id', $this->restauranteId)
            ->where('pedidos.created_at', '>=', $this->primerDia($dias))
            ->selectRaw('platos.id, platos.nombre, SUM(lineas_pedido.cantidad) AS unidades, SUM(lineas_pedido.cantidad * lineas_pedido.precio_centimos) AS importe_centimos')
            ->groupBy('platos.id', 'platos.nombre')
            ->orderByDesc('unidades')
            // A igualdad de unidades, por nombre: así el orden no cambia de una vez a otra.
            ->orderBy('platos.nombre')
            ->limit($limite)
            ->get()
            ->map(fn ($fila) => [
                'nombre' => $fila->nombre,
                'unidades' => (int) $fila->unidades,
                'importe_centimos' => (int) $fila->importe_centimos,
            ])
            ->all();
    }

    /**
     * Cobros de los últimos $dias días por método: efectivo, Stripe y TPV
     * (tarjeta sin referencia de Stripe), con su importe y número de pagos.
     * "tarjeta" es Stripe + TPV, para el gráfico circular.
     *
     * SQL (el CASE decide el "canal" de cada pago):
     *   SELECT CASE WHEN metodo = 'efectivo' THEN 'efectivo'
     *               WHEN referencia_stripe IS NULL THEN 'tpv'
     *               ELSE 'stripe' END AS canal,
     *          SUM(importe_centimos), COUNT(*)
     *   FROM pagos JOIN partes_cuenta ... JOIN cuentas ...
     *   WHERE cuentas.restaurante_id = ? AND pagos.created_at >= ?
     *   GROUP BY canal
     *
     * @return array<string, array{importe_centimos: int, numero: int}>
     */
    public function metodosDePago(int $dias = 30): array
    {
        $canal = "CASE WHEN pagos.metodo = 'efectivo' THEN 'efectivo' WHEN pagos.referencia_stripe IS NULL THEN 'tpv' ELSE 'stripe' END";

        $filas = $this->pagosDelRestaurante()
            ->where('pagos.created_at', '>=', $this->primerDia($dias))
            ->selectRaw("{$canal} AS canal, SUM(pagos.importe_centimos) AS importe_centimos, COUNT(*) AS numero")
            ->groupByRaw($canal)
            ->get()
            ->keyBy('canal');

        $resultado = [];
        foreach (['efectivo', 'stripe', 'tpv'] as $clave) {
            $resultado[$clave] = [
                'importe_centimos' => (int) ($filas->get($clave)->importe_centimos ?? 0),
                'numero' => (int) ($filas->get($clave)->numero ?? 0),
            ];
        }

        $resultado['tarjeta'] = [
            'importe_centimos' => $resultado['stripe']['importe_centimos'] + $resultado['tpv']['importe_centimos'],
            'numero' => $resultado['stripe']['numero'] + $resultado['tpv']['numero'],
        ];

        return $resultado;
    }

    /**
     * Pagos de ESTE restaurante. pagos no tiene restaurante_id, así que se
     * llega a él por el camino pago → parte de la cuenta → cuenta:
     *   pagos.parte_cuenta_id = partes_cuenta.id
     *   partes_cuenta.cuenta_id = cuentas.id
     *   cuentas.restaurante_id = el de este restaurante
     */
    private function pagosDelRestaurante(): Builder
    {
        return DB::table('pagos')
            ->join('partes_cuenta', 'partes_cuenta.id', '=', 'pagos.parte_cuenta_id')
            ->join('cuentas', 'cuentas.id', '=', 'partes_cuenta.cuenta_id')
            ->where('cuentas.restaurante_id', $this->restauranteId);
    }

    /**
     * Suma (en céntimos) de los pagos entre dos momentos.
     */
    private function sumaDePagosEntre(Carbon $desde, Carbon $hasta): int
    {
        return (int) $this->pagosDelRestaurante()
            ->whereBetween('pagos.created_at', [$desde, $hasta])
            ->sum('pagos.importe_centimos');
    }

    /**
     * Inicio (00:00) del primer día del periodo. Con 30 días, hoy es el día 30.
     */
    private function primerDia(int $dias): Carbon
    {
        return today()->subDays($dias - 1);
    }
}
