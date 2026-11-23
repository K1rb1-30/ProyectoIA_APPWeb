<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Mesa;
use App\Models\Pedido;
use App\Models\Plato;
use App\Models\Restaurante;
use App\Servicios\PasarelaStripe;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Fakes\PasarelaStripeFalsa;
use Tests\TestCase;

/**
 * Rama 9: autopedido del cliente desde el QR (sin login).
 *
 * Desde la rama 15 el pedido se paga con tarjeta antes de ir a cocina, así
 * que "enviar" es: pulsar «Pagar y enviar a cocina» y volver de Stripe con el
 * pago hecho. Stripe es de mentira (PasarelaStripeFalsa). Lo propio del pago
 * (cuenta, partes, errores de Stripe) está en PagarAlPedirTest.
 */
class AutopedidoTest extends TestCase
{
    use RefreshDatabase;

    private Restaurante $restaurante;

    private Mesa $mesa;

    private Plato $croquetas;

    private Plato $bravas;

    private PasarelaStripeFalsa $stripe;

    protected function setUp(): void
    {
        parent::setUp();

        $this->stripe = new PasarelaStripeFalsa;
        $this->app->instance(PasarelaStripe::class, $this->stripe);

        $this->restaurante = Restaurante::factory()->create(['slug' => 'la-tasca']);
        $this->mesa = Mesa::factory()->create(['restaurante_id' => $this->restaurante->id, 'numero' => 5]);
        $categoria = Categoria::factory()->create(['restaurante_id' => $this->restaurante->id]);
        $this->croquetas = Plato::factory()->create(['categoria_id' => $categoria->id, 'nombre' => 'Croquetas', 'precio_centimos' => 850]);
        $this->bravas = Plato::factory()->create(['categoria_id' => $categoria->id, 'nombre' => 'Bravas', 'precio_centimos' => 600]);
    }

    private function ruta(string $nombre, int $numero = 5): string
    {
        return route($nombre, ['slug' => 'la-tasca', 'numero' => $numero]);
    }

    /** Pulsa «Pagar y enviar a cocina» (todavía no se crea nada). */
    private function enviar(array $lineas, int $numero = 5)
    {
        return $this->post($this->ruta('cliente.pedido.enviar', $numero), ['lineas' => $lineas]);
    }

    /** Paga en Stripe y vuelve: ahora sí se crea el pedido. */
    private function pagarYEnviar(array $lineas, int $numero = 5)
    {
        $this->enviar($lineas, $numero)->assertRedirect();

        return $this->get($this->ruta('cliente.pedido.pagado', $numero).'?session_id='.$this->stripe->ultimaSesion);
    }

    /*
     * Golden test nº 1 ampliado: el pedido que hace el cliente crea el
     * pedido (sin usuario) con sus líneas y el precio del momento.
     */
    public function test_el_cliente_paga_su_pedido_y_se_crea_con_sus_lineas(): void
    {
        $respuesta = $this->pagarYEnviar([
            ['plato_id' => $this->croquetas->id, 'cantidad' => 2, 'notas' => 'Sin cebolla'],
            ['plato_id' => $this->bravas->id, 'cantidad' => 1],
        ]);

        $respuesta->assertRedirect($this->ruta('cliente.estado'));
        $respuesta->assertSessionHas('vaciarCarrito', true);

        $pedido = Pedido::withoutGlobalScopes()->with('lineas')->sole();
        $this->assertNull($pedido->user_id);
        $this->assertSame('pendiente', $pedido->estado);
        $this->assertSame($this->mesa->id, $pedido->mesa_id);
        $this->assertSame($this->restaurante->id, $pedido->restaurante_id);
        $this->assertCount(2, $pedido->lineas);
        $this->assertSame(2 * 850 + 600, $pedido->totalCentimos());
        $this->assertSame('Sin cebolla', $pedido->lineas->firstWhere('plato_id', $this->croquetas->id)->notas);

        // La mesa pasa a ocupada.
        $this->assertSame('ocupada', $this->mesa->fresh()->estado);
    }

    public function test_el_precio_es_el_del_servidor_aunque_el_cliente_mande_otro(): void
    {
        // El formulario no manda precio; aunque se añada a mano, se ignora:
        // Stripe cobra el precio del servidor.
        $this->pagarYEnviar([['plato_id' => $this->croquetas->id, 'cantidad' => 1, 'precio_centimos' => 1]]);

        $this->assertSame(850, $this->stripe->sesiones['cs_test_1']['importe']);
        $this->assertSame(850, Pedido::withoutGlobalScopes()->sole()->totalCentimos());
    }

    /*
     * Golden test nº 5 (segunda mitad): un plato no disponible no se puede pedir.
     */
    public function test_no_se_puede_pedir_un_plato_agotado(): void
    {
        $this->croquetas->update(['disponible' => false]);

        $this->from($this->ruta('cliente.pedido'))
            ->enviar([['plato_id' => $this->croquetas->id, 'cantidad' => 1]])
            ->assertRedirect($this->ruta('cliente.pedido'))
            ->assertSessionHasErrors('lineas');

        // Ni se va a Stripe ni se crea nada.
        $this->assertSame([], $this->stripe->sesiones);
        $this->assertSame(0, Pedido::withoutGlobalScopes()->count());
    }

