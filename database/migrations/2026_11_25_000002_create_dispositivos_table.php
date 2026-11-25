<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Dispositivos del bar (tablets y móviles) emparejados por el admin.
//
// Cómo se empareja uno:
//  1. El admin pulsa «Emparejar dispositivo», elige cocina o camarero y se
//     guarda una fila con un código de 6 caracteres (solo su hash) que caduca
//     a los 10 minutos.
//  2. En el dispositivo se escribe el código en /dispositivo. Si es válido, se
//     borra el código, se guarda el hash de un token aleatorio y el token va a
//     una cookie del dispositivo. Desde entonces, esa cookie es su "llave".
//
// Nunca se guardan ni el código ni el token tal cual: solo su hash (sha256).
// Así, aunque alguien viera la base de datos, no podría emparejar ni entrar.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dispositivos', function (Blueprint $table) {
            $table->id();
            // Si se borra el restaurante, se borran sus dispositivos.
            $table->foreignId('restaurante_id')->constrained('restaurantes')->cascadeOnDelete();
            // cocina: entra directo al tablero · camarero: cada camarero entra con su PIN.
            $table->enum('tipo', ['cocina', 'camarero']);
            // Para que el admin los distinga: «Tablet de la barra», «Cocina 1»...
            $table->string('nombre', 60);

            // Mientras se empareja: hash del código y hasta cuándo vale.
            $table->string('codigo_hash', 64)->nullable()->unique();
            $table->timestamp('codigo_caduca_en')->nullable();

            // Ya emparejado: hash del token que lleva la cookie del dispositivo.
            $table->string('token_hash', 64)->nullable()->unique();
            $table->timestamp('emparejado_en')->nullable();
            $table->timestamp('ultimo_uso_en')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dispositivos');
    }
};
