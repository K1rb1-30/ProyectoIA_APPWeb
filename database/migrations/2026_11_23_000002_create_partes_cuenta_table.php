<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Parte de una cuenta: lo que paga cada persona (rama 10).
// No lleva restaurante_id: queda aislada a través de su cuenta.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('partes_cuenta', function (Blueprint $table) {
            $table->id();
            // Si se borra la cuenta, se borran sus partes.
            $table->foreignId('cuenta_id')->constrained('cuentas')->cascadeOnDelete();
            // Número de la persona en la mesa: 1, 2, 3...
            $table->unsignedTinyInteger('persona');
            $table->unsignedInteger('importe_centimos');
            // Vacío hasta que la persona elige cómo pagar (ramas 11 y 12).
            $table->enum('metodo', ['tarjeta', 'efectivo'])->nullable();
            // pendiente → pagado (tarjeta) · pendiente → pendiente_efectivo → pagado (efectivo).
            $table->enum('estado', ['pendiente', 'pendiente_efectivo', 'pagado'])->default('pendiente');
            $table->timestamps();

            // En una cuenta no puede haber dos "persona 2".
            $table->unique(['cuenta_id', 'persona']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partes_cuenta');
    }
};
