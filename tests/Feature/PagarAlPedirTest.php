<?php

namespace Tests\Feature;

use App\Acciones\CrearPedido;
use App\Models\Categoria;
use App\Models\Cuenta;
use App\Models\Mesa;
use App\Models\Pago;
use App\Models\Pedido;
use App\Models\Plato;
use App\Models\Restaurante;
use App\Models\User;
use App\Servicios\PasarelaStripe;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Fakes\PasarelaStripeFalsa;
use Tests\TestCase;

/**
 * Rama 15: el pedido del cliente se paga con tarjeta al hacerlo.
 *  - Sin pago confirmado por Stripe no existe ningún pedido.
 *  - Al pagarlo, llega a cocina y queda en la cuenta como una parte YA PAGADA,
 *    así que no se vuelve a cobrar al dividir ni al cerrar la mesa.
 * Stripe es de mentira (PasarelaStripeFalsa).
 */
class PagarAlPedirTest extends TestCase
{
    use RefreshDatabase;

    private Mesa $mesa;

    private Plato $croquetas;

    private Plato $bravas;

    private User $camarero;

    private PasarelaStripeFalsa $stripe;

    protected function setUp(): void
    {
        parent::setUp();

        $this->stripe = new PasarelaStripeFalsa;
        $this->app->instance(PasarelaStripe::class, $this->stripe);

        $restaurante = Restaurante::factory()->create(['slug' => 'la-tasca']);
        $this->mesa = Mesa::factory()->create(['restaurante_id' => $restaurante->id, 'numero' => 5]);
        $categoria = Categoria::factory()->create(['restaurante_id' => $restaurante->id]);
        $this->croquetas = Plato::factory()->create(['categoria_id' => $categoria->id, 'nombre' => 'Croquetas', 'precio_centimos' => 850]);
        $this->bravas = Plato::factory()->create(['categoria_id' => $categoria->id, 'nombre' => 'Bravas', 'precio_centimos' => 600]);
        $this->camarero = User::factory()->camarero()->create(['restaurante_id' => $restaurante->id]);
    }

    private function ruta(string $nombre): string
    {
        return route($nombre, ['slug' => 'la-tasca', 'numero' => 5]);
    }

    /** Pulsa «Pagar y enviar a cocina» con 2 croquetas (17,00 €). */
    private function irAPagar()
    {
        return $this->post($this->ruta('cliente.pedido.enviar'), [
            'lineas' => [['plato_id' => $this->croquetas->id, 'cantidad' => 2]],
        ]);
    }

    /** Vuelve de Stripe a la URL de éxito. */
    private function volverDeStripe(?string $idSesion = null)
    {
        return $this->get($this->ruta('cliente.pedido.pagado').'?session_id='.($idSesion ?? $this->stripe->ultimaSesion));
    }

    public function test_pulsar_pagar_abre_stripe_por_el_total_y_todavia_no_crea_el_pedido(): void
    {
        $this->irAPagar()->assertRedirect('https://checkout.stripe.com/c/pay/cs_test_1');

        $this->assertSame(['importe' => 1700, 'mesa_id' => $this->mesa->id], $this->stripe->sesiones['cs_test_1']);
        $this->assertSame(0, Pedido::withoutGlobalScopes()->count());
        $this->assertSame('libre', $this->mesa->fresh()->estado);
    }

    public function test_al_volver_pagado_se_crea_el_pedido_y_una_parte_ya_pagada_en_la_cuenta(): void
    {
        $this->irAPagar();
        $this->volverDeStripe()->assertRedirect($this->ruta('cliente.estado'));

        $pedido = Pedido::withoutGlobalScopes()->sole();
        $cuenta = Cuenta::withoutGlobalScopes()->with('partes')->sole();
        $parte = $cuenta->partes->sole();

        $this->assertSame('pendiente', $pedido->estado); // ya está en cocina
        $this->assertSame($cuenta->id, $pedido->cuenta_id);
        $this->assertSame(1700, $cuenta->total_centimos);
        $this->assertSame($pedido->id, $parte->pedido_id);
        $this->assertSame(1700, $parte->importe_centimos);
        $this->assertSame('pagado', $parte->estado);
        $this->assertSame('tarjeta', $parte->metodo);
        $this->assertSame("Pedido #{$pedido->id} · pagado al pedir", $parte->nombre());
        $this->assertSame('cs_test_1', Pago::sole()->referencia_stripe);
    }

