<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Apunta en cada pedido en qué cuenta se ha cobrado (rama 10).
// Vacío hasta que se divide la cuenta. Así, cuando la mesa se cierra y
// llegan clientes nuevos, sus pedidos no se mezclan con los de antes.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            // nullOnDelete: si se borrara la cuenta, el pedido vuelve a quedar sin cuenta.
            $table->foreignId('cuenta_id')->nullable()->after('user_id')
                ->constrained('cuentas')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropConstrainedForeignId('cuenta_id');
        });
    }
};
