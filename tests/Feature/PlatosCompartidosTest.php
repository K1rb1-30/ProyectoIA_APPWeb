<?php

namespace Tests\Feature;

use App\Acciones\CrearPedido;
use App\Models\Categoria;
use App\Models\Cuenta;
use App\Models\Mesa;
use App\Models\Pedido;
use App\Models\Plato;
use App\Models\Restaurante;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Rama 14: al dividir por platos, un plato puede ser «Todos» (compartido) y
 * se reparte entre todas las personas. En el formulario, la persona 0.
 */
class PlatosCompartidosTest extends TestCase
{
    use RefreshDatabase;

    private Mesa $mesa;

    private Categoria $categoria;

    private Pedido $pedido;

    protected function setUp(): void
    {
        parent::setUp();

        $restaurante = Restaurante::factory()->create(['slug' => 'la-tasca']);
        $this->mesa = Mesa::factory()->create(['restaurante_id' => $restaurante->id, 'numero' => 5]);
        $this->categoria = Categoria::factory()->create(['restaurante_id' => $restaurante->id]);
        $croquetas = $this->plato('Croquetas', 850);
        $bravas = $this->plato('Bravas', 600);

        // Pedido de 14,50 €: 1 croquetas (8,50) + 1 bravas (6,00).
        $this->pedido = (new CrearPedido)->ejecutar($this->mesa, [
            ['plato_id' => $croquetas->id, 'cantidad' => 1],
            ['plato_id' => $bravas->id, 'cantidad' => 1],
        ]);
    }

    private function plato(string $nombre, int $precio): Plato
    {
        return Plato::factory()->create(['categoria_id' => $this->categoria->id, 'nombre' => $nombre, 'precio_centimos' => $precio]);
    }

    /** Id de la línea del pedido de un plato, buscando por su nombre. */
    private function linea(string $nombre, ?Pedido $pedido = null): int
    {
        return ($pedido ?? $this->pedido)->lineas()->whereHas('plato', fn ($consulta) => $consulta->where('nombre', $nombre))->value('id');
    }

    private function dividirPorPlatos(int $personas, array $asignaciones)
    {
        return $this->post(route('cliente.cuenta.dividir', ['slug' => 'la-tasca', 'numero' => 5]), [
            'modo' => 'platos',
            'personas' => $personas,
            'asignaciones' => $asignaciones,
        ]);
    }

    private function importes(): array
    {
        return Cuenta::withoutGlobalScopes()->sole()->partes()->orderBy('persona')->pluck('importe_centimos', 'persona')->all();
    }

    public function test_un_plato_compartido_se_reparte_y_se_suma_a_lo_de_cada_uno(): void
    {
        // Croquetas para la persona 1 y bravas para todos (6,00 € entre 3 = 2,00 cada uno).
        $this->dividirPorPlatos(3, [
            $this->linea('Croquetas') => 1,
            $this->linea('Bravas') => 0,
        ])->assertSessionHasNoErrors();

        $this->assertSame([1 => 850 + 200, 2 => 200, 3 => 200], $this->importes());
        $this->assertSame(1450, array_sum($this->importes()));
    }

    public function test_el_centimo_que_sobra_de_lo_compartido_va_a_la_primera_persona(): void
    {
        // Todo compartido: 14,50 € entre 3 → 4,84 + 4,83 + 4,83.
        $this->dividirPorPlatos(3, [
            $this->linea('Croquetas') => 0,
            $this->linea('Bravas') => 0,
        ])->assertSessionHasNoErrors();

        $this->assertSame([1 => 484, 2 => 483, 3 => 483], $this->importes());
    }

    public function test_todo_compartido_da_lo_mismo_que_a_partes_iguales(): void
    {
        $this->dividirPorPlatos(4, [
            $this->linea('Croquetas') => 0,
            $this->linea('Bravas') => 0,
        ]);
        $porPlatos = $this->importes();

        $this->post(route('cliente.cuenta.dividir', ['slug' => 'la-tasca', 'numero' => 5]), ['modo' => 'iguales', 'personas' => 4]);

        $this->assertSame($porPlatos, $this->importes());
    }

    public function test_los_compartidos_se_suman_antes_de_repartir(): void
    {
        // Tres platos de 1,00 € compartidos entre 3. Sumados (3,00 €) dan 1,00 a
        // cada uno. Si se repartiera plato a plato (0,34 + 0,33 + 0,33 cada uno),
        // la persona 1 pagaría 1,02 y las otras 0,99: no sería justo.
        $pedido = (new CrearPedido)->ejecutar($this->mesa, [
            ['plato_id' => $this->plato('Pan', 100)->id, 'cantidad' => 1],
            ['plato_id' => $this->plato('Aceitunas', 100)->id, 'cantidad' => 1],
            ['plato_id' => $this->plato('Patatas fritas', 100)->id, 'cantidad' => 1],
        ]);

        $this->dividirPorPlatos(3, [
            $this->linea('Croquetas') => 1,
            $this->linea('Bravas') => 2,
            $this->linea('Pan', $pedido) => 0,
            $this->linea('Aceitunas', $pedido) => 0,
            $this->linea('Patatas fritas', $pedido) => 0,
        ])->assertSessionHasNoErrors();

        $this->assertSame([1 => 850 + 100, 2 => 600 + 100, 3 => 100], $this->importes());
        $this->assertSame(1750, array_sum($this->importes()));
    }

