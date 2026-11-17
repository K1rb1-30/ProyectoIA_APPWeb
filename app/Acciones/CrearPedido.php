<?php

namespace App\Acciones;

use App\Models\Mesa;
use App\Models\Pedido;
use App\Models\Plato;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Crea un pedido con sus líneas. Es el ÚNICO sitio de la aplicación donde se
 * crean pedidos, para que todos sigan las mismas reglas:
 *  - comanda del camarero (rama 5): se llama con el camarero logueado;
 *  - autopedido del cliente desde el QR (rama 9): se llama con $usuario = null.
 *
 * Uso:
 *   $pedido = (new CrearPedido)->ejecutar($mesa, [
 *       ['plato_id' => 3, 'cantidad' => 2, 'notas' => 'Sin cebolla'],
 *       ['plato_id' => 7, 'cantidad' => 1],
 *   ], $request->user());
 */
class CrearPedido
{
    /**
     * @param  Mesa  $mesa  Mesa que pide. De ella sale el restaurante.
     * @param  array<int, array{plato_id: int, cantidad: int, notas?: ?string}>  $lineas
     * @param  User|null  $usuario  Camarero que toma la comanda, o null si pide el cliente.
     *
     * @throws ValidationException si algún plato no se puede pedir.
     */
    public function ejecutar(Mesa $mesa, array $lineas, ?User $usuario = null): Pedido
    {
        if ($lineas === []) {
            throw ValidationException::withMessages([
                'lineas' => 'Añade al menos un plato a la comanda.',
            ]);
        }

        // 1. Buscamos los platos pedidos, filtrando A MANO por el restaurante
        //    de la mesa y por disponible. No nos fiamos del global scope del
        //    trait: cuando pide el cliente (rama 9) no hay nadie logueado y el
        //    scope no filtra nada. withoutGlobalScope evita que se aplique dos
        //    veces cuando sí hay alguien logueado.
        $idsPlatos = array_unique(array_map(fn ($linea) => (int) $linea['plato_id'], $lineas));

        $platos = Plato::withoutGlobalScope('restaurante')
            ->where('restaurante_id', $mesa->restaurante_id)
            ->where('disponible', true)
            ->whereIn('id', $idsPlatos)
            ->get()
            ->keyBy('id'); // Colección indexada por id: $platos[3] es el plato 3.

        // Si falta alguno, es que no existe, es de otro restaurante o está
        // agotado. No se guarda nada y se avisa con un error de validación.
        if ($platos->count() !== count($idsPlatos)) {
            throw ValidationException::withMessages([
                'lineas' => 'Algún plato de la comanda no está disponible. Revisa la comanda.',
            ]);
        }

        // 2. Creamos pedido y líneas en una transacción: o se guarda todo, o
        //    nada. Si falla una línea a medias, no queda un pedido cojo.
        return DB::transaction(function () use ($mesa, $lineas, $usuario, $platos) {
            $pedido = Pedido::create([
                // Explícito (no lo deja al trait): en la rama 9 no hay usuario.
                'restaurante_id' => $mesa->restaurante_id,
                'mesa_id' => $mesa->id,
                'user_id' => $usuario?->id,
                'estado' => 'pendiente',
            ]);

            foreach ($lineas as $linea) {
                $plato = $platos[(int) $linea['plato_id']];

                $pedido->lineas()->create([
                    'plato_id' => $plato->id,
                    'cantidad' => (int) $linea['cantidad'],
                    // Precio del momento, copiado del plato. Si mañana sube el
                    // precio en la carta, este pedido no cambia.
                    'precio_centimos' => $plato->precio_centimos,
                    // trim quita espacios; si queda vacío, guardamos null.
                    'notas' => trim($linea['notas'] ?? '') ?: null,
                ]);
            }

            // 3. La mesa pasa a ocupada si estaba libre.
            if ($mesa->estado === 'libre') {
                $mesa->update(['estado' => 'ocupada']);
            }

            return $pedido;
        });
    }
}
