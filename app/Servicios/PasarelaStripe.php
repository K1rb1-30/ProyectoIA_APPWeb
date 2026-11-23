<?php

namespace App\Servicios;

use App\Models\ParteCuenta;
use RuntimeException;
use Stripe\Exception\InvalidRequestException;
use Stripe\StripeClient;

/**
 * ÚNICO sitio del proyecto que habla con Stripe (rama 11).
 *
 * ¿Por qué una clase propia en vez de usar Stripe directamente en el controlador?
 *  - En los tests la sustituimos por una versión simulada ($this->mock(...))
 *    y nunca se llama a Stripe de verdad (ni hace falta internet ni claves).
 *  - Devuelve arrays sencillos y no objetos de Stripe: el resto del código no
 *    necesita saber cómo es la librería por dentro.
 *
 * Usa Stripe Checkout: Stripe aloja la página con el formulario de la tarjeta,
 * así que los datos de la tarjeta NUNCA pasan por nuestro servidor.
 */
class PasarelaStripe
{
    /**
     * Crea una sesión de Stripe Checkout para pagar una parte de la cuenta.
     *
     * @param  string  $urlExito  Adonde vuelve Stripe si el pago va bien. Tiene que
     *                            llevar {CHECKOUT_SESSION_ID}: Stripe lo cambia por el id de la sesión.
     * @param  string  $urlCancelar  Adonde vuelve si el cliente pulsa "volver".
     * @return array{id: string, url: string}
     */
    public function crearSesion(ParteCuenta $parte, string $concepto, string $urlExito, string $urlCancelar): array
    {
        $sesion = $this->cliente()->checkout->sessions->create([
            // 'payment' = un pago único (no una suscripción).
            'mode' => 'payment',
            'line_items' => [[
                'quantity' => 1,
                // price_data: el precio se indica aquí mismo, sin crear productos en Stripe.
                'price_data' => [
                    'currency' => 'eur',
                    // Stripe también trabaja en céntimos: 1034 = 10,34 €.
                    'unit_amount' => $parte->importe_centimos,
                    'product_data' => ['name' => $concepto],
                ],
            ]],
            'success_url' => $urlExito,
            'cancel_url' => $urlCancelar,
            // Guardamos qué parte se paga. Al volver lo comprobamos: así una
            // sesión pagada de otra parte no sirve para marcar esta.
            'metadata' => ['parte_cuenta_id' => (string) $parte->id],
            'locale' => 'es',
        ]);

        return ['id' => $sesion->id, 'url' => $sesion->url];
    }

    /**
     * Crea una sesión de Stripe Checkout para pagar un PEDIDO del cliente antes
     * de mandarlo a cocina (rama 15). Igual que crearSesion(), pero el importe
     * es el total del pedido y en los metadatos guardamos la mesa.
     *
     * @return array{id: string, url: string}
     */
    public function crearSesionPedido(int $importeCentimos, int $mesaId, string $concepto, string $urlExito, string $urlCancelar): array
    {
        $sesion = $this->cliente()->checkout->sessions->create([
            'mode' => 'payment',
            'line_items' => [[
                'quantity' => 1,
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => $importeCentimos,
                    'product_data' => ['name' => $concepto],
                ],
            ]],
            'success_url' => $urlExito,
            'cancel_url' => $urlCancelar,
            // Al volver comprobamos que la sesión es de un pedido y de ESTA mesa.
            'metadata' => ['tipo' => 'pedido', 'mesa_id' => (string) $mesaId],
            'locale' => 'es',
        ]);

        return ['id' => $sesion->id, 'url' => $sesion->url];
    }

    /**
     * Pregunta a Stripe cómo está una sesión. Es lo que hace fiable el pago:
     * no basta con que el cliente llegue a la URL de éxito (se puede escribir a mano).
     *
     * "tipo" y "mesa_id" solo vienen en las sesiones de pedidos (rama 15).
     *
     * @return array{pagada: bool, parte_cuenta_id: int|null, importe_centimos: int, moneda: string, tipo: string|null, mesa_id: int|null}
     */
    public function consultarSesion(string $idSesion): array
    {
        try {
            $sesion = $this->cliente()->checkout->sessions->retrieve($idSesion);
        } catch (InvalidRequestException) {
            // El id no existe en Stripe (por ejemplo, alguien lo ha escrito a
            // mano en la URL): lo tratamos como una sesión NO pagada.
            return ['pagada' => false, 'parte_cuenta_id' => null, 'importe_centimos' => 0, 'moneda' => '', 'tipo' => null, 'mesa_id' => null];
        }

        return [
            'pagada' => $sesion->payment_status === 'paid',
            'parte_cuenta_id' => isset($sesion->metadata['parte_cuenta_id']) ? (int) $sesion->metadata['parte_cuenta_id'] : null,
            'importe_centimos' => (int) $sesion->amount_total,
            'moneda' => (string) $sesion->currency,
            'tipo' => $sesion->metadata['tipo'] ?? null,
            'mesa_id' => isset($sesion->metadata['mesa_id']) ? (int) $sesion->metadata['mesa_id'] : null,
        ];
    }

    /**
     * Cliente de Stripe con la clave secreta del .env (vía config/services.php).
     *
     * Seguro extra: si la clave no es de MODO TEST (sk_test_...), no seguimos.
     * Así, aunque alguien pegue por error una clave live, nunca se cobra de verdad.
     */
    private function cliente(): StripeClient
    {
        $secreta = (string) config('services.stripe.secret');

        if (! str_starts_with($secreta, 'sk_test_')) {
            throw new RuntimeException('Falta STRIPE_SECRET en el .env o no es una clave de modo test (sk_test_...).');
        }

        return new StripeClient($secreta);
    }
}
