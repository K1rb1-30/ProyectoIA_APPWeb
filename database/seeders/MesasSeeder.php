<?php

namespace Database\Seeders;

use App\Models\Restaurante;
use Illuminate\Database\Seeder;

/**
 * Crea mesas de ejemplo en los restaurantes de RestaurantesSeeder:
 * 8 en La Tasca y 6 en Bar El Rincón, numeradas desde el 1 en cada uno
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
