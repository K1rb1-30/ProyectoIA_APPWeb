<?php

namespace Database\Factories;

use App\Models\Mesa;
use App\Models\Restaurante;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Crea mesas para los tests.
 *
 * @extends Factory<Mesa>
 */
class MesaFactory extends Factory
{
    public function definition(): array
    {
        return [
            // Si no se indica restaurante, se crea uno nuevo para la mesa.
            'restaurante_id' => Restaurante::factory(),
            'numero' => fake()->unique()->numberBetween(1, 200),
            'estado' => 'libre',
        ];
    }

    /**
     * Mesa ocupada.
     */
    public function ocupada(): static
    {
        return $this->state(fn (array $attributes) => ['estado' => 'ocupada']);
    }
}
