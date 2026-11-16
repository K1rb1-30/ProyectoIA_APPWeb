<?php

namespace Tests\Feature;

use App\Models\Concerns\PerteneceARestaurante;
use App\Models\Mesa;
use App\Models\Restaurante;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * Golden test nº 6: el restaurante A no ve datos del restaurante B.
 *
 * Todavía no existe ninguna tabla de negocio (platos y mesas llegan en las
 * ramas 3 y 4). Para probar el trait ya, creamos una tabla TEMPORAL de MySQL
 * con un modelo que solo existe en este test. Las tablas temporales se borran
 * solas y, a diferencia de las normales, no cierran la transacción que usa
 * RefreshDatabase para deshacer los cambios de cada test.
 *
 * Cuando existan Plato y Mesa, añadiremos aquí comprobaciones con ellos.
 */
class AislamientoRestaurantesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('registros_de_prueba', function (Blueprint $table) {
            $table->temporary();
            $table->id();
            $table->unsignedBigInteger('restaurante_id');
            $table->string('nombre');
            $table->timestamps();
        });
    }

    public function test_el_restaurante_a_no_ve_datos_del_restaurante_b(): void
    {
        $restauranteA = Restaurante::factory()->create();
        $restauranteB = Restaurante::factory()->create();
        $adminA = User::factory()->create(['restaurante_id' => $restauranteA->id]);

        // Creamos los datos sin nadie logueado, así el filtro no actúa todavía.
        RegistroDePrueba::create(['restaurante_id' => $restauranteA->id, 'nombre' => 'Dato de A']);
        RegistroDePrueba::create(['restaurante_id' => $restauranteB->id, 'nombre' => 'Dato de B']);

        $this->actingAs($adminA);

        $nombresVisibles = RegistroDePrueba::pluck('nombre')->all();

        $this->assertSame(['Dato de A'], $nombresVisibles);
    }

    public function test_el_restaurante_a_no_puede_buscar_un_dato_del_b_por_su_id(): void
    {
        $restauranteB = Restaurante::factory()->create();
        $datoDeB = RegistroDePrueba::create(['restaurante_id' => $restauranteB->id, 'nombre' => 'Dato de B']);

        $this->actingAs(User::factory()->create());

        $this->assertNull(RegistroDePrueba::find($datoDeB->id));
    }

    public function test_al_crear_un_registro_se_asigna_el_restaurante_del_usuario(): void
    {
        $camarero = User::factory()->camarero()->create();
        $this->actingAs($camarero);

        $registro = RegistroDePrueba::create(['nombre' => 'Sin restaurante indicado']);

        $this->assertSame($camarero->restaurante_id, $registro->restaurante_id);
    }

    public function test_el_restaurante_a_no_ve_las_mesas_del_restaurante_b(): void
    {
        $restauranteA = Restaurante::factory()->create();
        $restauranteB = Restaurante::factory()->create();
        Mesa::factory()->create(['restaurante_id' => $restauranteA->id, 'numero' => 1]);
        $mesaDeB = Mesa::factory()->create(['restaurante_id' => $restauranteB->id, 'numero' => 1]);

        $this->actingAs(User::factory()->create(['restaurante_id' => $restauranteA->id]));

        // Las dos se llaman "mesa 1", pero el admin de A solo ve la suya.
        $this->assertSame([$restauranteA->id], Mesa::pluck('restaurante_id')->all());
        $this->assertNull(Mesa::find($mesaDeB->id));
    }
}

/**
 * Modelo de prueba que usa el trait como lo harán Plato, Mesa, Pedido...
 */
class RegistroDePrueba extends Model
{
    use PerteneceARestaurante;

    protected $table = 'registros_de_prueba';

    protected $fillable = ['restaurante_id', 'nombre'];
}
