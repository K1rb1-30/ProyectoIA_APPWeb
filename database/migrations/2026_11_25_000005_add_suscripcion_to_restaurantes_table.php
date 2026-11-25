<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Suscripción mensual de cada restaurante (rama 24). Un restaurante solo
// tiene una suscripción a la vez, así que se guarda en columnas de
// `restaurantes` en vez de en una tabla aparte (decidido el 25/11/2026).
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('restaurantes', function (Blueprint $table) {
            // Ids que da Stripe: el cliente (cus_...) y la suscripción (sub_...).
            // Únicos: el webhook buscará el restaurante por la suscripción.
            $table->string('stripe_cliente_id')->nullable()->unique()->after('slug');
            $table->string('stripe_suscripcion_id')->nullable()->unique()->after('stripe_cliente_id');
            // pendiente / activa / impagada / cancelada. Solo "activa" deja usar la app.
            $table->string('suscripcion_estado', 20)->default('pendiente')->after('stripe_suscripcion_id');
            // Hasta cuándo está pagada: la próxima fecha de cobro.
            $table->timestamp('suscripcion_fin_en')->nullable()->after('suscripcion_estado');
        });

        // Los restaurantes que ya existían (los de la demo) siguen funcionando:
        // si se quedaran en "pendiente", nadie podría usarlos al migrar.
        DB::table('restaurantes')->update(['suscripcion_estado' => 'activa']);
    }

    public function down(): void
    {
        Schema::table('restaurantes', function (Blueprint $table) {
            $table->dropUnique(['stripe_cliente_id']);
            $table->dropUnique(['stripe_suscripcion_id']);
            $table->dropColumn(['stripe_cliente_id', 'stripe_suscripcion_id', 'suscripcion_estado', 'suscripcion_fin_en']);
        });
    }
};
