<?php

namespace Tests\Feature;

use App\Acciones\CrearPedido;
use App\Acciones\DividirCuenta;
use App\Models\Categoria;
use App\Models\Mesa;
use App\Models\Pago;
use App\Models\ParteCuenta;
use App\Models\Plato;
use App\Models\Restaurante;
use App\Servicios\PasarelaStripe;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use Tests\TestCase;

/**
 * Rama 11: pagar una parte de la cuenta con tarjeta (Stripe Checkout).
 *
 * Aquí NUNCA se llama a Stripe de verdad: con $this->mock() cambiamos la
 * clase PasarelaStripe por una simulada que devuelve lo que le decimos.
 */
class PagoStripeTest extends TestCase
{
    use RefreshDatabase;

    /** Parte de la persona 1 de la mesa 5 de "la-tasca": 10,34 € */
    private ParteCuenta $parte;

    protected function setUp(): void
    {
        parent::setUp();

        $this->parte = $this->crearCuentaDividida('la-tasca', 5)->partes->first();
    }

    /**
     * Crea un restaurante con una mesa, un pedido de 31,00 € y la cuenta
     * dividida entre 3 personas (10,34 + 10,33 + 10,33).
     */
    private function crearCuentaDividida(string $slug, int $numeroMesa)
    {
        $restaurante = Restaurante::factory()->create(['slug' => $slug]);
        $mesa = Mesa::factory()->create(['restaurante_id' => $restaurante->id, 'numero' => $numeroMesa]);
        $categoria = Categoria::factory()->create(['restaurante_id' => $restaurante->id]);
        $plato = Plato::factory()->create(['categoria_id' => $categoria->id, 'precio_centimos' => 3100]);

        (new CrearPedido)->ejecutar($mesa, [['plato_id' => $plato->id, 'cantidad' => 1]]);

        return (new DividirCuenta)->ejecutar($mesa, 'iguales', 3);
    }

    private function ruta(string $nombre, ?ParteCuenta $parte = null, array $extra = []): string
    {
        return route($nombre, ['slug' => 'la-tasca', 'numero' => 5, 'parte' => ($parte ?? $this->parte)->id] + $extra);
    }

    private function rutaCuenta(): string
    {
        return route('cliente.cuenta', ['slug' => 'la-tasca', 'numero' => 5]);
    }

    /**
     * Respuesta simulada de PasarelaStripe::consultarSesion().
     */
    private function sesion(bool $pagada = true, ?int $parteId = null, int $importe = 1034): array
    {
        return [
            'pagada' => $pagada,
            'parte_cuenta_id' => $parteId ?? $this->parte->id,
            'importe_centimos' => $importe,
            'moneda' => 'eur',
        ];
    }

    public function test_la_pantalla_de_pago_muestra_el_importe_de_la_parte_y_el_modo_test(): void
    {
        $this->get($this->ruta('cliente.pago.tarjeta'))
            ->assertOk()
            ->assertSee('10,34')
            ->assertSee('Persona 1 de 3')
            ->assertSee('4242 4242 4242 4242');
    }

    public function test_pagar_crea_la_sesion_con_el_importe_en_centimos_y_redirige_a_stripe(): void
    {
        $this->mock(PasarelaStripe::class, function (MockInterface $pasarela) {
            $pasarela->shouldReceive('crearSesion')
                ->once()
                ->withArgs(fn (ParteCuenta $parte, string $concepto, string $urlExito, string $urlCancelar) => $parte->id === $this->parte->id
                    && $parte->importe_centimos === 1034
                    // Stripe tiene que recibir el marcador tal cual, sin codificar.
                    && str_ends_with($urlExito, '?session_id={CHECKOUT_SESSION_ID}')
                    && $urlCancelar === $this->ruta('cliente.pago.cancelado'))
                ->andReturn(['id' => 'cs_test_123', 'url' => 'https://checkout.stripe.com/c/pay/cs_test_123']);
        });

        $this->post($this->ruta('cliente.pago.pagar'))
            ->assertRedirect('https://checkout.stripe.com/c/pay/cs_test_123');

        // Ir a Stripe todavía no cambia nada.
        $this->assertSame('pendiente', $this->parte->fresh()->estado);
        $this->assertSame(0, Pago::count());
    }

    public function test_volver_de_stripe_con_la_sesion_pagada_crea_el_pago_y_marca_la_parte_pagada(): void
    {
        $this->mock(PasarelaStripe::class, function (MockInterface $pasarela) {
            $pasarela->shouldReceive('consultarSesion')->once()->with('cs_test_123')->andReturn($this->sesion());
        });

        $this->get($this->ruta('cliente.pago.exito', extra: ['session_id' => 'cs_test_123']))
            ->assertOk()
            ->assertSee('¡Pago completado con éxito!')
            ->assertSee('cs_test_123');

        $parte = $this->parte->fresh();
        $this->assertSame('pagado', $parte->estado);
        $this->assertSame('tarjeta', $parte->metodo);

        $pago = Pago::sole();
        $this->assertSame($parte->id, $pago->parte_cuenta_id);
        $this->assertSame(1034, $pago->importe_centimos);
        $this->assertSame('tarjeta', $pago->metodo);
        $this->assertSame('cs_test_123', $pago->referencia_stripe);
    }

