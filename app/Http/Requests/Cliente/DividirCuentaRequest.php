<?php

namespace App\Http\Requests\Cliente;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Valida el formulario de dividir la cuenta:
 *  - modo=iguales&personas=3
 *  - modo=platos&personas=3&asignaciones[12]=1&asignaciones[13]=0   (idLinea => persona)
 *
 * En "asignaciones", el 0 significa "Todos": el plato es compartido y se
 * reparte entre todas las personas (rama 14).
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
            // Entre 1 y 20 personas por mesa. También hace falta por platos:
            // es el número de personas entre las que se reparte lo compartido.
            'personas' => ['required', 'integer', 'min:1', 'max:20'],
            'asignaciones' => ['required_if:modo,platos', 'nullable', 'array'],
            // 0 = "Todos" (compartido). Nadie puede ser la persona 5 si hay 3:
            // lte:personas compara con el número de personas enviado.
            'asignaciones.*' => ['integer', 'min:0', 'lte:personas'],
        ];
    }

    public function messages(): array
    {
        return [
            'asignaciones.*.lte' => 'Hay un plato asignado a una persona que no existe. Revisa el número de personas.',
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
