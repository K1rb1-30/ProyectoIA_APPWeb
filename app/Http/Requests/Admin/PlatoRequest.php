<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/**
 * Valida el formulario de crear y editar un plato.
 *
 * Laravel ejecuta estas reglas antes de llegar al controlador. Si algo falla,
 * vuelve al formulario con los errores (en castellano, por lang/es/) y con lo
 * que se había escrito.
 */
class PlatoRequest extends FormRequest
{
    // El acceso ya lo controla el middleware rol:admin de la ruta.
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:120'],
            'descripcion' => ['nullable', 'string', 'max:500'],

            // La categoría tiene que existir Y ser del restaurante del admin.
            // Sin el where, alguien podría enviar el id de una categoría de
            // otro restaurante cambiando el formulario a mano.
            'categoria_id' => [
                'required',
                Rule::exists('categorias', 'id')->where('restaurante_id', $this->user()->restaurante_id),
            ],

            // El precio se escribe como en la carta: 8,50 o 8.50 o 8.
            // Hasta 4 cifras de euros y 2 de céntimos. Luego lo pasamos a céntimos.
            'precio' => ['required', 'regex:/^\d{1,4}([.,]\d{1,2})?$/'],

            // Foto opcional: solo imágenes JPG, PNG o WebP de 5 MB como mucho.
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'quitar_foto' => ['nullable', 'boolean'],

            // Lista de ids de alérgenos marcados (puede venir vacía).
            'alergenos' => ['nullable', 'array'],
            'alergenos.*' => ['integer', 'exists:alergenos,id'],

            'disponible' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Nombres de los campos en los mensajes de error ("El campo precio...").
     */
    /**
     * Comprobaciones que se hacen después de las reglas: el formato del
     * precio ya está bien, pero no puede ser 0 (0, 0,00...). Un plato gratis
     * no se puede cobrar con Stripe y casi siempre es un error al escribirlo.
     */
    public function after(): array
    {
        return [
            function (Validator $validador) {
                if (! $validador->errors()->has('precio') && $this->precioEnCentimos() === 0) {
                    $validador->errors()->add('precio', 'El precio tiene que ser mayor que 0.');
                }
            },
        ];
    }

    public function attributes(): array
    {
        return [
            'categoria_id' => 'categoría',
            'descripcion' => 'descripción',
            'alergenos.*' => 'alérgeno',
        ];
    }

    public function messages(): array
    {
        return [
            'precio.regex' => 'El precio tiene que ser un importe como 8,50.',
        ];
    }

    /**
     * Convierte el precio escrito ("8,50") a céntimos (850).
     *
     * Lo hacemos con texto y no con decimales (float) para no arrastrar
     * errores de redondeo: separamos euros y céntimos y los juntamos.
     */
    public function precioEnCentimos(): int
    {
        $partes = preg_split('/[.,]/', $this->input('precio'));
        $euros = (int) $partes[0];
        // "8,5" son 50 céntimos, no 5: rellenamos con ceros por la derecha.
        $centimos = isset($partes[1]) ? (int) str_pad($partes[1], 2, '0') : 0;

        return $euros * 100 + $centimos;
    }
}
