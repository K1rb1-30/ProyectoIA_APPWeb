<?php

namespace Tests\Feature;

use App\Models\Mesa;
use App\Models\Restaurante;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

// CRUD de mesas y QR (rama 4): solo el admin entra, y solo ve y toca las
// mesas de su restaurante.
class MesasTest extends TestCase
{
    use RefreshDatabase;

    private Restaurante $restauranteA;

    private Restaurante $restauranteB;

    private User $adminA;

    protected function setUp(): void
    {
        parent::setUp();

        $this->restauranteA = Restaurante::factory()->create(['nombre' => 'La Tasca', 'slug' => 'la-tasca']);
        $this->restauranteB = Restaurante::factory()->create(['nombre' => 'El Rincón', 'slug' => 'el-rincon']);
        $this->adminA = User::factory()->create(['restaurante_id' => $this->restauranteA->id]);
    }

    public function test_el_admin_solo_ve_las_mesas_de_su_restaurante(): void
    {
        Mesa::factory()->create(['restaurante_id' => $this->restauranteA->id, 'numero' => 11]);
        Mesa::factory()->create(['restaurante_id' => $this->restauranteB->id, 'numero' => 22]);

        $respuesta = $this->actingAs($this->adminA)->get('/mesas');

        $respuesta->assertOk();
        $respuesta->assertSee('Mesa 11');
        $respuesta->assertDontSee('Mesa 22');
    }

    public function test_el_admin_crea_una_mesa_en_su_restaurante(): void
    {
        $respuesta = $this->actingAs($this->adminA)->post('/mesas', ['numero' => 7]);

        $respuesta->assertRedirect(route('mesas.index'));
        $this->assertDatabaseHas('mesas', [
            'restaurante_id' => $this->restauranteA->id,
            'numero' => 7,
            'estado' => 'libre',
        ]);
    }

    public function test_no_se_puede_colar_el_restaurante_de_otro_al_crear(): void
    {
        // restaurante_id no está en Fillable: aunque se envíe, se ignora.
        $this->actingAs($this->adminA)->post('/mesas', [
            'numero' => 8,
            'restaurante_id' => $this->restauranteB->id,
        ]);

        $this->assertDatabaseHas('mesas', ['numero' => 8, 'restaurante_id' => $this->restauranteA->id]);
        $this->assertDatabaseMissing('mesas', ['restaurante_id' => $this->restauranteB->id]);
    }

    public function test_no_se_puede_repetir_el_numero_en_el_mismo_restaurante(): void
    {
        Mesa::factory()->create(['restaurante_id' => $this->restauranteA->id, 'numero' => 3]);

        $respuesta = $this->actingAs($this->adminA)->post('/mesas', ['numero' => 3]);

        $respuesta->assertSessionHasErrors(['numero' => 'Ya hay una mesa con ese número.']);
        $this->assertSame(1, Mesa::count());
    }

    public function test_se_puede_repetir_el_numero_en_otro_restaurante(): void
    {
        Mesa::factory()->create(['restaurante_id' => $this->restauranteB->id, 'numero' => 3]);

        $respuesta = $this->actingAs($this->adminA)->post('/mesas', ['numero' => 3]);

        $respuesta->assertSessionHasNoErrors();
        $this->assertDatabaseHas('mesas', ['restaurante_id' => $this->restauranteA->id, 'numero' => 3]);
    }

    public function test_el_numero_es_obligatorio_y_positivo(): void
    {
        $this->actingAs($this->adminA)->post('/mesas', ['numero' => ''])
            ->assertSessionHasErrors('numero');

        $this->actingAs($this->adminA)->post('/mesas', ['numero' => 0])
            ->assertSessionHasErrors('numero');
    }

    public function test_el_admin_edita_y_borra_una_mesa_de_su_restaurante(): void
    {
        $mesa = Mesa::factory()->create(['restaurante_id' => $this->restauranteA->id, 'numero' => 4]);

        $this->actingAs($this->adminA)->put("/mesas/{$mesa->id}", ['numero' => 40])
            ->assertRedirect(route('mesas.index'));
        $this->assertSame(40, $mesa->fresh()->numero);

        $this->actingAs($this->adminA)->delete("/mesas/{$mesa->id}")
            ->assertRedirect(route('mesas.index'));
        $this->assertModelMissing($mesa);
    }

    public function test_el_admin_no_puede_editar_ni_borrar_una_mesa_de_otro_restaurante(): void
    {
        $mesaDeB = Mesa::factory()->create(['restaurante_id' => $this->restauranteB->id, 'numero' => 9]);

        // Para el admin de A esa mesa "no existe": 404.
        $this->actingAs($this->adminA)->get("/mesas/{$mesaDeB->id}/edit")->assertNotFound();
        $this->actingAs($this->adminA)->put("/mesas/{$mesaDeB->id}", ['numero' => 99])->assertNotFound();
        $this->actingAs($this->adminA)->delete("/mesas/{$mesaDeB->id}")->assertNotFound();

        $this->assertDatabaseHas('mesas', ['id' => $mesaDeB->id, 'numero' => 9]);
    }

    public function test_un_camarero_no_puede_entrar_al_crud(): void
    {
        $camarero = User::factory()->camarero()->create(['restaurante_id' => $this->restauranteA->id]);

        $this->actingAs($camarero)->get('/mesas')->assertForbidden();
        $this->actingAs($camarero)->post('/mesas', ['numero' => 1])->assertForbidden();
    }

    public function test_cocina_no_puede_entrar_al_crud(): void
    {
        $cocina = User::factory()->cocina()->create(['restaurante_id' => $this->restauranteA->id]);

        $this->actingAs($cocina)->get('/mesas')->assertForbidden();
        $this->actingAs($cocina)->get('/mesas/imprimir')->assertForbidden();
    }

    public function test_el_qr_contiene_la_url_con_el_slug_y_el_numero(): void
    {
        $mesa = Mesa::factory()->create(['restaurante_id' => $this->restauranteA->id, 'numero' => 5]);

        $this->assertSame(url('/r/la-tasca/mesa/5'), $mesa->urlQr());

        // La página muestra la URL y el dibujo del QR (un <svg>).
        // El test no puede "leer" el QR: comprueba la URL que recibe el generador.
        $this->actingAs($this->adminA)->get('/mesas')
            ->assertSee(url('/r/la-tasca/mesa/5'))
            ->assertSee('<svg', false);
    }

    public function test_la_pagina_para_imprimir_solo_tiene_las_mesas_del_restaurante(): void
    {
        Mesa::factory()->create(['restaurante_id' => $this->restauranteA->id, 'numero' => 1]);
        Mesa::factory()->create(['restaurante_id' => $this->restauranteB->id, 'numero' => 2]);

        $this->actingAs($this->adminA)->get('/mesas/imprimir')
            ->assertOk()
            ->assertSee(url('/r/la-tasca/mesa/1'))
            ->assertDontSee(url('/r/el-rincon/mesa/2'));
    }
}
