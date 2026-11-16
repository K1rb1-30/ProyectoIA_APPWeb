<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CategoriaRequest;
use App\Models\Categoria;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * Categorías de la carta (solo admin). Todo se hace en una sola pantalla:
 * arriba el formulario para crear y debajo la lista, cada una con su
 * formulario para editar y su botón de borrar.
 */
class CategoriaController extends Controller
{
    public function index(): View
    {
        return view('admin.categorias.index', [
            'categorias' => Categoria::withCount('platos')->orderBy('orden')->orderBy('nombre')->get(),
        ]);
    }

    public function store(CategoriaRequest $request): RedirectResponse
    {
        // restaurante_id lo pone el trait PerteneceARestaurante.
        $categoria = Categoria::create($request->validated());

        return back()->with('estado', "Categoría «{$categoria->nombre}» creada.");
    }

    public function update(CategoriaRequest $request, Categoria $categoria): RedirectResponse
    {
        $categoria->update($request->validated());

        return back()->with('estado', "Categoría «{$categoria->nombre}» guardada.");
    }

    public function destroy(Categoria $categoria): RedirectResponse
    {
        // No dejamos borrar una categoría con platos: se quedarían sin
        // categoría. (La base de datos tampoco lo permitiría.)
        if ($categoria->platos()->exists()) {
            return back()->with('aviso', "No se puede borrar «{$categoria->nombre}» porque tiene platos. Muévelos o bórralos antes.");
        }

        $categoria->delete();

        return back()->with('estado', "Categoría «{$categoria->nombre}» eliminada.");
    }
}
