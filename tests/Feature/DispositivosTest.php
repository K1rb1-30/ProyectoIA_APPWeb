<?php

namespace Tests\Feature;

use App\Acciones\CrearPedido;
use App\Models\Categoria;
use App\Models\Dispositivo;
use App\Models\Mesa;
use App\Models\Pedido;
use App\Models\Plato;
use App\Models\Restaurante;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Emparejar dispositivos: el admin genera un código, la tablet lo escribe en
 * /dispositivo y queda vinculada con una cookie. Las tablets de cocina entran
 * directas al tablero, sin usuario.
 */
class DispositivosTest extends TestCase
{
    use RefreshDatabase;

    private Restaurante $restauranteA;

    private User $adminA;

    protected function setUp(): void
    {
        parent::setUp();

        $this->restauranteA = Restaurante::factory()->create(['nombre' => 'La Tasca']);
        $this->adminA = User::factory()->create(['restaurante_id' => $this->restauranteA->id]);
    }

    /** Dispositivo ya emparejado. Devuelve [dispositivo, token de su cookie]. */
    private function emparejado(Restaurante $restaurante, string $tipo = 'cocina', string $nombre = 'Cocina 1'): array
    {
        $dispositivo = Dispositivo::create(['restaurante_id' => $restaurante->id, 'tipo' => $tipo, 'nombre' => $nombre]);

        return [$dispositivo, $dispositivo->emparejar()];
    }

    /** Pedido pendiente en la mesa $numero del restaurante. */
    private function pedido(Restaurante $restaurante, int $numero = 1): Pedido
    {
        $mesa = Mesa::factory()->create(['restaurante_id' => $restaurante->id, 'numero' => $numero]);
        $categoria = Categoria::factory()->create(['restaurante_id' => $restaurante->id]);
        $plato = Plato::factory()->create(['categoria_id' => $categoria->id]);

        return (new CrearPedido)->ejecutar($mesa, [['plato_id' => $plato->id, 'cantidad' => 1]]);
    }

    // Ojo: en los tests, getJson()/patchJson() solo envían las cookies con
    // withCredentials(). En el navegador, fetch() las envía siempre a la misma web.

    /** Ids de los pedidos que ve la tablet en el JSON del tablero. */
    private function idsQueVe(string $token): array
    {
        return collect($this->withCookie(Dispositivo::COOKIE, $token)
            ->withCredentials()->getJson(route('cocina.pedidos.datos'))
            ->assertOk()
            ->json('pedidos'))->pluck('id')->all();
    }

    public function test_el_admin_empareja_un_dispositivo_y_ve_el_codigo(): void
    {
        $respuesta = $this->actingAs($this->adminA)->post(route('admin.dispositivos.guardar'), ['tipo' => 'cocina'])
            ->assertRedirect(route('admin.dispositivos'));

        $codigo = $respuesta->getSession()->get('codigoNuevo')['codigo'];
        $this->assertMatchesRegularExpression('/^[A-Z0-9]{3}-[A-Z0-9]{3}$/', $codigo);

        // Sin nombre, se llama «Cocina 1». El código se guarda cifrado, nunca tal cual.
        $dispositivo = Dispositivo::sole();
        $this->assertSame('Cocina 1', $dispositivo->nombre);
        $this->assertSame(Dispositivo::hashCodigo($codigo), $dispositivo->codigo_hash);
        $this->assertNull($dispositivo->token_hash);

        $this->actingAs($this->adminA)->get(route('admin.dispositivos'))
            ->assertOk()
            ->assertSee('Esperando el código');
    }

