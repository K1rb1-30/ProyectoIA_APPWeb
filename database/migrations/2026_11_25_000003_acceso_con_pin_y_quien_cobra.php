<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Rama 2 del acceso por dispositivos:
//  - Los camareros ya no tienen correo ni contraseña: entran en un dispositivo
//    emparejado con su nombre y un PIN de 4 cifras. Por eso email y password
//    pasan a ser opcionales (el admin y los desarrolladores los siguen usando)
//    y se añade la columna pin (cifrada, como las contraseñas).
//  - pagos.user_id: quién cobró cada pago (el camarero), para tener control.
//    Vacío cuando paga el propio cliente con Stripe.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // El unique de email se queda: MySQL deja varios null en un unique.
            $table->string('email')->nullable()->change();
            $table->string('password')->nullable()->change();
            $table->string('pin')->nullable()->after('password');
        });

        Schema::table('pagos', function (Blueprint $table) {
            // Si se borra el camarero, el pago se conserva sin quién lo cobró.
            $table->foreignId('user_id')->nullable()->after('parte_cuenta_id')
                ->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pagos', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('pin');
        });

        // Ojo: para volver a "obligatorios" no puede quedar ningún usuario sin
        // correo o sin contraseña (los camareros con PIN). Si los hay, la
        // marcha atrás falla; habría que borrarlos o darles correo antes.
        Schema::table('users', function (Blueprint $table) {
            $table->string('email')->nullable(false)->change();
            $table->string('password')->nullable(false)->change();
        });
    }
};
