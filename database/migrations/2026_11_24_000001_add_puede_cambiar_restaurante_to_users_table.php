<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Marca a los usuarios que pueden cambiar de restaurante desde el panel
// (cuentas del equipo y del profesor, para la demo). Por defecto, nadie:
// un admin normal solo ve y toca su restaurante.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('puede_cambiar_restaurante')->default(false)->after('restaurante_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('puede_cambiar_restaurante');
        });
    }
};
