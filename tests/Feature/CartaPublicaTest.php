<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Mesa;
use App\Models\Plato;
use App\Models\Restaurante;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Rama 8: carta pública que abre el cliente con el QR de su mesa
 * (/r/{slug}/mesa/{numero}, sin login).
 */
class CartaPublicaTest extends TestCase
{
    use RefreshDatabase;

    private Restaurante $restaurante;

    private Categoria $categoria;

    protected function setUp(): void
    {
        parent::setUp();

        // La Tasca, con la mesa 5 y una categoría "Tapas".
        $this->restaurante = Restaurante::factory()->create(['slug' => 'la-tasca', 'nombre' => 'La Tasca']);
        Mesa::factory()->create(['restaurante_id' => $this->restaurante->id, 'numero' => 5]);
        $this->categoria = Categoria::factory()->create(['restaurante_id' => $this->restaurante->id, 'nombre' => 'Tapas']);
    }

    private function urlCarta(string $slug = 'la-tasca', int $numero = 5): string
    {
        return route('cliente.carta', ['slug' => $slug, 'numero' => $numero]);
    }

    public function test_la_carta_se_abre_sin_iniciar_sesion(): void
    {
        Plato::factory()->create(['categoria_id' => $this->categoria->id, 'nombre' => 'Patatas bravas', 'precio_centimos' => 550]);

        $this->get($this->urlCarta())
            ->assertOk()
            ->assertSee('La Tasca')
            ->assertSee('Mesa 5')
            ->assertSee('Tapas')
            ->assertSee('Patatas bravas')
            ->assertSee('5,50');

        $this->assertGuest();
    }

    /*
     * Golden test nº 5 (parte de la carta): un plato no disponible no
     * aparece en la carta del QR. (La parte de "no se puede pedir" llega
     * con la rama 9, autopedido.)
     */
    public function test_un_plato_no_disponible_no_aparece_en_la_carta_del_qr(): void
    {
        Plato::factory()->create(['categoria_id' => $this->categoria->id, 'nombre' => 'Croquetas']);
        Plato::factory()->agotado()->create(['categoria_id' => $this->categoria->id, 'nombre' => 'Calamares agotados']);

        $this->get($this->urlCarta())
            ->assertOk()
            ->assertSee('Croquetas')
            ->assertDontSee('Calamares agotados');
    }

    public function test_una_categoria_sin_platos_disponibles_no_se_muestra(): void
    {
        $postres = Categoria::factory()->create(['restaurante_id' => $this->restaurante->id, 'nombre' => 'Postres']);
        Plato::factory()->agotado()->create(['categoria_id' => $postres->id]);
        Plato::factory()->create(['categoria_id' => $this->categoria->id]);

        $this->get($this->urlCarta())->assertOk()->assertDontSee('Postres');
    }

    public function test_da_404_si_el_restaurante_no_existe(): void
    {
        $this->get($this->urlCarta('no-existe'))->assertNotFound();
    }

    public function test_da_404_si_la_mesa_no_existe(): void
    {
        $this->get($this->urlCarta('la-tasca', 99))->assertNotFound();
    }

    public function test_da_404_si_la_mesa_es_de_otro_restaurante(): void
    {
        // La mesa 7 existe, pero es de otro restaurante: con el slug de La Tasca no vale.
        Mesa::factory()->create(['restaurante_id' => Restaurante::factory(), 'numero' => 7]);

        $this->get($this->urlCarta('la-tasca', 7))->assertNotFound();
    }

    public function test_un_numero_de_mesa_que_no_es_un_numero_da_404(): void
    {
        $this->get('/r/la-tasca/mesa/abc')->assertNotFound();
    }

    /*
     * Golden test nº 6 aplicado a la carta pública: con el slug de A solo
     * salen platos de A, aunque quien mira esté logueado en B.
     */
    public function test_solo_salen_los_platos_del_restaurante_del_slug(): void
    {
        Plato::factory()->create(['categoria_id' => $this->categoria->id, 'nombre' => 'Plato de La Tasca']);
        Plato::factory()->create(['nombre' => 'Plato de otro bar']);

        $this->get($this->urlCarta())
            ->assertSee('Plato de La Tasca')
            ->assertDontSee('Plato de otro bar');
    }

    public function test_un_admin_de_otro_restaurante_ve_la_carta_del_slug_y_no_la_suya(): void
    {
        Plato::factory()->create(['categoria_id' => $this->categoria->id, 'nombre' => 'Plato de La Tasca']);

        // Admin de otro restaurante, con su propio plato.
        $adminAjeno = User::factory()->create();
        $categoriaAjena = Categoria::factory()->create(['restaurante_id' => $adminAjeno->restaurante_id]);
        Plato::factory()->create(['categoria_id' => $categoriaAjena->id, 'nombre' => 'Plato del admin ajeno']);

        $this->actingAs($adminAjeno)
            ->get($this->urlCarta())
            ->assertOk()
            ->assertSee('Plato de La Tasca')
            ->assertDontSee('Plato del admin ajeno');
    }

    public function test_la_url_del_qr_de_la_mesa_abre_su_carta(): void
    {
        $mesa = Mesa::withoutGlobalScopes()->where('numero', 5)->first();

        $this->assertSame($this->urlCarta(), $mesa->urlQr());
        $this->get($mesa->urlQr())->assertOk();
    }

    public function test_el_admin_tiene_la_opcion_cliente_en_ver_como(): void
    {
        $admin = User::factory()->create(['restaurante_id' => $this->restaurante->id]);

        $this->actingAs($admin)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Cliente (mesa 5)')
            ->assertSee($this->urlCarta());
    }

    public function test_sin_mesas_la_opcion_cliente_sale_desactivada(): void
    {
        $adminSinMesas = User::factory()->create();

        $this->actingAs($adminSinMesas)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Cliente (crea una mesa)');
    }
}
