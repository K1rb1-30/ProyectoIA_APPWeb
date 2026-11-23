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
            // Los 14 alérgenos legales, antes que la carta que los usa.
            AlergenosSeeder::class,
            // Carta de ejemplo de cada restaurante.
            CartaSeeder::class,
            // Mesas de ejemplo de los dos restaurantes.
            MesasSeeder::class,
            // Ventas de ejemplo de los últimos 30 días, para las estadísticas
            // (rama 13). Va al final porque usa las mesas y la carta. No se
            // ejecuta en producción.
            VentasDemoSeeder::class,
        ]);
    }
}
