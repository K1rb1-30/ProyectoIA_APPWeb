<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\Concerns\InteractsWithViews;
use Tests\TestCase;

/**
 * Comprueba los componentes Blade del diseño base.
 * No usan base de datos: se renderiza el componente y se mira el HTML.
 */
class ComponentesDisenoTest extends TestCase
{
    use InteractsWithViews;

    public function test_el_precio_muestra_los_centimos_en_euros_con_coma(): void
    {
        $vista = $this->blade('<x-precio :centimos="1250" />');

        // false = no escapar el texto buscado, porque contiene &nbsp;
        $vista->assertSee('12,50&nbsp;€', false);
        $vista->assertSee('tabular-nums', false);
    }

    public function test_el_precio_muestra_siempre_dos_decimales(): void
    {
        $this->blade('<x-precio :centimos="900" />')->assertSee('9,00&nbsp;€', false);
        $this->blade('<x-precio :centimos="5" />')->assertSee('0,05&nbsp;€', false);
    }

    public function test_el_precio_separa_los_miles_con_punto(): void
    {
        $this->blade('<x-precio :centimos="148250" />')->assertSee('1.482,50&nbsp;€', false);
    }

    public function test_el_chip_muestra_cada_estado_de_pedido(): void
    {
        $this->blade('<x-chip-estado estado="pendiente" />')->assertSee('Pendiente');
        $this->blade('<x-chip-estado estado="preparando" />')->assertSee('Preparando');
        $this->blade('<x-chip-estado estado="listo" />')->assertSee('Listo');
        $this->blade('<x-chip-estado estado="servido" />')->assertSee('Servido');
    }

    public function test_el_badge_de_alergeno_critico_sale_en_rojo(): void
    {
        $this->blade('<x-badge-alergeno critico>Frutos secos</x-badge-alergeno>')
            ->assertSee('Frutos secos')
            ->assertSee('text-red-700', false);
    }
}
