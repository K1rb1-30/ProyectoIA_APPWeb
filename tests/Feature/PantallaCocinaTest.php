<?php

namespace Tests\Feature;

use App\Acciones\CrearPedido;
use App\Models\Categoria;
use App\Models\Mesa;
use App\Models\Pedido;
use App\Models\Plato;
use App\Models\Restaurante;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

// Pantalla de cocina (rama 6). Incluye el golden test nº 2.
class PantallaCocinaTest extends TestCase
{
    use RefreshDatabase;

    private Restaurante $restauranteA;

    private Restaurante $restauranteB;

    private User $cocineroA;

    private Pedido $pedidoA;

    private Pedido $pedidoB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->restauranteA = Restaurante::factory()->create(['nombre' => 'La Tasca', 'slug' => 'la-tasca']);
        $this->restauranteB = Restaurante::factory()->create(['nombre' => 'El Rincón', 'slug' => 'el-rincon']);
        $this->cocineroA = User::factory()->cocina()->create(['restaurante_id' => $this->restauranteA->id]);

        $this->pedidoA = $this->crearPedido($this->restauranteA, 4, 'Croquetas');
        $this->pedidoB = $this->crearPedido($this->restauranteB, 9, 'Pulpo a feira');
    }

    /**
     * Crea un pedido pendiente de un restaurante con la clase de la rama 5.
     */
    private function crearPedido(Restaurante $restaurante, int $numeroMesa, string $nombrePlato): Pedido
    {
        $mesa = Mesa::factory()->create(['restaurante_id' => $restaurante->id, 'numero' => $numeroMesa]);
        $categoria = Categoria::factory()->create(['restaurante_id' => $restaurante->id]);
        $plato = Plato::factory()->create(['categoria_id' => $categoria->id, 'nombre' => $nombrePlato]);

        return (new CrearPedido)->ejecutar($mesa, [['plato_id' => $plato->id, 'cantidad' => 1]]);
    }

    private function cambiarEstado(User $usuario, Pedido $pedido, string $estado)
    {
        return $this->actingAs($usuario)->patchJson(route('cocina.pedidos.estado', $pedido), ['estado' => $estado]);
    }

    // Golden test nº 2.
    public function test_no_se_puede_pasar_de_pendiente_a_listo(): void
    {
        // En el modelo: devuelve false y no cambia nada.
        $this->assertFalse($this->pedidoA->cambiarEstado('listo'));
        $this->assertSame('pendiente', $this->pedidoA->estado);
        $this->assertSame('pendiente', $this->pedidoA->fresh()->estado);

        // Desde la pantalla de cocina: error 422 y sigue pendiente.
        $this->cambiarEstado($this->cocineroA, $this->pedidoA, 'listo')
            ->assertStatus(422)
            ->assertJsonPath('mensaje', "El pedido #{$this->pedidoA->id} no puede pasar de pendiente a listo.");
        $this->assertSame('pendiente', $this->pedidoA->fresh()->estado);
    }

    public function test_el_pedido_sigue_el_orden_hasta_servido(): void
    {
        $this->assertTrue($this->pedidoA->cambiarEstado('preparando'));
        $this->assertTrue($this->pedidoA->cambiarEstado('listo'));
        $this->assertTrue($this->pedidoA->cambiarEstado('servido'));

        $this->assertSame('servido', $this->pedidoA->fresh()->estado);
    }

    public function test_no_se_puede_volver_atras_ni_cambiar_un_pedido_servido(): void
    {
        $this->pedidoA->cambiarEstado('preparando');
        $this->assertFalse($this->pedidoA->cambiarEstado('pendiente'));
        $this->assertSame('preparando', $this->pedidoA->fresh()->estado);

        $this->pedidoA->cambiarEstado('listo');
        $this->pedidoA->cambiarEstado('servido');
        $this->assertFalse($this->pedidoA->cambiarEstado('listo'));
        $this->assertSame('servido', $this->pedidoA->fresh()->estado);
    }

    public function test_un_pedido_desactualizado_no_se_salta_pasos(): void
    {
        // Dos cocineros tienen el mismo pedido en pantalla. El primero lo pasa
        // a preparando; el segundo, con su copia antigua, intenta lo mismo.
        $copiaAntigua = Pedido::find($this->pedidoA->id);
        $this->assertTrue($this->pedidoA->cambiarEstado('preparando'));

        $this->assertFalse($copiaAntigua->cambiarEstado('preparando'));
        $this->assertSame('preparando', $this->pedidoA->fresh()->estado);
    }

    public function test_la_cocina_cambia_el_estado_desde_el_tablero(): void
    {
        $this->cambiarEstado($this->cocineroA, $this->pedidoA, 'preparando')->assertOk();
        $this->assertSame('preparando', $this->pedidoA->fresh()->estado);

        // El JSON ya lo muestra en su nueva columna, con el siguiente paso.
        $this->actingAs($this->cocineroA)->getJson(route('cocina.pedidos.datos'))
            ->assertJsonPath('pedidos.0.estado', 'preparando')
            ->assertJsonPath('pedidos.0.siguiente_estado', 'listo');
    }

    public function test_el_estado_tiene_que_ser_uno_valido(): void
    {
        $this->cambiarEstado($this->cocineroA, $this->pedidoA, 'quemado')
            ->assertStatus(422)
            ->assertJsonValidationErrors('estado');
    }

    public function test_el_endpoint_no_devuelve_pedidos_servidos(): void
    {
        $this->pedidoA->cambiarEstado('preparando');
        $this->pedidoA->cambiarEstado('listo');
        $this->pedidoA->cambiarEstado('servido');

        $this->actingAs($this->cocineroA)->getJson(route('cocina.pedidos.datos'))
            ->assertOk()
            ->assertJsonCount(0, 'pedidos');
    }

    public function test_la_cocina_solo_ve_pedidos_de_su_restaurante(): void
    {
        $this->actingAs($this->cocineroA)->getJson(route('cocina.pedidos.datos'))
            ->assertOk()
            ->assertJsonCount(1, 'pedidos')
            ->assertJsonPath('pedidos.0.id', $this->pedidoA->id)
            ->assertJsonMissing(['plato' => 'Pulpo a feira']);

        // La pantalla también trae los datos iniciales filtrados.
        $this->actingAs($this->cocineroA)->get(route('cocina.pedidos'))
            ->assertOk()
            ->assertSee('Croquetas')
            ->assertDontSee('Pulpo a feira');
    }

    public function test_la_cocina_no_puede_cambiar_pedidos_de_otro_restaurante(): void
    {
        // Para la cocina A, el pedido de B no existe: 404.
        $this->cambiarEstado($this->cocineroA, $this->pedidoB, 'preparando')->assertNotFound();

        $this->assertSame('pendiente', Pedido::withoutGlobalScopes()->find($this->pedidoB->id)->estado);
    }

    public function test_un_camarero_no_puede_entrar_en_cocina(): void
    {
        $camarero = User::factory()->camarero()->create(['restaurante_id' => $this->restauranteA->id]);

        $this->actingAs($camarero)->get(route('cocina.pedidos'))->assertForbidden();
        $this->actingAs($camarero)->getJson(route('cocina.pedidos.datos'))->assertForbidden();
        $this->cambiarEstado($camarero, $this->pedidoA, 'preparando')->assertForbidden();

        $this->assertSame('pendiente', $this->pedidoA->fresh()->estado);
    }

    public function test_un_invitado_no_puede_ver_los_pedidos(): void
    {
        $this->get(route('cocina.pedidos'))->assertRedirect(route('login'));
        // Las peticiones JSON (las del polling) reciben 401 en vez de redirección.
        $this->getJson(route('cocina.pedidos.datos'))->assertUnauthorized();
    }
}
