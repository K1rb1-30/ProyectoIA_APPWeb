<?php

namespace Tests\Feature;

use App\Acciones\CrearPedido;
use App\Acciones\DividirCuenta;
use App\Models\Categoria;
use App\Models\Cuenta;
use App\Models\Mesa;
use App\Models\Pago;
use App\Models\ParteCuenta;
use App\Models\Plato;
use App\Models\Restaurante;
use App\Models\User;
use App\Servicios\PasarelaStripe;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use Tests\TestCase;

/**
 * Rama 12: pago en efectivo (cliente elige, camarero confirma) y cierre de mesa.
 */
class EfectivoYCierreTest extends TestCase
{
    use RefreshDatabase;

    private Mesa $mesa;

    private Cuenta $cuenta;

    private User $camarero;

    protected function setUp(): void
    {
        parent::setUp();

        [$this->mesa, $this->cuenta] = $this->mesaConCuentaDividida('la-tasca', 5);
        $this->camarero = User::factory()->camarero()->create(['restaurante_id' => $this->mesa->restaurante_id]);
    }

    /**
     * Restaurante con una mesa, un pedido de 31,00 € y la cuenta dividida
     * entre 3 (10,34 + 10,33 + 10,33). La mesa queda "ocupada".
     *
     * @return array{0: Mesa, 1: Cuenta}
     */
    private function mesaConCuentaDividida(string $slug, int $numero): array
    {
        $restaurante = Restaurante::factory()->create(['slug' => $slug]);
        $mesa = Mesa::factory()->create(['restaurante_id' => $restaurante->id, 'numero' => $numero]);
        $categoria = Categoria::factory()->create(['restaurante_id' => $restaurante->id]);
        $plato = Plato::factory()->create(['categoria_id' => $categoria->id, 'precio_centimos' => 3100]);

        (new CrearPedido)->ejecutar($mesa, [['plato_id' => $plato->id, 'cantidad' => 1]]);
        $cuenta = (new DividirCuenta)->ejecutar($mesa, 'iguales', 3);

        return [$mesa->fresh(), $cuenta];
    }

    private function parte(int $persona): ParteCuenta
    {
        return $this->cuenta->partes()->where('persona', $persona)->sole();
    }

    /** URL pública del cliente para una parte (la-tasca, mesa 5). */
    private function rutaCliente(string $nombre, ParteCuenta $parte): string
    {
        return route($nombre, ['slug' => 'la-tasca', 'numero' => 5, 'parte' => $parte->id]);
    }

    private function cobrar(ParteCuenta $parte, ?Mesa $mesa = null)
    {
        return $this->actingAs($this->camarero)
            ->patch(route('camarero.mesas.cobrar', [$mesa ?? $this->mesa, $parte->id]));
    }

    private function cerrar(?Mesa $mesa = null)
    {
        return $this->actingAs($this->camarero)->post(route('camarero.mesas.cerrar', $mesa ?? $this->mesa));
    }

    /*
     * Golden test nº 4: pagos mixtos. Una parte con tarjeta y dos en efectivo.
     * La mesa no se cierra hasta que está todo cobrado.
     */
    public function test_pagos_mixtos_la_mesa_solo_se_cierra_cuando_todo_esta_pagado(): void
    {
        [$p1, $p2, $p3] = [$this->parte(1), $this->parte(2), $this->parte(3)];

        // Persona 1: tarjeta. Stripe (simulado) confirma la sesión al volver.
        $this->mock(PasarelaStripe::class, fn (MockInterface $pasarela) => $pasarela
            ->shouldReceive('consultarSesion')
            ->andReturn(['pagada' => true, 'parte_cuenta_id' => $p1->id, 'importe_centimos' => 1034, 'moneda' => 'eur']));
        $this->get($this->rutaCliente('cliente.pago.exito', $p1).'?session_id=cs_test_1')->assertOk();

        // Personas 2 y 3: eligen efectivo desde su móvil.
        $this->post($this->rutaCliente('cliente.pago.efectivo.elegir', $p2))->assertRedirect($this->rutaCliente('cliente.pago.efectivo', $p2));
        $this->post($this->rutaCliente('cliente.pago.efectivo.elegir', $p3))->assertRedirect($this->rutaCliente('cliente.pago.efectivo', $p3));

        // El camarero cobra a la persona 2...
        $this->cobrar($p2)->assertSessionHasNoErrors();

        // ...e intenta cerrar: falta la persona 3. No se cierra.
        $this->cerrar()->assertRedirect(route('camarero.mesas.detalle', $this->mesa))->assertSessionHasErrors('cuenta');
        $this->assertSame('ocupada', $this->mesa->fresh()->estado);
        $this->assertSame('abierta', $this->cuenta->fresh()->estado);

        // Cobra a la persona 3 y ahora sí se cierra.
        $this->cobrar($p3)->assertSessionHasNoErrors();
        $this->cerrar()->assertRedirect(route('camarero.mesas'))->assertSessionHasNoErrors();

        $this->assertSame('libre', $this->mesa->fresh()->estado);
        $this->assertSame('cerrada', $this->cuenta->fresh()->estado);

        // Tres pagos: uno con tarjeta (con referencia) y dos en efectivo (sin ella).
        $this->assertSame(3100, (int) Pago::sum('importe_centimos'));
        // sort() en PHP: MySQL ordena un enum por su posición, no por el texto.
        $this->assertSame(['efectivo', 'efectivo', 'tarjeta'], Pago::pluck('metodo')->sort()->values()->all());
        $this->assertSame(['cs_test_1'], Pago::whereNotNull('referencia_stripe')->pluck('referencia_stripe')->all());
    }

