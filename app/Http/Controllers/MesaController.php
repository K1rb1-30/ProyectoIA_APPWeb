<?php

namespace App\Http\Controllers;

use App\Models\Mesa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * CRUD de mesas del panel de administración (solo rol admin).
 *
 * No hace falta filtrar por restaurante en ningún método: el modelo Mesa usa
 * el trait PerteneceARestaurante, que lo hace solo. Además, cuando la URL
 * trae una mesa (/mesas/{mesa}/edit), Laravel la busca con ese filtro, así
 * que la mesa de otro restaurante da un 404.
 */
class MesaController extends Controller
{
    /**
     * Listado de mesas con su QR (panelAdmin/mesas-qr).
     */
    public function index(): View
    {
        $mesas = Mesa::with('restaurante')->orderBy('numero')->get();

        return view('mesas.index', ['mesas' => $mesas]);
    }

    /**
     * Página para imprimir los QR o guardarlos como PDF desde el navegador.
     */
    public function imprimir(): View
    {
        $mesas = Mesa::with('restaurante')->orderBy('numero')->get();

        return view('mesas.imprimir', ['mesas' => $mesas]);
    }

    public function create(): View
    {
        // El siguiente número libre, para proponerlo en el formulario.
        $siguienteNumero = (Mesa::max('numero') ?? 0) + 1;

        return view('mesas.formulario', ['mesa' => new Mesa(['numero' => $siguienteNumero])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $datos = $this->validar($request);

        // restaurante_id lo pone el trait con el del admin logueado.
        $mesa = Mesa::create($datos);

        return redirect()->route('mesas.index')
            ->with('estado', 'Mesa '.$mesa->numero.' creada.');
    }

    public function edit(Mesa $mesa): View
    {
        return view('mesas.formulario', ['mesa' => $mesa]);
    }

    public function update(Request $request, Mesa $mesa): RedirectResponse
    {
        $mesa->update($this->validar($request, $mesa));

        return redirect()->route('mesas.index')
            ->with('estado', 'Mesa '.$mesa->numero.' actualizada.');
    }

    public function destroy(Mesa $mesa): RedirectResponse
    {
        $mesa->delete();

        return redirect()->route('mesas.index')
            ->with('estado', 'Mesa '.$mesa->numero.' eliminada.');
    }

    /**
     * Reglas comunes de crear y editar. Solo se edita el número: el estado
     * (libre / ocupada) lo cambian el camarero y el cierre de mesa.
     */
    private function validar(Request $request, ?Mesa $mesa = null): array
    {
        return $request->validate([
            'numero' => [
                'required',
                'integer',
                'min:1',
                'max:999',
                // Rule::unique consulta la tabla directamente, sin pasar por el
                // modelo, así que el filtro del trait NO actúa: por eso hay que
                // decirle a mano que solo mire las mesas de este restaurante.
                // ignore() deja guardar una mesa sin cambiarle el número.
                Rule::unique('mesas')
                    ->where('restaurante_id', $request->user()->restaurante_id)
                    ->ignore($mesa),
            ],
        ], [
            'numero.unique' => 'Ya hay una mesa con ese número.',
        ]);
    }
}
