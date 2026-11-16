<?php

namespace App\Models;

use App\Models\Concerns\PerteneceARestaurante;
use Database\Factories\PlatoFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;

// Plato de la carta. Usa PerteneceARestaurante para el aislamiento.
#[Fillable(['restaurante_id', 'categoria_id', 'nombre', 'descripcion', 'precio_centimos', 'foto', 'disponible'])]
class Plato extends Model
{
    /** @use HasFactory<PlatoFactory> */
    use HasFactory, PerteneceARestaurante;

    protected $table = 'platos';

    protected function casts(): array
    {
        return [
            'precio_centimos' => 'integer',
            'disponible' => 'boolean',
        ];
    }

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }

    // Laravel busca la tabla pivote "alergeno_plato" por convención.
    public function alergenos(): BelongsToMany
    {
        return $this->belongsToMany(Alergeno::class, 'alergeno_plato');
    }

    /**
     * URL pública de la foto, o null si no tiene.
     * Funciona porque "php artisan storage:link" enlaza public/storage
     * con storage/app/public.
     */
    public function urlFoto(): ?string
    {
        return $this->foto ? Storage::disk('public')->url($this->foto) : null;
    }
}
