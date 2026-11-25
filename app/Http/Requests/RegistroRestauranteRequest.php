<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

/**
 * Valida el alta de un restaurante nuevo (rama 24): nombre del restaurante,
 * nombre del administrador, correo y contraseña con confirmación.
 *
 * Si algo falla, Laravel vuelve al formulario con los errores en castellano
 * y lo escrito (menos las contraseñas).
 */
class RegistroRestauranteRequest extends FormRequest
{
    // Es una página pública: puede enviarla cualquiera sin sesión.
    public function authorize(): bool
    {
        return true;
    }

    /**
     * El correo sin espacios y en minúsculas (igual que en Personal, rama 18),
     * para que "Ana@Bar.test " y "ana@bar.test" cuenten como el mismo.
     */
    protected function prepareForValidation(): void
    {
        if (is_string($this->input('email'))) {
            $this->merge(['email' => mb_strtolower(trim($this->input('email')))]);
        }
    }

    public function rules(): array
    {
        return [
            'nombre_restaurante' => ['required', 'string', 'max:100'],
            'name' => ['required', 'string', 'max:255'],
            // Único en toda la tabla users: es con lo que se inicia sesión.
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            // confirmed: tiene que coincidir con "password_confirmation".
            // Password::defaults(): como mínimo 8 caracteres.
            'password' => ['required', 'confirmed', Password::defaults()],
        ];
    }

    /**
     * Nombres de los campos en los mensajes de error («El campo nombre del
     * restaurante es obligatorio»). Los demás ya están en lang/es/validation.php.
     */
    public function attributes(): array
    {
        return [
            'nombre_restaurante' => 'nombre del restaurante',
            'name' => 'tu nombre',
        ];
    }
}
