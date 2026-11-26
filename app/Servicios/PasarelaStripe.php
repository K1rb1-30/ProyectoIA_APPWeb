<?php

namespace App\Servicios;

use App\Models\ParteCuenta;
use App\Models\Restaurante;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;
use RuntimeException;
use Stripe\Exception\ApiErrorException;
use Stripe\Exception\InvalidRequestException;
use Stripe\Exception\SignatureVerificationException;
use Stripe\StripeClient;
use Stripe\Webhook;

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
 *
 * Rama 26: hay DOS cuentas de Stripe y nunca se mezclan.
 *  - Cobros a clientes (crearSesion, crearSesionPedido, consultarSesion):
 *    con la clave del RESTAURANTE de la mesa → clienteRestaurante().
 *    El dinero va a la cuenta de Stripe del bar.
 *  - Suscripción a MesaYa, portal y webhook: con la clave de la PLATAFORMA
 *    (STRIPE_SECRET del .env) → clientePlataforma().
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
     *
     * @throws StripeNoConfigurado  el restaurante no tiene sus claves de Stripe
     */
    public function crearSesion(Restaurante $restaurante, ParteCuenta $parte, string $concepto, string $urlExito, string $urlCancelar): array
    {
        $sesion = $this->clienteRestaurante($restaurante)->checkout->sessions->create([
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
     *
     * @throws StripeNoConfigurado  el restaurante no tiene sus claves de Stripe
     */
    public function crearSesionPedido(Restaurante $restaurante, int $importeCentimos, int $mesaId, string $concepto, string $urlExito, string $urlCancelar): array
    {
        $sesion = $this->clienteRestaurante($restaurante)->checkout->sessions->create([
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
     * Rama 26: se pregunta con la clave del MISMO restaurante que creó el
     * pago (el de la mesa de la URL). Una sesión creada con la cuenta de otro
     * restaurante no existe en esta cuenta → Stripe da error → "no pagada".
     *
     * @return array{pagada: bool, parte_cuenta_id: int|null, importe_centimos: int, moneda: string, tipo: string|null, mesa_id: int|null}
     *
     * @throws StripeNoConfigurado  el restaurante no tiene sus claves de Stripe
     */
    public function consultarSesion(Restaurante $restaurante, string $idSesion): array
    {
        try {
            $sesion = $this->clienteRestaurante($restaurante)->checkout->sessions->retrieve($idSesion);
        } catch (InvalidRequestException) {
            // El id no existe en esta cuenta de Stripe (escrito a mano en la
            // URL o de otro restaurante): lo tratamos como una sesión NO pagada.
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
     * Crea una sesión de Stripe Checkout para la SUSCRIPCIÓN MENSUAL de un
     * restaurante (rama 24).
     *
     * Diferencias con crearSesion():
     *  - mode 'subscription': Stripe cobra ahora y luego cada mes él solo.
     *  - price_data lleva 'recurring' (cada cuánto se repite el cobro).
     *  - El importe sale de config (STRIPE_SUSCRIPCION_PRECIO_CENTIMOS), no del código.
     *  - Si el restaurante ya fue cliente (reactivar), se reutiliza su cliente
     *    de Stripe en vez de crear otro.
     *
     * @return array{id: string, url: string}
     */
    public function crearSesionSuscripcion(Restaurante $restaurante, string $urlExito, string $urlCancelar): array
    {
        $datos = [
            'mode' => 'subscription',
            'line_items' => [[
                'quantity' => 1,
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => (int) config('services.stripe.suscripcion_precio_centimos'),
                    'recurring' => ['interval' => 'month'],
                    'product_data' => ['name' => 'Suscripción mensual a MesaYa · '.$restaurante->nombre],
                ],
            ]],
            'success_url' => $urlExito,
            'cancel_url' => $urlCancelar,
            // Al volver comprobamos que la sesión es de ESTE restaurante.
            'metadata' => ['restaurante_id' => (string) $restaurante->id],
            // También en la suscripción: así el webhook sabrá de qué restaurante es.
            'subscription_data' => ['metadata' => ['restaurante_id' => (string) $restaurante->id]],
            'locale' => 'es',
        ];

        if ($restaurante->stripe_cliente_id) {
            $datos['customer'] = $restaurante->stripe_cliente_id;
        }

        $sesion = $this->clientePlataforma()->checkout->sessions->create($datos);

        return ['id' => $sesion->id, 'url' => $sesion->url];
    }

    /**
     * Pregunta a Stripe cómo está una sesión de suscripción (rama 24).
     *
     * 'expand' pide que Stripe devuelva la suscripción entera dentro de la
     * sesión (y no solo su id), para leer la próxima fecha de cobro sin hacer
     * otra llamada. Con la versión actual de la API, esa fecha está en el
     * elemento de la suscripción (items), no en la suscripción.
     *
     * @return array{pagada: bool, restaurante_id: int|null, cliente_id: string|null, suscripcion_id: string|null, fin_en: CarbonInterface|null}
     */
    public function consultarSesionSuscripcion(string $idSesion): array
    {
        try {
            $sesion = $this->clientePlataforma()->checkout->sessions->retrieve($idSesion, ['expand' => ['subscription']]);
        } catch (InvalidRequestException) {
            // Id inventado o de otra cuenta: como si no estuviera pagada.
            return ['pagada' => false, 'restaurante_id' => null, 'cliente_id' => null, 'suscripcion_id' => null, 'fin_en' => null];
        }

        $suscripcion = $sesion->subscription;
        $finEn = $suscripcion?->items?->data[0]?->current_period_end ?? null;

        return [
            // 'complete' = el cliente terminó el Checkout; 'paid' = se ha cobrado.
            'pagada' => $sesion->mode === 'subscription' && $sesion->status === 'complete' && $sesion->payment_status === 'paid',
            'restaurante_id' => isset($sesion->metadata['restaurante_id']) ? (int) $sesion->metadata['restaurante_id'] : null,
            'cliente_id' => is_string($sesion->customer) ? $sesion->customer : $sesion->customer?->id,
            'suscripcion_id' => $suscripcion?->id,
            // Stripe da las fechas como segundos desde 1970 (timestamp).
            'fin_en' => $finEn ? Carbon::createFromTimestamp($finEn) : null,
        ];
    }

    /**
     * Abre el portal de cliente de Stripe (rama 24): ahí el admin cambia la
     * tarjeta, ve sus facturas o cancela. La página la pone Stripe.
     * Hay que activarlo una vez en el panel de Stripe (modo test):
     * Settings → Billing → Customer portal.
     *
     * @return string La URL del portal.
     */
    public function crearSesionPortal(Restaurante $restaurante, string $urlVolver): string
    {
        $sesion = $this->clientePlataforma()->billingPortal->sessions->create([
            'customer' => $restaurante->stripe_cliente_id,
            'return_url' => $urlVolver,
            'locale' => 'es',
        ]);

        return $sesion->url;
    }

    /**
     * Lee un aviso (evento) que Stripe manda al webhook /stripe/webhook (rama 24, PR 2).
     *
     * Cualquiera puede hacer un POST a esa URL, así que antes de creer nada
     * comprobamos la FIRMA: Stripe firma cada aviso con el secreto del webhook
     * (STRIPE_WEBHOOK_SECRET, whsec_...) y Webhook::constructEvent() la
     * comprueba en local, sin llamar a Stripe. Si no cuadra, lanza
     * SignatureVerificationException y el controlador responde 400.
     *
     * Devuelve solo lo que necesitamos, en un array sencillo:
     *  - tipo: 'customer.subscription.updated', 'invoice.payment_failed'...
     *  - suscripcion_id: la suscripción (sub_...) a la que se refiere el aviso.
     *  - estado_stripe: estado de la suscripción según Stripe (active,
     *    past_due, canceled...). Solo viene en los avisos de suscripción.
     *  - fin_en: fin del periodo pagado (próximo cobro), si viene.
     *
     * @return array{tipo: string, suscripcion_id: string|null, estado_stripe: string|null, fin_en: CarbonInterface|null}
     *
     * @throws SignatureVerificationException  firma que no cuadra
     * @throws RuntimeException  falta el secreto o el aviso no es de modo test
     */
    public function leerEventoWebhook(string $contenido, string $firma): array
    {
        $secreto = (string) config('services.stripe.webhook_secret');

        if (! str_starts_with($secreto, 'whsec_')) {
            throw new RuntimeException('Falta STRIPE_WEBHOOK_SECRET en el .env (whsec_...).');
        }

        $evento = Webhook::constructEvent($contenido, $firma, $secreto);

        // Seguro extra, como con sk_test_: un aviso de modo real no se acepta nunca.
        if ($evento->livemode) {
            throw new RuntimeException('Aviso de Stripe en modo real: solo se aceptan los de modo test.');
        }

        $objeto = $evento->data->object;

        if (str_starts_with($evento->type, 'customer.subscription.')) {
            // El objeto es la suscripción. La fecha del periodo va en su
            // elemento (items), igual que en consultarSesionSuscripcion().
            $finEn = $objeto->items?->data[0]?->current_period_end ?? null;

            return [
                'tipo' => $evento->type,
                'suscripcion_id' => $objeto->id,
                'estado_stripe' => $objeto->status,
                'fin_en' => $finEn ? Carbon::createFromTimestamp($finEn) : null,
            ];
        }

        if (str_starts_with($evento->type, 'invoice.')) {
            // El objeto es la factura. Con esta versión de la API, la
            // suscripción que la generó está en parent.subscription_details.
            $suscripcion = $objeto->parent?->subscription_details?->subscription ?? null;

            return [
                'tipo' => $evento->type,
                'suscripcion_id' => is_string($suscripcion) ? $suscripcion : $suscripcion?->id,
                'estado_stripe' => null,
                'fin_en' => null,
            ];
        }

        // Otros avisos: no los usamos.
        return ['tipo' => $evento->type, 'suscripcion_id' => null, 'estado_stripe' => null, 'fin_en' => null];
    }

    /**
     * Comprueba con Stripe que una clave secreta de un restaurante funciona
     * (rama 26), antes de guardarla.
     *
     * Pide la lista de sesiones de Checkout (solo 1): es justo el permiso que
     * necesitan los cobros, así que también sirve para una clave restringida
     * (rk_test_) con "Checkout Sessions: escritura". No crea ni cobra nada.
     *
     * Devuelve true o false y NUNCA el mensaje de Stripe: así la clave no
     * puede acabar en una pantalla ni en un log.
     */
    public function comprobarClave(string $claveSecreta): bool
    {
        if (! self::esClaveSecretaDeTest($claveSecreta)) {
            return false;
        }

        try {
            (new StripeClient($claveSecreta))->checkout->sessions->all(['limit' => 1]);

            return true;
        } catch (ApiErrorException) {
            // Clave que no existe, revocada, sin permisos, sin internet...
            return false;
        }
    }

    /**
     * True si es una clave secreta de MODO TEST: normal (sk_test_) o
     * restringida (rk_test_). Las de modo real (_live_) nunca valen.
     */
    public static function esClaveSecretaDeTest(?string $clave): bool
    {
        return is_string($clave)
            && (str_starts_with($clave, 'sk_test_') || str_starts_with($clave, 'rk_test_'));
    }

    /**
     * Cliente de Stripe con la clave del RESTAURANTE (rama 26): para cobrar a
     * sus clientes. Es el único sitio donde se descifra la clave secreta (al
     * leer el atributo, por el cast "encrypted" del modelo).
     *
     * Seguro extra, igual que con la plataforma: si la clave guardada no es de
     * modo test, no se cobra.
     *
     * @throws StripeNoConfigurado
     */
    private function clienteRestaurante(Restaurante $restaurante): StripeClient
    {
        if (! $restaurante->tieneStripeConfigurado()) {
            throw new StripeNoConfigurado('El restaurante '.$restaurante->id.' no tiene configurado Stripe.');
        }

        $secreta = $restaurante->stripe_cobros_clave_secreta;

        if (! self::esClaveSecretaDeTest($secreta)) {
            // El mensaje NO lleva la clave.
            throw new StripeNoConfigurado('La clave de Stripe del restaurante '.$restaurante->id.' no es de modo test.');
        }

        return new StripeClient($secreta);
    }

    /**
     * Cliente de Stripe con la clave secreta de la PLATAFORMA, la del .env
     * (vía config/services.php). Solo para la suscripción, el portal y el webhook.
     *
     * Seguro extra: si la clave no es de MODO TEST (sk_test_...), no seguimos.
     * Así, aunque alguien pegue por error una clave live, nunca se cobra de verdad.
     */
    private function clientePlataforma(): StripeClient
    {
        $secreta = (string) config('services.stripe.secret');

        if (! str_starts_with($secreta, 'sk_test_')) {
            throw new RuntimeException('Falta STRIPE_SECRET en el .env o no es una clave de modo test (sk_test_...).');
        }

        return new StripeClient($secreta);
    }
}
