<?php

namespace Tests\Feature;

use App\Models\Mesa;
use App\Models\Restaurante;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Rama 16: página de inicio pública en "/" y páginas de privacidad y cookies.
 */
class InicioTest extends TestCase
{
    use RefreshDatabase;

    public function test_la_pagina_de_inicio_responde_sin_login_con_mesaya_y_el_enlace_al_login(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('MesaYa')
            ->assertSee('Pide desde tu mesa')
            ->assertSee('Acceso del personal')
            ->assertSee('href="'.route('login').'"', false);

        $this->assertSame(url('/'), route('inicio'));
    }

    public function test_un_usuario_logueado_ve_el_enlace_a_su_pantalla(): void
    {
        $restaurante = Restaurante::factory()->create();

        $casos = [
            'camarero.mesas' => User::factory()->camarero()->create(['restaurante_id' => $restaurante->id]),
            'cocina.pedidos' => User::factory()->cocina()->create(['restaurante_id' => $restaurante->id]),
            'panel' => User::factory()->create(['restaurante_id' => $restaurante->id]), // admin
        ];

        foreach ($casos as $ruta => $usuario) {
            $this->actingAs($usuario)
                ->get('/')
                ->assertOk()
                ->assertSee('Ir a mi pantalla')
                ->assertSee('href="'.route($ruta).'"', false)
                ->assertDontSee('Acceso del personal');
        }
    }

    public function test_no_carga_nada_de_servidores_externos(): void
    {
        // withoutVite(): si alguien tiene "npm run dev" arrancado, Laravel
        // enlaza el servidor de Vite (http://[::1]:5173), que no es externo.
        $html = $this->withoutVite()->get('/')->assertOk()->getContent();

        $this->assertStringNotContainsString('googleusercontent', $html);
        $this->assertStringNotContainsString('fonts.googleapis', $html);
        // Ningún src ni href apunta a otro dominio.
        preg_match_all('/(?:src|href)="(https?:\/\/[^"]+)"/', $html, $enlaces);
        foreach ($enlaces[1] as $enlace) {
            $this->assertStringStartsWith(url('/'), $enlace, "Enlace externo: {$enlace}");
        }
    }

    public function test_las_anclas_del_menu_apuntan_a_secciones_que_existen(): void
    {
        $respuesta = $this->get('/')->assertOk();

        foreach (['como-funciona', 'cocina-y-sala', 'panel'] as $ancla) {
            $respuesta->assertSee('href="'.route('inicio').'#'.$ancla.'"', false)
                ->assertSee('id="'.$ancla.'"', false);
        }
    }

    public function test_no_hay_textos_inventados_de_la_maqueta(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertDontSee('TicketBAI')
            ->assertDontSee('VeriFactu')
            ->assertDontSee('Bizum')
            ->assertDontSee('24/7')
            ->assertDontSee('demostración gratuita')
            ->assertDontSee('Precios');
    }

    public function test_el_boton_de_la_carta_de_ejemplo_sale_solo_si_existe(): void
    {
        $this->get('/')->assertDontSee('Ver una carta de ejemplo');

        $restaurante = Restaurante::factory()->create(['slug' => 'la-tasca']);
        Mesa::factory()->create(['restaurante_id' => $restaurante->id, 'numero' => 1]);

        $this->get('/')
            ->assertSee('Ver una carta de ejemplo')
            ->assertSee(route('cliente.carta', ['slug' => 'la-tasca', 'numero' => 1]));
    }

    public function test_las_paginas_de_privacidad_y_cookies_responden_y_estan_enlazadas(): void
    {
        $this->get('/')
            ->assertSee(route('legal.privacidad'))
            ->assertSee(route('legal.cookies'));

        $this->get(route('legal.privacidad'))->assertOk()->assertSee('Qué datos guarda MesaYa')->assertSee('Stripe');
        $this->get(route('legal.cookies'))->assertOk()->assertSee(config('session.cookie'))->assertSee('XSRF-TOKEN');
    }
}
