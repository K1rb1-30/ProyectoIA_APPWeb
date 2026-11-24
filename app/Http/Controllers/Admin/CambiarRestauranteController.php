<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Restaurante;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Cambia el restaurante del admin que está logueado, solo si su cuenta lo
 * permite (las tres cuentas del equipo y del profesor, para la demo).
 *
 * Cómo funciona: cambia el restaurante_id DE SU PROPIO USUARIO. Como todo el
 * aislamiento (el trait PerteneceARestaurante) filtra por ese campo, a partir
 * de ahí ve solo los datos del restaurante nuevo, como cualquier otro admin
 * suyo. No se toca ninguna regla de aislamiento.
 */
class CambiarRestauranteController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $usuario = $request->user();

        // Un admin normal (o alguien que envía el formulario a mano) no puede.
        abort_unless($usuario->puedeCambiarDeRestaurante(), 403, 'Tu cuenta no puede cambiar de restaurante.');

        $datos = $request->validate([
            'restaurante_id' => ['required', 'integer', 'exists:restaurantes,id'],
        ], [], ['restaurante_id' => 'restaurante']);

        $restaurante = Restaurante::findOrFail($datos['restaurante_id']);

        // forceFill: restaurante_id sí es Fillable, pero así queda claro que
        // lo cambiamos a propósito y solo aquí.
        $usuario->forceFill(['restaurante_id' => $restaurante->id])->save();

        return redirect()->route('panel')->with('estado', "Ahora estás en «{$restaurante->nombre}».");
    }
}
