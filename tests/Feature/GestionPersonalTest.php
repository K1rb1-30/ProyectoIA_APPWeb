<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Mesa;
use App\Models\Pedido;
use App\Models\Restaurante;
use App\Models\User;
use Database\Seeders\RestaurantesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Rama 18: el admin gestiona las cuentas de sus empleados en /admin/personal.
 *
 * User no usa el trait del global scope, así que estos tests comprueban sobre
 * todo que el filtro a mano por restaurante funciona (listado y {empleado}).
 */
class GestionPersonalTest extends TestCase
{
    use RefreshDatabase;

    private Restaurante $restauranteA;

    private User $adminA;

    protected function setUp(): void
    {
        parent::setUp();

        $this->restauranteA = Restaurante::factory()->create();
        $this->adminA = User::factory()->create(['name' => 'Admin de A', 'restaurante_id' => $this->restauranteA->id]);
    }

    public function test_el_admin_crea_un_camarero_que_puede_iniciar_sesion(): void
    {
        $this->actingAs($this->adminA)
            ->post(route('admin.personal.guardar'), [
                'name' => 'Camarero Inventado',
                'email' => '  Nuevo.Camarero@Ejemplo.TEST ',
                'rol' => 'camarero',
                'password' => 'contrasena-segura',
                'password_confirmation' => 'contrasena-segura',
            ])
            ->assertRedirect(route('admin.personal'))
            ->assertSessionHasNoErrors();

        // El correo se guarda limpio y en minúsculas.
        $camarero = User::where('email', 'nuevo.camarero@ejemplo.test')->firstOrFail();
        $this->assertSame('camarero', $camarero->rol);
        $this->assertSame($this->restauranteA->id, $camarero->restaurante_id);
        $this->assertNotNull($camarero->email_verified_at);
        // La contraseña nunca se guarda en claro.
        $this->assertNotSame('contrasena-segura', $camarero->password);
        $this->assertTrue(Hash::check('contrasena-segura', $camarero->password));

        // Ese camarero inicia sesión y va a su pantalla.
        auth()->logout();
        $this->post(route('login'), ['email' => 'nuevo.camarero@ejemplo.test', 'password' => 'contrasena-segura'])
            ->assertRedirect(route('camarero.mesas'));
        $this->assertAuthenticatedAs($camarero);
    }

    public function test_el_restaurante_id_del_formulario_se_ignora(): void
    {
        $otro = Restaurante::factory()->create();

        $this->actingAs($this->adminA)->post(route('admin.personal.guardar'), [
            'name' => 'Cocina Inventada',
            'email' => 'cocina.inventada@ejemplo.test',
            'rol' => 'cocina',
            'restaurante_id' => $otro->id,
            'password' => 'contrasena-segura',
            'password_confirmation' => 'contrasena-segura',
        ]);

        $this->assertSame($this->restauranteA->id, User::where('email', 'cocina.inventada@ejemplo.test')->value('restaurante_id'));
    }

    public function test_editar_sin_contrasena_no_la_cambia(): void
    {
        $camarero = $this->empleado('camarero');
        $hashAntes = $camarero->password;

        $this->actingAs($this->adminA)
            ->put(route('admin.personal.actualizar', $camarero), [
                'name' => 'Nombre Cambiado',
                'email' => $camarero->email,
                'rol' => 'cocina',
                'password' => '',
                'password_confirmation' => '',
            ])
            ->assertRedirect(route('admin.personal'))
            ->assertSessionHasNoErrors();

        $camarero->refresh();
        $this->assertSame('Nombre Cambiado', $camarero->name);
        $this->assertSame('cocina', $camarero->rol);
        $this->assertSame($hashAntes, $camarero->password);
    }

    public function test_cambiar_la_contrasena_la_guarda_con_hash_y_cierra_sus_sesiones(): void
    {
        $camarero = $this->empleado('camarero');
        DB::table('sessions')->insert(['id' => 'sesion-del-camarero', 'user_id' => $camarero->id, 'payload' => '', 'last_activity' => time()]);

        $this->actingAs($this->adminA)->put(route('admin.personal.actualizar', $camarero), [
            'name' => $camarero->name,
            'email' => $camarero->email,
            'rol' => 'camarero',
            'password' => 'otra-contrasena',
            'password_confirmation' => 'otra-contrasena',
        ])->assertSessionHasNoErrors();

        $this->assertTrue(Hash::check('otra-contrasena', $camarero->fresh()->password));
        $this->assertDatabaseMissing('sessions', ['id' => 'sesion-del-camarero']);
    }

