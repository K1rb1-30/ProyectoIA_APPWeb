<?php

namespace Tests\Feature;

use App\Models\Alergeno;
use App\Models\Categoria;
use App\Models\Plato;
use App\Models\Restaurante;
use App\Models\User;
use Database\Seeders\AlergenosSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Rama 3: gestión de la carta (platos, categorías y alérgenos) por el admin.
 */
class GestionCartaTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private Categoria $categoria;

    protected function setUp(): void
    {
        parent::setUp();

        // Un admin con una categoría en su restaurante.
        $this->admin = User::factory()->create();
        $this->categoria = Categoria::factory()->create([
            'restaurante_id' => $this->admin->restaurante_id,
            'nombre' => 'Tapas',
        ]);
    }

    public function test_el_admin_ve_la_carta_de_su_restaurante(): void
    {
        Plato::factory()->create(['categoria_id' => $this->categoria->id, 'nombre' => 'Patatas bravas']);

        $this->actingAs($this->admin)
            ->get(route('admin.carta'))
            ->assertOk()
            ->assertSee('Patatas bravas');
    }

    public function test_camarero_y_cocina_no_pueden_entrar_en_la_carta(): void
    {
        $camarero = User::factory()->camarero()->create();
        $cocina = User::factory()->cocina()->create();

        $this->actingAs($camarero)->get(route('admin.carta'))->assertForbidden();
        $this->actingAs($cocina)->get(route('admin.carta'))->assertForbidden();
    }

    public function test_crear_un_plato_guarda_el_precio_en_centimos_la_foto_y_los_alergenos(): void
    {
        // Storage::fake usa un disco de mentira: no se escriben archivos reales.
        Storage::fake('public');
        $this->seed(AlergenosSeeder::class);
        $gluten = Alergeno::where('nombre', 'Gluten')->first();
        $huevos = Alergeno::where('nombre', 'Huevos')->first();

        $this->actingAs($this->admin)->post(route('admin.carta.guardar'), [
            'nombre' => 'Croquetas caseras',
            'descripcion' => 'De jamón.',
            'categoria_id' => $this->categoria->id,
            'precio' => '8,50',
            'foto' => UploadedFile::fake()->image('croquetas.jpg'),
            'alergenos' => [$gluten->id, $huevos->id],
            'disponible' => '1',
        ])->assertRedirect(route('admin.carta'));

        $plato = Plato::where('nombre', 'Croquetas caseras')->first();

        $this->assertSame(850, $plato->precio_centimos);
        $this->assertSame($this->admin->restaurante_id, $plato->restaurante_id);
        $this->assertTrue($plato->disponible);
        $this->assertEqualsCanonicalizing([$gluten->id, $huevos->id], $plato->alergenos->pluck('id')->all());
        Storage::disk('public')->assertExists($plato->foto);
    }

    public function test_el_precio_se_convierte_bien_a_centimos(): void
    {
        // Precio escrito => céntimos esperados.
        $casos = ['8' => 800, '8,5' => 850, '8.50' => 850, '0,05' => 5, '12,99' => 1299];

        $this->actingAs($this->admin);

        foreach ($casos as $escrito => $centimos) {
            $this->post(route('admin.carta.guardar'), [
                'nombre' => "Plato {$escrito}",
                'categoria_id' => $this->categoria->id,
                'precio' => $escrito,
            ])->assertSessionHasNoErrors();

            $this->assertSame($centimos, Plato::where('nombre', "Plato {$escrito}")->value('precio_centimos'));
        }
    }

    public function test_no_se_acepta_un_precio_mal_escrito(): void
    {
        $this->actingAs($this->admin)->post(route('admin.carta.guardar'), [
            'nombre' => 'Plato raro',
            'categoria_id' => $this->categoria->id,
            'precio' => '8,505',
        ])->assertSessionHasErrors('precio');

        $this->assertSame(0, Plato::count());
    }

    public function test_no_se_puede_usar_una_categoria_de_otro_restaurante(): void
    {
        $categoriaAjena = Categoria::factory()->create();

        $this->actingAs($this->admin)->post(route('admin.carta.guardar'), [
            'nombre' => 'Plato colado',
            'categoria_id' => $categoriaAjena->id,
            'precio' => '5',
        ])->assertSessionHasErrors('categoria_id');
    }

    public function test_editar_un_plato_cambia_sus_datos_y_su_foto(): void
    {
        Storage::fake('public');
        $fotoVieja = UploadedFile::fake()->image('vieja.jpg')->store('platos', 'public');
        $plato = Plato::factory()->create(['categoria_id' => $this->categoria->id, 'foto' => $fotoVieja]);

        $this->actingAs($this->admin)->put(route('admin.carta.actualizar', $plato), [
            'nombre' => 'Nombre nuevo',
            'categoria_id' => $this->categoria->id,
            'precio' => '3,20',
            'foto' => UploadedFile::fake()->image('nueva.png'),
        ])->assertRedirect(route('admin.carta'));

        $plato->refresh();
        $this->assertSame('Nombre nuevo', $plato->nombre);
        $this->assertSame(320, $plato->precio_centimos);
        // Sin la casilla "disponible" marcada, el plato queda agotado.
        $this->assertFalse($plato->disponible);
        // La foto vieja se borra y la nueva existe.
        Storage::disk('public')->assertMissing($fotoVieja);
        Storage::disk('public')->assertExists($plato->foto);
    }

    public function test_el_interruptor_cambia_la_disponibilidad(): void
    {
        $plato = Plato::factory()->create(['categoria_id' => $this->categoria->id, 'disponible' => true]);

        $this->actingAs($this->admin)->patch(route('admin.carta.disponibilidad', $plato));
        $this->assertFalse($plato->fresh()->disponible);

        $this->actingAs($this->admin)->patch(route('admin.carta.disponibilidad', $plato));
        $this->assertTrue($plato->fresh()->disponible);
    }

    public function test_borrar_un_plato(): void
    {
        $plato = Plato::factory()->create(['categoria_id' => $this->categoria->id]);

        $this->actingAs($this->admin)->delete(route('admin.carta.borrar', $plato))->assertRedirect();

        $this->assertModelMissing($plato);
    }

    public function test_no_se_puede_borrar_una_categoria_con_platos(): void
    {
        Plato::factory()->create(['categoria_id' => $this->categoria->id]);

        $this->actingAs($this->admin)
            ->delete(route('admin.carta.categorias.borrar', $this->categoria))
            ->assertSessionHas('aviso');

        $this->assertModelExists($this->categoria);
    }

    public function test_crear_editar_y_borrar_una_categoria(): void
    {
        $this->actingAs($this->admin);

        $this->post(route('admin.carta.categorias.guardar'), ['nombre' => 'Postres', 'orden' => 5])->assertSessionHasNoErrors();
        $postres = Categoria::where('nombre', 'Postres')->first();
        $this->assertSame($this->admin->restaurante_id, $postres->restaurante_id);

        $this->put(route('admin.carta.categorias.actualizar', $postres), ['nombre' => 'Dulces', 'orden' => 2])->assertSessionHasNoErrors();
        $this->assertSame('Dulces', $postres->fresh()->nombre);

        $this->delete(route('admin.carta.categorias.borrar', $postres));
        $this->assertModelMissing($postres);
    }

    public function test_no_puede_haber_dos_categorias_con_el_mismo_nombre_en_un_restaurante(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.carta.categorias.guardar'), ['nombre' => 'Tapas', 'orden' => 1])
            ->assertSessionHasErrors('nombre');
    }

    public function test_la_pagina_de_alergenos_muestra_los_14(): void
    {
        $this->seed(AlergenosSeeder::class);

        $this->actingAs($this->admin)
            ->get(route('admin.alergenos'))
            ->assertOk()
            ->assertSee('Gluten')
            ->assertSee('Moluscos');
    }

    public function test_el_seeder_crea_exactamente_14_alergenos_y_no_duplica(): void
    {
        $this->seed(AlergenosSeeder::class);
        $this->seed(AlergenosSeeder::class);

        $this->assertSame(14, Alergeno::count());
    }

    /*
     * Golden test nº 6 aplicado a la carta: el restaurante A no ve ni toca
     * los platos ni las categorías del restaurante B.
     */
    public function test_el_admin_no_ve_los_platos_de_otro_restaurante(): void
    {
        $platoAjeno = Plato::factory()->create(['nombre' => 'Plato del otro bar']);

        $this->actingAs($this->admin)
            ->get(route('admin.carta'))
            ->assertOk()
            ->assertDontSee('Plato del otro bar');

        $this->assertNotSame($this->admin->restaurante_id, $platoAjeno->restaurante_id);
    }

    public function test_el_admin_no_puede_editar_ni_borrar_platos_de_otro_restaurante(): void
    {
        $platoAjeno = Plato::factory()->create();

        $this->actingAs($this->admin);

        $this->get(route('admin.carta.editar', $platoAjeno))->assertNotFound();
        $this->put(route('admin.carta.actualizar', $platoAjeno), [
            'nombre' => 'Hackeado',
            'categoria_id' => $this->categoria->id,
            'precio' => '1',
        ])->assertNotFound();
        $this->patch(route('admin.carta.disponibilidad', $platoAjeno))->assertNotFound();
        $this->delete(route('admin.carta.borrar', $platoAjeno))->assertNotFound();

        // El plato del otro restaurante sigue igual.
        $this->assertModelExists($platoAjeno);
        $this->assertNotSame('Hackeado', $platoAjeno->fresh()->nombre);
    }

    public function test_el_admin_no_puede_tocar_categorias_de_otro_restaurante(): void
    {
        $categoriaAjena = Categoria::factory()->create(['restaurante_id' => Restaurante::factory()]);

        $this->actingAs($this->admin);

        $this->put(route('admin.carta.categorias.actualizar', $categoriaAjena), ['nombre' => 'X', 'orden' => 1])->assertNotFound();
        $this->delete(route('admin.carta.categorias.borrar', $categoriaAjena))->assertNotFound();
        $this->assertModelExists($categoriaAjena);
    }
}
