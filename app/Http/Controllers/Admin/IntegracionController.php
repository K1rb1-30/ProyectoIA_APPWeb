<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\IntegracionStripeRequest;
use App\Models\Restaurante;
use App\Servicios\PasarelaStripe;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * «Integraciones» del admin (rama 26). De momento solo hay una: Stripe, la
 * cuenta del RESTAURANTE con la que cobra a sus clientes (la suscripción a
 * MesaYa va aparte, con la cuenta de la plataforma).
 *
 * Aislamiento: las rutas NO llevan id de restaurante. Siempre se usa el del
 * usuario logueado, así que un admin no puede ver ni tocar la de otro.
 * Los desarrolladores (puede_cambiar_restaurante) trabajan con el restaurante
 * que tengan elegido, igual que en el resto del panel.
 *
 * La clave secreta nunca se manda a la vista: solo sus 4 últimas cifras.
 */
class IntegracionController extends Controller
{
    // En los tests se cambia por una simulada con $this->mock(PasarelaStripe::class).
    public function __construct(private PasarelaStripe $pasarela) {}

    /**
     * La pantalla: estado de Stripe y el formulario para pegar las claves.
     */
    public function index(Request $request): View
    {
        return view('admin.integraciones', [
            'restaurante' => $this->restaurante($request),
        ]);
    }

    /**
     * Guarda las claves. Orden:
     *  1. IntegracionStripeRequest valida el formato (y rechaza _live_).
     *  2. Preguntamos a Stripe si la clave secreta funciona.
     *  3. Solo si funciona, se guarda (cifrada, por el cast del modelo).
     */
    public function guardarStripe(IntegracionStripeRequest $request): RedirectResponse
    {
        $restaurante = $this->restaurante($request);
        $secreta = $request->validated('clave_secreta');

        if (! $this->pasarela->comprobarClave($secreta)) {
            // No se guarda nada. El mensaje no repite la clave, y la clave no
            // vuelve al formulario: solo devolvemos la publicable.
            return redirect()->route('admin.integraciones')
                ->withInput($request->only('clave_publicable'))
                ->withErrors(['clave_secreta' => 'Stripe no ha aceptado la clave secreta. Comprueba que es de modo de prueba, que la has copiado entera y, si es restringida, que puede crear sesiones de Checkout.']);
        }

        $restaurante->guardarClavesStripe($request->validated('clave_publicable'), $secreta);

        return redirect()->route('admin.integraciones')
            ->with('estado', 'Stripe conectado. Tus clientes ya pueden pagar con tarjeta.');
    }

    /**
     * Quita las claves: el restaurante pasa a cobrar solo en efectivo.
     */
    public function quitarStripe(Request $request): RedirectResponse
    {
        $this->restaurante($request)->quitarClavesStripe();

        return redirect()->route('admin.integraciones')
            ->with('estado', 'Claves de Stripe quitadas. Tus clientes solo podrán pagar en efectivo.');
    }

    /**
     * El restaurante del usuario logueado (nunca uno que llegue en la URL).
     */
    private function restaurante(Request $request): Restaurante
    {
        return $request->user()->restaurante;
    }
}
