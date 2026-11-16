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
            RestaurantesSeeder::class,
        ]);
    }
}
