<?php

namespace Tests\Feature;

use App\Models\Dispositivo;
use App\Models\Mesa;
use App\Models\Restaurante;
use App\Models\User;
use App\Servicios\PasarelaStripe;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Mockery\MockInterface;
use Tests\TestCase;

/**
 * Rama 24: alta pública del restaurante y suscripción mensual con Stripe.
 *
 * Aquí NUNCA se llama a Stripe de verdad: con $this->mock() cambiamos
 * PasarelaStripe por una simulada que devuelve lo que le decimos.
 */
class SuscripcionTest extends TestCase
{
    use RefreshDatabase;

    /** Datos válidos del formulario de /registro (inventados). */
    private function datosRegistro(array $cambios = []): array
    {
        return $cambios + [
            'nombre_restaurante' => 'Bar La Prueba',
            'name' => 'Persona Inventada',
            'email' => 'admin@bar-la-prueba.test',
            'password' => 'contrasena-de-prueba',
            'password_confirmation' => 'contrasena-de-prueba',
        ];
    }

    /** Respuesta simulada de PasarelaStripe::consultarSesionSuscripcion(). */
    private function sesion(int $restauranteId, bool $pagada = true): array
    {
        return [
            'pagada' => $pagada,
            'restaurante_id' => $restauranteId,
            'cliente_id' => 'cus_prueba',
            'suscripcion_id' => 'sub_prueba',
            'fin_en' => Carbon::parse('2026-12-25 10:00:00'),
        ];
    }

    /** Admin de un restaurante recién dado de alta, sin pagar. */
    private function adminPendiente(): User
    {
        $restaurante = Restaurante::factory()->pendiente()->create();

        return User::factory()->create(['restaurante_id' => $restaurante->id]);
    }

    public function test_el_registro_crea_restaurante_y_admin_pendientes_y_redirige_a_stripe(): void
    {
        $this->mock(PasarelaStripe::class, function (MockInterface $pasarela) {
            $pasarela->shouldReceive('crearSesionSuscripcion')
                ->once()
                ->withArgs(fn (Restaurante $restaurante, string $urlExito) => $restaurante->slug === 'bar-la-prueba'
                    && str_contains($urlExito, '{CHECKOUT_SESSION_ID}'))
                ->andReturn(['id' => 'cs_test_1', 'url' => 'https://checkout.stripe.com/c/pay/cs_test_1']);
        });

        $this->get(route('registro'))->assertOk()->assertSee('Da de alta tu restaurante');

        $this->post(route('registro.guardar'), $this->datosRegistro())
            ->assertRedirect('https://checkout.stripe.com/c/pay/cs_test_1');

        $restaurante = Restaurante::where('slug', 'bar-la-prueba')->firstOrFail();
        $this->assertSame('Bar La Prueba', $restaurante->nombre);
        $this->assertSame('pendiente', $restaurante->suscripcion_estado);

        $admin = User::where('email', 'admin@bar-la-prueba.test')->firstOrFail();
        $this->assertSame('admin', $admin->rol);
        $this->assertSame($restaurante->id, $admin->restaurante_id);
        $this->assertFalse($admin->esDesarrollador());

        // Queda con la sesión iniciada como ese admin.
        $this->assertAuthenticatedAs($admin);
    }

    public function test_el_registro_genera_un_slug_unico(): void
    {
        Restaurante::factory()->create(['slug' => 'bar-la-prueba']);
        $this->mock(PasarelaStripe::class, fn (MockInterface $p) => $p->shouldReceive('crearSesionSuscripcion')
            ->andReturn(['id' => 'cs_test_1', 'url' => 'https://checkout.stripe.com/x']));

        $this->post(route('registro.guardar'), $this->datosRegistro());

        $this->assertDatabaseHas('restaurantes', ['slug' => 'bar-la-prueba-2', 'suscripcion_estado' => 'pendiente']);
    }

    public function test_el_registro_valida_los_datos_y_no_crea_nada(): void
    {
        User::factory()->create(['email' => 'repetido@bar.test']);
        $this->mock(PasarelaStripe::class, fn (MockInterface $p) => $p->shouldNotReceive('crearSesionSuscripcion'));

        $this->post(route('registro.guardar'), $this->datosRegistro([
            'nombre_restaurante' => '',
            'email' => 'repetido@bar.test',
            'password_confirmation' => 'otra-distinta',
        ]))->assertSessionHasErrors(['nombre_restaurante', 'email', 'password']);

        $this->assertDatabaseMissing('restaurantes', ['nombre' => 'Bar La Prueba']);
        $this->assertGuest();
    }

