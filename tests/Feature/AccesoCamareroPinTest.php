<?php

namespace Tests\Feature;

use App\Acciones\CrearPedido;
use App\Models\Categoria;
use App\Models\Dispositivo;
use App\Models\Mesa;
use App\Models\Pago;
use App\Models\Plato;
use App\Models\Restaurante;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Los camareros entran en un dispositivo de camarero emparejado: tocan su
 * nombre y escriben su PIN de 4 cifras (UserFactory::camarero() pone 1234).
 *
 * Aquí NO se usa actingAs() con camareros: se entra de verdad con el PIN.
 */
class AccesoCamareroPinTest extends TestCase
{
    use RefreshDatabase;

    private Restaurante $restaurante;

    private User $camarero;

    private Dispositivo $dispositivo;

    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->restaurante = Restaurante::factory()->create(['nombre' => 'La Tasca']);
        $this->camarero = User::factory()->camarero()->create(['name' => 'Ana', 'restaurante_id' => $this->restaurante->id]);
        $this->dispositivo = Dispositivo::create(['restaurante_id' => $this->restaurante->id, 'tipo' => 'camarero', 'nombre' => 'Móvil barra']);
        $this->token = $this->dispositivo->emparejar();
    }

    /** Petición desde el dispositivo de camarero emparejado. */
    private function desdeElDispositivo(): static
    {
        return $this->withCookie(Dispositivo::COOKIE, $this->token);
    }

    private function entrar(User $camarero, string $pin = '1234')
    {
        return $this->desdeElDispositivo()->post(route('dispositivo.entrar'), ['camarero' => $camarero->id, 'pin' => $pin]);
    }

    public function test_el_dispositivo_ensena_los_camareros_de_su_restaurante(): void
    {
        User::factory()->camarero()->create(['name' => 'Camarero de otro bar', 'restaurante_id' => Restaurante::factory()]);
        User::factory()->create(['name' => 'La Jefa', 'restaurante_id' => $this->restaurante->id]);

        $this->desdeElDispositivo()->get(route('dispositivo'))
            ->assertOk()
            ->assertSee('La Tasca')
            ->assertSee('Ana')
            ->assertDontSee('Camarero de otro bar')
            ->assertDontSee('La Jefa');
    }

    public function test_el_camarero_entra_con_su_nombre_y_su_pin(): void
    {
        $this->entrar($this->camarero)->assertRedirect(route('camarero.mesas'));

        $this->assertAuthenticatedAs($this->camarero);
        $this->desdeElDispositivo()->get(route('camarero.mesas'))->assertOk()->assertSee('Ana');
        // Si vuelve a /dispositivo, va a sus mesas.
        $this->desdeElDispositivo()->get(route('dispositivo'))->assertRedirect(route('camarero.mesas'));
    }

    public function test_con_un_pin_incorrecto_no_entra(): void
    {
        $this->entrar($this->camarero, '9999')->assertSessionHasErrors(['pin' => 'El PIN no es correcto.']);

        $this->assertGuest();
    }

    public function test_el_pin_no_sirve_sin_un_dispositivo_emparejado(): void
    {
        // Sin la cookie del dispositivo (desde casa, por ejemplo), aunque sepa el PIN.
        $this->post(route('dispositivo.entrar'), ['camarero' => $this->camarero->id, 'pin' => '1234'])->assertForbidden();

        $this->assertGuest();
    }

    public function test_el_pin_no_sirve_en_una_tablet_de_cocina(): void
    {
        $cocina = Dispositivo::create(['restaurante_id' => $this->restaurante->id, 'tipo' => 'cocina', 'nombre' => 'Cocina 1']);

        $this->withCookie(Dispositivo::COOKIE, $cocina->emparejar())
            ->post(route('dispositivo.entrar'), ['camarero' => $this->camarero->id, 'pin' => '1234'])
            ->assertForbidden();
    }

    public function test_un_camarero_de_otro_restaurante_no_entra_en_este_dispositivo(): void
    {
        $deOtroBar = User::factory()->camarero()->create(['restaurante_id' => Restaurante::factory()]);

        $this->entrar($deOtroBar)->assertNotFound();
        $this->assertGuest();
    }

    public function test_tras_5_fallos_hay_que_esperar(): void
    {
        for ($fallo = 1; $fallo <= 5; $fallo++) {
            $this->entrar($this->camarero, '0000')->assertSessionHasErrors(['pin' => 'El PIN no es correcto.']);
        }

        // Ni siquiera con el PIN bueno: hay que esperar.
        $this->entrar($this->camarero)->assertSessionHasErrors('pin');
        $this->assertGuest();

        $this->travel(61)->seconds();
        $this->entrar($this->camarero)->assertRedirect(route('camarero.mesas'));
    }

    public function test_cambiar_de_camarero_cierra_la_sesion_y_deja_el_dispositivo_emparejado(): void
    {
        $this->entrar($this->camarero);

        $this->desdeElDispositivo()->post(route('dispositivo.salir'))->assertRedirect(route('dispositivo'));

        $this->assertGuest();
        $this->assertNotNull($this->dispositivo->fresh()->token_hash);
        $this->desdeElDispositivo()->get(route('dispositivo'))->assertOk()->assertSee('Toca tu nombre');
    }

    public function test_al_desvincular_el_dispositivo_el_camarero_sale(): void
    {
        $this->entrar($this->camarero);
        $this->dispositivo->delete();

        $this->desdeElDispositivo()->get(route('camarero.mesas'))->assertRedirect(route('dispositivo'));
        $this->assertGuest();
    }

    public function test_la_sesion_del_camarero_no_sirve_sin_el_dispositivo(): void
    {
        $this->entrar($this->camarero);

        // La misma sesión, pero sin la cookie del dispositivo (otro navegador): fuera.
        $this->withUnencryptedCookie(Dispositivo::COOKIE, '')->get(route('camarero.mesas'))->assertRedirect(route('dispositivo'));
        $this->assertGuest();
    }

    public function test_el_camarero_no_tiene_perfil(): void
    {
        $this->entrar($this->camarero);

        $this->desdeElDispositivo()->get(route('perfil.editar'))->assertForbidden();
    }

    public function test_el_pago_guarda_que_camarero_lo_cobro(): void
    {
        $mesa = Mesa::factory()->create(['restaurante_id' => $this->restaurante->id, 'numero' => 3]);
        $categoria = Categoria::factory()->create(['restaurante_id' => $this->restaurante->id]);
        $plato = Plato::factory()->create(['categoria_id' => $categoria->id, 'precio_centimos' => 1000]);
        (new CrearPedido)->ejecutar($mesa, [['plato_id' => $plato->id, 'cantidad' => 1]]);

        $this->entrar($this->camarero);
        $this->desdeElDispositivo()->post(route('camarero.mesas.dividir', $mesa), ['personas' => 1])->assertSessionHasNoErrors();

        $parte = $mesa->fresh()->pedidos()->withoutGlobalScopes()->first()->cuenta()->withoutGlobalScopes()->first()->partes()->first();
        $this->desdeElDispositivo()->patch(route('camarero.mesas.cobrar', [$mesa, $parte->id]), ['metodo' => 'efectivo'])
            ->assertSessionHasNoErrors();

        $pago = Pago::sole();
        $this->assertSame($this->camarero->id, $pago->user_id);
        $this->desdeElDispositivo()->get(route('camarero.cobros'))->assertOk()->assertSee('cobró Ana');
    }
}
