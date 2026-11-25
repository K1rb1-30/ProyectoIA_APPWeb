<?php

namespace App\Http\Middleware;

use App\Models\Dispositivo;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Deja entrar a una tablet de cocina emparejada sin que nadie inicie sesión.
 *
 * Está registrado con el alias "dispositivo" en bootstrap/app.php y va en las
 * rutas de cocina ANTES de 'auth':
 *   ->middleware(['dispositivo', 'auth', 'rol:cocina'])
 *
 * Si la petición trae la cookie de un dispositivo de cocina que sigue
 * emparejado:
 *  1. Auth::shouldUse('dispositivo') hace que, durante esta petición, el
 *     guard por defecto sea el de los dispositivos (config/auth.php).
 *  2. setUser() pone el dispositivo como "usuario". Desde ahí, 'auth' ve que
 *     hay alguien, 'rol:cocina' ve rol = cocina y el filtro
 *     PerteneceARestaurante usa su restaurante_id.
 *
 * No guarda nada en la sesión: se comprueba la cookie en cada petición. Si el
 * admin desvincula el dispositivo, la siguiente petición ya no lo encuentra y
 * 'auth' lo manda al login.
 *
 * Si no hay cookie (o no vale), no hace nada: entra quien tenga sesión normal
 * (por ejemplo, un desarrollador con «Ver como»).
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
        }

        return $next($request);
    }
}
