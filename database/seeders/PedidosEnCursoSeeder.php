<?php

namespace Database\Seeders;

use App\Acciones\CrearPedido;
use App\Acciones\DividirCuenta;
use App\Acciones\PagarPedido;
use App\Models\Cuenta;
use App\Models\Mesa;
use App\Models\Pedido;
use App\Models\Plato;
use App\Models\Restaurante;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Deja los restaurantes «en pleno servicio» para probar y para la demo:
 * pedidos en cocina en todos los estados, avisos de plato listo, un pedido
 * pagado al hacerlo, cuentas divididas a medio cobrar y una mesa lista para
 * cerrar. Complementa a VentasDemoSeeder, que solo crea historial ya cobrado.
 *
 * Todo se crea con las mismas clases que usa la aplicación (CrearPedido,
 * Pedido::cambiarEstado, DividirCuenta, PagarPedido y los métodos de cobro de
 * ParteCuenta), así que cumple las mismas reglas: estados sin saltos, precio
 * del momento y partes que suman exactamente el total.
 *
 * - NO se ejecuta en producción.
 * - Si un restaurante ya tiene pedidos sin servir o cuentas abiertas, no
 *   añade nada (se puede ejecutar varias veces sin duplicar).
 *
 * La Tasca (8 mesas):
 *   1 → pedido pendiente (comanda del camarero, con nota)
 *   2 → un pedido preparándose y otro pagado al pedir por el cliente
 *   3 → pedido listo (sale el aviso al camarero)
 *   4 → servido; cuenta entre 3: una parte con tarjeta, otra esperando
 *       efectivo y otra pendiente
 *   5 → servido; cuenta por platos (con un plato compartido) toda pagada:
 *       lista para que el camarero cierre la mesa
 *   6 → servido; todavía no han pedido la cuenta
 *   7 y 8 → libres
 * Bar El Rincón (6 mesas): un pedido pendiente, uno listo y uno servido.
 * Marisquería O Porto (14 mesas): seis ocupadas, con pedidos en todos los
 *   estados, uno pagado al pedir y una cuenta entre 4 a medio cobrar.
 * Café Central (5 mesas): tres ocupadas (pendiente, listo y una cuenta entre
 *   2 con una parte pagada).
 * En todos quedan mesas libres, para que se vean los dos estados.
 */
class PedidosEnCursoSeeder extends Seeder
{
    public function __construct(
        private CrearPedido $crearPedido,
        private DividirCuenta $dividirCuenta,
        private PagarPedido $pagarPedido,
    ) {}

    public function run(): void
    {
        if (app()->environment('production')) {
            return;
        }

        $this->laTasca();
        $this->barElRincon();
        $this->marisqueria();
        $this->cafeCentral();
    }

    private function laTasca(): void
    {
        $restaurante = Restaurante::where('slug', 'la-tasca')->first();
        if (! $restaurante || $this->yaTieneServicio($restaurante)) {
            return;
        }
        $camarero = $this->camarero($restaurante);
        $carta = $this->platos($restaurante);

        // Mesa 1: acaba de entrar en cocina.
        $this->pedido($restaurante, 1, [
            [$carta['Patatas bravas'], 2, null],
            [$carta['Caña de cerveza'], 2, 'Bien fría'],
        ], $camarero, 'pendiente', 2);

        // Mesa 2: un pedido preparándose y, después, otro que el cliente
        // ha pagado con tarjeta al hacerlo (rama 15).
        $this->pedido($restaurante, 2, [
            [$carta['Croquetas caseras'], 1, null],
            [$carta['Tortilla de patatas'], 1, 'Poco hecha'],
        ], $camarero, 'preparando', 9);
        $mesa2 = $this->mesa($restaurante, 2);
        $lineasCliente = [['plato_id' => $carta['Agua mineral'], 'cantidad' => 2], ['plato_id' => $carta['Ensaladilla rusa'], 'cantidad' => 1]];
        $this->pagarPedido->confirmar(
            $mesa2,
            $lineasCliente,
            'cs_test_demo_'.$restaurante->slug.'_mesa2',
            $this->pagarPedido->calcularTotal($mesa2, $lineasCliente),
        );

        // Mesa 3: listo para servir → aviso al camarero.
        $this->pedido($restaurante, 3, [
            [$carta['Calamares a la romana'], 1, null],
            [$carta['Ensaladilla rusa'], 1, 'Sin aceitunas'],
        ], $camarero, 'listo', 14);

        // Mesa 4: ya han comido; cuenta entre 3 a medio cobrar.
        $mesa4 = $this->pedido($restaurante, 4, [
            [$carta['Croquetas caseras'], 2, null],
            [$carta['Patatas bravas'], 1, null],
            [$carta['Caña de cerveza'], 3, null],
        ], $camarero, 'servido', 45)->mesa;
        $cuenta4 = $this->dividirCuenta->ejecutar($mesa4, 'iguales', 3);
        [$parte1, $parte2] = $cuenta4->partes->values();
        $parte1->pagarConTarjeta('cs_test_demo_'.$restaurante->slug.'_mesa4');
        $parte2->pedirEfectivo();

        // Mesa 5: cuenta por platos con unas bravas compartidas, toda pagada
        // (TPV y efectivo): el camarero ya puede cerrar la mesa.
        $pedido5 = $this->pedido($restaurante, 5, [
            [$carta['Tortilla de patatas'], 1, null],
            [$carta['Calamares a la romana'], 1, null],
            [$carta['Patatas bravas'], 1, null],
        ], $camarero, 'servido', 60);
        $lineas5 = $pedido5->lineas()->with('plato')->get();
        $asignaciones = $lineas5->mapWithKeys(fn ($linea) => [
            $linea->id => match ($linea->plato->nombre) {
                'Tortilla de patatas' => 1,
                'Calamares a la romana' => 2,
                default => 0, // las bravas, para todos
            },
        ])->all();
        $cuenta5 = $this->dividirCuenta->ejecutar($pedido5->mesa, 'platos', 2, $asignaciones);
        [$parteA, $parteB] = $cuenta5->partes->values();
        $parteA->pagarConTpv();
        $parteB->cobrarEfectivoEnMesa();

        // Mesa 6: servido, todavía sin pedir la cuenta.
        $this->pedido($restaurante, 6, [
            [$carta['Café con leche'], 2, null],
            [$carta['Tarta de queso'], 1, null],
            [$carta['Flan casero'], 1, null],
        ], $camarero, 'servido', 30);
    }