    public function test_con_el_codigo_la_tablet_de_cocina_entra_directa_al_tablero(): void
    {
        $pedido = $this->pedido($this->restauranteA, 7);
        $dispositivo = Dispositivo::create(['restaurante_id' => $this->restauranteA->id, 'tipo' => 'cocina', 'nombre' => 'Tablet cocina']);
        $codigo = $dispositivo->generarCodigo();

        // Se puede escribir en minúsculas y sin guion.
        $respuesta = $this->post(route('dispositivo.emparejar'), ['codigo' => strtolower($codigo)])
            ->assertRedirect(route('dispositivo'))
            ->assertCookie(Dispositivo::COOKIE);
        $token = $respuesta->getCookie(Dispositivo::COOKIE)->getValue();

        $this->assertNotNull($dispositivo->fresh()->emparejado_en);
        $this->assertNull($dispositivo->fresh()->codigo_hash);

        // Con la cookie, /dispositivo lleva al tablero y el tablero se ve sin iniciar sesión.
        $this->withCookie(Dispositivo::COOKIE, $token)->get(route('dispositivo'))->assertRedirect(route('cocina.pedidos'));
        $this->withCookie(Dispositivo::COOKIE, $token)->get(route('cocina.pedidos'))
            ->assertOk()
            ->assertSee('Tablet cocina')
            ->assertDontSee('Salir');
        $this->assertSame([$pedido->id], $this->idsQueVe($token));
        $this->assertGuest('web');
    }

    public function test_la_tablet_de_cocina_cambia_el_estado_de_un_pedido(): void
    {
        $pedido = $this->pedido($this->restauranteA);
        [, $token] = $this->emparejado($this->restauranteA);

        $this->withCookie(Dispositivo::COOKIE, $token)
            ->withCredentials()->patchJson(route('cocina.pedidos.estado', $pedido), ['estado' => 'preparando'])
            ->assertOk();

        $this->assertSame('preparando', $pedido->fresh()->estado);
    }

    public function test_un_codigo_caducado_no_vale(): void
    {
        $dispositivo = Dispositivo::create(['restaurante_id' => $this->restauranteA->id, 'tipo' => 'cocina', 'nombre' => 'Cocina 1']);
        $codigo = $dispositivo->generarCodigo();

        $this->travel(Dispositivo::MINUTOS_CODIGO + 1)->minutes();

        $this->post(route('dispositivo.emparejar'), ['codigo' => $codigo])
            ->assertSessionHasErrors('codigo')
            ->assertCookieMissing(Dispositivo::COOKIE);
        $this->assertNull($dispositivo->fresh()->token_hash);
    }

    public function test_un_codigo_solo_sirve_una_vez(): void
    {
        $dispositivo = Dispositivo::create(['restaurante_id' => $this->restauranteA->id, 'tipo' => 'cocina', 'nombre' => 'Cocina 1']);
        $codigo = $dispositivo->generarCodigo();

        $this->post(route('dispositivo.emparejar'), ['codigo' => $codigo])->assertCookie(Dispositivo::COOKIE);
        $this->post(route('dispositivo.emparejar'), ['codigo' => $codigo])->assertSessionHasErrors('codigo');
    }

    public function test_un_codigo_nuevo_anula_el_anterior(): void
    {
        $dispositivo = Dispositivo::create(['restaurante_id' => $this->restauranteA->id, 'tipo' => 'cocina', 'nombre' => 'Cocina 1']);
        $viejo = $dispositivo->generarCodigo();

        $this->actingAs($this->adminA)->post(route('admin.dispositivos.codigo', $dispositivo))->assertSessionHas('codigoNuevo');
        auth()->logout();

        $this->post(route('dispositivo.emparejar'), ['codigo' => $viejo])->assertSessionHasErrors('codigo');
    }

    public function test_la_cocina_solo_ve_y_toca_pedidos_de_su_restaurante(): void
    {
        $restauranteB = Restaurante::factory()->create();
        $pedidoA = $this->pedido($this->restauranteA);
        $pedidoB = $this->pedido($restauranteB);
        [, $token] = $this->emparejado($this->restauranteA);

        $this->assertSame([$pedidoA->id], $this->idsQueVe($token));

        // Cambiando el id de la URL no se llega a un pedido de otro restaurante.
        $this->withCookie(Dispositivo::COOKIE, $token)
            ->withCredentials()->patchJson(route('cocina.pedidos.estado', $pedidoB), ['estado' => 'preparando'])
            ->assertNotFound();
        $this->assertSame('pendiente', $pedidoB->fresh()->estado);
    }

