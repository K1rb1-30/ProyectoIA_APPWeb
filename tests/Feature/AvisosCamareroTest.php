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

// Avisos de plato listo para el camarero (rama 7).
class AvisosCamareroTest extends TestCase
{
    use RefreshDatabase;

    private User $camareroA;

    private Pedido $pedidoListoA;

    private Pedido $pedidoPendienteA;

    private Pedido $pedidoListoB;

    protected function setUp(): void
    {
        parent::setUp();

        $restauranteA = Restaurante::factory()->create(['slug' => 'la-tasca']);
        $restauranteB = Restaurante::factory()->create(['slug' => 'el-rincon']);
        $this->camareroA = User::factory()->camarero()->create(['restaurante_id' => $restauranteA->id]);

        $this->pedidoListoA = $this->crearPedido($restauranteA, 4, 'Croquetas', 'listo');
        $this->pedidoPendienteA = $this->crearPedido($restauranteA, 5, 'Tortilla', 'pendiente');
        $this->pedidoListoB = $this->crearPedido($restauranteB, 9, 'Pulpo a feira', 'listo');
    }

    /**
     * Crea un pedido con la clase de la rama 5 y lo deja en $estado
     * siguiendo los pasos permitidos (pendiente → preparando → listo).
     */
    private function crearPedido(Restaurante $restaurante, int $numeroMesa, string $nombrePlato, string $estado): Pedido
    {
        $mesa = Mesa::factory()->create(['restaurante_id' => $restaurante->id, 'numero' => $numeroMesa]);
        $categoria = Categoria::factory()->create(['restaurante_id' => $restaurante->id]);
        $plato = Plato::factory()->create(['categoria_id' => $categoria->id, 'nombre' => $nombrePlato]);

        $pedido = (new CrearPedido)->ejecutar($mesa, [['plato_id' => $plato->id, 'cantidad' => 2]]);

        foreach (['preparando', 'listo'] as $paso) {
            if ($pedido->estado === $estado) {
                break;
            }
            $pedido->cambiarEstado($paso);
        }

        return $pedido;
    }

    public function test_el_camarero_solo_ve_pedidos_listos_de_su_restaurante(): void
    {
        $this->actingAs($this->camareroA)
            ->getJson(route('camarero.avisos.datos'))
            ->assertOk()
            ->assertJsonCount(1, 'pedidos')
            ->assertJsonPath('pedidos.0.id', $this->pedidoListoA->id)
            ->assertJsonPath('pedidos.0.mesa', 4)
            ->assertJsonPath('pedidos.0.lineas.0.plato', 'Croquetas')
            ->assertJsonPath('pedidos.0.lineas.0.cantidad', 2);
    }

    public function test_la_pantalla_de_avisos_se_abre(): void
    {
        $this->actingAs($this->camareroA)
            ->get(route('camarero.avisos'))
            ->assertOk()
            ->assertSee('Listos para servir');
    }

    public function test_marcar_como_servido_cambia_el_estado(): void
    {
        $this->actingAs($this->camareroA)
            ->patchJson(route('camarero.avisos.servido', $this->pedidoListoA))
            ->assertOk();

        $this->assertSame('servido', $this->pedidoListoA->fresh()->estado);

        // Y ya no sale en los avisos.
        $this->actingAs($this->camareroA)
            ->getJson(route('camarero.avisos.datos'))
            ->assertJsonCount(0, 'pedidos');
    }

    public function test_no_se_puede_servir_un_pedido_que_no_esta_listo(): void
    {
        $this->actingAs($this->camareroA)
            ->patchJson(route('camarero.avisos.servido', $this->pedidoPendienteA))
            ->assertStatus(422);

        $this->assertSame('pendiente', $this->pedidoPendienteA->fresh()->estado);
    }

    public function test_no_se_puede_servir_dos_veces(): void
    {
        $this->actingAs($this->camareroA)->patchJson(route('camarero.avisos.servido', $this->pedidoListoA))->assertOk();
        $this->actingAs($this->camareroA)->patchJson(route('camarero.avisos.servido', $this->pedidoListoA))->assertStatus(422);
    }

    public function test_no_se_puede_servir_un_pedido_de_otro_restaurante(): void
    {
        $this->actingAs($this->camareroA)
            ->patchJson(route('camarero.avisos.servido', $this->pedidoListoB))
            ->assertNotFound();

        $this->assertSame('listo', Pedido::withoutGlobalScope('restaurante')->find($this->pedidoListoB->id)->estado);
    }

    public function test_cocina_y_visitantes_no_acceden_a_los_avisos(): void
    {
        $cocinero = User::factory()->cocina()->create(['restaurante_id' => $this->camareroA->restaurante_id]);

        $this->actingAs($cocinero)->getJson(route('camarero.avisos.datos'))->assertForbidden();
        $this->actingAs($cocinero)->patchJson(route('camarero.avisos.servido', $this->pedidoListoA))->assertForbidden();
        $this->assertSame('listo', $this->pedidoListoA->fresh()->estado);

        auth()->logout();
        $this->get(route('camarero.avisos'))->assertRedirect(route('login'));
    }
}
