<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Cuenta de Stripe PROPIA de cada restaurante para cobrar a sus clientes
// (rama 26). Un restaurante solo tiene una integración, así que va en columnas
// de `restaurantes` y no en una tabla aparte (decidido el 26/11/2026).
//
// El prefijo "stripe_cobros_" las separa de stripe_cliente_id y
// stripe_suscripcion_id, que son de la cuenta de la PLATAFORMA (rama 24).
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('restaurantes', function (Blueprint $table) {
            // pk_test_... (no es secreta, pero solo se acepta de modo test).
            $table->string('stripe_cobros_clave_publicable')->nullable()->after('suscripcion_fin_en');
            // sk_test_... o rk_test_..., CIFRADA con APP_KEY (cast "encrypted"
            // del modelo). Es "text" porque el texto cifrado ocupa más de 200
            // caracteres aunque la clave sea corta.
            $table->text('stripe_cobros_clave_secreta')->nullable()->after('stripe_cobros_clave_publicable');
            // Las 4 últimas cifras, para enseñar «termina en ••••1234» sin descifrar.
            $table->string('stripe_cobros_final_clave', 4)->nullable()->after('stripe_cobros_clave_secreta');
            // Cuándo comprobamos con Stripe que la clave funciona.
            $table->timestamp('stripe_cobros_comprobada_en')->nullable()->after('stripe_cobros_final_clave');
        });
    }

    public function down(): void
    {
        Schema::table('restaurantes', function (Blueprint $table) {
            $table->dropColumn([
                'stripe_cobros_clave_publicable',
                'stripe_cobros_clave_secreta',
                'stripe_cobros_final_clave',
                'stripe_cobros_comprobada_en',
            ]);
        });
    }
};
