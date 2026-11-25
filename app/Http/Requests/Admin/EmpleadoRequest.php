<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Validator;

/**
 * Valida el formulario de crear y editar un empleado (rama 18).
 *
 * Laravel ejecuta estas reglas antes de llegar al controlador. Si algo falla,
 * vuelve al formulario con los errores en castellano y con lo que se había
 * escrito (menos las contraseñas, que nunca se devuelven).
 *
 * Ojo: aquí NO hay regla para restaurante_id. Solo se guardan los campos que
 * se validan, así que aunque alguien lo añada al formulario a mano, se ignora.
 *
 * Según el rol se piden datos distintos:
 *  - admin:    correo y contraseña (inicia sesión en /login).
 *  - camarero: solo un PIN de 4 cifras (entra en un dispositivo emparejado).
 * exclude_unless quita del resultado los campos que no tocan a ese rol: un
 * camarero nunca guarda correo ni contraseña aunque se envíen a mano.
 * La cocina ya no tiene usuarios: son dispositivos («Dispositivos»).
 */
class EmpleadoRequest extends FormRequest
{
    /** Roles que el admin puede dar. */
    public const ROLES = ['admin', 'camarero'];

    // El acceso ya lo controla el middleware rol:admin de la ruta.
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Antes de validar: el correo sin espacios y en minúsculas, para que
     * "Ana@Bar.test " y "ana@bar.test" cuenten como el mismo.
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
            'name' => ['required', 'string', 'max:255'],

            // Único en TODA la tabla users, no solo en este restaurante: el
            // correo es con lo que se inicia sesión. ignore() deja guardar a
            // un empleado sin cambiarle el correo.
            'email' => [
                'exclude_unless:rol,admin',
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->empleado()),
            ],

            'rol' => ['required', Rule::in(self::ROLES)],

            // Solo admin. Obligatoria si es nuevo o todavía no tiene (por
            // ejemplo, un camarero que pasa a admin); si no, vacía = no se cambia.
            // confirmed: tiene que coincidir con "password_confirmation".
            // Password::defaults(): como mínimo 8 caracteres.
            'password' => [
                'exclude_unless:rol,admin',
                $this->empleado()?->password ? 'nullable' : 'required',
                'confirmed',
                Password::defaults(),
            ],

            // Solo camarero. 4 cifras exactas (digits:4 admite "0042").
            // Obligatorio si es nuevo o todavía no tiene; si no, vacío = no se cambia.
            'pin' => [
                'exclude_unless:rol,camarero',
                $this->empleado()?->pin ? 'nullable' : 'required',
                'digits:4',
                'confirmed',
            ],
        ];
    }

    /**
     * Comprobaciones que dependen de otros datos, después de las reglas.
     * Cada closure recibe el validador y puede añadir errores.
     */
    public function after(): array
    {
        return [
            function (Validator $validador) {
                $empleado = $this->empleado();

                // Solo importa al editar a alguien que ahora es admin y deja de serlo.
                if (! $empleado || $empleado->rol !== 'admin' || $this->input('rol') === 'admin') {
                    return;
                }

                // 1) El admin no puede quitarse su propio rol.
                if ($empleado->is($this->user())) {
                    $validador->errors()->add('rol', 'No puedes quitarte tu propio rol de administrador.');

                    return;
                }

                // 2) El restaurante no se puede quedar sin ningún admin.
                if (! self::quedaOtroAdmin($empleado)) {
                    $validador->errors()->add('rol', 'El restaurante tiene que tener al menos un administrador.');
                }
            },
        ];
    }

    /**
     * ¿Hay en el restaurante de $empleado algún admin además de él?
     * La usan también el borrado del controlador.
     *
     * Las cuentas del equipo y del profesor (delPersonal) no cuentan: el
     * restaurante tiene que tener siempre un admin propio.
     */
    public static function quedaOtroAdmin(User $empleado): bool
    {
        return User::delPersonal()->where('restaurante_id', $empleado->restaurante_id)
            ->where('rol', 'admin')
            ->whereKeyNot($empleado->id)
            ->exists();
    }

    /**
     * Nombres de los campos en los mensajes de error ("El campo rol...").
     */
    public function attributes(): array
    {
        return [
            'name' => 'nombre',
            'email' => 'correo electrónico',
            'password' => 'contraseña',
            'pin' => 'PIN',
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'Ya existe una cuenta con ese correo. Cada persona necesita un correo distinto, porque es con el que inicia sesión.',
            'rol.in' => 'Elige un rol: administración o camarero.',
            'password.confirmed' => 'Las dos contraseñas no coinciden.',
            'pin.digits' => 'El PIN tiene que tener 4 cifras.',
            'pin.confirmed' => 'Los dos PIN no coinciden.',
        ];
    }

    /**
     * El empleado que se está editando (ya filtrado por restaurante en
     * routes/web.php), o null si se está creando uno nuevo.
     */
    public function empleado(): ?User
    {
        return $this->route('empleado');
    }
}
