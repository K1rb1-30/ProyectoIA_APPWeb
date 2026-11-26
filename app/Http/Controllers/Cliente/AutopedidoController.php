<?php

namespace App\Http\Controllers\Cliente;

use App\Acciones\PagarPedido;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cliente\PedidoClienteRequest;
use App\Models\Mesa;
use App\Models\Pago;
use App\Models\Pedido;
use App\Servicios\PasarelaStripe;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

/**
 * Autopedido del cliente desde el QR (rama 9). Pantallas públicas, sin login.
 *
 * - El carrito vive en el navegador del cliente (localStorage, ver
 *   resources/js/carrito.js). El servidor solo se entera al enviar.
 * - Rama 15: el pedido se PAGA con tarjeta (Stripe) antes de ir a cocina. No
 *   existe ningún pedido del cliente sin pagar. Para crearlo se usa
 *   PagarPedido, que a su vez usa CrearPedido (rama 5) con usuario null.
 * - Para que nadie pueda ver pedidos ajenos cambiando la URL, guardamos en la
 *   SESIÓN del navegador los ids de los pedidos que ha hecho. La pantalla de
 *   estado solo enseña esos.
 */
class AutopedidoController extends Controller
{
    // Clave de la sesión donde guardamos los ids de los pedidos del cliente.
    private const CLAVE_SESION = 'pedidos_cliente';

    // Carritos que se están pagando en Stripe, por id de sesión de Stripe (rama 15).
    private const CLAVE_POR_PAGAR = 'pedidos_por_pagar';

    // Laravel nos pasa estas clases. En los tests, PasarelaStripe se cambia
    // por una simulada con $this->mock() y no se llama a Stripe de verdad.
    public function __construct(
        private PasarelaStripe $pasarela,
        private PagarPedido $pagarPedido,
    ) {}

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
     * "Pagar y enviar a cocina" (rama 15). Ya NO crea el pedido: calcula el
     * total con los precios del servidor, abre el pago con Stripe y guarda el
     * carrito en la sesión hasta que el cliente vuelva. El pedido solo se crea
     * en pagado(), cuando Stripe confirma el cobro.
     */
    public function store(PedidoClienteRequest $request, string $slug, int $numero): RedirectResponse
    {
        $mesa = Mesa::delQr($slug, $numero);
        $lineas = array_values($request->validated('lineas'));

        $rutaMesa = ['slug' => $slug, 'numero' => $numero];

        // Rama 26: sin cuenta de Stripe en el restaurante no se puede pagar al
        // pedir, así que el pedido no se envía (no hay pedidos sin pagar).
        if (! $mesa->restaurante->tieneStripeConfigurado()) {
            return redirect()->route('cliente.pedido', $rutaMesa)
                ->withErrors(['pago' => 'Este restaurante no acepta pagos con tarjeta. Llama al camarero y te toma nota.']);
        }

        // Si algún plato no se puede pedir, error de validación → vuelve a "Mi pedido".
        $total = $this->pagarPedido->calcularTotal($mesa, $lineas);

        // {CHECKOUT_SESSION_ID} va a mano: Stripe lo cambia por el id de la sesión.
        $urlExito = route('cliente.pedido.pagado', $rutaMesa).'?session_id={CHECKOUT_SESSION_ID}';
        $urlCancelar = route('cliente.pedido.cancelado', $rutaMesa);
        $concepto = "{$mesa->restaurante->nombre} · Mesa {$mesa->numero} · Pedido";

        try {
            // Rama 26: con la cuenta de Stripe del restaurante de ESTA mesa.
            $sesion = $this->pasarela->crearSesionPedido($mesa->restaurante, $total, $mesa->id, $concepto, $urlExito, $urlCancelar);
        } catch (Throwable $error) {
            report($error);

            return redirect()->route('cliente.pedido', $rutaMesa)
                ->withErrors(['pago' => 'Ahora mismo no se puede pagar con tarjeta. Llama al camarero y te toma nota.']);
        }

        // Guardamos el carrito y su total con el id de la sesión de Stripe.
        $request->session()->put(self::CLAVE_POR_PAGAR.'.'.$sesion['id'], [
            'mesa_id' => $mesa->id,
            'lineas' => $lineas,
            'total' => $total,
        ]);

        return redirect()->away($sesion['url']);
    }

