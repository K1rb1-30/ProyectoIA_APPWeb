<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Database\Factories\RestauranteFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

// Las columnas de la suscripción (rama 24) NO están en Fillable a propósito:
// ningún formulario las puede rellenar. Solo cambian con activarSuscripcion()
// después de preguntar a Stripe.
#[Fillable(['nombre', 'slug'])]
class Restaurante extends Model
{
    /** @use HasFactory<RestauranteFactory> */
    use HasFactory;

    // Estados de la suscripción. Solo ACTIVA deja usar la aplicación.
    public const SUSCRIPCION_PENDIENTE = 'pendiente';   // se ha dado de alta y aún no ha pagado

    public const SUSCRIPCION_ACTIVA = 'activa';         // pagada

    public const SUSCRIPCION_IMPAGADA = 'impagada';     // Stripe no ha podido cobrar el mes

    public const SUSCRIPCION_CANCELADA = 'cancelada';   // cancelada desde el portal de Stripe

    // Laravel pluralizaría en inglés ("restaurantes" acierta por casualidad),
    // pero lo dejamos escrito como pide CLAUDE.md.
    protected $table = 'restaurantes';

    protected function casts(): array
    {
        return [
            'suscripcion_fin_en' => 'datetime',
        ];
    }

    /**
     * El personal (admin, camareros y cocina) del restaurante.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Las mesas del restaurante.
     */
    public function mesas(): HasMany
    {
        return $this->hasMany(Mesa::class);
    }

    /**
     * True si el restaurante puede usar la aplicación (rama 24).
     */
    public function tieneSuscripcionActiva(): bool
    {
        return $this->suscripcion_estado === self::SUSCRIPCION_ACTIVA;
    }

    /**
     * Marca la suscripción como pagada con los datos que ha confirmado Stripe.
     * Solo se llama después de consultar la sesión en Stripe (nunca con datos
     * que lleguen en la URL).
     */
    public function activarSuscripcion(string $clienteId, ?string $suscripcionId, ?CarbonInterface $finEn): void
    {
        // forceFill: estas columnas no están en Fillable (ver arriba).
        $this->forceFill([
            'stripe_cliente_id' => $clienteId,
            'stripe_suscripcion_id' => $suscripcionId,
            'suscripcion_estado' => self::SUSCRIPCION_ACTIVA,
            'suscripcion_fin_en' => $finEn,
        ])->save();
    }

    /**
     * Cambia el estado de la suscripción con lo que avisa Stripe por el
     * webhook (rama 24, PR 2). Si no llega fecha, se deja la que había.
     */
    public function cambiarEstadoSuscripcion(string $estado, ?CarbonInterface $finEn = null): void
    {
        $this->forceFill(['suscripcion_estado' => $estado]);

        if ($finEn !== null) {
            $this->forceFill(['suscripcion_fin_en' => $finEn]);
        }

        $this->save();
    }

    /**
     * Traduce el estado de una suscripción de Stripe al nuestro:
     *  - active (pagada) y trialing (en prueba)  → activa
     *  - past_due (Stripe sigue reintentando el cobro) y unpaid → impagada
     *  - canceled e incomplete_expired           → cancelada
     * Devuelve null con los estados que no nos cambian nada (por ejemplo,
     * incomplete: el primer pago aún no ha terminado).
     */
    public static function estadoDesdeStripe(?string $estadoStripe): ?string
    {
        return match ($estadoStripe) {
            'active', 'trialing' => self::SUSCRIPCION_ACTIVA,
            'past_due', 'unpaid' => self::SUSCRIPCION_IMPAGADA,
            'canceled', 'incomplete_expired' => self::SUSCRIPCION_CANCELADA,
            default => null,
        };
    }

    /**
     * Slug único a partir del nombre: "Bar Pepe" → "bar-pepe". Si ya existe,
     * prueba "bar-pepe-2", "bar-pepe-3"... El slug sale en la URL del QR.
     */
    public static function slugUnico(string $nombre): string
    {
        // Si el nombre no tiene letras ni números (por ejemplo "¡¡¡"), Str::slug
        // da una cadena vacía: usamos "restaurante".
        $base = Str::slug($nombre) ?: 'restaurante';
        $slug = $base;

        for ($n = 2; static::where('slug', $slug)->exists(); $n++) {
            $slug = $base.'-'.$n;
        }

        return $slug;
    }
}