    public function test_una_parte_no_pasa_de_pendiente_a_pagado_en_efectivo_sin_pendiente_efectivo(): void
    {
        $parte = $this->parte(1);

        // En el modelo: confirmarEfectivo() desde "pendiente" no hace nada.
        $this->assertFalse($parte->confirmarEfectivo());
        $this->assertSame('pendiente', $parte->fresh()->estado);

        // Por la ruta del camarero, tampoco.
        $this->cobrar($parte)->assertSessionHasErrors('cobro');
        $this->assertSame('pendiente', $parte->fresh()->estado);
        $this->assertSame(0, Pago::count());

        // Con el paso intermedio, sí.
        $this->assertTrue($parte->pedirEfectivo());
        $this->assertSame('efectivo', $parte->fresh()->metodo);
        $this->assertTrue($parte->confirmarEfectivo());
        $this->assertSame('pagado', $parte->fresh()->estado);
    }

    public function test_una_parte_pagada_no_se_puede_volver_a_cobrar(): void
    {
        $parte = $this->parte(1);
        $parte->pedirEfectivo();

        $this->cobrar($parte)->assertSessionHasNoErrors();
        $this->cobrar($parte)->assertSessionHasErrors('cobro');

        $this->assertSame(1, Pago::count());
        // Tampoco se puede pagar con tarjeta ni volver a elegir efectivo.
        $this->assertFalse($parte->fresh()->pagarConTarjeta('cs_test_2'));
        $this->assertFalse($parte->fresh()->pedirEfectivo());
        $this->assertSame(1, Pago::count());
    }

    public function test_el_camarero_de_a_no_puede_cobrar_ni_cerrar_mesas_de_b(): void
    {
        [$mesaB, $cuentaB] = $this->mesaConCuentaDividida('el-rincon', 5);
        $parteB = $cuentaB->partes->first();
        $parteB->pedirEfectivo();

        // La mesa de B no existe para el camarero de A (global scope): 404.
        $this->actingAs($this->camarero)->get(route('camarero.mesas.detalle', $mesaB))->assertNotFound();
        $this->cobrar($parteB, $mesaB)->assertNotFound();
        $this->cerrar($mesaB)->assertNotFound();

        $this->assertSame('pendiente_efectivo', $parteB->fresh()->estado);
        $this->assertSame('abierta', $cuentaB->fresh()->estado);
        $this->assertSame(0, Pago::count());
    }

    public function test_no_se_cobra_una_parte_de_otra_mesa_con_la_url_de_esta(): void
    {
        // Parte de B metida en la URL de la mesa de A: no es de esta mesa → 404.
        [, $cuentaB] = $this->mesaConCuentaDividida('el-rincon', 5);
        $parteB = $cuentaB->partes->first();
        $parteB->pedirEfectivo();

        $this->cobrar($parteB)->assertNotFound();
        $this->assertSame('pendiente_efectivo', $parteB->fresh()->estado);
    }

    public function test_el_cliente_no_puede_marcar_su_parte_como_pagada(): void
    {
        $parte = $this->parte(1);

        // Lo máximo que hace el cliente es pedir efectivo.
        $this->post($this->rutaCliente('cliente.pago.efectivo.elegir', $parte));
        $this->assertSame('pendiente_efectivo', $parte->fresh()->estado);

        // Repetirlo no lo marca como pagado.
        $this->post($this->rutaCliente('cliente.pago.efectivo.elegir', $parte));
        $this->assertSame('pendiente_efectivo', $parte->fresh()->estado);

        // La ruta del camarero sin login manda al login y no cobra.
        $this->patch(route('camarero.mesas.cobrar', [$this->mesa, $parte->id]))->assertRedirect(route('login'));
        $this->assertSame('pendiente_efectivo', $parte->fresh()->estado);
        $this->assertSame(0, Pago::count());
    }

