<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Platos de la carta de cada restaurante.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('platos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurante_id')->constrained('restaurantes')->cascadeOnDelete();
            // restrictOnDelete: MySQL no deja borrar una categoría que tenga platos.
            $table->foreignId('categoria_id')->constrained('categorias')->restrictOnDelete();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            // Dinero SIEMPRE en céntimos y entero: 1250 = 12,50 €. Nunca float.
            $table->unsignedInteger('precio_centimos');
            // Ruta de la foto dentro del disco "public" (por ejemplo platos/abc.jpg).
            $table->string('foto')->nullable();
            // Si es false, el plato está agotado y no sale en la carta del QR.
            $table->boolean('disponible')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('platos');
    }
};
