<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Pedidos (comandas). Los crea el camarero (rama 5) o el cliente desde el QR
// (rama 9). La cocina los va pasando de estado (rama 6).
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurante_id')->constrained('restaurantes')->cascadeOnDelete();
            // restrictOnDelete: MySQL no deja borrar una mesa que tenga pedidos,
            // para no perder el historial (lo necesitan cuentas y estadísticas).
            $table->foreignId('mesa_id')->constrained('mesas')->restrictOnDelete();
            // Quién tomó la comanda. Nulo si el pedido lo hace el cliente desde
            // el QR, o si el camarero se borra más adelante (nullOnDelete).
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            // Orden obligatorio: pendiente → preparando → listo → servido.
            $table->enum('estado', ['pendiente', 'preparando', 'listo', 'servido'])->default('pendiente');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};
