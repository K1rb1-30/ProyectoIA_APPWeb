<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UsuariosAdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

// Comprueba que las cuentas fijas del equipo se crean y sirven para entrar.
class UsuariosAdminSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_crea_las_cuentas_del_equipo_y_del_profesor(): void
    {
        $this->seed(UsuariosAdminSeeder::class);

        $this->assertDatabaseHas('users', ['email' => 'gabriel@mesaya.com', 'rol' => 'admin']);
        $this->assertDatabaseHas('users', ['email' => 'ivan@mesaya.com', 'rol' => 'admin']);
        $this->assertDatabaseHas('users', ['email' => 'profesor@mesaya.com', 'rol' => 'admin']);
    }

    public function test_las_cuentas_pertenecen_a_la_tasca_de_prueba(): void
    {
        $this->seed(UsuariosAdminSeeder::class);

        $ivan = User::where('email', 'ivan@mesaya.com')->first();

        $this->assertSame('la-tasca', $ivan->restaurante->slug);
    }

    public function test_se_puede_ejecutar_dos_veces_sin_duplicar(): void
    {
        $this->seed(UsuariosAdminSeeder::class);
        $this->seed(UsuariosAdminSeeder::class);

        $this->assertSame(3, User::count());
    }

    public function test_se_puede_entrar_con_la_contrasena_de_prueba(): void
    {
        $this->seed(UsuariosAdminSeeder::class);

        $respuesta = $this->post('/login', [
            'email' => 'ivan@mesaya.com',
            'password' => '1234',
        ]);

        $this->assertAuthenticated();
        // Son admin, así que tras el login van al panel.
        $respuesta->assertRedirect(route('panel'));
    }
}
