<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Crea la tabla de mesas. Cada mesa pertenece a un restaurante y tiene un
// número que se imprime en su QR (/r/{slug}/mesa/{numero}).
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mesas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurante_id')->constrained('restaurantes')->cascadeOnDelete();
            $table->unsignedSmallInteger('numero');
            // La mesa empieza libre. La ocupan y la liberan el camarero y el
            // cierre de la cuenta (ramas 5 y 12).
            $table->enum('estado', ['libre', 'ocupada'])->default('libre');
            $table->timestamps();

            // El número no se repite dentro de un restaurante, pero dos
            // restaurantes distintos sí pueden tener la "mesa 5".
            $table->unique(['restaurante_id', 'numero']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mesas');
    }
};
