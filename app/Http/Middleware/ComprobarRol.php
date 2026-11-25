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
 * Los desarrolladores (User::esDesarrollador) pasan siempre, aunque su rol
 * no esté en la lista. Un admin normal solo entra donde ponga "admin".
 * Se pone siempre detrás de 'auth', que ya se encarga de los invitados.
 */
class ComprobarRol
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $usuario = $request->user();

        // Los desarrolladores (equipo y profesor) pueden entrar en todas las
        // pantallas del personal (también en las de camarero y cocina) para
        // probarlas con «Ver como». Siguen limitados a los datos del
        // restaurante en el que estén: eso lo controla el trait
        // PerteneceARestaurante, no este middleware.
        if ($usuario?->esDesarrollador()) {
            return $next($request);
        }

        if ($usuario === null || ! in_array($usuario->rol, $roles, true)) {
            abort(403, 'No tienes permiso para entrar en esta página.');
        }

        // Tiene un rol permitido: seguimos hacia el controlador.
        return $next($request);
    }
}
