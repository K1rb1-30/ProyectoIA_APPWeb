<?php

namespace Database\Factories;

use App\Models\Restaurante;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            // Por defecto, admin: así los tests de Breeze siguen yendo al panel.
            'rol' => 'admin',
            // Si no se indica restaurante, se crea uno nuevo para este usuario.
            'restaurante_id' => Restaurante::factory(),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Desarrollador: admin que puede cambiar de restaurante y entrar en las
     * pantallas de todos los roles (cuentas del equipo y del profesor).
     */
    public function desarrollador(): static
    {
        return $this->state(fn (array $attributes) => ['rol' => 'admin', 'puede_cambiar_restaurante' => true]);
    }

    /**
     * Usuario con rol de camarero.
     */
    public function camarero(): static
    {
        return $this->state(fn (array $attributes) => ['rol' => 'camarero']);
    }

    /**
     * Usuario con rol de cocina.
     */
    public function cocina(): static
    {
        return $this->state(fn (array $attributes) => ['rol' => 'cocina']);
    }
}
