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
        ];
    }

    /**
     * Restaurante recién dado de alta que todavía no ha pagado.
     */
    public function pendiente(): static
    {
        return $this->state(['suscripcion_estado' => Restaurante::SUSCRIPCION_PENDIENTE]);
    }
}
