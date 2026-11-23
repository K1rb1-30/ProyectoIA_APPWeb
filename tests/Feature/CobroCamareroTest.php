<?php

namespace Tests\Feature;

use App\Acciones\CrearPedido;
use App\Acciones\DividirCuenta;
use App\Models\Categoria;
use App\Models\Cuenta;
use App\Models\Mesa;
use App\Models\Pago;
use App\Models\Plato;
use App\Models\Restaurante;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Rama 12 (segunda parte): el camarero divide la cuenta, cobra en efectivo o
 * con TPV sin que el cliente use el móvil, y ve la pestaña «Cobros».
 */
class CobroCamareroTest extends TestCase
{
    use RefreshDatabase;

    private Mesa $mesa;

    private Plato $plato;

    private User $camarero;

    protected function setUp(): void
    {
        parent::setUp();

        [$this->mesa, $this->plato] = $this->mesaConPedido('la-tasca');
        $this->camarero = User::factory()->camarero()->create(['restaurante_id' => $this->mesa->restaurante_id]);
    }

    /**
     * Restaurante con una mesa y un pedido de 31,00 €, SIN cuenta todavía
     * (como cuando el camarero toma la comanda y nadie usa el QR).
     *
     * @return array{0: Mesa, 1: Plato}
     */
    private function mesaConPedido(string $slug): array
    {
        $restaurante = Restaurante::factory()->create(['slug' => $slug]);
        $mesa = Mesa::factory()->create(['restaurante_id' => $restaurante->id, 'numero' => 5]);
        $categoria = Categoria::factory()->create(['restaurante_id' => $restaurante->id]);
        $plato = Plato::factory()->create(['categoria_id' => $categoria->id, 'precio_centimos' => 3100]);

        (new CrearPedido)->ejecutar($mesa, [['plato_id' => $plato->id, 'cantidad' => 1]]);

        return [$mesa->fresh(), $plato];
    }

    private function cuenta(): Cuenta
    {
        return Cuenta::withoutGlobalScopes()->where('mesa_id', $this->mesa->id)->with('partes')->sole();
    }

    private function cobrar(int $parteId, ?string $metodo, ?Mesa $mesa = null)
    {
        return $this->actingAs($this->camarero)
            ->patch(route('camarero.mesas.cobrar', [$mesa ?? $this->mesa, $parteId]), array_filter(['metodo' => $metodo]));
    }

    public function test_el_camarero_divide_la_cuenta_de_una_mesa_sin_cuenta(): void
    {
        $this->actingAs($this->camarero)
            ->get(route('camarero.mesas.detalle', $this->mesa))
            ->assertSee('Puedes dividirla tú aquí abajo');

        $this->actingAs($this->camarero)
            ->post(route('camarero.mesas.dividir', $this->mesa), ['personas' => 3])
            ->assertRedirect(route('camarero.mesas.detalle', $this->mesa))
            ->assertSessionHasNoErrors();

        $this->assertSame([1034, 1033, 1033], $this->cuenta()->partes->pluck('importe_centimos')->all());
    }

    public function test_cobrar_en_efectivo_en_la_mesa_pasa_por_pendiente_efectivo_y_crea_el_pago(): void
    {
        $this->actingAs($this->camarero)->post(route('camarero.mesas.dividir', $this->mesa), ['personas' => 1]);
        $parte = $this->cuenta()->partes->first();

        $this->cobrar($parte->id, 'efectivo')->assertSessionHasNoErrors();

        $parte->refresh();
        $this->assertSame('pagado', $parte->estado);
        $this->assertSame('efectivo', $parte->metodo);
        $pago = Pago::sole();
        $this->assertSame('efectivo', $pago->metodo);
        $this->assertNull($pago->referencia_stripe);
        $this->assertSame(3100, $pago->importe_centimos);
    }

    public function test_cobrar_con_tpv_crea_un_pago_con_tarjeta_sin_referencia_de_stripe(): void
    {
        $this->actingAs($this->camarero)->post(route('camarero.mesas.dividir', $this->mesa), ['personas' => 2]);
        $parte = $this->cuenta()->partes->first();

        $this->cobrar($parte->id, 'tpv')->assertSessionHasNoErrors();

        $this->assertSame('pagado', $parte->fresh()->estado);
        $this->assertSame('tarjeta', $parte->fresh()->metodo);
        $this->assertNull(Pago::sole()->referencia_stripe);

        // El detalle lo enseña como TPV.
        $this->actingAs($this->camarero)->get(route('camarero.mesas.detalle', $this->mesa))->assertSee('(TPV)');
    }

