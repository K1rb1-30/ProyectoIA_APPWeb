<?php

namespace Tests\Fakes;

use App\Servicios\PasarelaStripe;

/**
 * Stripe de mentira para los tests del pago al pedir (rama 15).
 *
 * Se pone en lugar de la PasarelaStripe de verdad con
 *   $this->app->instance(PasarelaStripe::class, $falsa);
 * y así nunca se llama a Stripe (ni hace falta internet ni claves).
 *
 * Recuerda las sesiones que se crean y, al consultarlas, responde que están
 * pagadas por el importe con el que se crearon. Se puede cambiar:
 *   $falsa->pagar = false;          → Stripe dice que NO está pagada
 *   $falsa->importeCobrado = 1;     → Stripe dice que se cobró otro importe
 *   $falsa->mesaDeLaSesion = 99;    → la sesión es de otra mesa
 */
class PasarelaStripeFalsa extends PasarelaStripe
{
    /** @var array<string, array{importe: int, mesa_id: int}> */
    public array $sesiones = [];

    public ?string $ultimaSesion = null;

    public bool $pagar = true;

    public ?int $importeCobrado = null;

    public ?int $mesaDeLaSesion = null;

    public int $consultas = 0;

    public function crearSesionPedido(int $importeCentimos, int $mesaId, string $concepto, string $urlExito, string $urlCancelar): array
    {
        $id = 'cs_test_'.(count($this->sesiones) + 1);
        $this->sesiones[$id] = ['importe' => $importeCentimos, 'mesa_id' => $mesaId];
        $this->ultimaSesion = $id;

        return ['id' => $id, 'url' => 'https://checkout.stripe.com/c/pay/'.$id];
    }

    public function consultarSesion(string $idSesion): array
    {
        $this->consultas++;
        $sesion = $this->sesiones[$idSesion] ?? null;

        return [
            'pagada' => $sesion !== null && $this->pagar,
            'parte_cuenta_id' => null,
            'importe_centimos' => $this->importeCobrado ?? ($sesion['importe'] ?? 0),
            'moneda' => 'eur',
            'tipo' => 'pedido',
            'mesa_id' => $this->mesaDeLaSesion ?? ($sesion['mesa_id'] ?? null),
        ];
    }
}
