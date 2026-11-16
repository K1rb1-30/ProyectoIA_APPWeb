<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Valida el formulario de crear y editar una categoría.
 */
class CategoriaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Al editar, la ruta trae la categoría; al crear, es null.
        $categoria = $this->route('categoria');

        return [
            'nombre' => [
                'required',
                'string',
                'max:60',
                // No puede repetirse dentro del mismo restaurante (sí en otro).
                // Al editar, ignoramos la propia categoría para que se pueda
                // guardar sin cambiar el nombre.
                Rule::unique('categorias', 'nombre')
                    ->where('restaurante_id', $this->user()->restaurante_id)
                    ->ignore($categoria?->id),
            ],
            'orden' => ['required', 'integer', 'min:0', 'max:999'],
        ];
    }
}
