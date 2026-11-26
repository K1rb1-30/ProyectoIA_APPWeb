<?php

namespace Tests\Feature;

use App\Acciones\CrearPedido;
use App\Acciones\DividirCuenta;
use App\Models\Categoria;
use App\Models\Cuenta;
use App\Models\Mesa;
use App\Models\ParteCuenta;
use App\Models\Plato;
use App\Models\Restaurante;
use App\Models\User;
use App\Servicios\PasarelaStripe;
use App\Servicios\StripeNoConfigurado;
use Database\Seeders\RestaurantesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Mockery\MockInterface;
use ReflectionMethod;
use Tests\Fakes\PasarelaStripeFalsa;
use Tests\TestCase;

/**
 * Rama 26: cada restaurante cobra a sus clientes en SU cuenta de Stripe.
 *
 * Aquí NUNCA se llama a Stripe: PasarelaStripe está simulada ($this->mock o
 * PasarelaStripeFalsa), y las claves son FALSAS (no existen en Stripe).
 */
class IntegracionStripeTest extends TestCase
{
    use RefreshDatabase;

    // Claves inventadas para los tests.
    private const PUBLICABLE = 'pk_test_falsa_publicable';

    private const SECRETA = 'rk_test_falsa_secreta_5678';

    private Restaurante $tasca;

    private Restaurante $rincon;

    protected function setUp(): void
    {
        parent::setUp();

        // La Tasca empieza sin Stripe; El Rincón tiene sus propias claves.
        $this->tasca = Restaurante::factory()->sinStripe()->create(['nombre' => 'La Tasca', 'slug' => 'la-tasca']);
        $this->rincon = Restaurante::factory()->sinStripe()->create(['nombre' => 'Bar El Rincón', 'slug' => 'bar-el-rincon']);
        $this->rincon->guardarClavesStripe('pk_test_falsa_rincon', 'sk_test_falsa_del_rincon_9999');
    }

    private function adminDe(Restaurante $restaurante): User
    {
        return User::factory()->create(['restaurante_id' => $restaurante->id]);
    }

    private function guardar(array $datos = [])
    {
        return $this->put(route('admin.integraciones.stripe.guardar'), $datos + [
            'clave_publicable' => self::PUBLICABLE,
            'clave_secreta' => self::SECRETA,
        ]);
    }

    /**
     * Mesa 5 del restaurante con un pedido de 31,00 € y la cuenta dividida entre 3.
     */
    private function cuentaDividida(Restaurante $restaurante): Cuenta
    {
        $mesa = Mesa::factory()->create(['restaurante_id' => $restaurante->id, 'numero' => 5]);
        $categoria = Categoria::factory()->create(['restaurante_id' => $restaurante->id]);
        $plato = Plato::factory()->create(['categoria_id' => $categoria->id, 'precio_centimos' => 3100]);

        (new CrearPedido)->ejecutar($mesa, [['plato_id' => $plato->id, 'cantidad' => 1]]);

        return (new DividirCuenta)->ejecutar($mesa, 'iguales', 3);
    }

    private function rutaParte(string $nombre, Restaurante $restaurante, ParteCuenta $parte): string
    {
        return route($nombre, ['slug' => $restaurante->slug, 'numero' => 5, 'parte' => $parte->id]);
    }

    // ---------------------------------------------------------------
    // Guardar las claves
    // ---------------------------------------------------------------

