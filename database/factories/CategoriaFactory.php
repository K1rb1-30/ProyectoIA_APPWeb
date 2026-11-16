<?php

namespace Database\Factories;

use App\Models\Categoria;
use App\Models\Restaurante;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Categoria>
 */
class CategoriaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'restaurante_id' => Restaurante::factory(),
            'nombre' => fake()->unique()->word(),
            'orden' => fake()->numberBetween(0, 10),
        ];
    }
}
