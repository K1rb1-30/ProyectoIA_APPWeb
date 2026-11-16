<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Tabla pivote: qué alérgenos tiene cada plato (relación muchos a muchos).
// El nombre alergeno_plato es el que Laravel espera por defecto: los dos
// modelos en singular y en orden alfabético.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alergeno_plato', function (Blueprint $table) {
            $table->foreignId('alergeno_id')->constrained('alergenos')->cascadeOnDelete();
            // Si se borra el plato, se borran sus filas de esta tabla.
            $table->foreignId('plato_id')->constrained('platos')->cascadeOnDelete();

            // Clave primaria doble: un plato no puede tener el mismo alérgeno dos veces.
            $table->primary(['alergeno_id', 'plato_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alergeno_plato');
    }
};