    public function test_el_admin_guarda_sus_claves_de_stripe_cifradas(): void
    {
        $this->mock(PasarelaStripe::class, fn (MockInterface $pasarela) => $pasarela
            ->shouldReceive('comprobarClave')->once()->with(self::SECRETA)->andReturn(true));

        $this->actingAs($this->adminDe($this->tasca))
            ->guardar()
            ->assertRedirect(route('admin.integraciones'))
            ->assertSessionHas('estado');

        $this->tasca->refresh();
        $this->assertTrue($this->tasca->tieneStripeConfigurado());
        $this->assertSame(self::PUBLICABLE, $this->tasca->stripe_cobros_clave_publicable);
        $this->assertSame('5678', $this->tasca->stripe_cobros_final_clave);
        $this->assertNotNull($this->tasca->stripe_cobros_comprobada_en);

        // En la base de datos NO está la clave tal cual: está cifrada con APP_KEY...
        $enLaBaseDeDatos = DB::table('restaurantes')->where('id', $this->tasca->id)->value('stripe_cobros_clave_secreta');
        $this->assertStringNotContainsString(self::SECRETA, $enLaBaseDeDatos);
        // ...y al descifrarla sale la misma. El modelo la descifra solo.
        $this->assertSame(self::SECRETA, Crypt::decryptString($enLaBaseDeDatos));
        $this->assertSame(self::SECRETA, $this->tasca->stripe_cobros_clave_secreta);
    }

    public function test_no_se_aceptan_claves_live(): void
    {
        // Con una clave live ni siquiera se pregunta a Stripe.
        $this->mock(PasarelaStripe::class, fn (MockInterface $pasarela) => $pasarela->shouldNotReceive('comprobarClave'));
        $this->actingAs($this->adminDe($this->tasca));

        $this->guardar(['clave_secreta' => 'sk_live_falsa_1234'])
            ->assertSessionHasErrors(['clave_secreta' => 'Esa es una clave de modo real (live). MesaYa solo acepta claves de modo de prueba (sk_test_... o rk_test_...).']);
        $this->guardar(['clave_secreta' => 'rk_live_falsa_1234'])->assertSessionHasErrors('clave_secreta');
        $this->guardar(['clave_publicable' => 'pk_live_falsa_1234'])->assertSessionHasErrors('clave_publicable');

        $this->assertFalse($this->tasca->fresh()->tieneStripeConfigurado());
    }

    public function test_se_rechazan_las_claves_con_otro_formato(): void
    {
        $this->mock(PasarelaStripe::class, fn (MockInterface $pasarela) => $pasarela->shouldNotReceive('comprobarClave'));
        $this->actingAs($this->adminDe($this->tasca));

        $this->guardar(['clave_secreta' => 'pk_test_falsa_1234'])->assertSessionHasErrors('clave_secreta');
        $this->guardar(['clave_publicable' => 'sk_test_falsa_1234'])->assertSessionHasErrors('clave_publicable');
        $this->guardar(['clave_secreta' => 'sk_test_falsa 1234'])->assertSessionHasErrors('clave_secreta');
        $this->guardar(['clave_secreta' => ''])->assertSessionHasErrors('clave_secreta');

        $this->assertFalse($this->tasca->fresh()->tieneStripeConfigurado());
    }

    public function test_si_stripe_no_acepta_la_clave_no_se_guarda_nada(): void
    {
        $this->mock(PasarelaStripe::class, fn (MockInterface $pasarela) => $pasarela
            ->shouldReceive('comprobarClave')->once()->andReturn(false));

        $this->actingAs($this->adminDe($this->tasca))
            ->guardar()
            ->assertRedirect(route('admin.integraciones'))
            ->assertSessionHasErrors('clave_secreta');

        $this->assertFalse($this->tasca->fresh()->tieneStripeConfigurado());
        $this->assertNull(DB::table('restaurantes')->where('id', $this->tasca->id)->value('stripe_cobros_clave_secreta'));
    }

    public function test_el_admin_quita_sus_claves(): void
    {
        $this->actingAs($this->adminDe($this->rincon))
            ->delete(route('admin.integraciones.stripe.quitar'))
            ->assertRedirect(route('admin.integraciones'));

        $this->rincon->refresh();
        $this->assertFalse($this->rincon->tieneStripeConfigurado());
        $this->assertNull($this->rincon->stripe_cobros_clave_secreta);
    }

    // ---------------------------------------------------------------
    // La clave secreta no se enseña nunca
    // ---------------------------------------------------------------

