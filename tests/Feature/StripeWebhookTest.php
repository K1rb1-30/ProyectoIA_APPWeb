<?php

namespace Tests\Feature;

use App\Models\Restaurante;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

/**
 * Rama 24, PR 2: webhook de Stripe (POST /stripe/webhook).
 *
 * Aquí NO se simula PasarelaStripe: se usa la de verdad, porque lo que hay
 * que probar es la comprobación de la FIRMA. No hace falta internet: la
 * firma se calcula y se comprueba en local. Firmamos los avisos como lo
 * hace Stripe, con un secreto de prueba inventado.
 */
class StripeWebhookTest extends TestCase
{
    use RefreshDatabase;

    private const SECRETO = 'whsec_secreto_de_prueba';

    private Restaurante $restaurante;

    protected function setUp(): void
    {
        parent::setUp();

        config(['services.stripe.webhook_secret' => self::SECRETO]);

        $this->restaurante = Restaurante::factory()->create();
        $this->restaurante->forceFill([
            'stripe_cliente_id' => 'cus_prueba',
            'stripe_suscripcion_id' => 'sub_prueba',
            'suscripcion_fin_en' => Carbon::parse('2026-12-25'),
        ])->save();
    }

    /** Aviso de Stripe con el formato de un evento (event) de su API. */
    private function evento(string $tipo, array $objeto, bool $modoReal = false): string
    {
        return json_encode([
            'id' => 'evt_prueba',
            'object' => 'event',
            'type' => $tipo,
            'livemode' => $modoReal,
            'data' => ['object' => $objeto],
        ]);
    }

    /** Objeto "subscription" con su estado y el fin del periodo. */
    private function suscripcion(string $estado, string $id = 'sub_prueba', ?int $finPeriodo = null): array
    {
        return [
            'id' => $id,
            'object' => 'subscription',
            'status' => $estado,
            'items' => ['object' => 'list', 'data' => [[
                'id' => 'si_prueba',
                'object' => 'subscription_item',
                'current_period_end' => $finPeriodo ?? Carbon::parse('2026-12-25')->timestamp,
            ]]],
        ];
    }

    /**
     * Envía el aviso con la cabecera Stripe-Signature como la calcula Stripe:
     * t=<hora>,v1=<HMAC-SHA256 de "<hora>.<cuerpo>" con el secreto>.
     */
    private function enviar(string $cuerpo, ?string $secreto = self::SECRETO): TestResponse
    {
        $hora = time();
        $firma = 't='.$hora.',v1='.hash_hmac('sha256', $hora.'.'.$cuerpo, (string) $secreto);

        // call() permite mandar el cuerpo tal cual (la firma va sobre esos bytes).
        return $this->call('POST', route('stripe.webhook'), [], [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_STRIPE_SIGNATURE' => $firma,
        ], $cuerpo);
    }

    public function test_el_webhook_con_firma_invalida_se_rechaza(): void
    {
        $cuerpo = $this->evento('customer.subscription.deleted', $this->suscripcion('canceled'));

        // Firmado con otro secreto: no viene de Stripe.
        $this->enviar($cuerpo, 'whsec_otro_secreto')->assertStatus(400);
        // Sin cabecera de firma.
        $this->call('POST', route('stripe.webhook'), [], [], [], ['CONTENT_TYPE' => 'application/json'], $cuerpo)
            ->assertStatus(400);

        $this->assertSame('activa', $this->restaurante->fresh()->suscripcion_estado);
    }

    public function test_sin_el_secreto_configurado_no_se_acepta_nada(): void
    {
        config(['services.stripe.webhook_secret' => null]);

        $this->enviar($this->evento('customer.subscription.deleted', $this->suscripcion('canceled')))
            ->assertStatus(400);

        $this->assertSame('activa', $this->restaurante->fresh()->suscripcion_estado);
    }

