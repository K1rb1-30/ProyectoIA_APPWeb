<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Añade a la tabla users de Laravel el rol del trabajador y el restaurante
// al que pertenece. Va después de la migración de restaurantes porque la
// clave foránea necesita que esa tabla ya exista.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Solo se admiten estos tres valores; MySQL rechaza cualquier otro.
            $table->enum('rol', ['admin', 'camarero', 'cocina'])->after('password');

            // Todo el personal pertenece a un restaurante. Si se borra el
            // restaurante, se borran también sus usuarios.
            $table->foreignId('restaurante_id')
                ->after('rol')
                ->constrained('restaurantes')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('restaurante_id');
            $table->dropColumn('rol');
        });
    }
};
