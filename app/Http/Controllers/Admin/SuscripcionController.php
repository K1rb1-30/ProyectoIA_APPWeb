<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Restaurante;
use App\Servicios\PasarelaStripe;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

/**
 * Pantalla «Suscripción» del admin (rama 24): ver el estado, pagar o
 * reactivar con Stripe Checkout y abrir el portal de cliente de Stripe.
 *
 * Aislamiento: el restaurante es SIEMPRE el del admin logueado
 * ($request->user()->restaurante). No hay ningún id en la URL, así que un
 * admin no puede ver ni tocar la suscripción de otro restaurante.
 *
 * El flujo del pago es el mismo que en la rama 11:
 *  1. pagar()   → sesión de Stripe Checkout (mode 'subscription') y a Stripe.
 *  2. exito()   → Stripe vuelve con ?session_id=... y PREGUNTAMOS a Stripe.
 *                 Solo si está pagada y es de ESTE restaurante, se activa.
 *  3. cancelada() → ha pulsado "volver" en Stripe: no se toca nada.
 */
class SuscripcionController extends Controller
{
    // En los tests se cambia por una simulada con $this->mock(PasarelaStripe::class).
    public function __construct(private PasarelaStripe $pasarela) {}

    /**
     * La pantalla: estado, próxima fecha de cobro, importe y el botón.
     */
    public function show(Request $request): View
    {
        return view('admin.suscripcion', [
            'restaurante' => $this->restaurante($request),
            'precioCentimos' => (int) config('services.stripe.suscripcion_precio_centimos'),
        ]);
    }

    /**
     * Pagar (o reactivar) la suscripción: manda al admin a Stripe Checkout.
     */
    public function pagar(Request $request): RedirectResponse
    {
        $restaurante = $this->restaurante($request);

        // Ya activa: no se abre otra sesión (crearía una segunda suscripción
        // y se cobraría dos veces cada mes).
        if ($restaurante->tieneSuscripcionActiva()) {
            return redirect()->route('admin.suscripcion')->with('estado', 'La suscripción ya está activa.');
        }

        return $this->irAStripe($restaurante, $this->pasarela);
    }

    /**
     * Vuelta de Stripe después de pagar.
     *
     * Llegar aquí NO demuestra nada (la URL se puede escribir a mano). Por eso
     * preguntamos a Stripe por la sesión y comprobamos dos cosas:
     *  - que está pagada;
     *  - que el restaurante_id de sus metadatos es el del admin logueado. Así
     *    la sesión pagada de otro restaurante no sirve para activar este.
     */
    public function exito(Request $request): RedirectResponse
    {
        $restaurante = $this->restaurante($request);
        $idSesion = (string) $request->query('session_id', '');

        // Recargar la página cuando ya está activa no hace nada.
        if ($restaurante->tieneSuscripcionActiva()) {
            return redirect()->route('admin.suscripcion')->with('estado', 'La suscripción ya está activa.');
        }

        $sesion = $idSesion !== '' ? $this->pasarela->consultarSesionSuscripcion($idSesion) : null;

        $valida = $sesion !== null
            && $sesion['pagada']
            && $sesion['restaurante_id'] === $restaurante->id
            && $sesion['cliente_id'] !== null;

        if (! $valida) {
            return redirect()->route('admin.suscripcion')
                ->with('aviso', 'No hemos podido confirmar el pago con Stripe. La suscripción no se ha activado.');
        }

        $restaurante->activarSuscripcion($sesion['cliente_id'], $sesion['suscripcion_id'], $sesion['fin_en']);

        return redirect()->route('panel')->with('estado', 'Suscripción activada. ¡Bienvenido a MesaYa! Empieza por la carta y las mesas.');
    }

    /**
     * Vuelta de Stripe sin pagar: la suscripción sigue como estaba.
     */
    public function cancelada(): RedirectResponse
    {
        return redirect()->route('admin.suscripcion')->with('aviso', 'No se ha hecho ningún cobro. Tu cuenta está guardada: puedes pagar cuando quieras desde esta pantalla.');
    }

    /**
     * Portal de cliente de Stripe: cambiar la tarjeta, ver facturas o cancelar.
     */
    public function portal(Request $request): RedirectResponse
    {
        $restaurante = $this->restaurante($request);

        // Sin cliente en Stripe (nunca ha pagado) no hay portal que abrir.
        if (! $restaurante->stripe_cliente_id) {
            return redirect()->route('admin.suscripcion')->with('aviso', 'Todavía no hay ninguna suscripción pagada.');
        }

        try {
            $url = $this->pasarela->crearSesionPortal($restaurante, route('admin.suscripcion'));
        } catch (Throwable $error) {
            report($error);

            return redirect()->route('admin.suscripcion')->with('aviso', 'Ahora mismo no se puede abrir el portal de Stripe. Inténtalo más tarde.');
        }

        return redirect()->away($url);
    }

    /**
     * Crea la sesión de Checkout y redirige a Stripe. Es pública y estática
     * para que la use también el registro (RegistroRestauranteController).
     * Si Stripe falla, el restaurante se queda como está y se vuelve a
     * «Tu suscripción», donde el botón permite reintentarlo.
     */
    public static function irAStripe(Restaurante $restaurante, PasarelaStripe $pasarela): RedirectResponse
    {
        // {CHECKOUT_SESSION_ID} se añade a mano (sin route()) para que las llaves
        // no se codifiquen: Stripe busca ese texto tal cual y lo cambia por el id.
        $urlExito = route('admin.suscripcion.exito').'?session_id={CHECKOUT_SESSION_ID}';

        try {
            $sesion = $pasarela->crearSesionSuscripcion($restaurante, $urlExito, route('admin.suscripcion.cancelada'));
        } catch (Throwable $error) {
            report($error);

            return redirect()->route('admin.suscripcion')
                ->with('aviso', 'Ahora mismo no se puede abrir el pago con Stripe. Inténtalo de nuevo en unos minutos.');
        }

        // away(): redirección a una web externa (la página de pago de Stripe).
        return redirect()->away($sesion['url']);
    }

    private function restaurante(Request $request): Restaurante
    {
        return $request->user()->restaurante;
    }
}
