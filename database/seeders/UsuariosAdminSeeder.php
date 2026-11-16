<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

// Crea las cuentas fijas del equipo para poder entrar siempre en local.
// Los correos son ficticios y la contraseña es de prueba: este seeder
// NO se ejecuta en producción (ver la comprobación de abajo).
class UsuariosAdminSeeder extends Seeder
{
    public function run(): void
    {
        // En producción no se crean: una contraseña "1234" en un servidor
        // público dejaría entrar a cualquiera.
        if (app()->environment('production')) {
            return;
        }

        $usuarios = [
            ['name' => 'Gabriel', 'email' => 'gabriel@mesaya.com'],
            ['name' => 'Iván', 'email' => 'ivan@mesaya.com'],
        ];

        foreach ($usuarios as $usuario) {
            // updateOrCreate busca por el correo: si el usuario ya existe lo
            // actualiza y si no lo crea. Así el seeder se puede ejecutar
            // varias veces sin fallar por el correo repetido.
            User::updateOrCreate(
                ['email' => $usuario['email']],
                [
                    'name' => $usuario['name'],
                    // El modelo User cifra la contraseña solo (cast 'hashed').
                    'password' => '1234',
                    'email_verified_at' => now(),
                ],
            );
        }

        // TODO rama 1 (restaurantes-y-roles): cuando exista la columna
        // users.rol, añadir 'rol' => 'admin' y su restaurante_id.
    }
}