    public function test_si_stripe_falla_el_restaurante_queda_pendiente_y_puede_reintentarlo(): void
    {
        $this->mock(PasarelaStripe::class, fn (MockInterface $p) => $p->shouldReceive('crearSesionSuscripcion')
            ->andThrow(new \RuntimeException('Stripe no responde')));

        $this->post(route('registro.guardar'), $this->datosRegistro())
            ->assertRedirect(route('admin.suscripcion'));

        $this->get(route('admin.suscripcion'))
            ->assertOk()
            ->assertSee('Pendiente')
            ->assertSee('Pagar la suscripción');
    }

    public function test_la_suscripcion_solo_se_activa_si_stripe_confirma_el_pago(): void
    {
        $admin = $this->adminPendiente();
        $restaurante = $admin->restaurante;

        $this->mock(PasarelaStripe::class, function (MockInterface $pasarela) use ($restaurante) {
            $pasarela->shouldReceive('consultarSesionSuscripcion')->with('cs_sin_pagar')->andReturn($this->sesion($restaurante->id, pagada: false));
            $pasarela->shouldReceive('consultarSesionSuscripcion')->with('cs_pagada')->andReturn($this->sesion($restaurante->id));
        });

        // Escribir la URL de éxito a mano (sin session_id) no activa nada.
        $this->actingAs($admin)->get(route('admin.suscripcion.exito'))->assertRedirect(route('admin.suscripcion'));
        // Una sesión que Stripe dice que NO está pagada, tampoco.
        $this->get(route('admin.suscripcion.exito', ['session_id' => 'cs_sin_pagar']))->assertRedirect(route('admin.suscripcion'));
        $this->assertSame('pendiente', $restaurante->fresh()->suscripcion_estado);

        // Pagada y de este restaurante: se activa y se guardan los datos de Stripe.
        $this->get(route('admin.suscripcion.exito', ['session_id' => 'cs_pagada']))->assertRedirect(route('panel'));

        $restaurante->refresh();
        $this->assertSame('activa', $restaurante->suscripcion_estado);
        $this->assertSame('cus_prueba', $restaurante->stripe_cliente_id);
        $this->assertSame('sub_prueba', $restaurante->stripe_suscripcion_id);
        $this->assertSame('2026-12-25', $restaurante->suscripcion_fin_en->format('Y-m-d'));

        // Ya puede usar la aplicación.
        $this->get(route('panel'))->assertOk();
    }

    public function test_un_restaurante_no_puede_activar_la_suscripcion_de_otro(): void
    {
        $adminA = $this->adminPendiente();
        $adminB = $this->adminPendiente();
        $restauranteB = $adminB->restaurante;

        // La sesión está pagada... pero es la del restaurante B.
        $this->mock(PasarelaStripe::class, fn (MockInterface $p) => $p->shouldReceive('consultarSesionSuscripcion')
            ->with('cs_de_b')->andReturn($this->sesion($restauranteB->id)));

        $this->actingAs($adminA)->get(route('admin.suscripcion.exito', ['session_id' => 'cs_de_b']))
            ->assertRedirect(route('admin.suscripcion'));

        $this->assertSame('pendiente', $adminA->restaurante->fresh()->suscripcion_estado);
        // Y la de B tampoco se toca desde la cuenta de A.
        $this->assertSame('pendiente', $restauranteB->fresh()->suscripcion_estado);
        $this->assertNull($restauranteB->fresh()->stripe_cliente_id);
    }

