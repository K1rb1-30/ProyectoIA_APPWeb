<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Pago de una parte de la cuenta (rama 11: tarjeta con Stripe; rama 12: efectivo).
// No lleva restaurante_id: queda aislado a través de parte → cuenta.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            // restrictOnDelete: no se puede borrar una parte que tenga pagos,
            // así un pago cobrado nunca desaparece "por arrastre".
            $table->foreignId('parte_cuenta_id')->constrained('partes_cuenta')->restrictOnDelete();
            // Dinero SIEMPRE en céntimos y entero.
            $table->unsignedInteger('importe_centimos');
            $table->enum('metodo', ['tarjeta', 'efectivo']);
            // Id de la sesión de Stripe Checkout (cs_test_...). Vacío en efectivo.
            // unique: la misma sesión de Stripe no puede registrarse dos veces.
            // MySQL permite varios NULL en un unique, así que no molesta al efectivo.
            $table->string('referencia_stripe')->nullable()->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};