    public function test_no_se_puede_pedir_un_plato_de_otro_restaurante(): void
    {
        $platoAjeno = Plato::factory()->create();

        $this->enviar([['plato_id' => $platoAjeno->id, 'cantidad' => 1]])->assertSessionHasErrors('lineas');

        $this->assertSame([], $this->stripe->sesiones);
    }

    public function test_un_pedido_vacio_o_con_cantidades_raras_no_se_envia(): void
    {
        $this->enviar([])->assertSessionHasErrors('lineas');
        $this->enviar([['plato_id' => $this->croquetas->id, 'cantidad' => 0]])->assertSessionHasErrors('lineas.0.cantidad');
        $this->enviar([['plato_id' => $this->croquetas->id, 'cantidad' => 100]])->assertSessionHasErrors('lineas.0.cantidad');

        $this->assertSame([], $this->stripe->sesiones);
    }

    public function test_pedir_en_una_mesa_que_no_existe_da_404(): void
    {
        $this->enviar([['plato_id' => $this->croquetas->id, 'cantidad' => 1]], 99)->assertNotFound();
    }

    public function test_la_pantalla_de_estado_muestra_el_pedido_recien_pagado(): void
    {
        $this->pagarYEnviar([['plato_id' => $this->croquetas->id, 'cantidad' => 2]]);

        $this->get($this->ruta('cliente.estado'))
            ->assertOk()
            ->assertSee('Croquetas')
            ->assertSee('Recibido en cocina');
    }

    public function test_otro_navegador_no_ve_los_pedidos_ajenos(): void
    {
        $this->pagarYEnviar([['plato_id' => $this->croquetas->id, 'cantidad' => 1]]);
        $this->assertSame(1, Pedido::withoutGlobalScopes()->count());

        // flushSession simula otro navegador: sin los ids guardados en sesión.
        $this->flushSession();

        $this->get($this->ruta('cliente.estado'))
            ->assertOk()
            ->assertDontSee('Croquetas')
            ->assertSee('Todavía no has hecho ningún pedido');
    }

    public function test_un_pedido_de_otra_mesa_no_sale_aunque_este_en_la_sesion(): void
    {
        $this->pagarYEnviar([['plato_id' => $this->croquetas->id, 'cantidad' => 1]]);
        Mesa::factory()->create(['restaurante_id' => $this->restaurante->id, 'numero' => 6]);

        // El pedido es de la mesa 5: en la pantalla de la mesa 6 no sale.
        $this->get($this->ruta('cliente.estado', 6))->assertOk()->assertDontSee('Croquetas');
    }

    public function test_los_datos_del_estado_cambian_cuando_cocina_avanza_el_pedido(): void
    {
        $this->pagarYEnviar([['plato_id' => $this->croquetas->id, 'cantidad' => 1]]);
        $pedido = Pedido::withoutGlobalScopes()->sole();

        $this->getJson($this->ruta('cliente.estado.datos'))
            ->assertOk()
            ->assertExactJson(['pedidos' => [['id' => $pedido->id, 'estado' => 'pendiente']]]);

        $pedido->update(['estado' => 'preparando']);

        $this->getJson($this->ruta('cliente.estado.datos'))
            ->assertJsonPath('pedidos.0.estado', 'preparando');
    }

    public function test_la_carta_tiene_el_boton_anadir_y_mi_pedido_se_abre(): void
    {
        $this->get($this->ruta('cliente.carta'))->assertOk()->assertSee('Añadir')->assertSee('Ver pedido');

        $this->get($this->ruta('cliente.pedido'))
            ->assertOk()
            ->assertSee('Mi pedido')
            ->assertSee('Pagar y enviar a cocina')
            ->assertSee('Llama al camarero', false)
            ->assertSee('Croquetas'); // Viene en los datos de los platos para Alpine.
    }

    public function test_mi_pedido_no_trae_los_platos_agotados(): void
    {
        $this->bravas->update(['disponible' => false]);

        $this->get($this->ruta('cliente.pedido'))->assertOk()->assertDontSee('Bravas');
    }

    public function test_no_se_pueden_intentar_mas_de_10_pagos_por_minuto(): void
    {
        for ($i = 0; $i < 10; $i++) {
            $this->enviar([['plato_id' => $this->croquetas->id, 'cantidad' => 1]])->assertRedirect();
        }

        // El undécimo, dentro del mismo minuto, se rechaza con 429 (demasiadas peticiones).
        $this->enviar([['plato_id' => $this->croquetas->id, 'cantidad' => 1]])->assertStatus(429);
        $this->assertCount(10, $this->stripe->sesiones);
    }
}
