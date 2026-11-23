<?php

namespace Tests\Feature;

use App\Estadisticas\EstadisticasRestaurante;
use App\Models\Categoria;
use App\Models\Cuenta;
use App\Models\Mesa;
use App\Models\Pago;
use App\Models\ParteCuenta;
use App\Models\Pedido;
use App\Models\Plato;
use App\Models\Restaurante;
use App\Models\User;
use Database\Seeders\AlergenosSeeder;
use Database\Seeders\CartaSeeder;
use Database\Seeders\MesasSeeder;
use Database\Seeders\RestaurantesSeeder;
use Database\Seeders\VentasDemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Rama 13: estadísticas del admin. Se fija "ahora" con Carbon::setTestNow para
 * que los días salgan siempre iguales.
 */
class EstadisticasTest extends TestCase
{
    use RefreshDatabase;

    private Restaurante $restauranteA;

    private Mesa $mesaA;

    private Plato $croquetas;

    private Plato $bravas;

    protected function setUp(): void
    {
        parent::setUp();

        // Lunes 23/11/2026 a las 18:00 (hora de España).
        Carbon::setTestNow(Carbon::parse('2026-11-23 18:00:00'));

        $this->restauranteA = Restaurante::factory()->create();
        $this->mesaA = Mesa::factory()->create(['restaurante_id' => $this->restauranteA->id]);
        $categoria = Categoria::factory()->create(['restaurante_id' => $this->restauranteA->id]);
        $this->croquetas = Plato::factory()->create(['categoria_id' => $categoria->id, 'nombre' => 'Croquetas', 'precio_centimos' => 850]);
        $this->bravas = Plato::factory()->create(['categoria_id' => $categoria->id, 'nombre' => 'Bravas', 'precio_centimos' => 600]);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    /**
     * Crea un pedido con sus líneas en una fecha.
     *
     * @param  array<int, array{0: Plato, 1: int}>  $lineas  [[plato, cantidad], ...]
     */
    private function pedido(Mesa $mesa, array $lineas, Carbon $cuando): Pedido
    {
        $pedido = new Pedido(['restaurante_id' => $mesa->restaurante_id, 'mesa_id' => $mesa->id, 'estado' => 'servido']);
        $pedido->created_at = $cuando;
        $pedido->save();

        foreach ($lineas as [$plato, $cantidad]) {
            $pedido->lineas()->create(['plato_id' => $plato->id, 'cantidad' => $cantidad, 'precio_centimos' => $plato->precio_centimos]);
        }

        return $pedido;
    }

    /**
     * Crea un pago (con su cuenta y su parte) en una fecha.
     * $canal: 'stripe', 'tpv' o 'efectivo'.
     */
    private function pago(Mesa $mesa, int $centimos, string $canal, Carbon $cuando): Pago
    {
        $cuenta = Cuenta::create(['restaurante_id' => $mesa->restaurante_id, 'mesa_id' => $mesa->id, 'total_centimos' => $centimos, 'estado' => 'cerrada']);
        $metodo = $canal === 'efectivo' ? 'efectivo' : 'tarjeta';
        $parte = ParteCuenta::create(['cuenta_id' => $cuenta->id, 'persona' => 1, 'importe_centimos' => $centimos, 'metodo' => $metodo, 'estado' => 'pagado']);

        $pago = new Pago([
            'parte_cuenta_id' => $parte->id,
            'importe_centimos' => $centimos,
            'metodo' => $metodo,
            'referencia_stripe' => $canal === 'stripe' ? 'cs_test_'.uniqid() : null,
        ]);
        $pago->created_at = $cuando;
        $pago->save();

        return $pago;
    }

    /**
     * Datos conocidos del restaurante A.
     */
    private function datosDeA(): void
    {
        // Hoy: 10,00 € con Stripe y 5,50 € en efectivo. Ayer: 10,00 € en efectivo.
        $this->pago($this->mesaA, 1000, 'stripe', now()->setTime(13, 0));
        $this->pago($this->mesaA, 550, 'efectivo', now()->setTime(14, 30));
        $this->pago($this->mesaA, 1000, 'efectivo', now()->subDay()->setTime(21, 0));
        // Hace 3 días: 20,00 € con TPV. Hace 40 días (fuera de los 30): 99,99 €.
        $this->pago($this->mesaA, 2000, 'tpv', now()->subDays(3)->setTime(20, 0));
        $this->pago($this->mesaA, 9999, 'efectivo', now()->subDays(40));

        // Pedidos: hoy 3 croquetas + 1 bravas; hace 3 días 1 bravas; hace 40 días 9 bravas (fuera).
        $this->pedido($this->mesaA, [[$this->croquetas, 3], [$this->bravas, 1]], now()->setTime(12, 45));
        $this->pedido($this->mesaA, [[$this->bravas, 1]], now()->subDays(3)->setTime(19, 30));
        $this->pedido($this->mesaA, [[$this->bravas, 9]], now()->subDays(40));
    }

    /**
     * Restaurante B con ventas grandes, para comprobar que A no las ve.
     */
    private function datosDeB(): Plato
    {
        $restauranteB = Restaurante::factory()->create();
        $mesaB = Mesa::factory()->create(['restaurante_id' => $restauranteB->id]);
        $categoriaB = Categoria::factory()->create(['restaurante_id' => $restauranteB->id]);
        $pulpo = Plato::factory()->create(['categoria_id' => $categoriaB->id, 'nombre' => 'Pulpo de B', 'precio_centimos' => 1800]);

        $this->pago($mesaB, 50000, 'stripe', now()->setTime(15, 0));
        $this->pago($mesaB, 30000, 'efectivo', now()->subDays(3));
        $this->pedido($mesaB, [[$pulpo, 20]], now()->setTime(14, 0));

        return $pulpo;
    }

    public function test_ventas_por_dia_suma_los_pagos_de_cada_dia_y_rellena_con_ceros(): void
    {
        $this->datosDeA();

        $ventas = (new EstadisticasRestaurante($this->restauranteA->id))->ventasPorDia(30);

        $this->assertCount(30, $ventas);
        $this->assertSame('2026-10-25', $ventas[0]['dia']);
        $this->assertSame(['dia' => '2026-11-23', 'total_centimos' => 1550, 'pagos' => 2], $ventas[29]);
        $this->assertSame(['dia' => '2026-11-22', 'total_centimos' => 1000, 'pagos' => 1], $ventas[28]);
        $this->assertSame(['dia' => '2026-11-20', 'total_centimos' => 2000, 'pagos' => 1], $ventas[26]);
        // El pago de hace 40 días no entra: el total de los 30 días es 15,50 + 10 + 20.
        $this->assertSame(4550, array_sum(array_column($ventas, 'total_centimos')));
        $this->assertSame(0, $ventas[27]['total_centimos']);
    }

    public function test_platos_mas_pedidos_suma_cantidades_e_importes(): void
    {
        $this->datosDeA();

        $platos = (new EstadisticasRestaurante($this->restauranteA->id))->platosMasPedidos(10, 30);

        $this->assertSame([
            ['nombre' => 'Croquetas', 'unidades' => 3, 'importe_centimos' => 2550],
            // Las 9 bravas de hace 40 días no cuentan.
            ['nombre' => 'Bravas', 'unidades' => 2, 'importe_centimos' => 1200],
        ], $platos);
    }

    public function test_metodos_de_pago_separa_efectivo_stripe_y_tpv(): void
    {
        $this->datosDeA();

        $metodos = (new EstadisticasRestaurante($this->restauranteA->id))->metodosDePago(30);

        $this->assertSame(['importe_centimos' => 1550, 'numero' => 2], $metodos['efectivo']);
        $this->assertSame(['importe_centimos' => 1000, 'numero' => 1], $metodos['stripe']);
        $this->assertSame(['importe_centimos' => 2000, 'numero' => 1], $metodos['tpv']);
        $this->assertSame(['importe_centimos' => 3000, 'numero' => 2], $metodos['tarjeta']);
    }

    public function test_resumen_de_hoy(): void
    {
        $this->datosDeA();

        $resumen = (new EstadisticasRestaurante($this->restauranteA->id))->resumenDeHoy();

        $this->assertSame([
            'ventas_hoy' => 1550,
            'ventas_ayer' => 1000,
            'variacion' => 55, // (15,50 − 10,00) / 10,00 = +55 %
            'pedidos_hoy' => 1,
            'cobros_hoy' => 2,
            'ticket_medio' => 1550,
        ], $resumen);
    }

    public function test_el_admin_de_a_no_ve_pagos_ni_pedidos_de_b(): void
    {
        $this->datosDeA();
        $this->datosDeB();

        $estadisticas = new EstadisticasRestaurante($this->restauranteA->id);

        // Los mismos números que sin B.
        $this->assertSame(4550, array_sum(array_column($estadisticas->ventasPorDia(30), 'total_centimos')));
        $this->assertSame(['Croquetas', 'Bravas'], array_column($estadisticas->platosMasPedidos(), 'nombre'));
        $this->assertSame(3000, $estadisticas->metodosDePago()['tarjeta']['importe_centimos']);
        $this->assertSame(1550, $estadisticas->resumenDeHoy()['ventas_hoy']);

        // Y en la página del admin de A tampoco sale nada de B.
        $adminA = User::factory()->create(['restaurante_id' => $this->restauranteA->id]);
        $this->actingAs($adminA)
            ->get(route('admin.estadisticas'))
            ->assertOk()
            ->assertSee('Croquetas')
            ->assertDontSee('Pulpo de B')
            ->assertDontSee('500,00');
    }

    public function test_la_pagina_muestra_los_datos_en_euros(): void
    {
        $this->datosDeA();
        $admin = User::factory()->create(['restaurante_id' => $this->restauranteA->id]);

        $this->actingAs($admin)
            ->get(route('admin.estadisticas'))
            ->assertOk()
            ->assertSee('15,50')          // ventas de hoy
            ->assertSee('+55 %')
            ->assertSee('data-grafico="ventas-por-dia"', false)
            ->assertSee('data-grafico="metodos-de-pago"', false);
    }

    public function test_sin_datos_se_ve_un_mensaje_y_ningun_grafico(): void
    {
        $admin = User::factory()->create(['restaurante_id' => $this->restauranteA->id]);

        $this->actingAs($admin)
            ->get(route('admin.estadisticas'))
            ->assertOk()
            ->assertSee('Todavía no hay ventas cobradas en los últimos 30 días.')
            ->assertSee('Todavía no hay pedidos en los últimos 30 días.')
            ->assertDontSee('<canvas', false);
    }

    public function test_camarero_y_cocina_no_pueden_entrar(): void
    {
        $camarero = User::factory()->camarero()->create(['restaurante_id' => $this->restauranteA->id]);
        $cocina = User::factory()->cocina()->create(['restaurante_id' => $this->restauranteA->id]);

        $this->actingAs($camarero)->get(route('admin.estadisticas'))->assertForbidden();
        $this->actingAs($cocina)->get(route('admin.estadisticas'))->assertForbidden();
    }

    public function test_sin_login_va_al_login(): void
    {
        $this->get(route('admin.estadisticas'))->assertRedirect(route('login'));
    }

    public function test_el_menu_del_admin_enlaza_a_estadisticas(): void
    {
        $admin = User::factory()->create(['restaurante_id' => $this->restauranteA->id]);

        $this->actingAs($admin)->get(route('admin.mesas'))->assertSee(route('admin.estadisticas'));
    }

    public function test_el_seeder_de_demo_crea_ventas_cuadradas_y_no_duplica(): void
    {
        $this->seed([RestaurantesSeeder::class, AlergenosSeeder::class, CartaSeeder::class, MesasSeeder::class, VentasDemoSeeder::class]);

        $pagos = Pago::count();
        $this->assertGreaterThan(0, $pagos);

        // En cada cuenta, la suma de sus pagos es exactamente su total.
        $descuadradas = DB::table('cuentas')
            ->leftJoin('partes_cuenta', 'partes_cuenta.cuenta_id', '=', 'cuentas.id')
            ->leftJoin('pagos', 'pagos.parte_cuenta_id', '=', 'partes_cuenta.id')
            ->select('cuentas.id')
            ->groupBy('cuentas.id', 'cuentas.total_centimos')
            ->havingRaw('COALESCE(SUM(pagos.importe_centimos), 0) <> cuentas.total_centimos')
            ->count();
        $this->assertSame(0, $descuadradas);

        // Ninguna fecha en el futuro.
        $this->assertSame(0, Pago::where('created_at', '>', now())->count());

        // Ejecutarlo otra vez no añade nada.
        $this->seed(VentasDemoSeeder::class);
        $this->assertSame($pagos, Pago::count());
    }
}
