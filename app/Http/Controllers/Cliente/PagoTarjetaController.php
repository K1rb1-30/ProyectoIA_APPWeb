<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\Mesa;
use App\Models\Pago;
use App\Models\ParteCuenta;
use App\Servicios\PasarelaStripe;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Throwable;

/**
 * Pago con tarjeta de una parte de la cuenta, con Stripe Checkout (rama 11).
 * Pantallas públicas del cliente, sin login.
 *
 * El flujo de ida y vuelta:
 *  1. show()      → nuestra pantalla con el resumen de la parte y el botón "Pagar".
 *  2. pagar()     → creamos una sesión de Stripe y mandamos al cliente a la página
 *                   de pago de Stripe (allí escribe la tarjeta 4242 4242 4242 4242).
 *  3a. exito()    → Stripe nos devuelve aquí con ?session_id=cs_test_...
 *                   PREGUNTAMOS a Stripe si esa sesión está pagada; solo entonces
 *                   guardamos el Pago y la parte pasa a "pagado".
 *  3b. cancelado() → el cliente ha pulsado "volver" en Stripe: no se toca nada.
 *
 * No hay webhooks: con comprobar la sesión al volver es suficiente para la práctica.
 */
class PagoTarjetaController extends Controller
{
    // Laravel crea la PasarelaStripe y nos la pasa aquí. En los tests se cambia
    // por una simulada con $this->mock(PasarelaStripe::class).
    public function __construct(private PasarelaStripe $pasarela) {}

    /**
     * 1. Pantalla "Pago con tarjeta" (maqueta cliente/pago-tarjeta).
     */
    public function show(string $slug, int $numero, int $parte): View|RedirectResponse
    {
        $mesa = Mesa::delQr($slug, $numero);
        $parte = $this->buscarParte($mesa, $parte) ?? abort(404);

        if ($aviso = $this->motivoParaNoPagar($parte)) {
            return $this->volverALaCuenta($slug, $numero)->with('estado', $aviso);
        }

        return view('cliente.pago-tarjeta', [
            'restaurante' => $mesa->restaurante,
            'mesa' => $mesa,
            'parte' => $parte,
            'totalPersonas' => $parte->cuenta->partes()->count(),
        ]);
    }

    /**
     * 2. Crea la sesión de Stripe Checkout y redirige al cliente a Stripe.
     */
    public function pagar(string $slug, int $numero, int $parte): RedirectResponse
    {
        $mesa = Mesa::delQr($slug, $numero);
        $parte = $this->buscarParte($mesa, $parte) ?? abort(404);

        // Una parte ya pagada no se puede volver a pagar: ni llamamos a Stripe.
        if ($aviso = $this->motivoParaNoPagar($parte)) {
            return $this->volverALaCuenta($slug, $numero)->with('estado', $aviso);
        }

        $rutaParte = ['slug' => $slug, 'numero' => $numero, 'parte' => $parte->id];

        // {CHECKOUT_SESSION_ID} se añade a mano (sin route()) para que las llaves
        // no se codifiquen: Stripe busca ese texto tal cual y lo cambia por el id.
        $urlExito = route('cliente.pago.exito', $rutaParte).'?session_id={CHECKOUT_SESSION_ID}';
        $urlCancelar = route('cliente.pago.cancelado', $rutaParte);
        $concepto = "{$mesa->restaurante->nombre} · Mesa {$mesa->numero} · Persona {$parte->persona}";

        try {
            $sesion = $this->pasarela->crearSesion($parte, $concepto, $urlExito, $urlCancelar);
        } catch (Throwable $error) {
            // Faltan las claves, no hay internet, Stripe falla...: lo apuntamos
            // en el log y al cliente le damos un mensaje entendible.
            report($error);

            return $this->volverALaCuenta($slug, $numero)
                ->withErrors(['pago' => 'Ahora mismo no se puede pagar con tarjeta. Avisa al camarero.']);
        }

        // away(): redirección a una web externa (la página de pago de Stripe).
        return redirect()->away($sesion['url']);
    }

