<?php

namespace Tests\Unit;

use App\Acciones\DividirCuenta;
use LogicException;
use PHPUnit\Framework\TestCase;

/**
 * Reparto a partes iguales de DividirCuenta (rama 10). Es una función pura
 * (no usa la base de datos), por eso va en tests/Unit.
 */
class RepartoCuentaTest extends TestCase
{
    /*
     * Golden test nº 3: dividir 31,00 € entre 3 → 10,34 + 10,33 + 10,33.
     * La suma de las partes es exactamente el total y el céntimo que sobra
     * va a la primera parte.
     */
    public function test_dividir_entre_3_la_suma_de_las_partes_es_exactamente_el_total(): void
    {
        $partes = DividirCuenta::repartirAPartesIguales(3100, 3);

        $this->assertSame([1034, 1033, 1033], $partes);
        $this->assertSame(3100, array_sum($partes));
    }

    public function test_otros_repartos_suman_siempre_el_total(): void
    {
        // [total, personas, partes esperadas]
        $casos = [
            [1000, 3, [334, 333, 333]],
            [100, 7, [16, 14, 14, 14, 14, 14, 14]],
            [2500, 1, [2500]],
            [900, 3, [300, 300, 300]],
            [1, 2, [1, 0]],
        ];

        foreach ($casos as [$total, $personas, $esperado]) {
            $partes = DividirCuenta::repartirAPartesIguales($total, $personas);

            $this->assertSame($esperado, $partes, "Reparto de {$total} entre {$personas}");
            $this->assertSame($total, array_sum($partes));
        }
    }

    public function test_no_se_puede_dividir_entre_0_personas(): void
    {
        $this->expectException(LogicException::class);

        DividirCuenta::repartirAPartesIguales(1000, 0);
    }
}
