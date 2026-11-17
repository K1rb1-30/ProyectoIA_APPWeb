<?php

namespace Tests\Feature;

use App\Acciones\CrearPedido;
use App\Models\Categoria;
use App\Models\LineaPedido;
use App\Models\Mesa;
use App\Models\Pedido;
use App\Models\Plato;
use App\Models\Restaurante;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

// Comandas del camarero (rama 5). Incluye el golden test nº 1.
class ComandasCamareroTest extends TestCase
{
    use RefreshDatabase;

    private Restaurante $restauranteA;

    private Restaurante $restauranteB;

    private User $camareroA;

    private Mesa $mesaA;

    private Plato $croquetas;

    private Plato $bravas;

    protected function setUp(): void
    {
        parent::setUp();

        $this->restauranteA = Restaurante::factory()->create(['nombre' => 'La Tasca', 'slug' => 'la-tasca']);
        $this->restauranteB = Restaurante::factory()->create(['nombre' => 'El Rincón', 'slug' => 'el-rincon']);

        $this->camareroA = User::factory()->camarero()->create(['restaurante_id' => $this->restauranteA->id]);
        $this->mesaA = Mesa::factory()->create(['restaurante_id' => $this->restauranteA->id, 'numero' => 3]);

        $tapas = Categoria::factory()->create(['restaurante_id' => $this->restauranteA->id, 'nombre' => 'Tapas']);
        $this->croquetas = Plato::factory()->create(['categoria_id' => $tapas->id, 'nombre' => 'Croquetas', 'precio_centimos' => 850]);
        $this->bravas = Plato::factory()->create(['categoria_id' => $tapas->id, 'nombre' => 'Patatas bravas', 'precio_centimos' => 600]);
    }

    /**
     * Envía la comanda como lo haría el formulario: lineas[id][plato_id], ...
     */
    private function enviarComanda(User $usuario, Mesa $mesa, array $lineas)
    {
        return $this->actingAs($usuario)->post(route('camarero.comanda.guardar', $mesa), ['lineas' => $lineas]);
    }

    // Golden test nº 1 (la parte de "aparece en cocina" llega con la rama 6).
    public function test_enviar_una_comanda_crea_el_pedido_con_sus_lineas_en_pendiente(): void
    {
        $respuesta = $this->enviarComanda($this->camareroA, $this->mesaA, [
            $this->croquetas->id => ['plato_id' => $this->croquetas->id, 'cantidad' => 2, 'notas' => 'Sin cebolla'],
            $this->bravas->id => ['plato_id' => $this->bravas->id, 'cantidad' => 1],
        ]);

        $respuesta->assertRedirect(route('camarero.mesas'));
        $respuesta->assertSessionHas('estado', 'Comanda de la mesa 3 enviada a cocina.');

        $this->assertDatabaseCount('pedidos', 1);
        $pedido = Pedido::withoutGlobalScopes()->first();
        $this->assertSame($this->restauranteA->id, $pedido->restaurante_id);
        $this->assertSame($this->mesaA->id, $pedido->mesa_id);
        $this->assertSame($this->camareroA->id, $pedido->user_id);
        $this->assertSame('pendiente', $pedido->estado);

        $this->assertDatabaseCount('lineas_pedido', 2);
        $this->assertDatabaseHas('lineas_pedido', [
            'pedido_id' => $pedido->id,
            'plato_id' => $this->croquetas->id,
            'cantidad' => 2,
            'precio_centimos' => 850,
            'notas' => 'Sin cebolla',
        ]);
        $this->assertDatabaseHas('lineas_pedido', [
            'pedido_id' => $pedido->id,
            'plato_id' => $this->bravas->id,
            'cantidad' => 1,
            'notas' => null,
        ]);

        // 2 × 8,50 + 1 × 6,00 = 23,00 €
        $this->assertSame(2300, $pedido->totalCentimos());
    }

    public function test_la_mesa_pasa_a_ocupada(): void
    {
        $this->enviarComanda($this->camareroA, $this->mesaA, [
            $this->croquetas->id => ['plato_id' => $this->croquetas->id, 'cantidad' => 1],
        ]);

        $this->assertSame('ocupada', $this->mesaA->fresh()->estado);
    }

    public function test_se_guarda_el_precio_del_momento(): void
    {
        $this->enviarComanda($this->camareroA, $this->mesaA, [
            $this->croquetas->id => ['plato_id' => $this->croquetas->id, 'cantidad' => 1],
        ]);

        // El admin sube el precio después del pedido: la línea no cambia.
        $this->croquetas->update(['precio_centimos' => 990]);

        $this->assertSame(850, LineaPedido::first()->precio_centimos);
    }