    public function test_dos_tablets_de_cocina_ven_los_mismos_pedidos(): void
    {
        $pedido = $this->pedido($this->restauranteA);
        [, $tokenUno] = $this->emparejado($this->restauranteA, 'cocina', 'Cocina 1');
        [, $tokenDos] = $this->emparejado($this->restauranteA, 'cocina', 'Cocina 2');

        // Una lo pasa a preparando y la otra lo ve igual.
        $this->withCookie(Dispositivo::COOKIE, $tokenUno)
            ->withCredentials()->patchJson(route('cocina.pedidos.estado', $pedido), ['estado' => 'preparando'])->assertOk();

        $estado = collect($this->withCookie(Dispositivo::COOKIE, $tokenDos)->withCredentials()->getJson(route('cocina.pedidos.datos'))->json('pedidos'))
            ->firstWhere('id', $pedido->id)['estado'];
        $this->assertSame('preparando', $estado);
        $this->assertSame($this->idsQueVe($tokenUno), $this->idsQueVe($tokenDos));
    }

    public function test_al_desvincular_la_tablet_deja_de_entrar(): void
    {
        [$dispositivo, $token] = $this->emparejado($this->restauranteA);

        $this->actingAs($this->adminA)->delete(route('admin.dispositivos.borrar', $dispositivo))
            ->assertRedirect(route('admin.dispositivos'));
        auth()->logout();

        $this->assertNull($dispositivo->fresh());
        $this->withCookie(Dispositivo::COOKIE, $token)->get(route('cocina.pedidos'))->assertRedirect(route('login'));
        $this->withCookie(Dispositivo::COOKIE, $token)->get(route('dispositivo'))->assertOk()->assertSee('Emparejar dispositivo');
    }

    public function test_el_admin_no_ve_ni_desvincula_dispositivos_de_otro_restaurante(): void
    {
        $restauranteB = Restaurante::factory()->create();
        [$dispositivoB] = $this->emparejado($restauranteB, 'cocina', 'Tablet del otro bar');

        $this->actingAs($this->adminA)->get(route('admin.dispositivos'))->assertOk()->assertDontSee('Tablet del otro bar');
        $this->actingAs($this->adminA)->delete(route('admin.dispositivos.borrar', $dispositivoB))->assertNotFound();
        $this->actingAs($this->adminA)->post(route('admin.dispositivos.codigo', $dispositivoB))->assertNotFound();

        $this->assertNotNull($dispositivoB->fresh());
    }

    // Dos tests separados porque, dentro de un mismo test, Laravel reutiliza la
    // aplicación entre peticiones y el guard de la petición anterior seguiría
    // activo. En la web real cada petición empieza de cero.

    public function test_la_tablet_de_cocina_no_entra_en_camarero(): void
    {
        [, $token] = $this->emparejado($this->restauranteA);

        // En camarero, la tablet de cocina "entra" pero su rol no es camarero: 403.
        $this->withCookie(Dispositivo::COOKIE, $token)->get(route('camarero.mesas'))->assertForbidden();
    }

    public function test_la_tablet_de_cocina_no_entra_en_administracion(): void
    {
        [, $token] = $this->emparejado($this->restauranteA);

        // El panel no usa el middleware 'dispositivo': sin sesión, al login.
        $this->withCookie(Dispositivo::COOKIE, $token)->get(route('panel'))->assertRedirect(route('login'));
    }

    public function test_camarero_no_entra_en_dispositivos(): void
    {
        $camarero = User::factory()->camarero()->create(['restaurante_id' => $this->restauranteA->id]);

        $this->actingAs($camarero)->get(route('admin.dispositivos'))->assertForbidden();
    }

    public function test_emparejar_tiene_limite_de_intentos(): void
    {
        for ($intento = 1; $intento <= 5; $intento++) {
            $this->post(route('dispositivo.emparejar'), ['codigo' => 'MAL-MAL'])->assertSessionHasErrors('codigo');
        }

        $this->post(route('dispositivo.emparejar'), ['codigo' => 'MAL-MAL'])->assertStatus(429);
    }

    public function test_un_dispositivo_de_camarero_emparejado_ve_su_pantalla(): void
    {
        [, $token] = $this->emparejado($this->restauranteA, 'camarero', 'Móvil barra');

        $this->withCookie(Dispositivo::COOKIE, $token)->get(route('dispositivo'))
            ->assertOk()
            ->assertSee('La Tasca')
            ->assertSee('Móvil barra');
    }
}
