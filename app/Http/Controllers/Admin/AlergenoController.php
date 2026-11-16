<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alergeno;
use Illuminate\View\View;

/**
 * Consulta de los 14 alérgenos legales. Son fijos (los pone la ley), así que
 * aquí solo se ven; se asignan a cada plato desde su formulario.
 */
class AlergenoController extends Controller
{
    public function index(): View
    {
        // withCount('platos') cuenta los platos con cada alérgeno. Como Plato
        // usa PerteneceARestaurante, solo cuenta los del restaurante del admin.
        return view('admin.alergenos.index', [
            'alergenos' => Alergeno::withCount('platos')->orderBy('id')->get(),
        ]);
    }
}
