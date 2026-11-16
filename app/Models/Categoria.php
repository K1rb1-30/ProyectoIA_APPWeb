<?php

namespace App\Models;

use App\Models\Concerns\PerteneceARestaurante;
use Database\Factories\CategoriaFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

// Categoría de la carta. Usa PerteneceARestaurante: cada admin solo ve las
// de su restaurante y, al crear una, se le pone su restaurante_id solo.
#[Fillable(['restaurante_id', 'nombre', 'orden'])]
class Categoria extends Model
{
    /** @use HasFactory<CategoriaFactory> */
    use HasFactory, PerteneceARestaurante;

    protected $table = 'categorias';

    public function platos(): HasMany
    {
        return $this->hasMany(Plato::class);
    }
}
