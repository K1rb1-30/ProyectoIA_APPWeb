<?php

namespace Database\Seeders;

use App\Models\Alergeno;
use App\Models\Categoria;
use App\Models\Plato;
use App\Models\Restaurante;
use Illuminate\Database\Seeder;

/**
 * Carta de ejemplo (inventada) de cada restaurante, para tener datos en
 * desarrollo y en la demo. Cada restaurante tiene SU carta: tipo de cocina,
 * precios, alérgenos y platos agotados distintos. No lleva fotos.
 * Necesita que antes se hayan ejecutado RestaurantesSeeder y AlergenosSeeder.
 *
 * Se puede ejecutar varias veces: busca por nombre antes de crear (los platos
 * que ya existen no se cambian).
 */
class CartaSeeder extends Seeder
{
    public function run(): void
    {
        // Tabla nombre => id de los alérgenos, para no consultar en cada plato.
        $idsAlergenos = Alergeno::pluck('id', 'nombre');

        foreach (Restaurante::all() as $restaurante) {
            [$carta, $agotados] = $this->cartaDe($restaurante->slug);
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
                            // Algunos platos empiezan agotados: en la demo se ve que
                            // no salen en la carta del QR y salen en gris al camarero.
                            'disponible' => ! in_array($nombre, $agotados),
                        ],
                    );