    public function test_no_se_puede_pedir_para_una_mesa_de_otro_restaurante(): void
    {
        $mesaB = Mesa::factory()->create(['restaurante_id' => $this->restauranteB->id, 'numero' => 9]);

        // La mesa se busca con el filtro del restaurante: para el camarero A no existe.
        $this->actingAs($this->camareroA)->get(route('camarero.comanda.crear', $mesaB))->assertNotFound();

        $respuesta = $this->enviarComanda($this->camareroA, $mesaB, [
            $this->croquetas->id => ['plato_id' => $this->croquetas->id, 'cantidad' => 1],
        ]);

        $respuesta->assertNotFound();
        $this->assertDatabaseCount('pedidos', 0);
        $this->assertSame('libre', $mesaB->fresh()->estado);
    }

    public function test_no_se_puede_pedir_un_plato_de_otro_restaurante(): void
    {
        $categoriaB = Categoria::factory()->create(['restaurante_id' => $this->restauranteB->id]);
        $platoB = Plato::factory()->create(['categoria_id' => $categoriaB->id]);

        $respuesta = $this->enviarComanda($this->camareroA, $this->mesaA, [
            $this->croquetas->id => ['plato_id' => $this->croquetas->id, 'cantidad' => 1],
            $platoB->id => ['plato_id' => $platoB->id, 'cantidad' => 1],
        ]);

        // Error de validación y no se guarda NADA (ni siquiera las croquetas).
        $respuesta->assertSessionHasErrors('lineas');
        $this->assertDatabaseCount('pedidos', 0);
        $this->assertDatabaseCount('lineas_pedido', 0);
        $this->assertSame('libre', $this->mesaA->fresh()->estado);
    }

    public function test_no_se_puede_pedir_un_plato_no_disponible(): void
    {
        $this->bravas->update(['disponible' => false]);

        $respuesta = $this->enviarComanda($this->camareroA, $this->mesaA, [
            $this->bravas->id => ['plato_id' => $this->bravas->id, 'cantidad' => 1],
        ]);

        $respuesta->assertSessionHasErrors('lineas');
        $this->assertDatabaseCount('pedidos', 0);
    }

    public function test_una_comanda_vacia_no_se_envia(): void
    {
        $respuesta = $this->enviarComanda($this->camareroA, $this->mesaA, []);

        $respuesta->assertSessionHasErrors('lineas');
        $this->assertDatabaseCount('pedidos', 0);
    }

    public function test_la_cocina_no_puede_crear_comandas(): void
    {
        $cocinero = User::factory()->cocina()->create(['restaurante_id' => $this->restauranteA->id]);

        $respuesta = $this->enviarComanda($cocinero, $this->mesaA, [
            $this->croquetas->id => ['plato_id' => $this->croquetas->id, 'cantidad' => 1],
        ]);

        $respuesta->assertForbidden();
        $this->assertDatabaseCount('pedidos', 0);
    }

    public function test_el_camarero_ve_sus_mesas_y_la_carta_de_su_restaurante(): void
    {
        Mesa::factory()->create(['restaurante_id' => $this->restauranteB->id, 'numero' => 77]);

        $this->actingAs($this->camareroA)->get(route('camarero.mesas'))
            ->assertOk()
            ->assertSee('Mesa 3')
            ->assertDontSee('Mesa 77');

        $this->actingAs($this->camareroA)->get(route('camarero.comanda.crear', $this->mesaA))
            ->assertOk()
            ->assertSee('Croquetas')
            ->assertSee('Enviar a cocina');
    }

    // Prepara la rama 9: el cliente pide sin login, así que user_id es nulo y
    // el filtro por restaurante lo hace la propia clase CrearPedido.
    public function test_crear_pedido_sin_usuario_logueado_guarda_user_id_nulo(): void
    {
        $categoriaB = Categoria::factory()->create(['restaurante_id' => $this->restauranteB->id]);
        $platoB = Plato::factory()->create(['categoria_id' => $categoriaB->id]);

        $pedido = (new CrearPedido)->ejecutar($this->mesaA, [
            ['plato_id' => $this->bravas->id, 'cantidad' => 3],
        ]);

        $this->assertNull($pedido->user_id);
        $this->assertSame($this->restauranteA->id, $pedido->restaurante_id);
        $this->assertSame(1800, $pedido->fresh()->totalCentimos());

        // Sin login tampoco se cuela un plato de otro restaurante.
        $this->expectException(\Illuminate\Validation\ValidationException::class);
        (new CrearPedido)->ejecutar($this->mesaA, [
            ['plato_id' => $platoB->id, 'cantidad' => 1],
        ]);
    }

    public function test_no_se_borra_una_mesa_ni_un_plato_con_pedidos(): void
    {
        $admin = User::factory()->create(['restaurante_id' => $this->restauranteA->id]);
        (new CrearPedido)->ejecutar($this->mesaA, [['plato_id' => $this->croquetas->id, 'cantidad' => 1]]);

        $this->actingAs($admin)->delete(route('admin.mesas.borrar', $this->mesaA))
            ->assertSessionHas('aviso');
        $this->actingAs($admin)->delete(route('admin.carta.borrar', $this->croquetas))
            ->assertSessionHas('aviso');

        $this->assertDatabaseHas('mesas', ['id' => $this->mesaA->id]);
        $this->assertDatabaseHas('platos', ['id' => $this->croquetas->id]);
    }
}
