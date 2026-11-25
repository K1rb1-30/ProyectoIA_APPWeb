<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Las partes de un pedido pagado al hacerlo (rama 15) no son una persona de la
// mesa: no tienen número de persona (null). Antes ocupaban la «persona 1» y, al
// dividir el resto entre 2, salían «Persona 2» y «Persona 3».
//
// El unique (cuenta_id, persona) se queda: MySQL deja varios null en un
// unique, así que puede haber varios pedidos pagados al pedir en la misma
// cuenta y sigue sin poder haber dos «persona 2».
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('partes_cuenta', function (Blueprint $table) {
            $table->unsignedTinyInteger('persona')->nullable()->change();
        });

        // Las partes de pedidos pagados al pedir que ya existían se quedan sin número.
        DB::table('partes_cuenta')->whereNotNull('pedido_id')->update(['persona' => null]);
    }

    public function down(): void
    {
        // Para volver a "persona obligatoria", cada parte sin número recibe el
        // siguiente libre de su cuenta (como se hacía antes).
        $sinNumero = DB::table('partes_cuenta')->whereNull('persona')->orderBy('id')->get(['id', 'cuenta_id']);

        foreach ($sinNumero as $parte) {
            $siguiente = (int) DB::table('partes_cuenta')->where('cuenta_id', $parte->cuenta_id)->max('persona') + 1;
            DB::table('partes_cuenta')->where('id', $parte->id)->update(['persona' => $siguiente]);
        }

        Schema::table('partes_cuenta', function (Blueprint $table) {
            $table->unsignedTinyInteger('persona')->nullable(false)->change();
        });
    }
};
