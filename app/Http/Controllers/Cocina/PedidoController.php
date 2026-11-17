<?php

namespace App\Http\Controllers\Cocina;

use App\Http\Controllers\Controller;
use App\Models\LineaPedido;
use App\Models\Pedido;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Tablero de la cocina.
 *
 * Todas las consultas pasan por el global scope de PerteneceARestaurante: la
 * cocina solo ve los pedidos de su restaurante. El {pedido} de la URL también
 * se busca con ese filtro, así que el pedido de otro restaurante da 404.
 */
class PedidoController extends Controller
{
    /**
     * Pantalla del tablero. Le pasamos ya los pedidos para que salga llena
     * desde el principio, sin esperar al primer refresco de 5 s.
     */
    public function index(): View
    {
        return view('cocina.pedidos', [
            'pedidos' => $this->pedidosActivos(),
        ]);
    }

    /**
     * Los mismos datos en JSON. El tablero llama aquí cada 5 s (polling).
     */
    public function datos(): JsonResponse
    {
        return response()->json([
            'pedidos' => $this->pedidosActivos(),
        ]);
    }

    /**
     * Pasa un pedido al estado siguiente. Responde en JSON porque la llamada
     * la hace el JavaScript del tablero, no un formulario normal.
     */
    public function cambiarEstado(Request $request, Pedido $pedido): JsonResponse
    {
        $datos = $request->validate([
            'estado' => ['required', Rule::in(Pedido::ESTADOS)],
        ]);

        $estadoAnterior = $pedido->estado;

        if (! $pedido->cambiarEstado($datos['estado'])) {
            // 422 = "no se puede procesar": la petición está bien escrita,
            // pero el salto de estado no está permitido.
            return response()->json([
                'mensaje' => "El pedido #{$pedido->id} no puede pasar de {$estadoAnterior} a {$datos['estado']}.",
            ], 422);
        }

        return response()->json([
            'mensaje' => "Pedido #{$pedido->id}: {$estadoAnterior} → {$pedido->estado}.",
        ]);
    }

    /**
     * Único sitio que monta los datos del tablero, para que la primera carga
     * y el JSON del polling tengan siempre la misma forma.
     *
     * Pedidos sin servir, del más antiguo al más nuevo (el que más lleva
     * esperando sale arriba). "with" carga mesas, líneas y platos de golpe.
     */
    private function pedidosActivos(): array
    {
        return Pedido::with(['mesa', 'lineas.plato'])
            ->whereIn('estado', ['pendiente', 'preparando', 'listo'])
            ->orderBy('created_at')
            ->orderBy('id')
            ->get()
            ->map(fn (Pedido $pedido) => [
                'id' => $pedido->id,
                'estado' => $pedido->estado,
                'siguiente_estado' => $pedido->siguienteEstado(),
                'mesa' => $pedido->mesa->numero,
                'hora' => $pedido->created_at->format('H:i'),
                // Minutos enteros desde que se pidió.
                'minutos' => (int) $pedido->created_at->diffInMinutes(now()),
                'lineas' => $pedido->lineas->map(fn (LineaPedido $linea) => [
                    'cantidad' => $linea->cantidad,
                    'plato' => $linea->plato->nombre,
                    'notas' => $linea->notas,
                ])->all(),
            ])
            ->all();
    }
}
