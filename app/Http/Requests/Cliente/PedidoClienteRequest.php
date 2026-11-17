<?php

namespace App\Http\Requests\Cliente;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Valida el pedido que envía el cliente desde "Mi pedido":
 *   lineas[0][plato_id] = 5, lineas[0][cantidad] = 2, lineas[0][notas] = "Sin cebolla"
 *
 * Son las mismas reglas que la comanda del camarero (ComandaRequest). Aquí
 * solo se mira que los datos tengan buena pinta; que el plato sea de este
 * restaurante y esté disponible lo comprueba CrearPedido.
 */
class PedidoClienteRequest extends FormRequest
{
    // Es una pantalla pública: puede enviarlo cualquiera que tenga el QR.
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'lineas' => ['required', 'array', 'min:1', 'max:50'],
            // distinct: el mismo plato no puede venir dos veces.
            'lineas.*.plato_id' => ['required', 'integer', 'distinct'],
            'lineas.*.cantidad' => ['required', 'integer', 'min:1', 'max:99'],
            'lineas.*.notas' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function attributes(): array
    {
        return [
            'lineas.*.plato_id' => 'plato',
            'lineas.*.cantidad' => 'cantidad',
            'lineas.*.notas' => 'notas',
        ];
    }

    public function messages(): array
    {
        return [
            'lineas.required' => 'Tu pedido está vacío: añade algún plato de la carta.',
            'lineas.min' => 'Tu pedido está vacío: añade algún plato de la carta.',
        ];
    }
}