    public function test_no_se_puede_asignar_un_plato_a_una_persona_que_no_existe(): void
    {
        $this->dividirPorPlatos(3, [
            $this->linea('Croquetas') => 5,
            $this->linea('Bravas') => 0,
        ])->assertSessionHasErrors('asignaciones.'.$this->linea('Croquetas').'.0');

        $this->assertSame(0, Cuenta::withoutGlobalScopes()->count());
    }

    /** Añade a la mesa un pedido de 2 cafés (1,30 € cada uno) y devuelve su línea. */
    private function pedirDosCafes(): int
    {
        $cafe = $this->plato('Café', 130);
        $pedido = (new CrearPedido)->ejecutar($this->mesa, [['plato_id' => $cafe->id, 'cantidad' => 2]]);

        return $this->linea('Café', $pedido);
    }

    public function test_las_unidades_de_una_linea_se_pueden_repartir_entre_personas(): void
    {
        // 2 cafés: uno para cada persona (antes los dos iban a la misma).
        $cafes = $this->pedirDosCafes();

        $this->dividirPorPlatos(2, [
            $this->linea('Croquetas') => [1],
            $this->linea('Bravas') => [2],
            $cafes => [1, 2],
        ])->assertSessionHasNoErrors();

        // Persona 1: croquetas 8,50 + café 1,30. Persona 2: bravas 6,00 + café 1,30.
        $this->assertSame([1 => 980, 2 => 730], $this->importes());
        $this->assertSame(1710, Cuenta::withoutGlobalScopes()->sole()->total_centimos);
    }

    public function test_una_unidad_puede_ser_compartida(): void
    {
        // Un café para la persona 1 y el otro para todos (0,65 cada una).
        $cafes = $this->pedirDosCafes();

        $this->dividirPorPlatos(2, [
            $this->linea('Croquetas') => 1,
            $this->linea('Bravas') => 2,
            $cafes => [1, 0],
        ])->assertSessionHasNoErrors();

        $this->assertSame([1 => 850 + 130 + 65, 2 => 600 + 65], $this->importes());
    }

    public function test_al_separar_hay_que_elegir_una_persona_por_unidad(): void
    {
        // 3 personas para una línea de 2 cafés: no cuadra.
        $cafes = $this->pedirDosCafes();

        $this->dividirPorPlatos(2, [
            $this->linea('Croquetas') => 1,
            $this->linea('Bravas') => 2,
            $cafes => [1, 2, 1],
        ])->assertSessionHasErrors('asignaciones');

        $this->assertSame(0, Cuenta::withoutGlobalScopes()->count());
    }

    public function test_por_platos_hace_falta_el_numero_de_personas(): void
    {
        $this->post(route('cliente.cuenta.dividir', ['slug' => 'la-tasca', 'numero' => 5]), [
            'modo' => 'platos',
            'asignaciones' => [$this->linea('Croquetas') => 0, $this->linea('Bravas') => 0],
        ])->assertSessionHasErrors('personas');
    }

    public function test_con_pagos_hechos_lo_nuevo_compartido_no_toca_las_partes_antiguas(): void
    {
        // Primero se divide entre 2 y la persona 1 paga su parte.
        $this->post(route('cliente.cuenta.dividir', ['slug' => 'la-tasca', 'numero' => 5]), ['modo' => 'iguales', 'personas' => 2]);
        $cuenta = Cuenta::withoutGlobalScopes()->sole();
        $cuenta->partes()->where('persona', 1)->update(['estado' => 'pagado', 'metodo' => 'tarjeta']);

        // Después piden una ración para compartir (9,00 € entre 3).
        $racion = (new CrearPedido)->ejecutar($this->mesa, [['plato_id' => $this->plato('Calamares', 900)->id, 'cantidad' => 1]]);

        $this->dividirPorPlatos(3, [$this->linea('Calamares', $racion) => 0])->assertSessionHasNoErrors();

        // Las dos partes de antes siguen igual (7,25 + 7,25) y se añaden la 3, 4 y 5.
        $this->assertSame([1 => 725, 2 => 725, 3 => 300, 4 => 300, 5 => 300], $this->importes());
        $this->assertSame('pagado', $cuenta->partes()->where('persona', 1)->value('estado'));
        $this->assertSame(1450 + 900, $cuenta->fresh()->total_centimos);
    }

    public function test_la_pantalla_ofrece_la_opcion_todos(): void
    {
        $respuesta = $this->get(route('cliente.cuenta', ['slug' => 'la-tasca', 'numero' => 5]))
            ->assertOk()
            ->assertSee('Todos (compartido)');

        // El JavaScript del formulario va dentro del atributo x-data. Si unas
        // comillas dobles lo cortan (pasó al hacer esta rama), el atributo se
        // queda a medias. Leemos el HTML como lo leería el navegador y
        // comprobamos que el x-data del formulario llega entero hasta el final.
        $html = new \DOMDocument;
        @$html->loadHTML($respuesta->getContent());
        $xData = '';
        foreach ($html->getElementsByTagName('form') as $formulario) {
            if (str_contains($formulario->getAttribute('x-data'), 'partesPorPlatos')) {
                $xData = $formulario->getAttribute('x-data');
            }
        }

        $this->assertStringContainsString('euros(c)', $xData);
    }
}
