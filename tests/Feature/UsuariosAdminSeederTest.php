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

    public function test_crea_las_dos_cuentas_del_equipo(): void
    {
        $this->seed(UsuariosAdminSeeder::class);

        $this->assertDatabaseHas('users', ['email' => 'gabriel@mesaya.com']);
        $this->assertDatabaseHas('users', ['email' => 'ivan@mesaya.com']);
    }

    public function test_se_puede_ejecutar_dos_veces_sin_duplicar(): void
    {
        $this->seed(UsuariosAdminSeeder::class);
        $this->seed(UsuariosAdminSeeder::class);

        $this->assertSame(2, User::count());
    }

    public function test_se_puede_entrar_con_la_contrasena_de_prueba(): void
    {
        $this->seed(UsuariosAdminSeeder::class);

        $respuesta = $this->post('/login', [
            'email' => 'ivan@mesaya.com',
            'password' => '1234',
        ]);

        $this->assertAuthenticated();
        $respuesta->assertRedirect();
    }
}
