<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// Ojo: este modelo NO usa el trait PerteneceARestaurante. Laravel consulta
// la tabla users para saber quién está logueado, y si el filtro preguntara a
// su vez por el usuario logueado, se llamaría a sí mismo sin fin.
#[Fillable(['name', 'email', 'password', 'rol', 'restaurante_id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Restaurante en el que trabaja el usuario.
     */
    public function restaurante(): BelongsTo
    {
        return $this->belongsTo(Restaurante::class);
    }

    /**
     * Nombre de la ruta a la que va el usuario después de iniciar sesión,
     * según su rol.
     */
    public function rutaDeInicio(): string
    {
        return match ($this->rol) {
            'camarero' => 'camarero.mesas',
            'cocina' => 'cocina.pedidos',
            default => 'dashboard',
        };
    }
}
