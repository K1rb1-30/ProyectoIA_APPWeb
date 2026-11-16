<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

// Comprueba que el middleware ComprobarRol ("rol:...") solo deja entrar a
// cada rol en sus pantallas.
class ComprobarRolTest extends TestCase
{
    use RefreshDatabase;

    public function test_el_admin_puede_entrar_en_el_panel(): void
    {
        $admin = User::factory()->create();

        $this->actingAs($admin)->get('/dashboard')->assertOk();
    }

    public function test_un_camarero_no_puede_entrar_en_el_panel_de_admin(): void
    {
        $camarero = User::factory()->camarero()->create();

        $this->actingAs($camarero)->get('/dashboard')->assertForbidden();
    }

    public function test_el_camarero_puede_entrar_en_sus_mesas(): void
    {
        $camarero = User::factory()->camarero()->create();

        $this->actingAs($camarero)->get('/camarero/mesas')->assertOk();
    }

    public function test_cocina_no_puede_entrar_en_las_mesas_del_camarero(): void
    {
        $cocina = User::factory()->cocina()->create();

        $this->actingAs($cocina)->get('/camarero/mesas')->assertForbidden();
    }

    public function test_un_invitado_es_enviado_al_login(): void
    {
        $this->get('/cocina/pedidos')->assertRedirect('/login');
    }
}
