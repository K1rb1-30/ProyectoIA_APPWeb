<?php

namespace Database\Factories;

use App\Models\Categoria;
use App\Models\Plato;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Plato>
 */
class PlatoFactory extends Factory
{
    public function definition(): array
    {
        return [
            // Se crea una categoría (con su restaurante) y el plato usa ese
            // mismo restaurante, para que plato y categoría nunca queden mezclados.
            'categoria_id' => Categoria::factory(),
            'restaurante_id' => fn (array $atributos) => Categoria::withoutGlobalScopes()
                ->find($atributos['categoria_id'])->restaurante_id,
            'nombre' => fake()->words(3, true),
            'descripcion' => fake()->sentence(),
            'precio_centimos' => fake()->numberBetween(150, 2500),
            'foto' => null,
            'disponible' => true,
        ];
    }

    /**
     * Plato agotado (no disponible).
     */
    public function agotado(): static
    {
        return $this->state(fn (array $atributos) => ['disponible' => false]);
    }
}
