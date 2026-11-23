<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Cuenta de una mesa (rama 10). Junta los pedidos de la mesa y se divide en
// partes (tabla partes_cuenta), una por persona.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cuentas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurante_id')->constrained('restaurantes')->cascadeOnDelete();
            // No se puede borrar una mesa que tenga cuentas.
            $table->foreignId('mesa_id')->constrained('mesas')->restrictOnDelete();
            // Dinero SIEMPRE en céntimos y entero.
            $table->unsignedInteger('total_centimos');
            // abierta: se está pagando. cerrada: todo pagado y mesa libre (rama 12).
            $table->enum('estado', ['abierta', 'cerrada'])->default('abierta');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cuentas');
    }
};