    public function test_el_pedido_pagado_sale_en_la_pantalla_de_cocina(): void
    {
        $this->irAPagar();
        $this->volverDeStripe();

        $cocina = User::factory()->cocina()->create(['restaurante_id' => $this->mesa->restaurante_id]);
        $this->actingAs($cocina)->get(route('cocina.pedidos'))->assertOk()->assertSee('Croquetas');
    }

    public function test_recargar_la_pagina_de_exito_no_duplica_el_pedido(): void
    {
        $this->irAPagar();
        $this->volverDeStripe();
        $this->volverDeStripe()->assertRedirect($this->ruta('cliente.estado'));

        $this->assertSame(1, Pedido::withoutGlobalScopes()->count());
        $this->assertSame(1, Pago::count());
    }

    public function test_si_stripe_dice_que_no_esta_pagado_no_se_crea_nada(): void
    {
        $this->stripe->pagar = false;
        $this->irAPagar();

        $this->volverDeStripe()->assertRedirect($this->ruta('cliente.pedido'))->assertSessionHasErrors('pago');

        $this->assertSame(0, Pedido::withoutGlobalScopes()->count());
        $this->assertSame(0, Cuenta::withoutGlobalScopes()->count());
    }

    public function test_una_sesion_de_otra_mesa_o_por_otro_importe_no_crea_nada(): void
    {
        $this->irAPagar();

        $this->stripe->mesaDeLaSesion = 999;
        $this->volverDeStripe()->assertSessionHasErrors('pago');

        $this->stripe->mesaDeLaSesion = null;
        $this->stripe->importeCobrado = 1;
        $this->volverDeStripe()->assertSessionHasErrors('pago');

        $this->assertSame(0, Pedido::withoutGlobalScopes()->count());
    }

    public function test_escribir_la_url_de_exito_a_mano_no_crea_nada(): void
    {
        // Sin haber pulsado "Pagar": no hay carrito guardado ni se pregunta a Stripe.
        $this->volverDeStripe('cs_test_inventada')->assertSessionHasErrors('pago');

        $this->assertSame(0, $this->stripe->consultas);
        $this->assertSame(0, Pedido::withoutGlobalScopes()->count());
    }

    public function test_si_se_agota_un_plato_mientras_paga_no_se_crea_el_pedido_y_se_da_la_referencia(): void
    {
        $this->irAPagar();
        $this->croquetas->update(['disponible' => false]);

        // Vuelve a "Mi pedido" (no a la página de Stripe) con la referencia del pago.
        $this->withHeader('Referer', 'https://checkout.stripe.com/')
            ->volverDeStripe()
            ->assertRedirect($this->ruta('cliente.pedido'))
            ->assertSessionHasErrors('pago');
        $this->assertStringContainsString('cs_test_1', session('errors')->first('pago'));

        $this->assertSame(0, Pedido::withoutGlobalScopes()->count());
    }

    public function test_cancelar_en_stripe_no_crea_nada(): void
    {
        $this->irAPagar();

        $this->get($this->ruta('cliente.pedido.cancelado'))
            ->assertRedirect($this->ruta('cliente.pedido'))
            ->assertSessionHas('estado');

        $this->assertSame(0, Pedido::withoutGlobalScopes()->count());
    }

