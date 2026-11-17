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
