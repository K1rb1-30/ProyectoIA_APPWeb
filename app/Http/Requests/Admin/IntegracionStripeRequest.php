<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Valida las claves de Stripe que pega el admin en «Integraciones» (rama 26).
 *
 * Solo comprueba el FORMATO. Que la clave funcione de verdad lo pregunta
 * después el controlador a Stripe (PasarelaStripe::comprobarClave()).
 *
 * Seguridad: los mensajes de error nunca repiten la clave, y la clave
 * secreta no se devuelve al formulario si algo falla (ver "dontFlash" en
 * bootstrap/app.php).
 */
class IntegracionStripeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // "bail": en cuanto falla una regla, no mira las demás. Así, con
            // una clave _live_ sale solo el mensaje de «modo real».
            'clave_publicable' => [
                'bail', 'required', 'string', 'max:255',
                'not_regex:/_live_/',
                'starts_with:pk_test_',
                'regex:/^[A-Za-z0-9_]+$/',
            ],
            'clave_secreta' => [
                'bail', 'required', 'string', 'max:255',
                'not_regex:/_live_/',
                // sk_test_ (normal) o rk_test_ (restringida, la recomendada).
                'starts_with:sk_test_,rk_test_',
                'regex:/^[A-Za-z0-9_]+$/',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'clave_publicable.required' => 'Pega la clave publicable.',
            'clave_publicable.not_regex' => 'Esa es una clave de modo real (live). MesaYa solo acepta claves de modo de prueba (pk_test_...).',
            'clave_publicable.starts_with' => 'La clave publicable empieza por pk_test_.',
            'clave_publicable.regex' => 'La clave publicable solo lleva letras, números y guiones bajos. Revisa que la has copiado entera.',
            'clave_secreta.required' => 'Pega la clave secreta.',
            'clave_secreta.not_regex' => 'Esa es una clave de modo real (live). MesaYa solo acepta claves de modo de prueba (sk_test_... o rk_test_...).',
            'clave_secreta.starts_with' => 'La clave secreta empieza por sk_test_ o, si es restringida, por rk_test_.',
            'clave_secreta.regex' => 'La clave secreta solo lleva letras, números y guiones bajos. Revisa que la has copiado entera.',
            '*.max' => 'La clave es demasiado larga. Revisa que la has copiado bien.',
        ];
    }
}
