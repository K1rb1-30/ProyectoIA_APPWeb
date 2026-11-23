<?php

namespace App\Http\Requests\Cliente;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Valida el formulario de dividir la cuenta:
 *  - modo=iguales&personas=3
 *  - modo=platos&asignaciones[12]=1&asignaciones[13]=2   (idLinea => persona)
 *
 * Que cada línea sea de esta mesa y esté asignada lo comprueba DividirCuenta.
 */
class DividirCuentaRequest extends FormRequest
{
    // Pantalla pública: puede usarla cualquiera que tenga el QR de la mesa.
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'modo' => ['required', 'in:iguales,platos'],
            // Entre 1 y 20 personas por mesa.
            'personas' => ['required_if:modo,iguales', 'nullable', 'integer', 'min:1', 'max:20'],
            'asignaciones' => ['required_if:modo,platos', 'nullable', 'array'],
            'asignaciones.*' => ['integer', 'min:1', 'max:20'],
        ];
    }

    public function attributes(): array
    {
        return [
            'personas' => 'número de personas',
            'asignaciones' => 'reparto por platos',
            'asignaciones.*' => 'persona',
        ];
    }
}