    public function test_el_admin_de_a_no_ve_ni_edita_ni_borra_empleados_de_b(): void
    {
        $restauranteB = Restaurante::factory()->create();
        $camareroB = User::factory()->camarero()->create(['name' => 'Camarero de B', 'restaurante_id' => $restauranteB->id]);

        $this->actingAs($this->adminA);

        $this->get(route('admin.personal'))->assertOk()->assertDontSee('Camarero de B');
        $this->get(route('admin.personal.editar', $camareroB))->assertNotFound();
        $this->put(route('admin.personal.actualizar', $camareroB), [
            'name' => 'Cambiado', 'email' => $camareroB->email, 'rol' => 'cocina',
        ])->assertNotFound();
        $this->delete(route('admin.personal.borrar', $camareroB))->assertNotFound();

        $this->assertDatabaseHas('users', ['id' => $camareroB->id, 'name' => 'Camarero de B', 'rol' => 'camarero']);
    }

    public function test_el_admin_no_puede_borrarse_ni_quitarse_el_rol(): void
    {
        $this->actingAs($this->adminA);

        $this->delete(route('admin.personal.borrar', $this->adminA))
            ->assertRedirect(route('admin.personal'))
            ->assertSessionHas('aviso', 'No puedes eliminar tu propia cuenta.');

        $this->put(route('admin.personal.actualizar', $this->adminA), [
            'name' => $this->adminA->name,
            'email' => $this->adminA->email,
            'rol' => 'camarero',
        ])->assertSessionHasErrors(['rol' => 'No puedes quitarte tu propio rol de administrador.']);

        $this->assertDatabaseHas('users', ['id' => $this->adminA->id, 'rol' => 'admin']);
    }

    public function test_el_restaurante_no_se_queda_sin_ningun_admin(): void
    {
        $adminB = $this->empleado('admin');

        // Con dos admins, uno puede quitarle el rol al otro...
        $this->actingAs($this->adminA)->put(route('admin.personal.actualizar', $adminB), [
            'name' => $adminB->name,
            'email' => $adminB->email,
            'rol' => 'camarero',
        ])->assertSessionHasNoErrors();
        $this->assertSame('camarero', $adminB->fresh()->rol);

        // ...pero si el único admin que queda no es quien está logueado (otro
        // admin entrando "a la vez"), no se le puede quitar el rol ni borrar.
        // refresh(): $adminB tiene en memoria el rol de antes del PUT; sin
        // recargarlo, update() no vería ningún cambio y no guardaría nada.
        $adminB->refresh()->update(['rol' => 'admin']);
        $this->adminA->update(['rol' => 'camarero']);

        $this->actingAs($this->adminA->fresh()->forceFill(['rol' => 'admin']))
            ->put(route('admin.personal.actualizar', $adminB), [
                'name' => $adminB->name, 'email' => $adminB->email, 'rol' => 'cocina',
            ])
            ->assertSessionHasErrors(['rol' => 'El restaurante tiene que tener al menos un administrador.']);

        $this->delete(route('admin.personal.borrar', $adminB))
            ->assertSessionHas('aviso', 'El restaurante tiene que tener al menos un administrador.');

        $this->assertDatabaseHas('users', ['id' => $adminB->id, 'rol' => 'admin']);
    }

    public function test_camarero_y_cocina_no_pueden_entrar_en_personal(): void
    {
        foreach (['camarero', 'cocina'] as $rol) {
            $this->actingAs($this->empleado($rol))->get(route('admin.personal'))->assertForbidden();
        }
    }

    public function test_el_correo_es_unico_en_toda_la_aplicacion(): void
    {
        // El correo ya lo usa alguien de OTRO restaurante: tampoco vale.
        User::factory()->create(['email' => 'repetido@ejemplo.test', 'restaurante_id' => Restaurante::factory()]);

        $this->actingAs($this->adminA)
            ->post(route('admin.personal.guardar'), [
                'name' => 'Otra Persona',
                'email' => 'REPETIDO@ejemplo.test',
                'rol' => 'camarero',
                'password' => 'contrasena-segura',
                'password_confirmation' => 'contrasena-segura',
            ])
            ->assertSessionHasErrors(['email' => 'Ya existe una cuenta con ese correo. Cada persona necesita un correo distinto, porque es con el que inicia sesión.']);
    }

    public function test_crear_exige_contrasena_confirmada(): void
    {
        $this->actingAs($this->adminA)
            ->post(route('admin.personal.guardar'), [
                'name' => 'Sin Contrasena',
                'email' => 'sin.contrasena@ejemplo.test',
                'rol' => 'camarero',
                'password' => 'contrasena-segura',
                'password_confirmation' => 'otra-distinta',
            ])
            ->assertSessionHasErrors(['password' => 'Las dos contraseñas no coinciden.']);

        $this->assertDatabaseMissing('users', ['email' => 'sin.contrasena@ejemplo.test']);
    }

