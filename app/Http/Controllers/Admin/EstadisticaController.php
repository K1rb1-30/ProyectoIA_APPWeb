<?php

namespace App\Http\Controllers\Admin;

use App\Estadisticas\EstadisticasRestaurante;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Pantalla de estadísticas del admin (rama 13).
 *
 * No calcula nada: crea EstadisticasRestaurante con el restaurante del admin
 * logueado y pasa los resultados a la vista. Así, el admin del restaurante A
 * solo ve las estadísticas de A.
 */
class EstadisticaController extends Controller
{
    public function index(Request $request): View
    {
        $estadisticas = new EstadisticasRestaurante($request->user()->restaurante_id);

        return view('admin.estadisticas', [
            'resumen' => $estadisticas->resumenDeHoy(),
            'ventasPorDia' => $estadisticas->ventasPorDia(30),
            'platos' => $estadisticas->platosMasPedidos(10, 30),
            'metodos' => $estadisticas->metodosDePago(30),
        ]);
    }
}
