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
     * Se monta con url() porque la ruta pública todavía no existe (llega con
     * la rama 8). Entonces se podrá cambiar por route() aquí, en un solo sitio.
     */
    public function urlQr(): string
    {
        return url('/r/'.$this->restaurante->slug.'/mesa/'.$this->numero);
    }
}