                    // sync deja en la tabla pivote exactamente estos alérgenos.
                    $plato->alergenos()->sync($idsAlergenos->only($alergenos)->values());
                }
            }
        }
    }

    /**
     * Carta de cada restaurante: [categoría => [[nombre, descripción, precio en
     * céntimos, alérgenos]]] y la lista de platos que empiezan agotados.
     * Un restaurante sin carta propia recibe la de la tasca.
     *
     * @return array{0: array<string, array<int, array{0: string, 1: string, 2: int, 3: array<int, string>}>>, 1: array<int, string>}
     */
    private function cartaDe(string $slug): array
    {
        return match ($slug) {
            // Bar de barrio: más barato, con bocadillos.
            'bar-el-rincon' => [[
                'Tapas' => [
                    ['Patatas bravas', 'Ración pequeña con salsa brava de la casa.', 450, ['Huevos']],
                    ['Croquetas caseras', 'Cuatro croquetas de pollo y jamón.', 700, ['Gluten', 'Huevos', 'Lácteos']],
                    ['Boquerones en vinagre', 'Con ajo y perejil.', 550, ['Pescado', 'Sulfitos']],
                    ['Aceitunas aliñadas', 'Aceitunas de la casa.', 250, []],
                ],
                'Bocadillos' => [
                    ['Bocadillo de calamares', 'Pan crujiente con calamares rebozados.', 650, ['Gluten', 'Moluscos', 'Huevos']],
                    ['Bocadillo de lomo con queso', 'Lomo a la plancha y queso fundido.', 600, ['Gluten', 'Lácteos']],
                    ['Montadito de pringá', 'Carne de cocido desmigada.', 350, ['Gluten', 'Apio']],
                ],
                'Raciones' => [
                    ['Calamares a la romana', 'Ración para compartir con limón.', 1000, ['Gluten', 'Moluscos', 'Huevos']],
                    ['Huevos rotos con jamón', 'Patatas, huevos y jamón serrano.', 900, ['Huevos']],
                ],
                'Bebidas' => [
                    ['Caña de cerveza', 'Cerveza de barril, 200 ml.', 200, ['Gluten']],
                    ['Tinto de verano', 'Vino tinto con gaseosa.', 250, ['Sulfitos']],
                    ['Café con leche', 'Café espresso con leche.', 140, ['Lácteos']],
                    ['Refresco', 'Lata de 33 cl.', 200, []],
                ],
                'Postres' => [
                    ['Crema catalana', 'Crema con azúcar quemado por encima.', 400, ['Huevos', 'Lácteos']],
                    ['Arroz con leche', 'Con canela.', 350, ['Lácteos']],
                ],
            ], ['Montadito de pringá']],

            // Marisquería gallega: más cara, mucho marisco.
            'marisqueria-o-porto' => [[
                'Mariscos' => [
                    ['Gambas a la plancha', 'Gamba roja con sal gorda.', 1800, ['Crustáceos']],
                    ['Mejillones al vapor', 'Mejillones gallegos con limón.', 1100, ['Moluscos']],
                    ['Zamburiñas a la gallega', 'Gratinadas con pimentón.', 1600, ['Moluscos']],
                    ['Centollo cocido', 'Centollo entero de la ría.', 3800, ['Crustáceos']],
                    ['Navajas a la plancha', 'Con ajo y perejil.', 1500, ['Moluscos']],
                ],
                'Pescados' => [
                    ['Merluza a la gallega', 'Con cachelos y ajada.', 1950, ['Pescado']],
                    ['Rodaballo al horno', 'Con patatas panaderas.', 2400, ['Pescado']],
                    ['Pulpo a feira', 'Con aceite, sal y pimentón.', 1700, ['Moluscos']],
                ],
                'Entrantes' => [
                    ['Empanada de zamburiñas', 'Masa casera de maíz.', 900, ['Gluten', 'Moluscos', 'Huevos']],
                    ['Pimientos de Padrón', 'Unos pican y otros no.', 650, []],
                    ['Ensalada de la huerta', 'Tomate, cebolla y bonito.', 850, ['Pescado', 'Mostaza']],
                ],
                'Bebidas' => [
                    ['Albariño (copa)', 'Rías Baixas.', 450, ['Sulfitos']],
                    ['Ribeiro (taza)', 'Vino blanco de la casa.', 300, ['Sulfitos']],
                    ['Agua mineral', 'Botella de 50 cl.', 250, []],
                ],
                'Postres' => [
                    ['Tarta de Santiago', 'Tarta de almendra.', 600, ['Frutos de cáscara', 'Huevos']],
                    ['Filloas con miel', 'Crepes gallegas.', 550, ['Gluten', 'Huevos', 'Lácteos']],
                ],
            ], ['Centollo cocido', 'Navajas a la plancha']],

            // Cafetería de desayunos y meriendas.
            'cafe-central' => [[
                'Cafés' => [
                    ['Café solo', 'Espresso.', 130, []],
                    ['Café con leche', 'Espresso con leche.', 160, ['Lácteos']],
                    ['Capuchino', 'Con cacao por encima.', 220, ['Lácteos']],
                    ['Chocolate caliente', 'Espeso, para mojar.', 280, ['Lácteos']],
                ],
                'Desayunos' => [
                    ['Tostada con tomate', 'Pan de pueblo con tomate y aceite.', 250, ['Gluten']],
                    ['Tostada de aguacate', 'Con semillas de sésamo.', 450, ['Gluten', 'Sésamo']],
                    ['Croissant', 'Croissant de mantequilla.', 180, ['Gluten', 'Lácteos', 'Huevos']],
                    ['Churros', 'Ración de seis churros.', 300, ['Gluten']],
                ],
                'Zumos' => [
                    ['Zumo de naranja natural', 'Exprimido al momento.', 320, []],
                    ['Batido de fresa', 'Con leche y fresas.', 380, ['Lácteos']],
                ],
                'Dulces' => [
                    ['Tarta de zanahoria', 'Con crema de queso y nueces.', 420, ['Gluten', 'Huevos', 'Lácteos', 'Frutos de cáscara']],
                    ['Brownie', 'Con nueces.', 380, ['Gluten', 'Huevos', 'Frutos de cáscara', 'Soja']],
                    ['Galletas de mantequilla de cacahuete', 'Tres galletas caseras.', 250, ['Gluten', 'Cacahuetes']],
                ],
            ], ['Croissant']],

            // La tasca (y cualquier otro restaurante sin carta propia).
            default => [[
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
            ], ['Pulpo a la gallega']],
        };
    }
}
