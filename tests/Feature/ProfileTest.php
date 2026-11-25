<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/perfil');

        $response->assertOk();
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/perfil', [
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/perfil');

        $user->refresh();

        $this->assertSame('Test User', $user->name);
        $this->assertSame('test@example.com', $user->email);
        $this->assertNull($user->email_verified_at);
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/perfil', [
                'name' => 'Test User',
                'email' => $user->email,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/perfil');

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    public function test_nadie_puede_borrar_su_propia_cuenta_desde_el_perfil(): void
    {
        // Las cuentas solo las borra el admin desde Personal: la ruta no existe.
        $user = User::factory()->create();

        $this->actingAs($user)
            ->delete('/perfil', ['password' => 'password'])
            ->assertStatus(405);

        $this->assertNotNull($user->fresh());
        $this->actingAs($user)->get('/perfil')->assertDontSee('Eliminar cuenta');
    }

    public function test_la_ruta_antigua_profile_redirige_a_perfil(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/profile')
            ->assertRedirect('/perfil');
    }
}
