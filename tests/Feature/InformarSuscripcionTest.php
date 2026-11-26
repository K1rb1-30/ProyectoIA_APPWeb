<?php

namespace Tests\Feature;

use App\Models\Restaurante;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Rama 25: que se entienda la suscripción en la portada, el login, el
 * registro y el panel del admin. Solo textos y vistas: la lógica de la
 * suscripción la prueban SuscripcionTest y StripeWebhookTest.
 */
class InformarSuscripcionTest extends TestCase
{
    use RefreshDatabase;

    public function test_la_portada_ensena_el_precio_de_config(): void
    {
        // Un precio que no es el de por defecto: si saliera escrito a mano
        // en la vista (29,00), este test fallaría.
        config(['services.stripe.suscripcion_precio_centimos' => 3450]);

        $this->get(route('inicio'))
            ->assertOk()
            ->assertSee('34,50')
            ->assertDontSee('29,00')
            ->assertSee('id="suscripcion"', false)
            ->assertSee('Qué incluye')
            ->assertSee('Cómo darte de alta')
            ->assertSee('href="'.route('registro').'"', false);
    }

    public function test_la_portada_no_dice_que_el_pago_es_de_prueba_ni_inventa_condiciones(): void
    {
        $this->get(route('inicio'))
            ->assertOk()
            ->assertDontSee('4242')
            ->assertDontSee('prueba gratis')
            ->assertDontSee('días de prueba')
            ->assertDontSee('descuento');
    }

    public function test_el_alta_es_el_boton_naranja_y_con_sesion_no_sale(): void
    {
        // Sin sesión: un solo botón naranja, el del alta.
        $html = $this->get(route('inicio'))->assertSee('Da de alta tu restaurante')->getContent();
        $this->assertSame(1, substr_count($html, 'bg-primary-container text-on-primary'));

        $admin = User::factory()->create();
        $html = $this->actingAs($admin)->get(route('inicio'))->assertOk()->getContent();

        // Con sesión, /registro no sirve (es solo para invitados): no hay botón de alta.
        $this->assertStringNotContainsString('bg-primary-container text-on-primary', $html);
    }

    public function test_el_login_enlaza_al_registro(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('¿Aún no usas MesaYa?')
            ->assertSee('href="'.route('registro').'"', false);
    }

    public function test_el_registro_explica_los_pasos_y_las_condiciones(): void
    {
        config(['services.stripe.suscripcion_precio_centimos' => 3450]);

        $this->get(route('registro'))
            ->assertOk()
            ->assertSee('aria-current="step"', false)
            ->assertSee('34,50')
            ->assertSee('El cobro se repite cada mes')
            ->assertSee('Si cierras Stripe sin pagar, tu cuenta se guarda')
            ->assertSee('4242 4242 4242 4242');
    }

    public function test_el_admin_redirigido_ve_el_aviso(): void
    {
        $restaurante = Restaurante::factory()->pendiente()->create();
        $admin = User::factory()->create(['restaurante_id' => $restaurante->id]);

        $this->actingAs($admin)->get(route('panel'))
            ->assertRedirect(route('admin.suscripcion'))
            ->assertSessionHas('aviso', 'Tu restaurante no está disponible hasta que pagues la suscripción.');

        // Y el aviso se ve en la pantalla, junto a lo que incluye.
        $this->followingRedirects()->get(route('panel'))
            ->assertOk()
            ->assertSee('Tu restaurante no está disponible hasta que pagues la suscripción.')
            ->assertSee('Incluye');
    }

    public function test_el_aviso_dice_si_esta_impagada_o_cancelada(): void
    {
        $impagada = Restaurante::factory()->create(['suscripcion_estado' => 'impagada']);
        $this->actingAs(User::factory()->create(['restaurante_id' => $impagada->id]))
            ->get(route('panel'))
            ->assertSessionHas('aviso', 'Tu restaurante no está disponible porque no se ha podido cobrar la suscripción.');

        $cancelada = Restaurante::factory()->create(['suscripcion_estado' => 'cancelada']);
        $this->actingAs(User::factory()->create(['restaurante_id' => $cancelada->id]))
            ->get(route('panel'))
            ->assertSessionHas('aviso', 'Tu restaurante no está disponible porque la suscripción está cancelada.');
    }
}
