<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Rellena la base de datos con los datos de desarrollo.
     */
    public function run(): void
    {
        $this->call([
            // Primero los restaurantes, porque todo usuario necesita uno.
            RestaurantesSeeder::class,
            // Cuentas fijas del equipo (Gabriel e Iván) para entrar siempre en local.
            UsuariosAdminSeeder::class,
        ]);
    }
}
