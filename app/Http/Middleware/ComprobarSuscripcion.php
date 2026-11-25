<?php

namespace App\Http\Middleware;

use App\Models\Restaurante;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Solo deja usar la aplicación a los restaurantes con la suscripción ACTIVA
 * (rama 24).
 *
 * Está registrado con el alias "suscripcion" en bootstrap/app.php y va en:
 *  - las rutas del admin, de camarero y de cocina, DETRÁS de 'auth' (hace
 *    falta saber quién es y de qué restaurante);
 *  - las rutas del QR (/r/{slug}/...), donde no hay nadie logueado: el
 *    restaurante se saca del {slug} de la URL.
 *
 * Si el restaurante no está activo:
 *  - el admin va a «Tu suscripción», donde puede pagar o reactivar;
 *  - camarero, cocina y cliente ven «Restaurante no disponible» (403). El
 *    polling recibe JSON con 403 y la pantalla se recarga para enseñarlo.
 *
 * Los desarrolladores (equipo y profesor) no se bloquean nunca.
 */
class ComprobarSuscripcion
{
    public function handle(Request $request, Closure $next): Response
    {
        $usuario = $request->user();

        // instanceof: en cocina el "usuario" es un Dispositivo (otro guard),
        // que no tiene esDesarrollador().
        if ($usuario instanceof User && $usuario->esDesarrollador()) {
            return $next($request);
        }

        $restaurante = $this->restauranteDeLaPeticion($request);

        // Si el slug del QR no existe, dejamos pasar: el controlador ya da 404.
        if ($restaurante === null || $restaurante->tieneSuscripcionActiva()) {
            return $next($request);
        }

        // Solo en las pantallas del admin. En el QR (lleva {slug}) siempre sale
        // «no disponible», aunque quien lo abra sea un admin con sesión: el QR
        // puede ser de otro restaurante y no tiene sentido mandarle a la suya.
        if ($usuario instanceof User && $usuario->rol === 'admin' && $request->route('slug') === null) {
            return redirect()->route('admin.suscripcion');
        }

        if ($request->expectsJson()) {
            return response()->json(['mensaje' => 'Restaurante no disponible.'], 403);
        }

        return response()->view('restaurante-no-disponible', ['restaurante' => $restaurante], 403);
    }

    /**
     * Con sesión (admin, camarero o tablet de cocina), el restaurante de quien
     * entra. Sin sesión (QR), el del {slug} de la URL.
     */
    private function restauranteDeLaPeticion(Request $request): ?Restaurante
    {
        $slug = $request->route('slug');

        if ($slug !== null) {
            return Restaurante::where('slug', $slug)->first();
        }

        $restauranteId = $request->user()?->restaurante_id;

        return $restauranteId ? Restaurante::find($restauranteId) : null;
    }
}
