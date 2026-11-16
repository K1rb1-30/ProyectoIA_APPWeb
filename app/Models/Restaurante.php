<?php

namespace App\Models;

use Database\Factories\RestauranteFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nombre', 'slug'])]
class Restaurante extends Model
{
    /** @use HasFactory<RestauranteFactory> */
    use HasFactory;

    // Laravel pluralizaría en inglés ("restaurantes" acierta por casualidad),
    // pero lo dejamos escrito como pide CLAUDE.md.
    protected $table = 'restaurantes';

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
}
