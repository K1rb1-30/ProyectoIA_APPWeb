<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dispositivo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Pantalla «Dispositivos» del admin: emparejar tablets y móviles del bar,
 * darles un código nuevo si el anterior caducó y desvincularlos.
 *
 * Dispositivo no usa el trait PerteneceARestaurante (ver el modelo), así que
 * aquí se filtra A MANO por el restaurante del admin. En las rutas con
 * {dispositivo}, el filtro lo hace Route::bind (routes/web.php): uno de otro
 * restaurante da 404.
 */
class DispositivoController extends Controller
{
    public function index(Request $request): View
    {
        $dispositivos = Dispositivo::where('restaurante_id', $request->user()->restaurante_id)
            ->orderBy('tipo')
            ->orderBy('nombre')
            ->get();

        return view('admin.dispositivos.index', [
            'emparejados' => $dispositivos->filter->estaEmparejado()->values(),
            'pendientes' => $dispositivos->reject->estaEmparejado()->values(),
        ]);
    }

    /**
     * «Emparejar dispositivo»: crea el dispositivo con su código y vuelve a la
     * lista enseñando el código (solo esta vez: en la base de datos va cifrado).
     */
    public function store(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'tipo' => ['required', Rule::in(['cocina', 'camarero'])],
            'nombre' => ['nullable', 'string', 'max:60'],
        ], [
            'tipo.required' => 'Elige si el dispositivo es para cocina o para camareros.',
        ]);

        $restauranteId = $request->user()->restaurante_id;

        // Si no le pone nombre, «Cocina 2», «Camarero 1»...
        $nombre = trim($datos['nombre'] ?? '');
        if ($nombre === '') {
            $cuantos = Dispositivo::where('restaurante_id', $restauranteId)->where('tipo', $datos['tipo'])->count();
            $nombre = ucfirst($datos['tipo']).' '.($cuantos + 1);
        }

        $dispositivo = Dispositivo::create([
            'restaurante_id' => $restauranteId,
            'tipo' => $datos['tipo'],
            'nombre' => $nombre,
        ]);

        return $this->volverConCodigo($dispositivo, $dispositivo->generarCodigo());
    }

    /**
     * Código nuevo para un dispositivo que todavía no se ha emparejado (por
     * ejemplo, porque el anterior caducó). El código anterior deja de valer.
     */
    public function nuevoCodigo(Dispositivo $dispositivo): RedirectResponse
    {
        if ($dispositivo->estaEmparejado()) {
            return redirect()->route('admin.dispositivos')
                ->with('aviso', "«{$dispositivo->nombre}» ya está emparejado.");
        }

        return $this->volverConCodigo($dispositivo, $dispositivo->generarCodigo());
    }

    /**
     * Desvincular: se borra el dispositivo. Su cookie deja de valer y en la
     * siguiente petición ya no puede entrar.
     */
    public function destroy(Dispositivo $dispositivo): RedirectResponse
    {
        $dispositivo->delete();

        return redirect()->route('admin.dispositivos')
            ->with('estado', "«{$dispositivo->nombre}» desvinculado. Ya no puede entrar.");
    }

    private function volverConCodigo(Dispositivo $dispositivo, string $codigo): RedirectResponse
    {
        return redirect()->route('admin.dispositivos')->with('codigoNuevo', [
            'id' => $dispositivo->id,
            'nombre' => $dispositivo->nombre,
            'codigo' => Dispositivo::formatearCodigo($codigo),
            'caduca' => $dispositivo->codigo_caduca_en->format('H:i'),
        ]);
    }
}