    public function test_el_tpv_no_cobra_una_parte_pagada_ni_una_que_espera_efectivo(): void
    {
        $this->actingAs($this->camarero)->post(route('camarero.mesas.dividir', $this->mesa), ['personas' => 2]);
        [$p1, $p2] = $this->cuenta()->partes->all();

        $this->cobrar($p1->id, 'tpv')->assertSessionHasNoErrors();
        $this->cobrar($p1->id, 'tpv')->assertSessionHasErrors('cobro');
        $this->cobrar($p1->id, 'efectivo')->assertSessionHasErrors('cobro');

        // La persona 2 eligió efectivo en el móvil: con TPV no.
        $p2->pedirEfectivo();
        $this->cobrar($p2->id, 'tpv')->assertSessionHasErrors('cobro');
        $this->assertSame('pendiente_efectivo', $p2->fresh()->estado);

        $this->assertSame(1, Pago::count());
    }

    public function test_un_metodo_de_cobro_que_no_existe_da_error_de_validacion(): void
    {
        $this->actingAs($this->camarero)->post(route('camarero.mesas.dividir', $this->mesa), ['personas' => 1]);
        $parte = $this->cuenta()->partes->first();

        $this->cobrar($parte->id, 'bizum')->assertSessionHasErrors('metodo');
        $this->assertSame('pendiente', $parte->fresh()->estado);
    }

    public function test_el_camarero_divide_lo_pedido_despues_y_puede_cerrar(): void
    {
        $this->actingAs($this->camarero)->post(route('camarero.mesas.dividir', $this->mesa), ['personas' => 1]);
        $this->cobrar($this->cuenta()->partes->first()->id, 'tpv');

        // Piden algo más después de pagar.
        (new CrearPedido)->ejecutar($this->mesa, [['plato_id' => $this->plato->id, 'cantidad' => 1]]);
        $this->actingAs($this->camarero)->get(route('camarero.mesas.detalle', $this->mesa))->assertSee('Dividir lo pedido después');

        $this->actingAs($this->camarero)->post(route('camarero.mesas.dividir', $this->mesa), ['personas' => 1])->assertSessionHasNoErrors();
        $nueva = $this->cuenta()->partes->last();
        $this->assertSame(2, $nueva->persona);
        $this->assertSame(6200, $this->cuenta()->total_centimos);

        $this->cobrar($nueva->id, 'efectivo');
        $this->actingAs($this->camarero)->post(route('camarero.mesas.cerrar', $this->mesa))->assertRedirect(route('camarero.mesas'));
        $this->assertSame('libre', $this->mesa->fresh()->estado);
    }

    public function test_el_camarero_de_a_no_puede_dividir_ni_cobrar_en_b(): void
    {
        [$mesaB] = $this->mesaConPedido('el-rincon');
        $cuentaB = (new DividirCuenta)->ejecutar($mesaB, 'iguales', 1);

        $this->actingAs($this->camarero)->post(route('camarero.mesas.dividir', $mesaB), ['personas' => 2])->assertNotFound();
        $this->cobrar($cuentaB->partes->first()->id, 'tpv', $mesaB)->assertNotFound();
        // Y con la URL de su propia mesa, la parte de B tampoco: no es de esa mesa.
        $this->cobrar($cuentaB->partes->first()->id, 'tpv')->assertNotFound();

        $this->assertSame('pendiente', $cuentaB->partes->first()->fresh()->estado);
        $this->assertSame(1, $cuentaB->fresh()->partes()->count());
        $this->assertSame(0, Pago::count());
    }

    public function test_cobros_muestra_solo_lo_del_restaurante_del_camarero(): void
    {
        // Restaurante A: persona 1 espera efectivo y persona 2 pagó con TPV.
        $this->actingAs($this->camarero)->post(route('camarero.mesas.dividir', $this->mesa), ['personas' => 2]);
        [$p1, $p2] = $this->cuenta()->partes->all();
        $p1->pedirEfectivo();
        $this->cobrar($p2->id, 'tpv');

        // Restaurante B: una parte esperando efectivo de 99,99 € y un pago en efectivo.
        [$mesaB] = $this->mesaConPedido('el-rincon');
        $cuentaB = (new DividirCuenta)->ejecutar($mesaB, 'iguales', 2);
        $cuentaB->partes->first()->pedirEfectivo();
        $cuentaB->partes->last()->cobrarEfectivoEnMesa();

        $respuesta = $this->actingAs($this->camarero)->get(route('camarero.cobros'))->assertOk();

        $respuesta->assertSee('Esperando cobro en efectivo')
            ->assertSee(route('camarero.mesas.detalle', $this->mesa))
            ->assertDontSee(route('camarero.mesas.detalle', $mesaB))
            ->assertSee('Personas esperando para pagar en efectivo', false);

        // Totales de hoy: 15,50 € con TPV y nada en efectivo (el efectivo es de B).
        $this->assertSame(1550, $respuesta->viewData('totalTpv'));
        $this->assertSame(0, $respuesta->viewData('totalEfectivo'));
        $this->assertCount(1, $respuesta->viewData('pagosHoy'));
        $this->assertCount(1, $respuesta->viewData('esperando')->flatten());
    }
}
