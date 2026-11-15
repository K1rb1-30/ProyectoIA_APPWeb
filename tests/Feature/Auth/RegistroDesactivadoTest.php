<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;

// En MesaYa nadie puede registrarse por su cuenta: las cuentas del personal
// las crea el administrador. Estos tests comprueban que las rutas de
// registro que trae Breeze ya no existen.
class RegistroDesactivadoTest extends TestCase
{
    public function test_la_pantalla_de_registro_no_existe(): void
    {
        $respuesta = $this->get('/register');

        $respuesta->assertNotFound();
    }

    public function test_no_se_puede_enviar_el_formulario_de_registro(): void
    {
        $respuesta = $this->post('/register', [
            'name' => 'Persona Inventada',
            'email' => 'inventada@example.com',
            'password' => 'contrasena-de-prueba',
            'password_confirmation' => 'contrasena-de-prueba',
        ]);

        $respuesta->assertNotFound();
        $this->assertGuest();
    }
}