    public function test_volver_dos_veces_a_la_url_de_exito_no_duplica_el_pago(): void
    {
        // once(): a Stripe solo se le pregunta la primera vez; la segunda la
        // parte ya está pagada y no hace falta.
        $this->mock(PasarelaStripe::class, function (MockInterface $pasarela) {
            $pasarela->shouldReceive('consultarSesion')->once()->andReturn($this->sesion());
        });

        $url = $this->ruta('cliente.pago.exito', extra: ['session_id' => 'cs_test_123']);
        $this->get($url)->assertOk();
        $this->get($url)->assertOk()->assertSee('¡Pago completado con éxito!');

        $this->assertSame(1, Pago::count());
    }

    public function test_una_sesion_no_pagada_no_cambia_nada(): void
    {
        $this->mock(PasarelaStripe::class, function (MockInterface $pasarela) {
            $pasarela->shouldReceive('consultarSesion')->once()->andReturn($this->sesion(pagada: false));
        });

        $this->get($this->ruta('cliente.pago.exito', extra: ['session_id' => 'cs_test_123']))
            ->assertRedirect($this->rutaCuenta())
            ->assertSessionHasErrors('pago');

        $this->assertSame('pendiente', $this->parte->fresh()->estado);
        $this->assertNull($this->parte->fresh()->metodo);
        $this->assertSame(0, Pago::count());
    }

    public function test_una_sesion_de_otra_parte_o_con_otro_importe_no_cambia_nada(): void
    {
        $otraParte = $this->parte->cuenta->partes->last();

        $this->mock(PasarelaStripe::class, function (MockInterface $pasarela) use ($otraParte) {
            $pasarela->shouldReceive('consultarSesion')->with('cs_otra_parte')->andReturn($this->sesion(parteId: $otraParte->id));
            $pasarela->shouldReceive('consultarSesion')->with('cs_otro_importe')->andReturn($this->sesion(importe: 1));
        });

        // Sesión pagada, pero de la persona 3 → no sirve para pagar la persona 1.
        $this->get($this->ruta('cliente.pago.exito', extra: ['session_id' => 'cs_otra_parte']))
            ->assertSessionHasErrors('pago');
        // Sesión pagada de 0,01 € → no sirve para pagar 10,34 €.
        $this->get($this->ruta('cliente.pago.exito', extra: ['session_id' => 'cs_otro_importe']))
            ->assertSessionHasErrors('pago');

        $this->assertSame('pendiente', $this->parte->fresh()->estado);
        $this->assertSame(0, Pago::count());
    }

    public function test_no_se_puede_pagar_una_parte_de_otro_restaurante(): void
    {
        $parteAjena = $this->crearCuentaDividida('el-rincon', 5)->partes->first();

        $this->mock(PasarelaStripe::class, function (MockInterface $pasarela) {
            $pasarela->shouldNotReceive('crearSesion');
            $pasarela->shouldNotReceive('consultarSesion');
        });

        // URL de la mesa 5 de "la-tasca" con el id de una parte de "el-rincon".
        $this->get($this->ruta('cliente.pago.tarjeta', $parteAjena))->assertNotFound();
        $this->post($this->ruta('cliente.pago.pagar', $parteAjena))->assertNotFound();
        $this->get($this->ruta('cliente.pago.exito', $parteAjena, ['session_id' => 'cs_test_123']))
            ->assertRedirect($this->rutaCuenta())
            ->assertSessionHasErrors('pago');

        $this->assertSame('pendiente', $parteAjena->fresh()->estado);
        $this->assertSame(0, Pago::count());
    }

    public function test_no_se_puede_volver_a_pagar_una_parte_ya_pagada(): void
    {
        $this->parte->update(['metodo' => 'tarjeta', 'estado' => 'pagado']);

        $this->mock(PasarelaStripe::class, function (MockInterface $pasarela) {
            $pasarela->shouldNotReceive('crearSesion');
        });

        $this->get($this->ruta('cliente.pago.tarjeta'))
            ->assertRedirect($this->rutaCuenta())
            ->assertSessionHas('estado', 'Esta parte ya está pagada.');
        $this->post($this->ruta('cliente.pago.pagar'))
            ->assertRedirect($this->rutaCuenta())
            ->assertSessionHas('estado', 'Esta parte ya está pagada.');
    }

    public function test_cancelar_deja_la_parte_pendiente(): void
    {
        $this->get($this->ruta('cliente.pago.cancelado'))
            ->assertRedirect($this->rutaCuenta())
            ->assertSessionHas('estado');

        $this->assertSame('pendiente', $this->parte->fresh()->estado);
        $this->assertSame(0, Pago::count());
    }

    public function test_con_una_clave_que_no_es_de_test_no_se_llama_a_stripe(): void
    {
        // Usa la PasarelaStripe de verdad: se para antes de conectar con Stripe.
        config(['services.stripe.secret' => 'sk_live_no_valida']);

        $this->post($this->ruta('cliente.pago.pagar'))
            ->assertRedirect($this->rutaCuenta())
            ->assertSessionHasErrors('pago');

        $this->assertSame('pendiente', $this->parte->fresh()->estado);
    }

    public function test_la_cuenta_muestra_el_boton_de_tarjeta_y_las_partes_pagadas(): void
    {
        $this->parte->update(['metodo' => 'tarjeta', 'estado' => 'pagado']);

        $this->get($this->rutaCuenta())
            ->assertOk()
            ->assertSee('Pagado con tarjeta')
            // Las otras dos partes siguen pendientes y tienen su botón.
            ->assertSee($this->ruta('cliente.pago.tarjeta', $this->parte->cuenta->partes->last()))
            ->assertDontSee($this->ruta('cliente.pago.tarjeta'));
    }
}
