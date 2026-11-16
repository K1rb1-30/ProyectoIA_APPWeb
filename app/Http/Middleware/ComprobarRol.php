<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Deja pasar solo a los usuarios con alguno de los roles indicados.
 *
 * Está registrado con el alias "rol" en bootstrap/app.php. Uso en las rutas:
 *   ->middleware('rol:admin')            solo administradores
 *   ->middleware('rol:admin,camarero')   administradores o camareros
 *
 * Lo que va después de los dos puntos llega aquí como la lista $roles.
 * Se pone siempre detrás de 'auth', que ya se encarga de los invitados.
 */
class ComprobarRol
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $usuario = $request->user();

        if ($usuario === null || ! in_array($usuario->rol, $roles, true)) {
            abort(403, 'No tienes permiso para entrar en esta página.');
        }

        // Tiene un rol permitido: seguimos hacia el controlador.
        return $next($request);
    }
}
