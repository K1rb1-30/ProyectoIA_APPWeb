<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Cuenta;
use App\Models\Mesa;
use App\Models\Pago;
use App\Models\ParteCuenta;
use App\Models\Pedido;
use App\Models\Plato;
use App\Models\Restaurante;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Rama 17: panel principal del administrador (/panel).
 * Se fija "ahora" con Carbon::setTestNow para que "hoy" sea siempre el mismo día.
 */
class PanelAdminTest extends TestCase
{
    use RefreshDatabase;

    private Restaurante $restauranteA;

    private User $adminA;

    protected function setUp(): void
    {
        parent::setUp();

        // Lunes 23/11/2026 a las 18:00 (hora de España).
        Carbon::setTestNow(Carbon::parse('2026-11-23 18:00:00'));

        $this->restauranteA = Restaurante::factory()->create(['nombre' => 'Taberna Inventada A']);
        $this->adminA = User::factory()->create(['name' => 'Admin Inventado', 'restaurante_id' => $this->restauranteA->id]);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_el_admin_ve_su_restaurante_y_las_cifras_de_hoy(): void
    {
        $this->datosConocidosDeA();

        $respuesta = $this->actingAs($this->adminA)->get(route('panel'));

        $respuesta->assertOk()
            ->assertSee('Taberna Inventada A')
            ->assertSee('Admin Inventado')
            // Ventas de hoy: un pago de 25,00 €. &nbsp; es el espacio que pone <x-precio>.
            ->assertSee('25,00&nbsp;€', false)
            // 2 pedidos hoy → ticket medio 25,00 / 2 = 12,50 €.
            ->assertSee('Ticket medio: <span class="tabular-nums whitespace-nowrap">12,50&nbsp;€', false)
            // Mesas: 1 de 2 ocupada.
            ->assertSee('1 de 2')
            ->assertSee('50 % de ocupación')
            // Carta: 3 platos, 1 agotado, y el agotado sale el primero.
            ->assertSee('3 platos')
            ->assertSee('1 agotado')
            ->assertSeeInOrder(['Calamares', 'Croquetas caseras', 'Patatas bravas'])
            // Pedidos en curso: 1 pendiente, 1 preparando y 1 listo de ayer = 3.
            ->assertSee('sin servir');

        // Las cifras que recibe la vista, una por una.
        $this->assertSame(2, $respuesta->viewData('resumen')['pedidos_hoy']);
        $this->assertSame(2500, $respuesta->viewData('resumen')['ventas_hoy']);
        $this->assertSame(['pendiente' => 1, 'preparando' => 1, 'listo' => 1], $respuesta->viewData('pedidosEnCurso'));
        $this->assertSame(1, $respuesta->viewData('mesasOcupadas'));
        $this->assertSame(1, $respuesta->viewData('platosAgotados'));
    }

    public function test_el_admin_de_a_no_ve_mesas_platos_ni_ventas_de_b(): void
    {
        $this->datosConocidosDeA();

        // Restaurante B con datos fáciles de reconocer.
        $restauranteB = Restaurante::factory()->create(['nombre' => 'Bodega Inventada B']);
        $mesaB = Mesa::factory()->create(['restaurante_id' => $restauranteB->id, 'numero' => 77, 'estado' => 'ocupada']);
        $categoriaB = Categoria::factory()->create(['restaurante_id' => $restauranteB->id]);
        Plato::factory()->create(['categoria_id' => $categoriaB->id, 'nombre' => 'Plato secreto de B', 'disponible' => false]);
        $this->pedido($mesaB, 'pendiente', now());
        $this->pago($mesaB, 77777, now());

        $respuesta = $this->actingAs($this->adminA)->get(route('panel'));

        $respuesta->assertOk()
            ->assertDontSee('Bodega Inventada B')
            ->assertDontSee('Mesa 77')
            ->assertDontSee('Plato secreto de B')
            ->assertDontSee('777,77');

        // Las cifras de A no cambian por tener B datos.
        $this->assertSame(2500, $respuesta->viewData('resumen')['ventas_hoy']);
        $this->assertSame(2, $respuesta->viewData('totalMesas'));
        $this->assertSame(3, $respuesta->viewData('totalPlatos'));
        $this->assertSame(1, $respuesta->viewData('pedidosEnCurso')['pendiente']);
    }

    public function test_camarero_y_cocina_no_pueden_entrar_en_el_panel(): void
    {
        $camarero = User::factory()->camarero()->create(['restaurante_id' => $this->restauranteA->id]);
        $cocina = User::factory()->cocina()->create(['restaurante_id' => $this->restauranteA->id]);

        $this->actingAs($camarero)->get(route('panel'))->assertForbidden();
        $this->actingAs($cocina)->get(route('panel'))->assertForbidden();
    }

    public function test_sin_login_el_panel_lleva_al_login(): void
    {
        $this->get(route('panel'))->assertRedirect(route('login'));
    }

    public function test_con_un_restaurante_nuevo_se_ven_mensajes_en_lugar_de_huecos(): void
    {
        $this->actingAs($this->adminA)->get(route('panel'))
            ->assertOk()
            ->assertSee('0,00&nbsp;€', false)
            ->assertSee('Todavía no hay ventas hoy.')
            ->assertSee('Todavía no hay pedidos hoy.')
            ->assertSee('Todavía no hay mesas.')
            ->assertSee('Todavía no hay platos en la carta.')
            ->assertSee('No hay pedidos en cocina ahora mismo.')
            ->assertSee('Crear la primera mesa')
            ->assertSee('Añadir el primer plato');
    }

    public function test_el_interruptor_de_disponible_vuelve_al_panel(): void
    {
        $categoria = Categoria::factory()->create(['restaurante_id' => $this->restauranteA->id]);
        $plato = Plato::factory()->create(['categoria_id' => $categoria->id, 'disponible' => true]);

        // from(): simula que el formulario se envía desde el panel.
        $this->actingAs($this->adminA)
            ->from(route('panel'))
            ->patch(route('admin.carta.disponibilidad', $plato))
            ->assertRedirect(route('panel'));

        $this->assertFalse($plato->fresh()->disponible);
    }

    public function test_dashboard_redirige_al_panel(): void
    {
        // Tarea previa P5: la URL antigua de Breeze sigue funcionando.
        $this->get('/dashboard')->assertRedirect('/panel');
    }

    public function test_el_numero_de_consultas_no_crece_con_los_datos(): void
    {
        // Con pocos datos...
        $this->datosConocidosDeA();
        $consultasConPocosDatos = $this->contarConsultasDelPanel();

        // ...y con muchos más: 30 mesas, 20 platos y 40 pedidos más.
        // Números 100 a 129 a mano, para no repetir el 1 y el 2 que ya existen.
        for ($numero = 100; $numero < 130; $numero++) {
            $mesa = Mesa::factory()->create(['restaurante_id' => $this->restauranteA->id, 'numero' => $numero]);
        }
        $categoria = Categoria::factory()->create(['restaurante_id' => $this->restauranteA->id]);
        Plato::factory()->count(20)->create(['categoria_id' => $categoria->id]);
        for ($i = 0; $i < 40; $i++) {
            $this->pedido($mesa, 'pendiente', now());
        }

        // Las cifras se piden ya contadas (COUNT, SUM): el número de consultas es el mismo.
        $this->assertSame($consultasConPocosDatos, $this->contarConsultasDelPanel());
    }

    /**
     * Datos conocidos del restaurante A:
     *  - mesas 1 (libre) y 2 (ocupada);
     *  - platos Croquetas caseras y Patatas bravas (disponibles) y Calamares (agotado);
     *  - hoy, un pedido pendiente y otro preparando; ayer, uno que sigue en listo;
     *  - un pago de hoy de 25,00 € y otro de ayer de 10,00 €.
     */
    private function datosConocidosDeA(): void
    {
        $id = $this->restauranteA->id;

        Mesa::factory()->create(['restaurante_id' => $id, 'numero' => 1, 'estado' => 'libre']);
        $mesa = Mesa::factory()->create(['restaurante_id' => $id, 'numero' => 2, 'estado' => 'ocupada']);

        $categoria = Categoria::factory()->create(['restaurante_id' => $id]);
        Plato::factory()->create(['categoria_id' => $categoria->id, 'nombre' => 'Croquetas caseras', 'precio_centimos' => 850]);
        Plato::factory()->create(['categoria_id' => $categoria->id, 'nombre' => 'Patatas bravas', 'precio_centimos' => 600]);
        Plato::factory()->create(['categoria_id' => $categoria->id, 'nombre' => 'Calamares', 'precio_centimos' => 1120, 'disponible' => false]);

        $this->pedido($mesa, 'pendiente', now()->setTime(13, 0));
        $this->pedido($mesa, 'preparando', now()->setTime(14, 0));
        $this->pedido($mesa, 'listo', now()->subDay()->setTime(21, 0));

        $this->pago($mesa, 2500, now()->setTime(15, 0));
        $this->pago($mesa, 1000, now()->subDay()->setTime(22, 0));
    }

    private function pedido(Mesa $mesa, string $estado, Carbon $cuando): Pedido
    {
        $pedido = new Pedido(['restaurante_id' => $mesa->restaurante_id, 'mesa_id' => $mesa->id, 'estado' => $estado]);
        $pedido->created_at = $cuando;
        $pedido->save();

        return $pedido;
    }

    /**
     * Crea un pago en efectivo (con su cuenta y su parte) en una fecha.
     */
    private function pago(Mesa $mesa, int $centimos, Carbon $cuando): Pago
    {
        $cuenta = Cuenta::create(['restaurante_id' => $mesa->restaurante_id, 'mesa_id' => $mesa->id, 'total_centimos' => $centimos, 'estado' => 'cerrada']);
        $parte = ParteCuenta::create(['cuenta_id' => $cuenta->id, 'persona' => 1, 'importe_centimos' => $centimos, 'metodo' => 'efectivo', 'estado' => 'pagado']);

        $pago = new Pago(['parte_cuenta_id' => $parte->id, 'importe_centimos' => $centimos, 'metodo' => 'efectivo']);
        $pago->created_at = $cuando;
        $pago->save();

        return $pago;
    }

    /**
     * Abre el panel y devuelve cuántas consultas SQL ha hecho.
     */
    private function contarConsultasDelPanel(): int
    {
        DB::flushQueryLog();
        DB::enableQueryLog();
        // fresh(): un User recién leído. Si no, la segunda vez ya tendría su
        // restaurante cargado de la primera y haría una consulta menos.
        $this->actingAs($this->adminA->fresh())->get(route('panel'))->assertOk();
        $numero = count(DB::getQueryLog());
        DB::disableQueryLog();

        return $numero;
    }
}
