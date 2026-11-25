<?php

namespace App\Http\Controllers;

use App\Models\Dispositivo;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Lo que se ve EN la tablet o el móvil del bar, en /dispositivo. Sin login.
 *
 *  - Sin emparejar: un campo para escribir el código que da el admin.
 *  - Emparejado de cocina: va directo al tablero de cocina.
 *  - Emparejado de camarero: la lista de camareros; cada uno toca su nombre y
 *    entra con su PIN de 4 cifras.
 */
class DispositivoController extends Controller
{
    /** Fallos de PIN seguidos que se permiten antes de hacer esperar. */
    private const INTENTOS_PIN = 5;

    public function show(Request $request): View|RedirectResponse
    {
        $dispositivo = Dispositivo::deLaPeticion($request);

        if ($dispositivo?->tipo === 'cocina') {
            return redirect()->route('cocina.pedidos');
        }

        if ($dispositivo?->tipo === 'camarero') {
            // Si ya hay un camarero dentro en este dispositivo, a sus mesas.
            if ($request->user()?->rol === 'camarero' && $request->session()->get('dispositivo_id') === $dispositivo->id) {
                return redirect()->route('camarero.mesas');
            }

            return view('dispositivo.camarero', [
                'dispositivo' => $dispositivo->load('restaurante'),
                'camareros' => $this->camarerosDe($dispositivo)->get(['id', 'name']),
            ]);
        }

        return view('dispositivo.emparejar');
    }

    /**
     * Comprueba el código y, si vale, empareja el dispositivo y le guarda la
     * cookie. La ruta lleva throttle: como mucho 5 intentos por minuto.
     */
    public function emparejar(Request $request): RedirectResponse
    {
        $datos = $request->validate(
            ['codigo' => ['required', 'string', 'max:20']],
            ['codigo.required' => 'Escribe el código que te ha dado el administrador.'],
        );

        // Solo vale si coincide, no ha caducado y no se ha usado ya.
        $dispositivo = Dispositivo::where('codigo_hash', Dispositivo::hashCodigo($datos['codigo']))
            ->where('codigo_caduca_en', '>', now())
            ->whereNull('token_hash')
            ->first();

        if (! $dispositivo) {
            return back()->withInput()->withErrors([
                'codigo' => 'El código no es válido o ha caducado. Pide otro al administrador.',
            ]);
        }

        $token = $dispositivo->emparejar();

        // La cookie la cifra Laravel y no se puede leer desde JavaScript (httpOnly).
        return redirect()->route('dispositivo')
            ->withCookie(cookie(Dispositivo::COOKIE, $token, Dispositivo::MINUTOS_COOKIE))
            ->with('estado', "Dispositivo emparejado: «{$dispositivo->nombre}».");
    }

    /**
     * Un camarero entra con su nombre y su PIN. Solo funciona en un
     * dispositivo de camarero emparejado de SU restaurante: aunque alguien
     * sepa el PIN, desde otro sitio no puede entrar.
     */
    public function entrar(Request $request): RedirectResponse
    {
        $dispositivo = Dispositivo::deLaPeticion($request);
        abort_unless($dispositivo?->tipo === 'camarero', 403, 'Este dispositivo no está emparejado para camareros.');

        $datos = $request->validate([
            'camarero' => ['required', 'integer'],
            'pin' => ['required', 'digits:4'],
        ], [
            'camarero.required' => 'Toca tu nombre.',
            'pin.required' => 'Escribe tu PIN.',
            'pin.digits' => 'El PIN tiene 4 cifras.',
        ]);

        // Solo camareros del restaurante del dispositivo.
        $camarero = $this->camarerosDe($dispositivo)->whereKey($datos['camarero'])->first();
        abort_unless($camarero, 404);

        // Límite de fallos por dispositivo y camarero: 5 seguidos y hay que
        // esperar un minuto. Así no se puede probar los 10.000 PIN a lo loco.
        $clave = 'pin:'.$dispositivo->id.':'.$camarero->id;
        if (RateLimiter::tooManyAttempts($clave, self::INTENTOS_PIN)) {
            throw ValidationException::withMessages([
                'pin' => 'Demasiados intentos. Espera '.RateLimiter::availableIn($clave).' segundos y vuelve a probar.',
            ]);
        }

        if (! $camarero->pin || ! Hash::check($datos['pin'], $camarero->pin)) {
            RateLimiter::hit($clave, 60);

            throw ValidationException::withMessages(['pin' => 'El PIN no es correcto.']);
        }

        RateLimiter::clear($clave);

        // Sesión normal (guard web), como cualquier usuario. regenerate() da un
        // id de sesión nuevo para que nadie pueda reutilizar uno anterior.
        Auth::guard('web')->login($camarero);
        $request->session()->regenerate();
        // IdentificarDispositivo comprueba en cada petición que siga en este dispositivo.
        $request->session()->put('dispositivo_id', $dispositivo->id);

        return redirect()->route('camarero.mesas');
    }

    /**
     * «Cambiar de camarero»: cierra la sesión del camarero y vuelve a la lista
     * de nombres. El dispositivo sigue emparejado (la cookie no se toca).
     */
    public function salir(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('dispositivo');
    }

    /** Camareros del restaurante del dispositivo, por nombre. */
    private function camarerosDe(Dispositivo $dispositivo)
    {
        return User::where('restaurante_id', $dispositivo->restaurante_id)
            ->where('rol', 'camarero')
            ->orderBy('name');
    }
}