    public function test_la_clave_secreta_nunca_aparece_en_la_pagina(): void
    {
        $this->actingAs($this->adminDe($this->rincon))
            ->get(route('admin.integraciones'))
            ->assertOk()
            ->assertSee('Conectada')
            ->assertSee('••••9999')
            ->assertDontSee('sk_test_falsa_del_rincon_9999')
            // Ni siquiera un trozo largo de la clave.
            ->assertDontSee('falsa_del_rincon')
            // La publicable tampoco se enseña entera.
            ->assertDontSee('pk_test_falsa_rincon');

        // Tampoco sale al convertir el modelo en array o JSON (respuestas, logs...).
        $this->assertArrayNotHasKey('stripe_cobros_clave_secreta', $this->rincon->toArray());
        $this->assertStringNotContainsString('falsa_del_rincon', $this->rincon->toJson());
    }

    public function test_la_clave_secreta_no_vuelve_al_formulario_si_hay_un_error(): void
    {
        $this->mock(PasarelaStripe::class, fn (MockInterface $pasarela) => $pasarela
            ->shouldReceive('comprobarClave')->andReturn(false));
        $this->actingAs($this->adminDe($this->tasca));

        // Falla Stripe: la clave no se guarda en la sesión (old) ni sale en la página.
        $this->guardar()->assertSessionMissing('_old_input.clave_secreta');
        $this->get(route('admin.integraciones'))->assertDontSee(self::SECRETA);

        // Falla la validación (publicable mala): tampoco.
        $this->guardar(['clave_publicable' => 'mala'])
            ->assertSessionHasErrors('clave_publicable')
            ->assertSessionMissing('_old_input.clave_secreta');
        $this->get(route('admin.integraciones'))->assertDontSee(self::SECRETA);
    }

    // ---------------------------------------------------------------
    // Aislamiento entre restaurantes
    // ---------------------------------------------------------------

    public function test_un_admin_no_puede_ver_ni_cambiar_la_integracion_de_otro_restaurante(): void
    {
        $this->mock(PasarelaStripe::class, fn (MockInterface $pasarela) => $pasarela
            ->shouldReceive('comprobarClave')->andReturn(true));
        $adminTasca = $this->adminDe($this->tasca);

        // Ve la de SU restaurante (sin configurar), no la de El Rincón.
        $this->actingAs($adminTasca)
            ->get(route('admin.integraciones'))
            ->assertOk()
            ->assertSee('Sin configurar')
            ->assertDontSee('••••9999')
            ->assertDontSee('pk_test_falsa_rincon');

        // Aunque intente mandar otro restaurante, se cambia el SUYO.
        $this->guardar(['restaurante_id' => $this->rincon->id]);
        $this->assertSame('5678', $this->tasca->fresh()->stripe_cobros_final_clave);
        $this->assertSame('sk_test_falsa_del_rincon_9999', $this->rincon->fresh()->stripe_cobros_clave_secreta);

        // Quitar también solo afecta al suyo.
        $this->delete(route('admin.integraciones.stripe.quitar'), ['restaurante_id' => $this->rincon->id]);
        $this->assertFalse($this->tasca->fresh()->tieneStripeConfigurado());
        $this->assertTrue($this->rincon->fresh()->tieneStripeConfigurado());
    }

    public function test_el_personal_que_no_es_admin_no_entra_en_integraciones(): void
    {
        $camarero = User::factory()->camarero()->create(['restaurante_id' => $this->rincon->id]);

        $this->actingAs($camarero)->get(route('admin.integraciones'))->assertForbidden();
    }

    public function test_un_desarrollador_ve_si_esta_configurada_pero_no_la_clave(): void
    {
        $desarrollador = User::factory()->desarrollador()->create(['restaurante_id' => $this->rincon->id]);

        $this->actingAs($desarrollador)
            ->get(route('admin.integraciones'))
            ->assertOk()
            ->assertSee('Conectada')
            ->assertSee('••••9999')
            ->assertDontSee('falsa_del_rincon');
    }

    // ---------------------------------------------------------------
    // Cobros: cada mesa con las claves de SU restaurante
    // ---------------------------------------------------------------