    private function barElRincon(): void
    {
        $restaurante = Restaurante::where('slug', 'bar-el-rincon')->first();
        if (! $restaurante || $this->yaTieneServicio($restaurante)) {
            return;
        }
        $camarero = $this->camarero($restaurante);
        $carta = $this->platos($restaurante);

        $this->pedido($restaurante, 1, [[$carta['Croquetas caseras'], 1, null], [$carta['Caña de cerveza'], 1, null]], $camarero, 'pendiente', 3);
        $this->pedido($restaurante, 2, [[$carta['Calamares a la romana'], 1, null]], $camarero, 'listo', 12);
        $this->pedido($restaurante, 3, [[$carta['Crema catalana'], 2, null], [$carta['Café con leche'], 2, null]], $camarero, 'servido', 35);
    }

    private function marisqueria(): void
    {
        $restaurante = Restaurante::where('slug', 'marisqueria-o-porto')->first();
        if (! $restaurante || $this->yaTieneServicio($restaurante)) {
            return;
        }
        $camarero = $this->camarero($restaurante);
        $carta = $this->platos($restaurante);

        $this->pedido($restaurante, 1, [[$carta['Gambas a la plancha'], 2, 'Bien hechas'], [$carta['Albariño (copa)'], 2, null]], $camarero, 'pendiente', 3);
        $this->pedido($restaurante, 3, [[$carta['Merluza a la gallega'], 2, null], [$carta['Pimientos de Padrón'], 1, null]], $camarero, 'preparando', 11);

        // Mesa 4: preparándose y, además, un pedido del cliente pagado al hacerlo.
        $this->pedido($restaurante, 4, [[$carta['Pulpo a feira'], 1, null], [$carta['Empanada de zamburiñas'], 1, null]], $camarero, 'preparando', 7);
        $this->pagadoAlPedir($restaurante, 4, [[$carta['Agua mineral'], 2], [$carta['Tarta de Santiago'], 2]]);

        $this->pedido($restaurante, 6, [[$carta['Mejillones al vapor'], 1, null], [$carta['Zamburiñas a la gallega'], 1, 'Sin pimentón']], $camarero, 'listo', 16);

        // Mesa 8: cuenta entre 4 a medio cobrar (tarjeta, TPV, esperando efectivo y pendiente).
        $mesa8 = $this->pedido($restaurante, 8, [
            [$carta['Rodaballo al horno'], 2, null],
            [$carta['Gambas a la plancha'], 1, null],
            [$carta['Ribeiro (taza)'], 4, null],
        ], $camarero, 'servido', 70)->mesa;
        $this->cobrar($this->dividirCuenta->ejecutar($mesa8, 'iguales', 4), ['tarjeta', 'tpv', 'pide_efectivo']);

        $this->pedido($restaurante, 11, [[$carta['Ensalada de la huerta'], 1, null], [$carta['Filloas con miel'], 2, null]], $camarero, 'servido', 25);
    }

    private function cafeCentral(): void
    {
        $restaurante = Restaurante::where('slug', 'cafe-central')->first();
        if (! $restaurante || $this->yaTieneServicio($restaurante)) {
            return;
        }
        $camarero = $this->camarero($restaurante);
        $carta = $this->platos($restaurante);

        $this->pedido($restaurante, 1, [[$carta['Café con leche'], 2, 'Uno con leche de avena'], [$carta['Tostada con tomate'], 2, null]], $camarero, 'pendiente', 2);
        $this->pedido($restaurante, 4, [[$carta['Chocolate caliente'], 2, null], [$carta['Churros'], 1, null]], $camarero, 'listo', 8);

        // Mesa 2: cuenta entre 2 con una parte ya pagada con tarjeta.
        $mesa2 = $this->pedido($restaurante, 2, [
            [$carta['Capuchino'], 2, null],
            [$carta['Tarta de zanahoria'], 1, null],
            [$carta['Zumo de naranja natural'], 1, null],
        ], $camarero, 'servido', 40)->mesa;
        $this->cobrar($this->dividirCuenta->ejecutar($mesa2, 'iguales', 2), ['tarjeta']);
    }

