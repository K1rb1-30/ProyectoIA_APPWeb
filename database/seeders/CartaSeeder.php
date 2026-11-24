<?php

namespace Database\Seeders;

use App\Models\Alergeno;
use App\Models\Categoria;
use App\Models\Plato;
use App\Models\Restaurante;
use Illuminate\Database\Seeder;

/**
 * Carta de ejemplo (inventada) para cada restaurante, para tener datos en
 * desarrollo y en la demo. No lleva fotos. Necesita que antes se hayan
 * ejecutado RestaurantesSeeder y AlergenosSeeder.
 *
 * Se puede ejecutar varias veces: busca por nombre antes de crear.
 */
class CartaSeeder extends Seeder
{
    public function run(): void
    {
        // Categoría => lista de [nombre, descripción, precio en céntimos, alérgenos].
        $carta = [
            'Tapas' => [
                ['Patatas bravas', 'Patata frita con salsa brava picante y alioli.', 550, ['Huevos']],
                ['Croquetas caseras', 'Seis croquetas de jamón con bechamel cremosa.', 850, ['Gluten', 'Huevos', 'Lácteos']],
                ['Ensaladilla rusa', 'Patata, atún, huevo y mayonesa casera.', 600, ['Huevos', 'Pescado']],
            ],
            'Raciones' => [
                ['Calamares a la romana', 'Anillas de calamar rebozadas con limón.', 1200, ['Gluten', 'Moluscos', 'Huevos']],
                ['Tortilla de patatas', 'Tortilla jugosa con cebolla, para compartir.', 950, ['Huevos']],
                ['Pulpo a la gallega', 'Pulpo cocido con pimentón, aceite y cachelos.', 1650, ['Moluscos']],
            ],
            'Bebidas' => [
                ['Caña de cerveza', 'Cerveza de barril, 200 ml.', 250, ['Gluten']],
                ['Agua mineral', 'Botella de 50 cl.', 180, []],
                ['Café con leche', 'Café espresso con leche.', 160, ['Lácteos']],
            ],
            'Postres' => [
                ['Flan casero', 'Flan de huevo con caramelo.', 450, ['Huevos', 'Lácteos']],
                ['Tarta de queso', 'Tarta de queso cremosa al horno.', 550, ['Gluten', 'Huevos', 'Lácteos']],
                ['Crema catalana', 'Crema con azúcar quemado por encima.', 500, ['Huevos', 'Lácteos']],
            ],
        ];

        // Platos que se crean agotados, para que en la demo se vea que no
        // salen en la carta del QR y salen en gris en la comanda del camarero.
        $agotados = ['Pulpo a la gallega'];

        // Tabla nombre => id de los alérgenos, para no consultar en cada plato.
        $idsAlergenos = Alergeno::pluck('id', 'nombre');

        foreach (Restaurante::all() as $restaurante) {
            $orden = 1;

            foreach ($carta as $nombreCategoria => $platos) {
                $categoria = Categoria::firstOrCreate(
                    ['restaurante_id' => $restaurante->id, 'nombre' => $nombreCategoria],
                    ['orden' => $orden++],
                );

                foreach ($platos as [$nombre, $descripcion, $precio, $alergenos]) {
                    $plato = Plato::firstOrCreate(
                        ['restaurante_id' => $restaurante->id, 'nombre' => $nombre],
                        [
                            'categoria_id' => $categoria->id,
                            'descripcion' => $descripcion,
                            'precio_centimos' => $precio,
                            'disponible' => ! in_array($nombre, $agotados),
                        ],
                    );

                    // sync deja en la tabla pivote exactamente estos alérgenos.
                    $plato->alergenos()->sync($idsAlergenos->only($alergenos)->values());
                }
            }
        }
    }
}
