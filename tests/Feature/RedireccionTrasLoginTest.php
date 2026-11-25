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

    public function test_un_camarero_no_entra_por_el_login(): void
    {
        // Cuenta antigua de camarero que todavía tiene correo y contraseña:
        // aun así no puede entrar por /login (entra con su PIN en un dispositivo).
        $camarero = User::factory()->create(['rol' => 'camarero']);

        $this->iniciarSesion($camarero)
            ->assertSessionHasErrors(['email' => 'Los camareros entran desde la tablet o el móvil del bar, con su nombre y su PIN.']);
        $this->assertGuest();
    }

    public function test_cocina_va_a_los_pedidos(): void
    {
        $this->iniciarSesion(User::factory()->cocina()->create())
            ->assertRedirect(route('cocina.pedidos'));
    }
}
