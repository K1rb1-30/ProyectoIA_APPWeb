<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Los 14 alérgenos de declaración obligatoria (Reglamento UE 1169/2011).
// Son los mismos para todos los restaurantes, por eso esta tabla NO lleva
// restaurante_id. Se rellena con AlergenosSeeder.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alergenos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            // Nombre de un icono de Material Symbols (por ejemplo "egg").
            $table->string('icono');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alergenos');
    }
};
