<?php

namespace Database\Seeders;

use App\Acciones\DividirCuenta;
use App\Models\Cuenta;
use App\Models\LineaPedido;
use App\Models\Mesa;
use App\Models\Pago;
use App\Models\ParteCuenta;
use App\Models\Pedido;
use App\Models\Plato;
use App\Models\Restaurante;
use Faker\Generator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Ventas de ejemplo de los últimos 30 días (rama 13), para que la pantalla de
 * estadísticas tenga algo que enseñar en la demo.
 *
 * Por cada día crea entre 3 y 10 "mesas cobradas": un pedido servido con
 * platos de la carta, su cuenta cerrada dividida a partes iguales y un pago
 * por parte (tarjeta con Stripe, TPV o efectivo). Las fechas son del pasado.
 *
 * - Faker es_ES solo se usa para números al azar: no hay datos de personas.
 * - NO se ejecuta en producción.
 * - Si el restaurante ya tiene pagos de días anteriores a hoy, no añade nada
 *   (así se puede ejecutar dos veces sin duplicar el historial).
 * - No cambia el estado de las mesas.
 *
 * Solo este seeder: php artisan db:seed --class=VentasDemoSeeder
 */
class VentasDemoSeeder extends Seeder
{
    private Generator $faker;

    public function run(): void
    {
        if (app()->environment('production')) {
            return;
        }

        $this->faker = fake('es_ES');

        foreach (Restaurante::whereIn('slug', ['la-tasca', 'bar-el-rincon'])->get() as $restaurante) {
            // Sin login, el global scope del trait no filtra: filtramos a mano.
            $mesas = Mesa::where('restaurante_id', $restaurante->id)->get();
            $platos = Plato::where('restaurante_id', $restaurante->id)->where('disponible', true)->get();

            if ($mesas->isEmpty() || $platos->isEmpty() || $this->yaTieneHistorial($restaurante)) {
                continue;
            }

            // Transacción: o se crea todo el historial del restaurante, o nada.
            DB::transaction(function () use ($mesas, $platos, $restaurante) {
                // Días anteriores: de 29 días atrás hasta ayer, entre las 12:00 y las 22:30.
                for ($diasAtras = 29; $diasAtras >= 1; $diasAtras--) {
                    $dia = today()->subDays($diasAtras);
                    $mesasDelDia = $this->faker->numberBetween(3, 10);

                    for ($i = 0; $i < $mesasDelDia; $i++) {
                        $momento = $dia->copy()->setTime($this->faker->numberBetween(12, 22), $this->faker->numberBetween(0, 59));
                        $this->crearMesaCobrada($restaurante, $mesas->random(), $platos, $momento);
                    }
                }

                // Hoy, unas pocas, siempre antes de ahora mismo.
                for ($i = 0; $i < $this->faker->numberBetween(2, 4); $i++) {
                    $momento = now()->subMinutes($this->faker->numberBetween(60, 180));
                    if ($momento->isToday()) {
                        $this->crearMesaCobrada($restaurante, $mesas->random(), $platos, $momento);
                    }
                }
            });
        }
    }

    /**
     * True si el restaurante ya tiene pagos de antes de hoy (por ejemplo, de
     * una ejecución anterior de este seeder).
     */
    private function yaTieneHistorial(Restaurante $restaurante): bool
    {
        return DB::table('pagos')
            ->join('partes_cuenta', 'partes_cuenta.id', '=', 'pagos.parte_cuenta_id')
            ->join('cuentas', 'cuentas.id', '=', 'partes_cuenta.cuenta_id')
            ->where('cuentas.restaurante_id', $restaurante->id)
            ->where('pagos.created_at', '<', today())
            ->exists();
    }

    /**
     * Una mesa que pidió, dividió la cuenta y pagó todo, en $momento.
     */
    private function crearMesaCobrada(Restaurante $restaurante, Mesa $mesa, Collection $platos, Carbon $momento): void
    {
        // 1. Pedido servido con 1 a 4 platos distintos, al precio de la carta.
        $pedido = $this->conFecha(new Pedido([
            'restaurante_id' => $restaurante->id,
            'mesa_id' => $mesa->id,
            'estado' => 'servido',
        ]), $momento);

        $total = 0;
        foreach ($platos->random(min($platos->count(), $this->faker->numberBetween(1, 4))) as $plato) {
            $cantidad = $this->faker->numberBetween(1, 3);
            $pedido->lineas()->save($this->conFecha(new LineaPedido([
                'plato_id' => $plato->id,
                'cantidad' => $cantidad,
                'precio_centimos' => $plato->precio_centimos,
            ]), $momento, guardar: false));
            $total += $cantidad * $plato->precio_centimos;
        }

        // 2. Cuenta cerrada, dividida a partes iguales con el mismo cálculo que
        //    la app: la suma de las partes es exactamente el total.
        $cobro = $momento->copy()->addMinutes($this->faker->numberBetween(30, 60));
        $cuenta = $this->conFecha(new Cuenta([
            'restaurante_id' => $restaurante->id,
            'mesa_id' => $mesa->id,
            'total_centimos' => $total,
            'estado' => 'cerrada',
        ]), $cobro);
        $pedido->update(['cuenta_id' => $cuenta->id]);

        $importes = DividirCuenta::repartirAPartesIguales($total, $this->faker->numberBetween(1, 4));

        // 3. Cada parte pagada: ~30 % Stripe, ~30 % TPV, ~40 % efectivo.
        foreach ($importes as $indice => $importe) {
            $canal = $this->faker->randomElement(['stripe', 'stripe', 'stripe', 'tpv', 'tpv', 'tpv', 'efectivo', 'efectivo', 'efectivo', 'efectivo']);

            $parte = $this->conFecha(new ParteCuenta([
                'cuenta_id' => $cuenta->id,
                'persona' => $indice + 1,
                'importe_centimos' => $importe,
                'metodo' => $canal === 'efectivo' ? 'efectivo' : 'tarjeta',
                'estado' => 'pagado',
            ]), $cobro);

            $this->conFecha(new Pago([
                'parte_cuenta_id' => $parte->id,
                'importe_centimos' => $importe,
                'metodo' => $canal === 'efectivo' ? 'efectivo' : 'tarjeta',
                // Referencia inventada y marcada como demo. TPV y efectivo van sin ella.
                'referencia_stripe' => $canal === 'stripe' ? 'cs_test_demo_'.Str::random(24) : null,
            ]), $cobro);
        }
    }

    /**
     * Pone created_at y updated_at a $momento (Eloquent respeta las fechas que
     * ya vienen puestas) y guarda el modelo, salvo que $guardar sea false.
     *
     * @template T of \Illuminate\Database\Eloquent\Model
     *
     * @param  T  $modelo
     * @return T
     */
    private function conFecha($modelo, Carbon $momento, bool $guardar = true)
    {
        $modelo->created_at = $momento;
        $modelo->updated_at = $momento;

        if ($guardar) {
            $modelo->save();
        }

        return $modelo;
    }
}
