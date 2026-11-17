<?php

namespace App\Http\Requests\Camarero;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Valida la forma de la comanda que envía el camarero:
 *   lineas[5][plato_id] = 5, lineas[5][cantidad] = 2, lineas[5][notas] = "Sin cebolla"
 *
 * Aquí solo se comprueba que los datos tengan buena pinta (números, longitudes).
 * Que el plato sea del restaurante y esté disponible lo comprueba la clase
 * CrearPedido, porque esa regla también hace falta en el autopedido (rama 9).
 */
class ComandaRequest extends FormRequest
{
    // El acceso ya lo controla el middleware rol:camarero de la ruta.
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Al menos un plato. Los platos con cantidad 0 no se envían.
            'lineas' => ['required', 'array', 'min:1'],
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
            'lineas.required' => 'Añade al menos un plato a la comanda.',
            'lineas.min' => 'Añade al menos un plato a la comanda.',
        ];
    }
}
