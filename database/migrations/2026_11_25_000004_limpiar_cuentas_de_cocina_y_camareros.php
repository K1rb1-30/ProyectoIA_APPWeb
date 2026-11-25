<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

// Limpieza de las cuentas que quedaron del sistema anterior (correo y
// contraseña para todo el personal):
//  - Cocina ya no tiene usuarios: entra con las tablets emparejadas
//    (tabla dispositivos). Se borran las cuentas de cocina. Sus sesiones
//    abiertas se borran con ellas.
//  - Los camareros no tienen correo ni contraseña: entran con su nombre y su
//    PIN. Se les quitan. Los que no tengan PIN tendrán que pedírselo al admin
//    (en Personal → Editar).
//
// El valor 'cocina' de users.rol no se quita: así no hay que tocar código
// ni tests que todavía lo usan. Desde la web ya no se puede crear ninguna
// cuenta de cocina (EmpleadoRequest::ROLES).
return new class extends Migration
{
    public function up(): void
    {
        $cocina = DB::table('users')->where('rol', 'cocina')->pluck('id');

        // Los pedidos y pagos que tuvieran se conservan sin usuario
        // (sus claves foráneas son nullOnDelete).
        DB::table('sessions')->whereIn('user_id', $cocina)->delete();
        DB::table('users')->whereIn('id', $cocina)->delete();

        DB::table('users')->where('rol', 'camarero')->update([
            'email' => null,
            'email_verified_at' => null,
            'password' => null,
            'remember_token' => null,
        ]);
    }

    public function down(): void
    {
        // No se puede deshacer: las cuentas borradas y los correos quitados
        // no se guardan en ningún sitio. Para volver a tenerlos: migrate:fresh --seed.
    }
};
