<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

// Comprueba el desplegable "Ver como" (componente selector-vista): lo ve el
// admin en todas sus pantallas y no lo ve nadie más.
class SelectorVistaTest extends TestCase
{
    use RefreshDatabase;

    public function test_el_admin_ve_el_selector_en_el_panel(): void
    {
        $admin = User::factory()->create();

        $this->actingAs($admin)->get('/dashboard')
            ->assertOk()
            ->assertSee('Ver como')
            ->assertSee(route('camarero.mesas'))
            ->assertSee(route('cocina.pedidos'));
    }

    public function test_el_admin_ve_el_selector_en_las_pantallas_de_camarero_y_cocina(): void
    {
        $admin = User::factory()->create();

        $this->actingAs($admin)->get('/camarero/mesas')->assertOk()->assertSee('Ver como');
        $this->actingAs($admin)->get('/cocina/pedidos')->assertOk()->assertSee('Ver como');
    }

    public function test_camarero_y_cocina_no_ven_el_selector(): void
    {
        $camarero = User::factory()->camarero()->create();
        $cocina = User::factory()->cocina()->create();

        $this->actingAs($camarero)->get('/camarero/mesas')->assertOk()->assertDontSee('Ver como');
        $this->actingAs($cocina)->get('/cocina/pedidos')->assertOk()->assertDontSee('Ver como');
    }
}
