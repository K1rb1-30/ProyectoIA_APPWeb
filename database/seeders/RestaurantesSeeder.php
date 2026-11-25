<?php

namespace Database\Seeders;

use App\Models\Restaurante;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Crea 4 restaurantes inventados con su personal: un admin y un número
 * distinto de camareros en cada uno.
 *
 *  - El admin entra con correo y contraseña: admin@<slug>.test y la
 *    contraseña de desarrollo de UserFactory ("password").
 *  - Los camareros no tienen correo: entran en un dispositivo de camarero
 *    emparejado con su nombre (de Faker) y su PIN. El PIN de prueba es la
 *    cifra de su número repetida: 1111 el primero, 2222 el segundo...
 *  - La cocina no tiene cuentas: se empareja una tablet en «Dispositivos».
 *
 * Son datos de prueba: nunca se usan en producción.
 */
class RestaurantesSeeder extends Seeder
{
    public function run(): void
    {
        // Cuatro restaurantes inventados, cada uno con un número distinto de
        // camareros, para que la aplicación parezca un SaaS en uso.
        $restaurantes = [
            ['nombre' => 'La Tasca de Prueba', 'slug' => 'la-tasca', 'camareros' => 3],
            ['nombre' => 'Bar El Rincón', 'slug' => 'bar-el-rincon', 'camareros' => 3],
            ['nombre' => 'Marisquería O Porto', 'slug' => 'marisqueria-o-porto', 'camareros' => 4],
            ['nombre' => 'Café Central', 'slug' => 'cafe-central', 'camareros' => 1],
        ];

        foreach ($restaurantes as $datos) {
            // firstOrCreate busca por el slug: si el restaurante ya existe lo
            // reutiliza. Así "php artisan db:seed" se puede ejecutar varias
            // veces sin fallar por el slug repetido.
            $restaurante = Restaurante::firstOrCreate(['slug' => $datos['slug']], ['nombre' => $datos['nombre']]);

            // El admin, con correo y contraseña. Solo se crea si no existe:
            // así "php artisan db:seed" se puede ejecutar varias veces.
            $correoAdmin = 'admin@'.$restaurante->slug.'.test';
            if (! User::where('email', $correoAdmin)->exists()) {
                User::factory()->create([
                    'email' => $correoAdmin,
                    'rol' => 'admin',
                    'restaurante_id' => $restaurante->id,
                ]);
            }

            // Los camareros que falten hasta llegar a los de la lista, con PIN
            // 1111, 2222... La factory camarero() los deja sin correo ni contraseña.
            $yaHay = User::where('restaurante_id', $restaurante->id)->where('rol', 'camarero')->count();
            for ($n = $yaHay + 1; $n <= $datos['camareros']; $n++) {
                User::factory()->camarero()->create([
                    'pin' => str_repeat((string) $n, 4),
                    'restaurante_id' => $restaurante->id,
                ]);
            }
        }
    }
}
