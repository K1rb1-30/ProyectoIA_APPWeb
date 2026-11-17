<?php

namespace App\Http\Controllers\Camarero;

use App\Acciones\CrearPedido;
use App\Http\Controllers\Controller;
use App\Http\Requests\Camarero\ComandaRequest;
use App\Models\Categoria;
use App\Models\Mesa;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * Pantallas del camarero para tomar comandas.
 *
 * Todas las consultas pasan por el global scope de PerteneceARestaurante, así
 * que el camarero solo ve mesas y platos de su restaurante. Y como {mesa} de
 * la URL también se busca con ese filtro, la mesa de otro restaurante da 404.
 */
class ComandaController extends Controller
{
    /**
     * Listado de mesas del restaurante, con su estado.
     */
    public function mesas(): View
    {
        $mesas = Mesa::orderBy('numero')->get();

        return view('camarero.mesas', [
            'mesas' => $mesas,
            'libres' => $mesas->where('estado', 'libre')->count(),
            'ocupadas' => $mesas->where('estado', 'ocupada')->count(),
        ]);
    }

    /**
     * Formulario de nueva comanda para una mesa.
     */
    public function create(Mesa $mesa): View
    {
        // Categorías con sus platos (también los agotados, que se ven en gris).
        // "with" carga platos y alérgenos de golpe, en vez de una consulta
        // por cada plato (el famoso problema N+1).
        $categorias = Categoria::with(['platos' => fn ($consulta) => $consulta->orderBy('nombre'), 'platos.alergenos'])
            ->orderBy('orden')
            ->get()
            ->filter(fn ($categoria) => $categoria->platos->isNotEmpty())
            ->values();

        return view('camarero.nueva-comanda', [
            'mesa' => $mesa,
            'mesas' => Mesa::orderBy('numero')->get(), // Para cambiar de mesa arriba.
            'categorias' => $categorias,
        ]);
    }

    /**
     * Guarda la comanda. La creación de verdad la hace CrearPedido.
     */
    public function store(ComandaRequest $request, Mesa $mesa, CrearPedido $crearPedido): RedirectResponse
    {
        // array_values quita las claves (que son los ids de plato) y deja una
        // lista normal. Si algún plato no vale, CrearPedido lanza un error de
        // validación y Laravel vuelve al formulario con el mensaje.
        $crearPedido->ejecutar($mesa, array_values($request->validated('lineas')), $request->user());

        return redirect()->route('camarero.mesas')
            ->with('estado', 'Comanda de la mesa '.$mesa->numero.' enviada a cocina.');
    }
}
