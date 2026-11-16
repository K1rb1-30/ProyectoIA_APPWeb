<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\Concerns\InteractsWithViews;
use Tests\TestCase;

/**
 * Comprueba que los cuatro layouts se renderizan sin errores y que
 * muestran su navegación y el contenido que se les pasa.
 */
class LayoutsTest extends TestCase
{
    use InteractsWithViews;

    public function test_el_layout_de_admin_muestra_el_menu_lateral(): void
    {
        $vista = $this->blade('<x-layouts.admin titulo="Carta">Contenido de prueba</x-layouts.admin>');

        $vista->assertSeeInOrder(['Carta', 'Alérgenos', 'Mesas y QR', 'Personal', 'Estadísticas']);
        $vista->assertSee('Contenido de prueba');
        $vista->assertSee('Cerrar sesión');
    }

    public function test_el_layout_de_camarero_marca_la_pestana_activa(): void
    {
        $vista = $this->blade('<x-layouts.camarero titulo="Mesas" seccion="avisos">Contenido de prueba</x-layouts.camarero>');

        $vista->assertSeeInOrder(['Mesas', 'Avisos', 'Cobros']);
        $vista->assertSee('Contenido de prueba');
        // Solo una pestaña lleva aria-current="page": la activa.
        $this->assertSame(1, substr_count((string) $vista, 'aria-current="page"'));
    }

    public function test_el_layout_de_cocina_muestra_el_titulo_y_el_contenido(): void
    {
        $vista = $this->blade('<x-layouts.cocina titulo="Pedidos">Contenido de prueba</x-layouts.cocina>');

        $vista->assertSee('Pedidos');
        $vista->assertSee('Contenido de prueba');
    }

    public function test_el_layout_de_cliente_muestra_el_restaurante_y_la_mesa(): void
    {
        $vista = $this->blade('<x-layouts.cliente restaurante="Bar de Prueba" :mesa="5">Contenido de prueba</x-layouts.cliente>');

        $vista->assertSee('Bar de Prueba');
        $vista->assertSee('Mesa 5');
        $vista->assertSeeInOrder(['Carta', 'Mi pedido', 'Cuenta']);
        $vista->assertSee('Contenido de prueba');
    }
}
