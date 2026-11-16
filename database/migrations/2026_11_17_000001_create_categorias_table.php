<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Categorías de la carta (Tapas, Raciones, Bebidas...). Cada restaurante tiene las suyas.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categorias', function (Blueprint $table) {
            $table->id();
            // Si se borra el restaurante, se borran sus categorías.
            $table->foreignId('restaurante_id')->constrained('restaurantes')->cascadeOnDelete();
            $table->string('nombre');
            // Posición en la carta: primero las de número más bajo.
            $table->unsignedInteger('orden')->default(0);
            $table->timestamps();

            // Un restaurante no puede tener dos categorías con el mismo nombre.
            $table->unique(['restaurante_id', 'nombre']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categorias');
    }
};
