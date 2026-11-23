<?php

namespace Tests\Feature;

use App\Acciones\CrearPedido;
use App\Models\Categoria;
use App\Models\Cuenta;
use App\Models\Mesa;
use App\Models\Pedido;
use App\Models\Plato;
use App\Models\Restaurante;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Rama 10: el cliente pide la cuenta y la divide (a partes iguales o por platos).
 */
class DividirCuentaTest extends TestCase
{
    use RefreshDatabase;

    private Mesa $mesa;

    private Pedido $pedido;

    protected function setUp(): void
    {
        parent::setUp();

        $restaurante = Restaurante::factory()->create(['slug' => 'la-tasca']);
        $this->mesa = Mesa::factory()->create(['restaurante_id' => $restaurante->id, 'numero' => 5]);
        $categoria = Categoria::factory()->create(['restaurante_id' => $restaurante->id]);
        $croquetas = Plato::factory()->create(['categoria_id' => $categoria->id, 'nombre' => 'Croquetas', 'precio_centimos' => 850]);
        $bravas = Plato::factory()->create(['categoria_id' => $categoria->id, 'nombre' => 'Bravas', 'precio_centimos' => 600]);
        $cana = Plato::factory()->create(['categoria_id' => $categoria->id, 'nombre' => 'Caña', 'precio_centimos' => 275]);

        $agua = Plato::factory()->create(['categoria_id' => $categoria->id, 'nombre' => 'Agua', 'precio_centimos' => 250]);

        // Pedido de 31,00 € (como en la maqueta):
        // 2 croquetas (17,00) + 1 bravas (6,00) + 2 cañas (5,50) + 1 agua (2,50) = 31,00 €.
        $this->pedido = (new CrearPedido)->ejecutar($this->mesa, [
            ['plato_id' => $croquetas->id, 'cantidad' => 2],
            ['plato_id' => $bravas->id, 'cantidad' => 1],
            ['plato_id' => $cana->id, 'cantidad' => 2],
            ['plato_id' => $agua->id, 'cantidad' => 1],
        ]);
    }

    private function ruta(string $nombre, int $numero = 5): string
    {
        return route($nombre, ['slug' => 'la-tasca', 'numero' => $numero]);
    }

    private function cuenta(): Cuenta
    {
        return Cuenta::withoutGlobalScopes()->with('partes')->sole();
    }

    public function test_la_pantalla_de_cuenta_muestra_el_total_de_la_mesa(): void
    {
        $this->get($this->ruta('cliente.cuenta'))
            ->assertOk()
            ->assertSee('31,00')
            ->assertSee('Dividir la cuenta');
    }

    /*
     * Golden test nº 3 (de punta a punta): dividir 31,00 € entre 3.
     */
    public function test_dividir_entre_3_crea_tres_partes_que_suman_el_total(): void
    {
        $this->post($this->ruta('cliente.cuenta.dividir'), ['modo' => 'iguales', 'personas' => 3])
            ->assertRedirect($this->ruta('cliente.cuenta'));

        $cuenta = $this->cuenta();
        $this->assertSame(3100, $cuenta->total_centimos);
        $this->assertSame('abierta', $cuenta->estado);
        $this->assertSame([1034, 1033, 1033], $cuenta->partes->pluck('importe_centimos')->all());
        $this->assertSame([1, 2, 3], $cuenta->partes->pluck('persona')->all());
        $this->assertSame(3100, $cuenta->partes->sum('importe_centimos'));
        $this->assertTrue($cuenta->partes->every(fn ($parte) => $parte->estado === 'pendiente' && $parte->metodo === null));

        // El pedido queda apuntado en la cuenta.
        $this->assertSame($cuenta->id, $this->pedido->fresh()->cuenta_id);
    }

    public function test_dividir_por_platos_suma_el_total(): void
    {
        $lineas = $this->pedido->lineas;

        // Croquetas y caña para la persona 1; bravas y agua para la persona 3.
        // La persona 3 se renumera como 2 (no quedan huecos).
        $asignaciones = [];
        foreach ($lineas as $linea) {
            $asignaciones[$linea->id] = in_array($linea->plato->nombre, ['Croquetas', 'Caña']) ? 1 : 3;
        }

        $this->post($this->ruta('cliente.cuenta.dividir'), ['modo' => 'platos', 'asignaciones' => $asignaciones])
            ->assertSessionHasNoErrors();

        $cuenta = $this->cuenta();
        $this->assertSame([1, 2], $cuenta->partes->pluck('persona')->all());
        $this->assertSame([1700 + 550, 600 + 250], $cuenta->partes->pluck('importe_centimos')->all());
        $this->assertSame($cuenta->total_centimos, $cuenta->partes->sum('importe_centimos'));
    }

    public function test_por_platos_hay_que_asignar_todos_los_platos(): void
    {
        $primeraLinea = $this->pedido->lineas->first();

        $this->post($this->ruta('cliente.cuenta.dividir'), ['modo' => 'platos', 'asignaciones' => [$primeraLinea->id => 1]])
            ->assertSessionHasErrors('asignaciones');

        $this->assertSame(0, Cuenta::withoutGlobalScopes()->count());
    }

