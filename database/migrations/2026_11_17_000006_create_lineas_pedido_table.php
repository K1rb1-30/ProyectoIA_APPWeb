<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Líneas de un pedido: qué plato, cuántos, a qué precio y con qué notas.
// No lleva restaurante_id: pertenece al restaurante de su pedido.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lineas_pedido', function (Blueprint $table) {
            $table->id();
            // Si se borra el pedido, se borran sus líneas.
            $table->foreignId('pedido_id')->constrained('pedidos')->cascadeOnDelete();
            // No se puede borrar un plato que ya se ha pedido alguna vez.
            $table->foreignId('plato_id')->constrained('platos')->restrictOnDelete();
            $table->unsignedSmallInteger('cantidad');
            // Precio UNITARIO del plato en el momento del pedido, en céntimos.
            // Así, si luego cambia la carta, el pedido no cambia.
            $table->unsignedInteger('precio_centimos');
            // Indicaciones para cocina: "sin cebolla", "muy hecho"...
            $table->string('notas', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lineas_pedido');
    }
};
