<?php

namespace App\Models;

use App\Models\Concerns\PerteneceARestaurante;
use Database\Factories\MesaFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

// El trait PerteneceARestaurante (rama 1) hace que cada admin solo vea y
// cree mesas de su restaurante, y añade la relación restaurante().
#[Fillable(['numero', 'estado'])]
class Mesa extends Model
{
    /** @use HasFactory<MesaFactory> */
    use HasFactory, PerteneceARestaurante;

    protected $table = 'mesas';

    /**
     * Pedidos (comandas) hechos en esta mesa.
     */
    public function pedidos(): HasMany
    {
        return $this->hasMany(Pedido::class);
    }

    /**
     * Busca la mesa de una URL de QR (/r/{slug}/mesa/{numero}) para las
     * pantallas públicas del cliente. Si el restaurante o la mesa no
     * existen, lanza un 404.
     *
     * Filtra A MANO por el restaurante del slug y quita el filtro del trait:
     * en estas pantallas no suele haber nadie logueado, y si lo hay (un admin
     * probando el QR) puede ser de otro restaurante.
     */
    public static function delQr(string $slug, int $numero): self
    {
        $restaurante = Restaurante::where('slug', $slug)->firstOrFail();

        $mesa = static::withoutGlobalScope('restaurante')
            ->where('restaurante_id', $restaurante->id)
            ->where('numero', $numero)
            ->firstOrFail();

        // Dejamos el restaurante ya cargado para no volver a consultarlo.
        return $mesa->setRelation('restaurante', $restaurante);
    }

    /**
     * URL absoluta a la que apunta el QR de la mesa: /r/{slug}/mesa/{numero}.
     *
     * Usa el nombre de la ruta pública de la carta (rama 8): si algún día
     * cambia la URL, el QR se actualiza solo.
     */
    public function urlQr(): string
    {
        return route('cliente.carta', ['slug' => $this->restaurante->slug, 'numero' => $this->numero]);
    }
}
