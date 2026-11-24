<?php

namespace Tests\Feature;

use App\Models\Cuenta;
use App\Models\Mesa;
use App\Models\ParteCuenta;
use App\Models\Pedido;
use App\Models\Plato;
use App\Models\Restaurante;
use Database\Seeders\AlergenosSeeder;
use Database\Seeders\CartaSeeder;
use Database\Seeders\MesasSeeder;
use Database\Seeders\PedidosEnCursoSeeder;
use Database\Seeders\RestaurantesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Comprueba que PedidosEnCursoSeeder deja un servicio en marcha completo y
 * coherente (mismas reglas que la aplicación).
 */
class PedidosEnCursoSeederTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([RestaurantesSeeder::class, AlergenosSeeder::class, CartaSeeder::class, MesasSeeder::class]);
    }

    private function laTasca(): Restaurante
    {
        return Restaurante::where('slug', 'la-tasca')->sole();
    }

    private function estadosDePedidos(Restaurante $restaurante): array
    {
        return Pedido::withoutGlobalScopes()->where('restaurante_id', $restaurante->id)
            ->selectRaw('estado, count(*) as total')->groupBy('estado')->pluck('total', 'estado')->all();
    }

    public function test_la_tasca_queda_con_pedidos_en_todos_los_estados(): void
    {
        $this->seed(PedidosEnCursoSeeder::class);

        $estados = $this->estadosDePedidos($this->laTasca());

        $this->assertSame(2, $estados['pendiente']); // mesa 1 y el pagado al pedir de la mesa 2
        $this->assertSame(1, $estados['preparando']);
        $this->assertSame(1, $estados['listo']);
        $this->assertSame(3, $estados['servido']);
    }

    public function test_las_cuentas_abiertas_cuadran_y_tienen_cobros_de_todos_los_tipos(): void
    {
        $this->seed(PedidosEnCursoSeeder::class);

        $cuentas = Cuenta::withoutGlobalScopes()->where('restaurante_id', $this->laTasca()->id)->with('partes')->get();

        // Mesas 2 (pagado al pedir), 4 (a medio cobrar) y 5 (toda pagada).
        $this->assertCount(3, $cuentas);
        foreach ($cuentas as $cuenta) {
            $this->assertSame($cuenta->total_centimos, $cuenta->partes->sum('importe_centimos'));
        }

        $partes = ParteCuenta::whereIn('cuenta_id', $cuentas->pluck('id'))->get();
        $this->assertTrue($partes->contains(fn ($parte) => $parte->pedido_id !== null && $parte->estaPagada()));
        $this->assertTrue($partes->contains('estado', 'pendiente_efectivo'));
        $this->assertTrue($partes->contains('estado', 'pendiente'));
        $this->assertTrue($partes->contains(fn ($parte) => $parte->estaPagada() && $parte->metodo === 'efectivo'));
    }

    public function test_la_mesa_5_esta_lista_para_cerrar(): void
    {
        $this->seed(PedidosEnCursoSeeder::class);

        $mesa5 = Mesa::withoutGlobalScopes()->where('restaurante_id', $this->laTasca()->id)->where('numero', 5)->sole();
        $cuenta = Cuenta::withoutGlobalScopes()->where('mesa_id', $mesa5->id)->sole();

        $this->assertSame([], $cuenta->faltaPorCobrar());
    }

    public function test_hay_postres_y_un_plato_agotado_en_la_carta(): void
    {
        $platos = Plato::withoutGlobalScopes()->where('restaurante_id', $this->laTasca()->id)->get();

        $this->assertTrue($platos->contains('nombre', 'Tarta de queso'));
        $this->assertFalse($platos->firstWhere('nombre', 'Pulpo a la gallega')->disponible);
    }

    public function test_ejecutarlo_dos_veces_no_duplica_nada(): void
    {
        $this->seed(PedidosEnCursoSeeder::class);
        $pedidos = Pedido::withoutGlobalScopes()->count();
        $cuentas = Cuenta::withoutGlobalScopes()->count();

        $this->seed(PedidosEnCursoSeeder::class);

        $this->assertSame($pedidos, Pedido::withoutGlobalScopes()->count());
        $this->assertSame($cuentas, Cuenta::withoutGlobalScopes()->count());
    }

    public function test_en_produccion_no_hace_nada(): void
    {
        $this->app['env'] = 'production';

        // Se ejecuta directamente: con $this->seed(), en producción Laravel
        // pediría confirmación por consola.
        app(PedidosEnCursoSeeder::class)->run();

        $this->assertSame(0, Pedido::withoutGlobalScopes()->count());
    }
}
