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
            // firstOrCreate busca por el slug: si el restaurante ya existe lo
            // reutiliza. Así "php artisan db:seed" se puede ejecutar varias
            // veces sin fallar por el slug repetido.
            $restaurante = Restaurante::firstOrCreate(['slug' => $datos['slug']], ['nombre' => $datos['nombre']]);

            foreach (['admin', 'camarero', 'cocina'] as $rol) {
                $email = $rol.'@'.$restaurante->slug.'.test';

                // Igual con los usuarios: solo se crean si no existen todavía.
                if (User::where('email', $email)->exists()) {
                    continue;
                }

                // La factory pone un nombre de Faker en es_ES y la contraseña.
                User::factory()->create([
                    'email' => $email,
                    'rol' => $rol,
                    'restaurante_id' => $restaurante->id,
                ]);
            }
        }
    }
}
