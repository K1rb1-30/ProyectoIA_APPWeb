<?php

namespace Database\Seeders;

use App\Models\Restaurante;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Crea 2 restaurantes inventados y, en cada uno, un usuario de cada rol.
 *
 * Los correos siguen el patrón <rol>@<slug>.test, por ejemplo
 * camarero@la-tasca.test. La contraseña de todos es la de desarrollo de
 * UserFactory. Son datos de prueba: nunca se usan en producción.
 */
class RestaurantesSeeder extends Seeder
{
    public function run(): void
    {
        $restaurantes = [
            ['nombre' => 'La Tasca de Prueba', 'slug' => 'la-tasca'],
            ['nombre' => 'Bar El Rincón', 'slug' => 'bar-el-rincon'],
        ];

        foreach ($restaurantes as $datos) {
            $restaurante = Restaurante::create($datos);

            foreach (['admin', 'camarero', 'cocina'] as $rol) {
                // La factory pone un nombre de Faker en es_ES y la contraseña.
                User::factory()->create([
                    'email' => $rol.'@'.$restaurante->slug.'.test',
                    'rol' => $rol,
                    'restaurante_id' => $restaurante->id,
                ]);
            }
        }
    }
}
