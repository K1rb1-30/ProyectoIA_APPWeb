<?php

namespace App\Http\Controllers\Cliente;

use App\Acciones\CrearPedido;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cliente\PedidoClienteRequest;
use App\Models\Mesa;
use App\Models\Pedido;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Autopedido del cliente desde el QR (rama 9). Pantallas públicas, sin login.
 *
 * - El carrito vive en el navegador del cliente (localStorage, ver
 *   resources/js/carrito.js). El servidor solo se entera al enviar.
 * - Para crear el pedido se usa CrearPedido (rama 5), con usuario null.
 * - Para que nadie pueda ver pedidos ajenos cambiando la URL, guardamos en la
 *   SESIÓN del navegador los ids de los pedidos que ha hecho. La pantalla de
 *   estado solo enseña esos.
 */
class AutopedidoController extends Controller
{
    // Clave de la sesión donde guardamos los ids de los pedidos del cliente.
    private const CLAVE_SESION = 'pedidos_cliente';

    /**
     * Pantalla "Mi pedido": lista del carrito, notas y botón de enviar.
     * Los platos los pinta Alpine a partir del carrito; aquí le pasamos los
     * datos de los platos disponibles (nombre, precio, foto...).
     */
    public function create(string $slug, int $numero): View
    {
        $mesa = Mesa::delQr($slug, $numero);
        $categorias = CartaController::categoriasDisponibles($mesa->restaurante);

        // { idPlato: { nombre, descripcion, precio, foto } } para Alpine.
        $platos = $categorias->flatMap->platos->mapWithKeys(fn ($plato) => [
            $plato->id => [
                'nombre' => $plato->nombre,
                'descripcion' => $plato->descripcion,
                'precio' => $plato->precio_centimos,
                'foto' => $plato->urlFoto(),
            ],
        ]);

        return view('cliente.mi-pedido', [
            'restaurante' => $mesa->restaurante,
            'mesa' => $mesa,
            'platos' => $platos,
        ]);
    }

    /**
     * Envía el pedido a cocina.
     */
    public function store(PedidoClienteRequest $request, string $slug, int $numero, CrearPedido $crearPedido): RedirectResponse
    {
        $mesa = Mesa::delQr($slug, $numero);

        // $usuario = null: lo pide el cliente, no un camarero. Si algún plato
        // no existe, es de otro restaurante o se ha agotado, CrearPedido lanza
        // un error de validación y Laravel vuelve a "Mi pedido" con el aviso.
        $pedido = $crearPedido->ejecutar($mesa, array_values($request->validated('lineas')), null);

        // Apuntamos el pedido en la sesión de este navegador.
        $request->session()->push(self::CLAVE_SESION, $pedido->id);

        // "vaciarCarrito" avisa a la pantalla de estado de que borre el carrito.
        return redirect()->route('cliente.estado', ['slug' => $slug, 'numero' => $numero])
            ->with('vaciarCarrito', true);
    }

    /**
     * Pantalla de estado: los pedidos que este navegador ha hecho en esta mesa.
     */
    public function estado(Request $request, string $slug, int $numero): View
    {
        $mesa = Mesa::delQr($slug, $numero);

        return view('cliente.estado-pedido', [
            'restaurante' => $mesa->restaurante,
            'mesa' => $mesa,
            'pedidos' => $this->pedidosDelCliente($request, $mesa),
        ]);
    }

    /**
     * Estados de esos mismos pedidos en JSON, para el polling cada 5 s:
     *   { "pedidos": [ { "id": 12, "estado": "preparando" } ] }
     */
    public function datosEstado(Request $request, string $slug, int $numero): JsonResponse
    {
        $mesa = Mesa::delQr($slug, $numero);

        return response()->json([
            'pedidos' => $this->pedidosDelCliente($request, $mesa)
                ->map(fn (Pedido $pedido) => ['id' => $pedido->id, 'estado' => $pedido->estado])
                ->values(),
        ]);
    }

    /**
     * Pedidos de esta mesa cuyos ids están en la sesión del navegador, del más
     * nuevo al más antiguo. Filtramos a mano por la mesa (y con ella, por el
     * restaurante) en vez de usar el filtro del trait, igual que en la carta.
     */
    private function pedidosDelCliente(Request $request, Mesa $mesa): Collection
    {
        $ids = $request->session()->get(self::CLAVE_SESION, []);

        return Pedido::withoutGlobalScope('restaurante')
            ->where('mesa_id', $mesa->id)
            ->whereIn('id', $ids)
            ->with(['lineas.plato' => fn ($consulta) => $consulta->withoutGlobalScope('restaurante')])
            ->latest('id')
            ->get();
    }
}
