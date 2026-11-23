<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Rama 15 (pagar al pedir): cuando el cliente paga su pedido al hacerlo, en la
// cuenta de la mesa se añade una parte YA PAGADA por ese pedido. Esta columna
// apunta a qué pedido corresponde, para mostrarla como «Pedido #12 · pagado al
// pedir» en vez de «Persona 3». En las partes normales queda vacía.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('partes_cuenta', function (Blueprint $table) {
            $table->foreignId('pedido_id')->nullable()->after('cuenta_id')
                ->constrained('pedidos')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('partes_cuenta', function (Blueprint $table) {
            $table->dropConstrainedForeignId('pedido_id');
        });
    }
};
