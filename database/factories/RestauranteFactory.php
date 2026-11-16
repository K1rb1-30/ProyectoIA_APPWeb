<?php

namespace Database\Factories;

use App\Models\Restaurante;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * Crea restaurantes inventados para los tests.
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
        ];
    }
}
