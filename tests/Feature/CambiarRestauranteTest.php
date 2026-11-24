<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Plato;
use App\Models\Restaurante;
use App\Models\User;
use Database\Seeders\UsuariosAdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Cambiar de restaurante desde el panel: solo las cuentas marcadas con
 * puede_cambiar_restaurante (equipo y profesor). Un admin normal sigue
 * viendo y tocando solo su restaurante (golden test nº 6).
 */
class CambiarRestauranteTest extends TestCase
{
    use RefreshDatabase;

    private Restaurante $tasca;

    private Restaurante $rincon;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tasca = Restaurante::factory()->create(['nombre' => 'La Tasca', 'slug' => 'la-tasca']);
        $this->rincon = Restaurante::factory()->create(['nombre' => 'Bar El Rincón', 'slug' => 'bar-el-rincon']);

        // Un plato en cada restaurante, para ver qué carta se ve.
        foreach ([[$this->tasca, 'Plato de La Tasca'], [$this->rincon, 'Plato de El Rincón']] as [$restaurante, $nombre]) {
            $categoria = Categoria::factory()->create(['restaurante_id' => $restaurante->id]);
            Plato::factory()->create(['categoria_id' => $categoria->id, 'nombre' => $nombre]);
        }
    }

    private function superAdmin(): User
    {
        $usuario = User::factory()->create(['restaurante_id' => $this->tasca->id]);
        // forceFill porque la columna no está en Fillable (a propósito).
        $usuario->forceFill(['puede_cambiar_restaurante' => true])->save();

        return $usuario;
    }

    public function test_una_cuenta_marcada_cambia_de_restaurante_y_ve_solo_su_carta(): void
    {
        $usuario = $this->superAdmin();

        $this->actingAs($usuario)
            ->post(route('panel.restaurante'), ['restaurante_id' => $this->rincon->id])
            ->assertRedirect(route('panel'))
            ->assertSessionHas('estado');

        $this->assertSame($this->rincon->id, $usuario->fresh()->restaurante_id);

        // Ahora es como un admin de El Rincón: ve su carta y no la de La Tasca.
        $this->actingAs($usuario->fresh())
            ->get(route('admin.carta'))
            ->assertOk()
            ->assertSee('Plato de El Rincón')
            ->assertDontSee('Plato de La Tasca');
    }

    public function test_un_admin_normal_no_puede_cambiar_de_restaurante(): void
    {
        $admin = User::factory()->create(['restaurante_id' => $this->tasca->id]);

        $this->actingAs($admin)
            ->post(route('panel.restaurante'), ['restaurante_id' => $this->rincon->id])
            ->assertForbidden();

        $this->assertSame($this->tasca->id, $admin->fresh()->restaurante_id);
    }

    public function test_camarero_y_cocina_no_pueden_aunque_tengan_la_marca(): void
    {
        foreach ([User::factory()->camarero(), User::factory()->cocina()] as $factoria) {
            $usuario = $factoria->create(['restaurante_id' => $this->tasca->id]);
            $usuario->forceFill(['puede_cambiar_restaurante' => true])->save();

            $this->actingAs($usuario)
                ->post(route('panel.restaurante'), ['restaurante_id' => $this->rincon->id])
                ->assertForbidden();

            $this->assertSame($this->tasca->id, $usuario->fresh()->restaurante_id);
        }
    }

    public function test_no_se_puede_elegir_un_restaurante_que_no_existe(): void
    {
        $usuario = $this->superAdmin();

        $this->actingAs($usuario)
            ->post(route('panel.restaurante'), ['restaurante_id' => 9999])
            ->assertSessionHasErrors('restaurante_id');

        $this->assertSame($this->tasca->id, $usuario->fresh()->restaurante_id);
    }

    public function test_solo_las_cuentas_marcadas_ven_el_desplegable(): void
    {
        $this->actingAs($this->superAdmin())->get(route('panel'))
            ->assertOk()
            ->assertSee(route('panel.restaurante'))
            ->assertSee('Bar El Rincón');

        $admin = User::factory()->create(['restaurante_id' => $this->tasca->id]);
        $this->actingAs($admin)->get(route('panel'))
            ->assertOk()
            ->assertDontSee(route('panel.restaurante'));
    }

    public function test_la_marca_no_se_puede_poner_desde_un_formulario(): void
    {
        // No está en Fillable: aunque llegue en los datos, se ignora.
        $usuario = new User(['name' => 'X', 'email' => 'x@example.com', 'password' => 'secreto123', 'puede_cambiar_restaurante' => true]);

        $this->assertNull($usuario->puede_cambiar_restaurante);
    }

    public function test_el_seeder_marca_las_cuentas_del_equipo_y_del_profesor(): void
    {
        $this->seed(UsuariosAdminSeeder::class);

        foreach (['ivan@mesaya.com', 'gabriel@mesaya.com', 'profesor@mesaya.com'] as $email) {
            $usuario = User::where('email', $email)->sole();
            $this->assertSame('admin', $usuario->rol);
            $this->assertTrue($usuario->puedeCambiarDeRestaurante(), $email);
        }
    }
}
