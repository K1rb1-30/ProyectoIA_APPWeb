<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

// Después de iniciar sesión, cada rol tiene que llegar a su pantalla.
class RedireccionTrasLoginTest extends TestCase
{
    use RefreshDatabase;

    private function iniciarSesion(User $usuario)
    {
        // "password" es la contraseña de prueba que pone UserFactory.
        return $this->post('/login', [
            'email' => $usuario->email,
            'password' => 'password',
        ]);
    }

    public function test_el_admin_va_al_panel(): void
    {
        $this->iniciarSesion(User::factory()->create())
            ->assertRedirect(route('panel'));
    }

    public function test_el_camarero_va_a_las_mesas(): void
    {
        $this->iniciarSesion(User::factory()->camarero()->create())
            ->assertRedirect(route('camarero.mesas'));
    }

    public function test_cocina_va_a_los_pedidos(): void
    {
        $this->iniciarSesion(User::factory()->cocina()->create())
            ->assertRedirect(route('cocina.pedidos'));
    }
}
