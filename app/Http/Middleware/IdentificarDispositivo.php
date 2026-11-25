<?php

namespace App\Http\Middleware;

use App\Models\Dispositivo;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Acceso desde los dispositivos emparejados del bar.
 *
 * Está registrado con el alias "dispositivo" en bootstrap/app.php y va en las
 * rutas de cocina y de camarero ANTES de 'auth':
 *   ->middleware(['dispositivo', 'auth', 'rol:cocina'])
 *
 * 1. Tablet de COCINA (la cocina no tiene usuarios): si la petición trae la
 *    cookie de un dispositivo de cocina emparejado,
 *     - Auth::shouldUse('dispositivo') hace que, durante esta petición, el
 *       guard por defecto sea el de los dispositivos (config/auth.php);
 *     - setUser() pone el dispositivo como "usuario". Desde ahí, 'auth' ve que
 *       hay alguien, 'rol:cocina' ve rol = cocina y el filtro
 *       PerteneceARestaurante usa su restaurante_id.
 *    No guarda nada en la sesión: se comprueba la cookie en cada petición.
 *
 * 2. CAMARERO (entra con su PIN en un dispositivo de camarero): solo sigue
 *    dentro mientras esté en ESE dispositivo y siga emparejado. Si el admin lo
 *    desvincula, o la sesión se usa en otro navegador sin la cookie, se cierra
 *    la sesión y vuelve a /dispositivo.
 *
 * Si no hay cookie (o no vale) y no es un camarero, no hace nada: entra quien
 * tenga sesión normal (por ejemplo, un desarrollador con «Ver como»).
 */
class IdentificarDispositivo
{
    public function handle(Request $request, Closure $next): Response
    {
        $dispositivo = Dispositivo::deLaPeticion($request);

        if ($dispositivo?->tipo === 'cocina') {
            Auth::shouldUse('dispositivo');
            Auth::guard('dispositivo')->setUser($dispositivo);
            $dispositivo->apuntarUso();

            return $next($request);
        }

        $usuario = Auth::guard('web')->user();

        if ($usuario?->rol === 'camarero') {
            // El dispositivo con el que entró (lo guarda DispositivoController::entrar).
            $enSuDispositivo = $dispositivo
                && $dispositivo->tipo === 'camarero'
                && $dispositivo->restaurante_id === $usuario->restaurante_id
                && $request->session()->get('dispositivo_id') === $dispositivo->id;

            if (! $enSuDispositivo) {
                Auth::guard('web')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                // El polling (JSON) recibe 401 y recarga; la página va a /dispositivo.
                return $request->expectsJson()
                    ? response()->json(['mensaje' => 'La sesión se ha cerrado.'], 401)
                    : redirect()->route('dispositivo');
            }

            $dispositivo->apuntarUso();
        }

        return $next($request);
    }
}
