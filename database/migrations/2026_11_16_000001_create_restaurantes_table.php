<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Crea la tabla de restaurantes. Cada restaurante es un "cliente" del SaaS:
// todas las tablas de negocio apuntarán aquí con su restaurante_id.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('restaurantes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            // El slug sale en la URL del QR (/r/{slug}/mesa/{numero}), por eso es único.
            $table->string('slug')->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('restaurantes');
    }
};
