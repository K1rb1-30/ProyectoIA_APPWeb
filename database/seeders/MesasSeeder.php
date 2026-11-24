<?php

namespace Database\Seeders;

use App\Models\Restaurante;
use Illuminate\Database\Seeder;

/**
 * Crea mesas de ejemplo en los restaurantes de RestaurantesSeeder:
 * 8 en La Tasca, 6 en Bar El Rincón, 14 en la Marisquería O Porto y 5 en el
 * Café Central, numeradas desde el 1 en cada uno
 * (así se ve que el número se puede repetir entre restaurantes).
 * Un par de mesas empiezan ocupadas para que se vean los dos estados.
 */
class MesasSeeder extends Seeder
{
    public function run(): void
    {
        $mesasPorRestaurante = [
            'la-tasca' => ['total' => 8, 'ocupadas' => [2, 5]],
            'bar-el-rincon' => ['total' => 6, 'ocupadas' => [3]],
            // Cada local con un tamaño distinto: la marisquería es grande y
            // la cafetería pequeña.
            'marisqueria-o-porto' => ['total' => 14, 'ocupadas' => []],
            'cafe-central' => ['total' => 5, 'ocupadas' => []],
        ];

        foreach ($mesasPorRestaurante as $slug => $datos) {
            $restaurante = Restaurante::where('slug', $slug)->first();

            // Si ese restaurante no existe, no hay nada que sembrar en él.
            if ($restaurante === null) {
                continue;
            }

            for ($numero = 1; $numero <= $datos['total']; $numero++) {
                // Crear a través de la relación mesas() rellena restaurante_id
                // solo. firstOrCreate permite ejecutar el seeder dos veces.
                $restaurante->mesas()->firstOrCreate(
                    ['numero' => $numero],
                    ['estado' => in_array($numero, $datos['ocupadas']) ? 'ocupada' : 'libre'],
                );
            }
        }
    }
}
