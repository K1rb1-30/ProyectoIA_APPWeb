<?php

namespace App\Http\Controllers;

use App\Models\Dispositivo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Lo que se ve EN la tablet o el móvil del bar, en /dispositivo. Sin login.
 *
 *  - Sin emparejar: un campo para escribir el código que da el admin.
 *  - Emparejado de cocina: va directo al tablero de cocina.
 *  - Emparejado de camarero: cada camarero entrará con su nombre y su PIN
 *    (rama siguiente).
 */
class DispositivoController extends Controller
{
    public function show(Request $request): View|RedirectResponse
    {
        $dispositivo = Dispositivo::deLaPeticion($request);

        if ($dispositivo?->tipo === 'cocina') {
            return redirect()->route('cocina.pedidos');
        }

        if ($dispositivo?->tipo === 'camarero') {
            return view('dispositivo.camarero', ['dispositivo' => $dispositivo->load('restaurante')]);
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
}
