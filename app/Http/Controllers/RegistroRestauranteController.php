<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Admin\SuscripcionController;
use App\Http\Requests\RegistroRestauranteRequest;
use App\Models\Restaurante;
use App\Models\User;
use App\Servicios\PasarelaStripe;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Alta pública de un restaurante nuevo (rama 24), en /registro.
 *
 * Al enviar el formulario:
 *  1. Crea el Restaurante (slug único sacado del nombre) con la suscripción
 *     "pendiente" y su User con rol admin. Los dos dentro de una transacción:
 *     o se guardan los dos, o ninguno.
 *  2. Inicia sesión con ese admin.
 *  3. Lo manda a pagar la suscripción a Stripe Checkout.
 *
 * La llamada a Stripe va FUERA de la transacción: si Stripe falla, el
 * restaurante ya existe en "pendiente" y el admin puede reintentar el pago
 * desde «Tu suscripción». Mientras no pague, el middleware ComprobarSuscripcion
 * no le deja usar la aplicación.
 *
 * No es el /register de Breeze (sigue desactivado): el resto del personal lo
 * crea el admin en «Personal».
 */
class RegistroRestauranteController extends Controller
{
    public function __construct(private PasarelaStripe $pasarela) {}

    public function create(): View
    {
        return view('auth.registro');
    }

    public function store(RegistroRestauranteRequest $request): RedirectResponse
    {
        $datos = $request->validated();

        $admin = DB::transaction(function () use ($datos) {
            $restaurante = Restaurante::create([
                'nombre' => $datos['nombre_restaurante'],
                'slug' => Restaurante::slugUnico($datos['nombre_restaurante']),
            ]);
            // Aunque la columna ya es "pendiente" por defecto, lo dejamos
            // escrito: así se ve claro que aún no puede usar la app.
            $restaurante->forceFill(['suscripcion_estado' => Restaurante::SUSCRIPCION_PENDIENTE])->save();

            $admin = User::create([
                'name' => $datos['name'],
                'email' => $datos['email'],
                'password' => $datos['password'], // el cast 'hashed' de User la cifra
                'rol' => 'admin',
                'restaurante_id' => $restaurante->id,
            ]);
            // No está en Fillable: ningún formulario puede marcar el correo como verificado.
            $admin->forceFill(['email_verified_at' => now()])->save();

            return $admin;
        });

        Auth::login($admin);
        // Sesión nueva tras iniciar sesión (como en el login de Breeze), para
        // que nadie pueda reutilizar el id de sesión que había antes.
        $request->session()->regenerate();

        return SuscripcionController::irAStripe($admin->restaurante, $this->pasarela);
    }
}