    /**
     * Pedido que el cliente paga con tarjeta al hacerlo (rama 15), con
     * PagarPedido. La referencia de Stripe es inventada (cs_test_demo_...).
     *
     * @param  array<int, array{0: int, 1: int}>  $lineas  [idPlato, cantidad]
     */
    private function pagadoAlPedir(Restaurante $restaurante, int $numeroMesa, array $lineas): void
    {
        $mesa = $this->mesa($restaurante, $numeroMesa);
        $lineas = array_map(fn ($linea) => ['plato_id' => $linea[0], 'cantidad' => $linea[1]], $lineas);

        $this->pagarPedido->confirmar(
            $mesa,
            $lineas,
            'cs_test_demo_'.$restaurante->slug.'_mesa'.$numeroMesa,
            $this->pagarPedido->calcularTotal($mesa, $lineas),
        );
    }

    /**
     * Cobra las partes de una cuenta en orden con los métodos de ParteCuenta:
     * 'tarjeta' (Stripe), 'tpv' (datáfono), 'efectivo' (en la mesa) o
     * 'pide_efectivo' (el cliente lo ha elegido y espera al camarero).
     * Las partes que no tienen método se quedan pendientes.
     *
     * @param  array<int, string>  $cobros
     */
    private function cobrar(Cuenta $cuenta, array $cobros): void
    {
        foreach ($cuenta->partes->values() as $indice => $parte) {
            match ($cobros[$indice] ?? null) {
                'tarjeta' => $parte->pagarConTarjeta('cs_test_demo_cuenta'.$cuenta->id.'_parte'.$parte->persona),
                'tpv' => $parte->pagarConTpv(),
                'efectivo' => $parte->cobrarEfectivoEnMesa(),
                'pide_efectivo' => $parte->pedirEfectivo(),
                default => null,
            };
        }
    }

    /**
     * Crea un pedido con CrearPedido y lo avanza paso a paso hasta $estado
     * con cambiarEstado() (así nunca se salta un estado). $haceMinutos lo
     * pone en el pasado para que la cocina enseñe «hace X min».
     *
     * @param  array<int, array{0: int, 1: int, 2: ?string}>  $lineas  [idPlato, cantidad, notas]
     */
    private function pedido(Restaurante $restaurante, int $numeroMesa, array $lineas, User $camarero, string $estado, int $haceMinutos): Pedido
    {
        $pedido = $this->crearPedido->ejecutar(
            $this->mesa($restaurante, $numeroMesa),
            array_map(fn ($linea) => ['plato_id' => $linea[0], 'cantidad' => $linea[1], 'notas' => $linea[2]], $lineas),
            $camarero,
        );

        foreach (array_slice(Pedido::ESTADOS, 1, array_search($estado, Pedido::ESTADOS, true)) as $siguiente) {
            $pedido->cambiarEstado($siguiente);
        }

        $hora = now()->subMinutes($haceMinutos);
        Pedido::withoutGlobalScope('restaurante')->whereKey($pedido->id)->update(['created_at' => $hora, 'updated_at' => $hora]);

        return $pedido->refresh();
    }

    private function mesa(Restaurante $restaurante, int $numero): Mesa
    {
        return Mesa::withoutGlobalScope('restaurante')
            ->where('restaurante_id', $restaurante->id)
            ->where('numero', $numero)
            ->firstOrFail();
    }

    private function camarero(Restaurante $restaurante): User
    {
        return User::where('restaurante_id', $restaurante->id)->where('rol', 'camarero')->orderBy('id')->firstOrFail();
    }

    /**
     * { nombre: id } de los platos disponibles del restaurante.
     *
     * @return array<string, int>
     */
    private function platos(Restaurante $restaurante): array
    {
        return Plato::withoutGlobalScope('restaurante')
            ->where('restaurante_id', $restaurante->id)
            ->where('disponible', true)
            ->pluck('id', 'nombre')
            ->all();
    }

    /**
     * True si el restaurante ya tiene pedidos sin servir o cuentas abiertas:
     * entonces no se añade nada para no duplicar.
     */
    private function yaTieneServicio(Restaurante $restaurante): bool
    {
        return Pedido::withoutGlobalScope('restaurante')->where('restaurante_id', $restaurante->id)->where('estado', '!=', 'servido')->exists()
            || Cuenta::withoutGlobalScope('restaurante')->where('restaurante_id', $restaurante->id)->where('estado', 'abierta')->exists();
    }
}