    public function test_volver_a_dividir_sustituye_las_partes(): void
    {
        $this->post($this->ruta('cliente.cuenta.dividir'), ['modo' => 'iguales', 'personas' => 3]);
        $this->post($this->ruta('cliente.cuenta.dividir'), ['modo' => 'iguales', 'personas' => 4]);

        $cuenta = $this->cuenta();
        $this->assertCount(4, $cuenta->partes);
        $this->assertSame([775, 775, 775, 775], $cuenta->partes->pluck('importe_centimos')->all());
        // Sigue habiendo una sola cuenta.
        $this->assertSame(1, Cuenta::withoutGlobalScopes()->count());
    }

    public function test_no_se_puede_volver_a_dividir_si_ya_hay_pagos(): void
    {
        $this->post($this->ruta('cliente.cuenta.dividir'), ['modo' => 'iguales', 'personas' => 2]);
        // Simulamos que la persona 1 ya ha pagado (lo harán las ramas 11 y 12).
        $this->cuenta()->partes->first()->update(['estado' => 'pagado', 'metodo' => 'tarjeta']);

        $this->post($this->ruta('cliente.cuenta.dividir'), ['modo' => 'iguales', 'personas' => 3])
            ->assertSessionHasErrors('cuenta');

        $this->assertCount(2, $this->cuenta()->partes);
    }

    public function test_un_pedido_hecho_despues_de_dividir_avisa_y_entra_al_volver_a_dividir(): void
    {
        $this->post($this->ruta('cliente.cuenta.dividir'), ['modo' => 'iguales', 'personas' => 2]);

        // Piden algo más después de dividir.
        $postre = Plato::factory()->create(['categoria_id' => Categoria::withoutGlobalScopes()->first()->id, 'precio_centimos' => 400]);
        (new CrearPedido)->ejecutar($this->mesa, [['plato_id' => $postre->id, 'cantidad' => 1]]);

        $this->get($this->ruta('cliente.cuenta'))->assertSee('Volved a dividirla');

        $this->post($this->ruta('cliente.cuenta.dividir'), ['modo' => 'iguales', 'personas' => 2]);
        $this->assertSame(3500, $this->cuenta()->total_centimos);
        $this->assertSame([1750, 1750], $this->cuenta()->partes->pluck('importe_centimos')->all());
    }

    public function test_una_mesa_sin_pedidos_no_puede_dividir_la_cuenta(): void
    {
        Mesa::factory()->create(['restaurante_id' => $this->mesa->restaurante_id, 'numero' => 6]);

        $this->get($this->ruta('cliente.cuenta', 6))->assertOk()->assertSee('Todavía no hay nada que pagar');
        $this->post($this->ruta('cliente.cuenta.dividir', 6), ['modo' => 'iguales', 'personas' => 2])
            ->assertSessionHasErrors('cuenta');
    }

    public function test_validacion_del_numero_de_personas(): void
    {
        $this->post($this->ruta('cliente.cuenta.dividir'), ['modo' => 'iguales', 'personas' => 0])->assertSessionHasErrors('personas');
        $this->post($this->ruta('cliente.cuenta.dividir'), ['modo' => 'iguales', 'personas' => 21])->assertSessionHasErrors('personas');
        $this->post($this->ruta('cliente.cuenta.dividir'), ['modo' => 'raro'])->assertSessionHasErrors('modo');
    }

    /*
     * Aislamiento: la cuenta de una mesa no incluye pedidos de otra mesa ni
     * de otro restaurante, y una mesa de otro restaurante da 404.
     */
    public function test_la_cuenta_solo_incluye_los_pedidos_de_su_mesa(): void
    {
        // Pedido en otra mesa del mismo restaurante.
        $otraMesa = Mesa::factory()->create(['restaurante_id' => $this->mesa->restaurante_id, 'numero' => 7]);
        $plato = Plato::withoutGlobalScopes()->where('nombre', 'Bravas')->first();
        (new CrearPedido)->ejecutar($otraMesa, [['plato_id' => $plato->id, 'cantidad' => 3]]);

        $this->post($this->ruta('cliente.cuenta.dividir'), ['modo' => 'iguales', 'personas' => 1]);

        $this->assertSame(3100, $this->cuenta()->total_centimos);
    }

    public function test_un_admin_de_otro_restaurante_logueado_ve_la_cuenta_del_qr(): void
    {
        $this->actingAs(User::factory()->create())
            ->get($this->ruta('cliente.cuenta'))
            ->assertOk()
            ->assertSee('31,00');
    }

    public function test_la_pestana_cuenta_de_la_carta_enlaza_a_la_cuenta(): void
    {
        $this->get($this->ruta('cliente.carta'))->assertOk()->assertSee($this->ruta('cliente.cuenta'));
    }
}