    /**
     * 3a. URL de éxito: Stripe vuelve aquí con ?session_id=...
     *
     * Llegar aquí NO demuestra que se haya pagado (cualquiera puede escribir
     * esta URL). Por eso preguntamos a Stripe por la sesión antes de guardar nada.
     */
    public function exito(Request $request, string $slug, int $numero, int $parte): View|RedirectResponse
    {
        $mesa = Mesa::delQr($slug, $numero);
        $idSesion = (string) $request->query('session_id', '');
        $parteCuenta = $this->buscarParte($mesa, $parte);

        // La parte ya no existe en esta mesa. Puede pasar si alguien vuelve a
        // dividir la cuenta mientras otra persona está pagando en Stripe.
        if (! $parteCuenta) {
            return $this->volverALaCuenta($slug, $numero)->withErrors([
                'pago' => 'No encontramos tu parte de la cuenta. Si ya has pagado, enseña esta referencia al camarero: '.($idSesion ?: '—'),
            ]);
        }

        // Transacción + lockForUpdate(): si llegan dos peticiones a la vez (doble
        // clic, recargar...), la segunda espera a que acabe la primera y entonces
        // ya ve la parte pagada. Así nunca se crean dos pagos.
        $confirmado = DB::transaction(function () use ($parteCuenta, $idSesion) {
            $parte = ParteCuenta::lockForUpdate()->find($parteCuenta->id);

            // Ya estaba pagada (por ejemplo, se recarga la página): no se repite.
            if ($parte->estaPagada()) {
                return true;
            }

            if ($idSesion === '') {
                return false;
            }

            $sesion = $this->pasarela->consultarSesion($idSesion);

            // Todo tiene que cuadrar: pagada, de ESTA parte, por ESTE importe y en euros.
            $sesionValida = $sesion['pagada']
                && $sesion['parte_cuenta_id'] === $parte->id
                && $sesion['importe_centimos'] === $parte->importe_centimos
                && $sesion['moneda'] === 'eur';

            if (! $sesionValida) {
                return false;
            }

            Pago::create([
                'parte_cuenta_id' => $parte->id,
                'importe_centimos' => $parte->importe_centimos,
                'metodo' => 'tarjeta',
                'referencia_stripe' => $idSesion,
            ]);

            $parte->update(['metodo' => 'tarjeta', 'estado' => 'pagado']);

            return true;
        });

        if (! $confirmado) {
            return $this->volverALaCuenta($slug, $numero)
                ->withErrors(['pago' => 'No hemos podido confirmar el pago con Stripe. Tu parte sigue pendiente.']);
        }

        $parteCuenta->refresh()->load('pagos');
        $cuenta = $parteCuenta->cuenta->load('partes');

        return view('cliente.pago-completado', [
            'restaurante' => $mesa->restaurante,
            'mesa' => $mesa,
            'parte' => $parteCuenta,
            'pago' => $parteCuenta->pagos->last(),
            'cuenta' => $cuenta,
        ]);
    }

    /**
     * 3b. URL de cancelación: el cliente ha vuelto de Stripe sin pagar.
     * No cambiamos nada: la parte sigue "pendiente".
     */
    public function cancelado(string $slug, int $numero, int $parte): RedirectResponse
    {
        Mesa::delQr($slug, $numero);

        return $this->volverALaCuenta($slug, $numero)
            ->with('estado', 'Has cancelado el pago con tarjeta. Tu parte sigue pendiente.');
    }

    /**
     * Busca la parte filtrando A MANO (no hay usuario logueado, así que el
     * filtro del trait PerteneceARestaurante no actúa): tiene que pertenecer a
     * una cuenta de ESTA mesa y de ESTE restaurante. Si no, devuelve null.
     */
    private function buscarParte(Mesa $mesa, int $idParte): ?ParteCuenta
    {
        $soloEstaMesa = fn ($consulta) => $consulta->withoutGlobalScope('restaurante')
            ->where('mesa_id', $mesa->id)
            ->where('restaurante_id', $mesa->restaurante_id);

        return ParteCuenta::whereKey($idParte)
            ->whereHas('cuenta', $soloEstaMesa)
            ->with(['cuenta' => $soloEstaMesa])
            ->first();
    }

    /**
     * Texto del aviso si la parte no se puede pagar con tarjeta, o null si sí.
     */
    private function motivoParaNoPagar(ParteCuenta $parte): ?string
    {
        return match (true) {
            $parte->estaPagada() => 'Esta parte ya está pagada.',
            $parte->estado === 'pendiente_efectivo' => 'Esta parte se va a pagar en efectivo.',
            $parte->cuenta->estado !== 'abierta' => 'Esta cuenta ya está cerrada.',
            default => null,
        };
    }

    private function volverALaCuenta(string $slug, int $numero): RedirectResponse
    {
        return redirect()->route('cliente.cuenta', ['slug' => $slug, 'numero' => $numero]);
    }
}
