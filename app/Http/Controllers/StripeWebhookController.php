<?php

namespace App\Http\Controllers;

use App\Models\Restaurante;
use App\Servicios\PasarelaStripe;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RuntimeException;
use Stripe\Exception\SignatureVerificationException;
use UnexpectedValueException;

/**
 * Webhook de Stripe: POST /stripe/webhook (rama 24, PR 2).
 *
 * Stripe nos avisa aquí de lo que pasa con una suscripción DESPUÉS del alta,
 * cuando el admin ya no está en nuestra web: un cobro mensual que falla, una
 * cancelación desde el portal, una renovación...
 *
 * Avisos que usamos:
 *  - customer.subscription.updated → estado traducido (activa / impagada /
 *    cancelada) y la próxima fecha de cobro.
 *  - customer.subscription.deleted → cancelada (fin del periodo pagado).
 *  - invoice.payment_failed        → impagada.
 * Los demás se contestan con 200 y no hacen nada.
 *
 * Seguridad:
 *  - Sin CSRF (Stripe no tiene nuestro token): se quita en bootstrap/app.php.
 *    En su lugar, la FIRMA del aviso (cabecera Stripe-Signature), que comprueba
 *    PasarelaStripe. Firma mala → 400 y no se toca nada.
 *  - El restaurante se busca por stripe_suscripcion_id, la que se guardó al
 *    confirmar el pago. Un aviso de una suscripción desconocida (por ejemplo,
 *    los de "stripe trigger", o la suscripción antigua de un restaurante que
 *    ha reactivado con otra) no toca ningún restaurante.
 *
 * Qué responder: Stripe reintenta el aviso si no recibe un 2xx. Por eso, si
 * el aviso es válido pero no nos sirve, respondemos 200 igualmente.
 */
class StripeWebhookController extends Controller
{
    public function __construct(private PasarelaStripe $pasarela) {}

    public function __invoke(Request $request): JsonResponse
    {
        try {
            // getContent(): el cuerpo TAL CUAL llega. La firma se calcula sobre
            // esos bytes exactos: si se leyera como JSON y se volviera a
            // convertir en texto, la firma ya no cuadraría.
            $evento = $this->pasarela->leerEventoWebhook(
                $request->getContent(),
                (string) $request->header('Stripe-Signature', ''),
            );
        // Ojo con el orden: UnexpectedValueException también es una
        // RuntimeException, así que tiene que ir en el primer catch.
        } catch (SignatureVerificationException|UnexpectedValueException) {
            // Firma que no cuadra o cuerpo que no es JSON: no viene de Stripe.
            return response()->json(['mensaje' => 'Firma no válida.'], 400);
        } catch (RuntimeException $error) {
            // Falta el secreto o es un aviso de modo real: se apunta en el log.
            report($error);

            return response()->json(['mensaje' => 'Aviso no aceptado.'], 400);
        }

        $restaurante = $evento['suscripcion_id']
            ? Restaurante::where('stripe_suscripcion_id', $evento['suscripcion_id'])->first()
            : null;

        if ($restaurante === null) {
            return response()->json(['mensaje' => 'Aviso ignorado.']);
        }

        $estadoNuevo = match ($evento['tipo']) {
            'customer.subscription.updated' => Restaurante::estadoDesdeStripe($evento['estado_stripe']),
            'customer.subscription.deleted' => Restaurante::SUSCRIPCION_CANCELADA,
            'invoice.payment_failed' => Restaurante::SUSCRIPCION_IMPAGADA,
            default => null,
        };

        if ($estadoNuevo !== null) {
            $restaurante->cambiarEstadoSuscripcion($estadoNuevo, $evento['fin_en']);
        }

        return response()->json(['mensaje' => 'Aviso recibido.']);
    }
}