    public function test_el_pago_de_una_mesa_usa_las_claves_de_su_restaurante(): void
    {
        $this->tasca->guardarClavesStripe(self::PUBLICABLE, self::SECRETA);
        $parteRincon = $this->cuentaDividida($this->rincon)->partes->first();

        // Pagar una parte: la sesión se crea y se consulta con El Rincón.
        $this->mock(PasarelaStripe::class, function (MockInterface $pasarela) {
            $pasarela->shouldReceive('crearSesion')->once()
                ->withArgs(fn (Restaurante $restaurante) => $restaurante->is($this->rincon))
                ->andReturn(['id' => 'cs_test_1', 'url' => 'https://checkout.stripe.com/c/pay/cs_test_1']);
            $pasarela->shouldReceive('consultarSesion')->once()
                ->withArgs(fn (Restaurante $restaurante, string $id) => $restaurante->is($this->rincon) && $id === 'cs_test_1')
                ->andReturn(['pagada' => false, 'parte_cuenta_id' => null, 'importe_centimos' => 0, 'moneda' => '', 'tipo' => null, 'mesa_id' => null]);
        });

        $this->post($this->rutaParte('cliente.pago.pagar', $this->rincon, $parteRincon))
            ->assertRedirect('https://checkout.stripe.com/c/pay/cs_test_1');
        $this->get($this->rutaParte('cliente.pago.exito', $this->rincon, $parteRincon).'?session_id=cs_test_1');

        // Y PasarelaStripe, con cada restaurante, usa SU clave (descifrada).
        // clienteRestaurante() es privado: lo llamamos con reflexión. No llama a Stripe.
        $metodo = new ReflectionMethod(PasarelaStripe::class, 'clienteRestaurante');
        $this->assertSame('sk_test_falsa_del_rincon_9999', $metodo->invoke(new PasarelaStripe, $this->rincon->fresh())->getApiKey());
        $this->assertSame(self::SECRETA, $metodo->invoke(new PasarelaStripe, $this->tasca->fresh())->getApiKey());
    }

    public function test_pagar_al_pedir_usa_las_claves_del_restaurante_de_la_mesa(): void
    {
        $falsa = new PasarelaStripeFalsa;
        $this->app->instance(PasarelaStripe::class, $falsa);
        $mesa = Mesa::factory()->create(['restaurante_id' => $this->rincon->id, 'numero' => 5]);
        $categoria = Categoria::factory()->create(['restaurante_id' => $this->rincon->id]);
        $plato = Plato::factory()->create(['categoria_id' => $categoria->id, 'precio_centimos' => 850]);

        $this->post(route('cliente.pedido.enviar', ['slug' => 'bar-el-rincon', 'numero' => 5]), [
            'lineas' => [['plato_id' => $plato->id, 'cantidad' => 1]],
        ])->assertRedirect();

        $this->assertTrue($falsa->restauranteDeLaSesion->is($this->rincon));
        $this->assertSame($mesa->id, $falsa->sesiones[$falsa->ultimaSesion]['mesa_id']);
    }

    public function test_la_pasarela_no_cobra_sin_clave_o_con_una_clave_que_no_es_de_test(): void
    {
        $parte = $this->cuentaDividida($this->tasca)->partes->first();
        $pasarela = new PasarelaStripe;

        // Sin clave: falla ANTES de llamar a Stripe.
        try {
            $pasarela->crearSesion($this->tasca, $parte, 'Prueba', 'https://exito', 'https://cancelar');
            $this->fail('Tenía que lanzar StripeNoConfigurado.');
        } catch (StripeNoConfigurado) {
            // Bien.
        }

        // Una clave live guardada por la puerta de atrás tampoco sirve.
        $this->tasca->forceFill(['stripe_cobros_clave_secreta' => 'sk_live_falsa_1234', 'stripe_cobros_final_clave' => '1234'])->save();
        $this->expectException(StripeNoConfigurado::class);
        $pasarela->crearSesion($this->tasca->fresh(), $parte, 'Prueba', 'https://exito', 'https://cancelar');
    }

    // ---------------------------------------------------------------
    // Restaurante sin Stripe: solo efectivo
    // ---------------------------------------------------------------

