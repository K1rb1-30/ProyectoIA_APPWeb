<?php

namespace Database\Seeders;

use App\Models\Alergeno;
use Illuminate\Database\Seeder;

/**
 * Los 14 alérgenos de declaración obligatoria (anexo II del Reglamento
 * UE 1169/2011), con un icono de Material Symbols para cada uno.
 *
 * Se ejecuta también en producción: son datos legales, no de prueba.
 */
class AlergenosSeeder extends Seeder
{
    public function run(): void
    {
        $alergenos = [
            ['nombre' => 'Gluten', 'icono' => 'grain'],
            ['nombre' => 'Crustáceos', 'icono' => 'set_meal'],
            ['nombre' => 'Huevos', 'icono' => 'egg'],
            ['nombre' => 'Pescado', 'icono' => 'phishing'],
            ['nombre' => 'Cacahuetes', 'icono' => 'spa'],
            ['nombre' => 'Soja', 'icono' => 'eco'],
            ['nombre' => 'Lácteos', 'icono' => 'water_drop'],
            ['nombre' => 'Frutos de cáscara', 'icono' => 'nutrition'],
            ['nombre' => 'Apio', 'icono' => 'grass'],
            ['nombre' => 'Mostaza', 'icono' => 'local_florist'],
            ['nombre' => 'Sésamo', 'icono' => 'scatter_plot'],
            ['nombre' => 'Sulfitos', 'icono' => 'wine_bar'],
            ['nombre' => 'Altramuces', 'icono' => 'potted_plant'],
            ['nombre' => 'Moluscos', 'icono' => 'set_meal'],
        ];

        foreach ($alergenos as $alergeno) {
            // updateOrCreate por el nombre: se puede ejecutar varias veces sin duplicar.
            Alergeno::updateOrCreate(['nombre' => $alergeno['nombre']], ['icono' => $alergeno['icono']]);
        }
    }
}