    public function test_borrar_un_camarero_con_pedidos_conserva_los_pedidos(): void
    {
        $camarero = $this->empleado('camarero');
        $mesa = Mesa::factory()->create(['restaurante_id' => $this->restauranteA->id]);
        $pedido = Pedido::create(['restaurante_id' => $this->restauranteA->id, 'mesa_id' => $mesa->id, 'user_id' => $camarero->id, 'estado' => 'servido']);

        // El listado avisa de lo que pasará con sus pedidos.
        $this->actingAs($this->adminA)->get(route('admin.personal'))
            ->assertSee('dejarán de tener asignado quién los tomó');

        $this->delete(route('admin.personal.borrar', $camarero))
            ->assertRedirect(route('admin.personal'))
            ->assertSessionHas('estado');

        $this->assertDatabaseMissing('users', ['id' => $camarero->id]);
        // El pedido sigue ahí, con user_id a NULL (nullOnDelete de la clave foránea).
        $this->assertDatabaseHas('pedidos', ['id' => $pedido->id, 'user_id' => null]);
    }

    public function test_el_listado_marca_tu_cuenta_y_el_menu_enlaza_a_personal(): void
    {
        $this->empleado('cocina', 'Persona de Cocina');

        $this->actingAs($this->adminA)->get(route('admin.personal'))
            ->assertOk()
            ->assertSee('Admin de A')
            ->assertSee('Persona de Cocina')
            ->assertSee('Tú')
            ->assertSee('href="'.route('admin.personal').'"', false);
    }

    /**
     * Cuenta del equipo o del profesor (puede cambiar de restaurante) que
     * ahora está en el restaurante A.
     */
    private function cuentaDelEquipo(): User
    {
        $cuenta = User::factory()->create(['name' => 'Cuenta del Equipo', 'restaurante_id' => $this->restauranteA->id]);
        $cuenta->forceFill(['puede_cambiar_restaurante' => true])->save();

        return $cuenta;
    }

    public function test_las_cuentas_del_equipo_no_salen_en_personal(): void
    {
        $this->cuentaDelEquipo();

        $this->actingAs($this->adminA)->get(route('admin.personal'))
            ->assertOk()
            ->assertSee('Admin de A')
            ->assertDontSee('Cuenta del Equipo');
    }

    public function test_el_admin_del_restaurante_no_puede_editar_ni_borrar_las_cuentas_del_equipo(): void
    {
        $cuenta = $this->cuentaDelEquipo();

        $this->actingAs($this->adminA)->get(route('admin.personal.editar', $cuenta))->assertNotFound();
        $this->actingAs($this->adminA)->put(route('admin.personal.actualizar', $cuenta), [
            'name' => 'Cambiado', 'email' => 'cambiado@ejemplo.test', 'rol' => 'cocina',
            'password' => 'otra-contrasena', 'password_confirmation' => 'otra-contrasena',
        ])->assertNotFound();
        $this->actingAs($this->adminA)->delete(route('admin.personal.borrar', $cuenta))->assertNotFound();

        $this->assertSame('Cuenta del Equipo', $cuenta->fresh()->name);
        $this->assertSame('admin', $cuenta->fresh()->rol);
    }

    public function test_las_cuentas_del_equipo_no_cuentan_como_el_admin_del_restaurante(): void
    {
        // La cuenta del equipo borra al único admin propio del restaurante: no la deja.
        $cuenta = $this->cuentaDelEquipo();

        $this->actingAs($cuenta)->delete(route('admin.personal.borrar', $this->adminA))
            ->assertRedirect(route('admin.personal'))
            ->assertSessionHas('aviso', 'El restaurante tiene que tener al menos un administrador.');

        $this->assertNotNull($this->adminA->fresh());
    }

    public function test_las_cuentas_del_equipo_si_pueden_borrar_empleados(): void
    {
        $camarero = User::factory()->camarero()->create(['restaurante_id' => $this->restauranteA->id]);

        $this->actingAs($this->cuentaDelEquipo())->delete(route('admin.personal.borrar', $camarero))
            ->assertRedirect(route('admin.personal'));

        $this->assertNull($camarero->fresh());
    }

    public function test_el_seeder_crea_varios_empleados_por_restaurante(): void
    {
        $this->seed(RestaurantesSeeder::class);
        // Se puede ejecutar dos veces sin repetir a nadie.
        $this->seed(RestaurantesSeeder::class);

        $laTasca = Restaurante::where('slug', 'la-tasca')->firstOrFail();
        $this->assertSame(6, User::where('restaurante_id', $laTasca->id)->count());
        $this->assertSame(3, User::where('restaurante_id', $laTasca->id)->where('rol', 'camarero')->count());
    }

    /**
     * Crea un empleado del restaurante A con el rol indicado.
     */
    private function empleado(string $rol, ?string $nombre = null): User
    {
        return User::factory()->create(array_filter([
            'rol' => $rol,
            'name' => $nombre,
            'restaurante_id' => $this->restauranteA->id,
        ]));
    }
}