    public function test_sin_stripe_configurado_no_se_ofrece_pagar_con_tarjeta(): void
    {
        $parte = $this->cuentaDividida($this->tasca)->partes->first();
        $this->mock(PasarelaStripe::class, function (MockInterface $pasarela) {
            $pasarela->shouldNotReceive('crearSesion');
            $pasarela->shouldNotReceive('crearSesionPedido');
        });

        // En la cuenta no hay botón «Tarjeta»: solo efectivo, y se explica.
        $this->get(route('cliente.cuenta', ['slug' => 'la-tasca', 'numero' => 5]))
            ->assertOk()
            ->assertSee('solo acepta pagos en')
            ->assertSee('Efectivo')
            ->assertDontSee($this->rutaParte('cliente.pago.tarjeta', $this->tasca, $parte));

        // Aunque se escriba la URL a mano, no se llega a Stripe.
        $this->get($this->rutaParte('cliente.pago.tarjeta', $this->tasca, $parte))
            ->assertRedirect(route('cliente.cuenta', ['slug' => 'la-tasca', 'numero' => 5]));
        $this->post($this->rutaParte('cliente.pago.pagar', $this->tasca, $parte))
            ->assertRedirect(route('cliente.cuenta', ['slug' => 'la-tasca', 'numero' => 5]));
        $this->assertSame('pendiente', $parte->fresh()->estado);

        // Pagar al pedir: no hay botón y, si se envía igual, no se abre el pago.
        $plato = Plato::withoutGlobalScopes()->where('restaurante_id', $this->tasca->id)->first();
        $this->get(route('cliente.pedido', ['slug' => 'la-tasca', 'numero' => 5]))
            ->assertOk()
            ->assertSee('no acepta pagos con tarjeta')
            ->assertDontSee('Pagar y enviar a cocina');
        $this->post(route('cliente.pedido.enviar', ['slug' => 'la-tasca', 'numero' => 5]), [
            'lineas' => [['plato_id' => $plato->id, 'cantidad' => 1]],
        ])->assertRedirect(route('cliente.pedido', ['slug' => 'la-tasca', 'numero' => 5]))
            ->assertSessionHasErrors('pago');
    }

    public function test_el_admin_ve_un_aviso_en_el_panel_si_no_tiene_stripe(): void
    {
        $this->actingAs($this->adminDe($this->tasca))
            ->get(route('panel'))
            ->assertOk()
            ->assertSee('tus clientes solo pueden pagar en efectivo')
            ->assertSee(route('admin.integraciones'));

        $this->actingAs($this->adminDe($this->rincon))
            ->get(route('panel'))
            ->assertOk()
            ->assertDontSee('tus clientes solo pueden pagar en efectivo');
    }

    // ---------------------------------------------------------------
    // Seeder de demo
    // ---------------------------------------------------------------

    public function test_el_seeder_conecta_la_demo_con_la_clave_de_test_del_equipo(): void
    {
        config(['services.stripe.secret' => 'sk_test_falsa_del_equipo', 'services.stripe.key' => 'pk_test_falsa_del_equipo']);

        $this->seed(RestaurantesSeeder::class);

        $demo = Restaurante::where('slug', 'cafe-central')->sole();
        $this->assertTrue($demo->tieneStripeConfigurado());
        $this->assertSame('sk_test_falsa_del_equipo', $demo->stripe_cobros_clave_secreta);
        // La Tasca ya existía sin claves: el seeder la conecta, pero no toca El Rincón (tiene las suyas).
        $this->assertSame('sk_test_falsa_del_rincon_9999', $this->rincon->fresh()->stripe_cobros_clave_secreta);
    }

    public function test_el_seeder_no_copia_una_clave_que_no_es_de_test(): void
    {
        config(['services.stripe.secret' => 'sk_live_falsa_del_equipo']);

        $this->seed(RestaurantesSeeder::class);

        $this->assertFalse(Restaurante::where('slug', 'cafe-central')->sole()->tieneStripeConfigurado());
    }
}