    public function test_el_cliente_elige_efectivo_y_ve_la_pantalla_de_aviso(): void
    {
        $parte = $this->parte(1);

        $this->followingRedirects()
            ->post($this->rutaCliente('cliente.pago.efectivo.elegir', $parte))
            ->assertOk()
            ->assertSee('Avisa al camarero, vendrá a cobrarte')
            ->assertSee('10,34');

        // En la cuenta sale como "esperando al camarero" y sin botones de pagar.
        $this->get(route('cliente.cuenta', ['slug' => 'la-tasca', 'numero' => 5]))
            ->assertSee('el camarero vendrá a cobrar')
            ->assertDontSee($this->rutaCliente('cliente.pago.tarjeta', $parte));
    }

    public function test_el_cliente_no_puede_elegir_efectivo_para_una_parte_de_otro_restaurante(): void
    {
        [, $cuentaB] = $this->mesaConCuentaDividida('el-rincon', 5);
        $parteB = $cuentaB->partes->first();

        $this->post($this->rutaCliente('cliente.pago.efectivo.elegir', $parteB))->assertNotFound();
        $this->get($this->rutaCliente('cliente.pago.efectivo', $parteB))->assertNotFound();
        $this->assertSame('pendiente', $parteB->fresh()->estado);
    }

    public function test_el_camarero_ve_el_detalle_con_las_partes_y_lo_que_falta(): void
    {
        $this->parte(2)->pedirEfectivo();

        $this->actingAs($this->camarero)
            ->get(route('camarero.mesas.detalle', $this->mesa))
            ->assertOk()
            ->assertSee('31,00')
            ->assertSee('Cobrar 10,33 € en efectivo')
            ->assertSee('Persona 2: 10,33 € (efectivo sin confirmar).')
            ->assertSee('Persona 1: 10,34 € (sin pagar).');
    }

    public function test_no_se_cierra_con_pedidos_fuera_de_la_cuenta(): void
    {
        foreach ($this->cuenta->partes as $parte) {
            $parte->pedirEfectivo();
            $parte->confirmarEfectivo();
        }

        // Después de pagar todo, piden un café que no está en ninguna parte.
        $cafe = Plato::factory()->create(['categoria_id' => Categoria::withoutGlobalScopes()->first()->id, 'precio_centimos' => 150]);
        (new CrearPedido)->ejecutar($this->mesa, [['plato_id' => $cafe->id, 'cantidad' => 1]]);

        $this->cerrar()->assertSessionHasErrors('cuenta');
        $this->assertSame('ocupada', $this->mesa->fresh()->estado);
        $this->assertSame('abierta', $this->cuenta->fresh()->estado);
    }

    public function test_se_puede_dividir_lo_pedido_despues_de_pagar_y_luego_cerrar(): void
    {
        $this->parte(1)->pedirEfectivo();
        $this->parte(1)->confirmarEfectivo();

        // Piden 2 cafés (3,00 €) después de que la persona 1 haya pagado.
        $cafe = Plato::factory()->create(['categoria_id' => Categoria::withoutGlobalScopes()->first()->id, 'precio_centimos' => 150]);
        (new CrearPedido)->ejecutar($this->mesa, [['plato_id' => $cafe->id, 'cantidad' => 2]]);

        $this->get(route('cliente.cuenta', ['slug' => 'la-tasca', 'numero' => 5]))
            ->assertSee('Dividir lo pedido después');

        // Dividen SOLO lo nuevo entre 2: salen las personas 4 y 5.
        $this->post(route('cliente.cuenta.dividir', ['slug' => 'la-tasca', 'numero' => 5]), ['modo' => 'iguales', 'personas' => 2])
            ->assertSessionHasNoErrors();

        $cuenta = $this->cuenta->fresh()->load('partes');
        $this->assertSame(3400, $cuenta->total_centimos);
        $this->assertSame([1, 2, 3, 4, 5], $cuenta->partes->pluck('persona')->all());
        $this->assertSame([1034, 1033, 1033, 150, 150], $cuenta->partes->pluck('importe_centimos')->all());
        $this->assertSame($cuenta->total_centimos, $cuenta->partes->sum('importe_centimos'));
        // La persona 1 sigue pagada: no se ha tocado.
        $this->assertSame('pagado', $cuenta->partes->first()->estado);

        // Cobran el resto y la mesa se cierra.
        foreach ($cuenta->partes->where('estado', 'pendiente') as $parte) {
            $parte->pedirEfectivo();
            $parte->confirmarEfectivo();
        }
        $this->cerrar()->assertRedirect(route('camarero.mesas'));
        $this->assertSame('libre', $this->mesa->fresh()->estado);
    }

    public function test_el_listado_de_mesas_enlaza_al_detalle_de_las_ocupadas(): void
    {
        $this->actingAs($this->camarero)
            ->get(route('camarero.mesas'))
            ->assertSee(route('camarero.mesas.detalle', $this->mesa));
    }
}