    public function test_al_dividir_la_cuenta_lo_pagado_al_pedir_no_se_vuelve_a_cobrar(): void
    {
        // El cliente paga 2 croquetas (17,00) y luego el camarero toma una comanda de bravas (6,00).
        $this->irAPagar();
        $this->volverDeStripe();
        (new CrearPedido)->ejecutar($this->mesa, [['plato_id' => $this->bravas->id, 'cantidad' => 1]], $this->camarero);

        // Al dividir entre 2, solo se reparten las bravas: 3,00 + 3,00.
        $this->post($this->ruta('cliente.cuenta.dividir'), ['modo' => 'iguales', 'personas' => 2])->assertSessionHasNoErrors();

        $cuenta = Cuenta::withoutGlobalScopes()->sole();
        $partes = $cuenta->partes()->orderBy('persona')->get();

        $this->assertSame(1700 + 600, $cuenta->total_centimos);
        $this->assertSame([1700, 300, 300], $partes->pluck('importe_centimos')->all());
        $this->assertSame(['pagado', 'pendiente', 'pendiente'], $partes->pluck('estado')->all());
        $this->assertSame($cuenta->total_centimos, $partes->sum('importe_centimos'));
    }

    public function test_lo_pagado_al_pedir_no_cuenta_como_persona_al_dividir(): void
    {
        // Antes, el pedido pagado ocupaba la «persona 1» y al dividir entre 2
        // salían «Persona 2» y «Persona 3».
        $this->irAPagar();
        $this->volverDeStripe();
        (new CrearPedido)->ejecutar($this->mesa, [['plato_id' => $this->bravas->id, 'cantidad' => 1]], $this->camarero);

        $this->post($this->ruta('cliente.cuenta.dividir'), ['modo' => 'iguales', 'personas' => 2])->assertSessionHasNoErrors();

        $cuenta = Cuenta::withoutGlobalScopes()->sole()->load('partes');

        // La parte del pedido pagado no tiene número; las personas son la 1 y la 2.
        $this->assertSame([null, 1, 2], $cuenta->partes->pluck('persona')->all());
        $this->assertSame(2, $cuenta->numeroDePersonas());
        $this->assertStringStartsWith('Pedido #', $cuenta->partes[0]->nombre());

        $this->get($this->ruta('cliente.cuenta'))
            ->assertSee('Persona 1')
            ->assertSee('Persona 2')
            ->assertDontSee('Persona 3');
    }

    public function test_si_todo_se_pago_al_pedir_el_camarero_cierra_la_mesa_sin_cobrar_nada(): void
    {
        $this->irAPagar();
        $this->volverDeStripe();

        $this->actingAs($this->camarero)->post(route('camarero.mesas.cerrar', $this->mesa))->assertSessionHasNoErrors();

        $this->assertSame('cerrada', Cuenta::withoutGlobalScopes()->sole()->estado);
        $this->assertSame('libre', $this->mesa->fresh()->estado);
    }

    public function test_el_camarero_ve_la_parte_como_pagada_al_pedir(): void
    {
        $this->irAPagar();
        $this->volverDeStripe();
        $pedido = Pedido::withoutGlobalScopes()->sole();

        $this->actingAs($this->camarero)
            ->get(route('camarero.mesas.detalle', $this->mesa))
            ->assertOk()
            ->assertSee("Pedido #{$pedido->id} · pagado al pedir");
    }

    public function test_si_stripe_falla_al_abrir_el_pago_se_dice_que_llame_al_camarero(): void
    {
        // Pasarela de verdad con una clave guardada que no es de test (rama 26:
        // la del restaurante): falla antes de llamar a Stripe.
        $this->app->instance(PasarelaStripe::class, new PasarelaStripe);
        $this->mesa->restaurante->forceFill(['stripe_cobros_clave_secreta' => 'clave_que_no_es_de_test'])->save();

        $this->irAPagar()->assertRedirect($this->ruta('cliente.pedido'))->assertSessionHasErrors('pago');
        $this->assertSame(0, Pedido::withoutGlobalScopes()->count());
    }
}