    public function test_un_restaurante_sin_suscripcion_activa_no_puede_usar_la_app(): void
    {
        $admin = $this->adminPendiente();
        $restaurante = $admin->restaurante;
        Mesa::factory()->create(['restaurante_id' => $restaurante->id, 'numero' => 1]);

        // Admin: cualquier pantalla le manda a «Tu suscripción».
        $this->actingAs($admin)->get(route('panel'))->assertRedirect(route('admin.suscripcion'));
        $this->get(route('admin.carta'))->assertRedirect(route('admin.suscripcion'));
        $this->get(route('admin.suscripcion'))->assertOk()->assertSee('Pagar la suscripción');
        auth()->logout();

        // Cliente por QR: «Restaurante no disponible».
        $this->get(route('cliente.carta', ['slug' => $restaurante->slug, 'numero' => 1]))
            ->assertForbidden()
            ->assertSee('Restaurante no disponible');

        // Tablet de cocina emparejada: tampoco (ni la pantalla ni el polling).
        $cocina = Dispositivo::create(['restaurante_id' => $restaurante->id, 'tipo' => 'cocina', 'nombre' => 'Cocina 1']);
        $tokenCocina = $cocina->emparejar();
        $this->withCookie(Dispositivo::COOKIE, $tokenCocina)->get(route('cocina.pedidos'))
            ->assertForbidden()
            ->assertSee('Restaurante no disponible');
        $this->withCookie(Dispositivo::COOKIE, $tokenCocina)->withCredentials()->getJson(route('cocina.pedidos.datos'))
            ->assertForbidden();

        // Camarero en su dispositivo emparejado: tampoco.
        $camarero = User::factory()->camarero()->create(['restaurante_id' => $restaurante->id]);
        $movil = Dispositivo::create(['restaurante_id' => $restaurante->id, 'tipo' => 'camarero', 'nombre' => 'Móvil 1']);
        $tokenMovil = $movil->emparejar();
        $this->actingAs($camarero)
            ->withSession(['dispositivo_id' => $movil->id])
            ->withCookie(Dispositivo::COOKIE, $tokenMovil)
            ->get(route('camarero.mesas'))
            ->assertForbidden()
            ->assertSee('Restaurante no disponible');
    }

    public function test_impagada_y_cancelada_tambien_bloquean(): void
    {
        foreach (['impagada', 'cancelada'] as $estado) {
            $restaurante = Restaurante::factory()->create(['suscripcion_estado' => $estado]);
            $admin = User::factory()->create(['restaurante_id' => $restaurante->id]);

            $this->actingAs($admin)->get(route('panel'))->assertRedirect(route('admin.suscripcion'));
            $this->get(route('admin.suscripcion'))->assertSee('Reactivar la suscripción');
        }
    }

    public function test_los_desarrolladores_no_se_bloquean_nunca(): void
    {
        $restaurante = Restaurante::factory()->pendiente()->create();
        $desarrollador = User::factory()->desarrollador()->create(['restaurante_id' => $restaurante->id]);

        $this->actingAs($desarrollador)->get(route('panel'))->assertOk();
        $this->get(route('cocina.pedidos'))->assertOk();
    }

    public function test_con_la_suscripcion_activa_no_se_abre_otro_pago_y_se_ve_el_portal(): void
    {
        $restaurante = Restaurante::factory()->create();
        $restaurante->forceFill(['stripe_cliente_id' => 'cus_prueba', 'suscripcion_fin_en' => Carbon::parse('2026-12-25')])->save();
        $admin = User::factory()->create(['restaurante_id' => $restaurante->id]);

        $this->mock(PasarelaStripe::class, function (MockInterface $pasarela) {
            $pasarela->shouldNotReceive('crearSesionSuscripcion');
            $pasarela->shouldReceive('crearSesionPortal')->once()->andReturn('https://billing.stripe.com/p/session/prueba');
        });

        $this->actingAs($admin)->get(route('admin.suscripcion'))
            ->assertOk()
            ->assertSee('Activa')
            ->assertSee('25/12/2026')
            ->assertSee('29,00')
            ->assertSee('Gestionar en Stripe');

        $this->post(route('admin.suscripcion.pagar'))->assertRedirect(route('admin.suscripcion'));
        $this->post(route('admin.suscripcion.portal'))->assertRedirect('https://billing.stripe.com/p/session/prueba');
    }

    public function test_camarero_y_cocina_no_entran_en_la_suscripcion(): void
    {
        $restaurante = Restaurante::factory()->create();
        $camarero = User::factory()->camarero()->create(['restaurante_id' => $restaurante->id]);

        $this->actingAs($camarero)->get(route('admin.suscripcion'))->assertForbidden();
    }

    public function test_los_seeders_dejan_los_restaurantes_de_demo_activos(): void
    {
        $this->seed(\Database\Seeders\RestaurantesSeeder::class);

        $this->assertSame(0, Restaurante::where('suscripcion_estado', '!=', 'activa')->count());
        $this->assertTrue(Restaurante::where('slug', 'la-tasca')->firstOrFail()->tieneSuscripcionActiva());
    }
}
