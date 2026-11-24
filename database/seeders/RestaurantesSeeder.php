<?php

namespace Database\Seeders;

use App\Models\Restaurante;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Crea 4 restaurantes inventados con su personal: un admin y un número
 * distinto de camareros y de personas de cocina en cada uno.
 *
 * Los correos siguen el patrón <usuario>@<slug>.test, por ejemplo
 * camarero@la-tasca.test o camarero2@la-tasca.test. La contraseña de todos es la de desarrollo de
 * UserFactory. Son datos de prueba: nunca se usan en producción.
 */
class RestaurantesSeeder extends Seeder
{
    public function run(): void
    {
        // Cuatro restaurantes inventados, cada uno con una plantilla distinta
        // (número de camareros y de personas de cocina), para que la
        // aplicación parezca un SaaS en uso. Los correos siguen el patrón
        // <usuario>@<slug>.test: camarero2@la-tasca.test, cocina@cafe-central.test...
        $restaurantes = [
            ['nombre' => 'La Tasca de Prueba', 'slug' => 'la-tasca', 'camareros' => 3, 'cocina' => 2],
            ['nombre' => 'Bar El Rincón', 'slug' => 'bar-el-rincon', 'camareros' => 3, 'cocina' => 2],
            ['nombre' => 'Marisquería O Porto', 'slug' => 'marisqueria-o-porto', 'camareros' => 4, 'cocina' => 3],
            ['nombre' => 'Café Central', 'slug' => 'cafe-central', 'camareros' => 1, 'cocina' => 1],
        ];

        foreach ($restaurantes as $datos) {
            // firstOrCreate busca por el slug: si el restaurante ya existe lo
            // reutiliza. Así "php artisan db:seed" se puede ejecutar varias
            // veces sin fallar por el slug repetido.
            $restaurante = Restaurante::firstOrCreate(['slug' => $datos['slug']], ['nombre' => $datos['nombre']]);

            // Un admin y los camareros y cocineros de cada restaurante:
            // camarero@, camarero2@, camarero3@... y cocina@, cocina2@...
            // (para que la pantalla de Personal, rama 18, tenga empleados).
            $cuentas = ['admin' => 'admin'];
            foreach (['camarero' => $datos['camareros'], 'cocina' => $datos['cocina']] as $rol => $cuantos) {
                for ($n = 1; $n <= $cuantos; $n++) {
                    $cuentas[$n === 1 ? $rol : $rol.$n] = $rol;
                }
            }

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