    public function test_un_aviso_de_modo_real_se_rechaza(): void
    {
        $this->enviar($this->evento('customer.subscription.deleted', $this->suscripcion('canceled'), modoReal: true))
            ->assertStatus(400);

        $this->assertSame('activa', $this->restaurante->fresh()->suscripcion_estado);
    }

    public function test_un_cobro_fallido_deja_la_suscripcion_impagada(): void
    {
        $factura = [
            'id' => 'in_prueba',
            'object' => 'invoice',
            'parent' => ['type' => 'subscription_details', 'subscription_details' => ['subscription' => 'sub_prueba']],
        ];

        $this->enviar($this->evento('invoice.payment_failed', $factura))->assertOk();

        $this->assertSame('impagada', $this->restaurante->fresh()->suscripcion_estado);
    }

    public function test_cancelar_desde_el_portal_deja_la_suscripcion_cancelada(): void
    {
        $this->enviar($this->evento('customer.subscription.deleted', $this->suscripcion('canceled')))->assertOk();

        $this->assertSame('cancelada', $this->restaurante->fresh()->suscripcion_estado);
    }

    public function test_al_cobrar_el_mes_siguiente_vuelve_a_activa_con_la_fecha_nueva(): void
    {
        $this->restaurante->cambiarEstadoSuscripcion('impagada');

        $finNuevo = Carbon::parse('2027-01-25 10:00:00')->timestamp;
        $this->enviar($this->evento('customer.subscription.updated', $this->suscripcion('active', finPeriodo: $finNuevo)))->assertOk();

        $restaurante = $this->restaurante->fresh();
        $this->assertSame('activa', $restaurante->suscripcion_estado);
        $this->assertSame('2027-01-25', $restaurante->suscripcion_fin_en->format('Y-m-d'));
    }

    public function test_los_estados_de_stripe_se_traducen_a_los_nuestros(): void
    {
        $casos = ['past_due' => 'impagada', 'unpaid' => 'impagada', 'canceled' => 'cancelada', 'active' => 'activa'];

        foreach ($casos as $estadoStripe => $esperado) {
            $this->enviar($this->evento('customer.subscription.updated', $this->suscripcion($estadoStripe)))->assertOk();
            $this->assertSame($esperado, $this->restaurante->fresh()->suscripcion_estado, $estadoStripe);
        }

        // "incomplete" (primer pago sin terminar) no cambia nada.
        $this->enviar($this->evento('customer.subscription.updated', $this->suscripcion('incomplete')))->assertOk();
        $this->assertSame('activa', $this->restaurante->fresh()->suscripcion_estado);
    }

    public function test_un_aviso_de_otra_suscripcion_no_toca_ningun_restaurante(): void
    {
        // Por ejemplo, los de "stripe trigger" o la suscripción antigua de un
        // restaurante que ha reactivado con otra.
        $this->enviar($this->evento('customer.subscription.deleted', $this->suscripcion('canceled', id: 'sub_desconocida')))
            ->assertOk();

        $this->assertSame('activa', $this->restaurante->fresh()->suscripcion_estado);
    }

    public function test_los_avisos_que_no_usamos_responden_bien_y_no_cambian_nada(): void
    {
        $this->enviar($this->evento('customer.created', ['id' => 'cus_prueba', 'object' => 'customer']))->assertOk();

        $this->assertSame('activa', $this->restaurante->fresh()->suscripcion_estado);
    }

    public function test_tras_un_impago_el_restaurante_queda_bloqueado(): void
    {
        $admin = \App\Models\User::factory()->create(['restaurante_id' => $this->restaurante->id]);
        $this->enviar($this->evento('customer.subscription.updated', $this->suscripcion('past_due')))->assertOk();

        $this->actingAs($admin)->get(route('panel'))->assertRedirect(route('admin.suscripcion'));
        $this->get(route('admin.suscripcion'))->assertSee('Impagada')->assertSee('Reactivar la suscripción');
    }
}
