<?php

namespace Database\Seeders;

use App\Models\Restaurante;
use App\Models\User;
use App\Servicios\PasarelaStripe;
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
 *  - Stripe del restaurante (rama 26): ver conectarStripeDeDemo().
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

            // Los restaurantes de demo tienen la suscripción activa (rama 24),
            // para que la demo funcione sin pasar por Stripe. forceFill porque
            // el estado no está en Fillable.
            $restaurante->forceFill(['suscripcion_estado' => Restaurante::SUSCRIPCION_ACTIVA])->save();

            $this->conectarStripeDeDemo($restaurante);

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

    /**
     * Rama 26: para que la demo siga cobrando con tarjeta como antes, los
     * restaurantes de demo usan la cuenta de Stripe de prueba del EQUIPO, la
     * misma del .env (STRIPE_KEY y STRIPE_SECRET). Así:
     *  - no hay ninguna clave escrita en el código ni en este seeder;
     *  - la clave se guarda cifrada, como si la hubiera pegado el admin;
     *  - solo se copia si es de modo test, y solo a estos 4 restaurantes:
     *    uno dado de alta con el formulario empieza «Sin configurar».
     * Si el restaurante ya tiene claves (las ha cambiado el admin), no se tocan.
     */
    private function conectarStripeDeDemo(Restaurante $restaurante): void
    {
        $secreta = (string) config('services.stripe.secret');
        $publicable = (string) config('services.stripe.key');

        if ($restaurante->tieneStripeConfigurado() || ! PasarelaStripe::esClaveSecretaDeTest($secreta)) {
            return;
        }

        $restaurante->guardarClavesStripe(str_starts_with($publicable, 'pk_test_') ? $publicable : null, $secreta);
    }
}
