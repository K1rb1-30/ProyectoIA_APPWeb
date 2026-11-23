<?php

namespace App\Http\Controllers;

use App\Models\Mesa;
use Illuminate\View\View;

/**
 * Página de inicio pública de MesaYa en "/" (rama 16). Sin login.
 *
 * Lo único que calcula es si existe la carta de demo del seeder (mesa 1 de
 * "la-tasca"), para enseñar el botón «Ver una carta de ejemplo» solo si
 * funciona. El botón de acceso (login o la pantalla de cada rol) lo decide
 * la vista con @auth.
 */
class InicioController extends Controller
{
    public function __invoke(): View
    {
        // rescue(): si la base de datos no responde (o aún no tiene tablas), la
        // página sale igual, sin el botón. report: false → no ensucia el log.
        $hayCartaDemo = rescue(
            fn () => Mesa::withoutGlobalScope('restaurante')
                ->where('numero', 1)
                ->whereHas('restaurante', fn ($consulta) => $consulta->where('slug', 'la-tasca'))
                ->exists(),
            false,
            report: false,
        );

        return view('inicio', [
            'urlCartaDemo' => $hayCartaDemo ? route('cliente.carta', ['slug' => 'la-tasca', 'numero' => 1]) : null,
        ]);
    }
}
