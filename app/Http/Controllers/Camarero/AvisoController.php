<?php

namespace App\Http\Controllers\Camarero;

use App\Http\Controllers\Controller;
use App\Models\LineaPedido;
use App\Models\Pedido;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

/**
 * Avisos de plato listo para el camarero (rama 7).
 *
 * Todas las consultas pasan por el global scope de PerteneceARestaurante: el
 * camarero solo ve los pedidos de su restaurante, y el {pedido} de la URL de
 * otro restaurante da 404.
 */
class AvisoController extends Controller
{
    /**
     * Pantalla de avisos. Los datos los trae el JavaScript del layout (ver
     * resources/js/avisos-camarero.js), así que aquí solo se devuelve la vista.
     */
    public function index(): View
    {
        return view('camarero.avisos');
    }

    /**
     * JSON con los pedidos listos para servir. El layout del camarero lo
     * consulta cada 5 s (polling) para la pantalla y para el contador.
     */
    public function datos(): JsonResponse
    {
        return response()->json([
            'pedidos' => $this->pedidosListos(),
        ]);
    }

    /**
     * Marca un pedido como servido. Usa cambiarEstado(), que solo deja pasar
     * de "listo" a "servido": si el pedido aún no está listo (o ya se sirvió),
     * no cambia nada y respondemos 422.
     */
    public function servido(Pedido $pedido): JsonResponse
    {
        if (! $pedido->cambiarEstado('servido')) {
            return response()->json([
                'mensaje' => "El pedido de la mesa {$pedido->mesa->numero} no está listo para servir.",
            ], 422);
        }

        return response()->json([
            'mensaje' => "Pedido de la mesa {$pedido->mesa->numero} servido.",
        ]);
    }

    /**
     * Pedidos en estado "listo", del que más lleva esperando al más nuevo.
     *
     * Usamos updated_at porque es la hora del último cambio de estado, es
     * decir, cuando la cocina lo marcó como listo.
     */
    private function pedidosListos(): array
    {
        return Pedido::with(['mesa', 'lineas.plato'])
            ->where('estado', 'listo')
            ->orderBy('updated_at')
            ->orderBy('id')
            ->get()
            ->map(fn (Pedido $pedido) => [
                'id' => $pedido->id,
                'mesa' => $pedido->mesa->numero,
                // Minutos enteros desde que la cocina lo dejó listo.
                'minutos' => (int) $pedido->updated_at->diffInMinutes(now()),
                'lineas' => $pedido->lineas->map(fn (LineaPedido $linea) => [
                    'cantidad' => $linea->cantidad,
                    'plato' => $linea->plato->nombre,
                    'notas' => $linea->notas,
                ])->all(),
            ])
            ->all();
    }
}