    /**
     * Stripe vuelve aquí con ?session_id=... cuando el pago ha ido bien.
     * Llegar aquí no demuestra nada (la URL se puede escribir a mano): por eso
     * preguntamos a Stripe antes de crear el pedido.
     */
    public function pagado(Request $request, string $slug, int $numero): RedirectResponse
    {
        $mesa = Mesa::delQr($slug, $numero);
        $rutaMesa = ['slug' => $slug, 'numero' => $numero];
        $idSesion = (string) $request->query('session_id', '');
        $porPagar = $idSesion !== '' ? $request->session()->get(self::CLAVE_POR_PAGAR.'.'.$idSesion) : null;

        // Ya confirmado antes (se ha recargado la página): vamos al estado.
        if ($idSesion !== '' && Pago::where('referencia_stripe', $idSesion)->exists()) {
            return redirect()->route('cliente.estado', $rutaMesa);
        }

        // Rama 26: con la clave del MISMO restaurante. Si falla (por ejemplo,
        // el admin ha quitado la clave), el pago no se puede confirmar.
        try {
            $sesion = $porPagar ? $this->pasarela->consultarSesion($mesa->restaurante, $idSesion) : null;
        } catch (Throwable $error) {
            report($error);
            $sesion = null;
        }

        // Todo tiene que cuadrar: pagada, de un pedido, de ESTA mesa, por el
        // total que calculamos nosotros y en euros.
        $valida = $sesion
            && $sesion['pagada']
            && ($sesion['tipo'] ?? null) === 'pedido'
            && ($sesion['mesa_id'] ?? null) === $mesa->id
            && $porPagar['mesa_id'] === $mesa->id
            && $sesion['importe_centimos'] === $porPagar['total']
            && $sesion['moneda'] === 'eur';

        if (! $valida) {
            return redirect()->route('cliente.pedido', $rutaMesa)
                ->withErrors(['pago' => 'No hemos podido confirmar el pago con Stripe. Tu pedido no se ha enviado.']);
        }

        // Crea el pedido, lo apunta en la cuenta y marca su parte como pagada.
        // Si la carta ha cambiado mientras pagaba, lanza un error de validación.
        // Lo capturamos para volver a "Mi pedido": si dejáramos que Laravel
        // hiciera "volver atrás", al venir de Stripe volvería a la web de Stripe.
        try {
            $pedido = $this->pagarPedido->confirmar($mesa, $porPagar['lineas'], $idSesion, $sesion['importe_centimos']);
        } catch (ValidationException $error) {
            return redirect()->route('cliente.pedido', $rutaMesa)->withErrors($error->errors());
        }

        $request->session()->forget(self::CLAVE_POR_PAGAR.'.'.$idSesion);
        $request->session()->push(self::CLAVE_SESION, $pedido->id);

        // "vaciarCarrito" avisa a la pantalla de estado de que borre el carrito.
        return redirect()->route('cliente.estado', $rutaMesa)
            ->with('vaciarCarrito', true)
            ->with('pedidoPagado', true);
    }

    /**
     * El cliente ha vuelto de Stripe sin pagar: no se crea nada y conserva el carrito.
     */
    public function cancelado(string $slug, int $numero): RedirectResponse
    {
        Mesa::delQr($slug, $numero);

        return redirect()->route('cliente.pedido', ['slug' => $slug, 'numero' => $numero])
            ->with('estado', 'Has cancelado el pago. Tu pedido no se ha enviado a cocina.');
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
