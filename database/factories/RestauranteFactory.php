<?php

namespace Database\Factories;

use App\Models\Restaurante;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * Crea restaurantes inventados para los tests.
 *
 * Por defecto con la suscripción ACTIVA (rama 24): así los tests que ya
 * había siguen funcionando sin tocarlos. Para probar el bloqueo:
 *   Restaurante::factory()->pendiente()->create()
 *
 * Rama 26: también con Stripe CONFIGURADO, con claves FALSAS (no existen en
 * Stripe: en los tests PasarelaStripe siempre está simulada). Para probar un
 * restaurante que solo cobra en efectivo:
 *   Restaurante::factory()->sinStripe()->create()
 *
 * @extends Factory<Restaurante>
 */
class RestauranteFactory extends Factory
{
    public function definition(): array
    {
        $nombre = 'Bar '.fake()->unique()->lastName();

        return [
            'nombre' => $nombre,
            'slug' => Str::slug($nombre),
            'suscripcion_estado' => Restaurante::SUSCRIPCION_ACTIVA,
            'stripe_cobros_clave_publicable' => 'pk_test_falsa_1234',
            'stripe_cobros_clave_secreta' => 'sk_test_falsa_1234',   // el cast la cifra
            'stripe_cobros_final_clave' => '1234',
            'stripe_cobros_comprobada_en' => now(),
        ];
    }

    /**
     * Restaurante sin cuenta de Stripe propia: solo cobra en efectivo.
     */
    public function sinStripe(): static
    {
        return $this->state([
            'stripe_cobros_clave_publicable' => null,
            'stripe_cobros_clave_secreta' => null,
            'stripe_cobros_final_clave' => null,
            'stripe_cobros_comprobada_en' => null,
        ]);
    }

    /**
     * Restaurante recién dado de alta que todavía no ha pagado.
     */
    public function pendiente(): static
    {
        return $this->state(['suscripcion_estado' => Restaurante::SUSCRIPCION_PENDIENTE]);
    }
}
