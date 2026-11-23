<?php

namespace Database\Seeders;

use App\Models\Restaurante;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Crea 2 restaurantes inventados y, en cada uno, 6 empleados: un admin, tres
 * camareros y dos personas de cocina.
 *
 * Los correos siguen el patrón <usuario>@<slug>.test, por ejemplo
 * camarero@la-tasca.test o camarero2@la-tasca.test. La contraseña de todos es la de desarrollo de
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

            // Un usuario de cada rol (admin@, camarero@, cocina@) y, para que la
            // pantalla de Personal (rama 18) tenga varios empleados, dos
            // camareros y una persona de cocina más (camarero2@, camarero3@, cocina2@).
            $cuentas = [
                'admin' => 'admin',
                'camarero' => 'camarero',
                'cocina' => 'cocina',
                'camarero2' => 'camarero',
                'camarero3' => 'camarero',
                'cocina2' => 'cocina',
            ];

            foreach ($cuentas as $usuario => $rol) {
                $email = $usuario.'@'.$restaurante->slug.'.test';

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
