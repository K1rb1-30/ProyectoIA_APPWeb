<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use App\Models\Mesa;
use App\Models\Restaurante;
use Illuminate\View\View;

/**
 * Carta pública que ve el cliente al escanear el QR de su mesa.
 * Ruta: /r/{slug}/mesa/{numero}, sin login.
 *
 * MUY IMPORTANTE (aislamiento entre restaurantes): aquí NO nos fiamos del
 * trait PerteneceARestaurante. Ese filtro usa el restaurante del usuario
 * logueado, y en esta pantalla:
 *  - normalmente no hay nadie logueado, así que no filtraría nada;
 *  - y si hay alguien (un admin probando el QR), filtraría por SU
 *    restaurante y no por el del QR.
 * Por eso quitamos ese filtro con withoutGlobalScope('restaurante') y
 * filtramos siempre a mano por el restaurante que sale en la URL.
 */
class CartaController extends Controller
{
    public function show(string $slug, int $numero): View
    {
        // 1. El restaurante del slug. Si no existe → 404.
        $restaurante = Restaurante::where('slug', $slug)->firstOrFail();

        // 2. La mesa con ese número DENTRO de ese restaurante. Si no existe → 404.
        $mesa = Mesa::withoutGlobalScope('restaurante')
            ->where('restaurante_id', $restaurante->id)
            ->where('numero', $numero)
            ->firstOrFail();

        // 3. Categorías del restaurante con SOLO sus platos disponibles.
        //    Los platos agotados no salen (golden test nº 5).
        $categorias = Categoria::withoutGlobalScope('restaurante')
            ->where('restaurante_id', $restaurante->id)
            ->with(['platos' => function ($consulta) use ($restaurante) {
                $consulta->withoutGlobalScope('restaurante')
                    ->where('restaurante_id', $restaurante->id)
                    ->where('disponible', true)
                    ->with('alergenos')
                    ->orderBy('nombre');
            }])
            ->orderBy('orden')
            ->orderBy('nombre')
            ->get()
            // Las categorías que se quedan sin platos disponibles no se muestran.
            ->filter(fn (Categoria $categoria) => $categoria->platos->isNotEmpty())
            ->values();

        return view('cliente.carta', [
            'restaurante' => $restaurante,
            'mesa' => $mesa,
            'categorias' => $categorias,
        ]);
    }
}
