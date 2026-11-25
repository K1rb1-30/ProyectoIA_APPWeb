<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// Ojo: este modelo NO usa el trait PerteneceARestaurante. Laravel consulta
// la tabla users para saber quién está logueado, y si el filtro preguntara a
// su vez por el usuario logueado, se llamaría a sí mismo sin fin.
#[Fillable(['name', 'email', 'password', 'pin', 'rol', 'restaurante_id'])]
#[Hidden(['password', 'pin', 'remember_token'])]
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
            // El PIN de los camareros (4 cifras) se guarda cifrado igual que
            // las contraseñas: al asignar '1234', Laravel guarda su hash.
            'pin' => 'hashed',
            // Ojo: NO está en Fillable a propósito. Así ningún formulario
            // (por ejemplo, el de Personal) puede activarlo: solo el seeder.
            'puede_cambiar_restaurante' => 'boolean',
        ];
    }

    /**
     * True si es una cuenta de desarrollador: las del equipo (Iván y Gabriel)
     * y la del profesor. Son admin con la marca puede_cambiar_restaurante.
     *
     * Diferencia con un admin normal (el de cada restaurante):
     *  - pueden cambiar de restaurante desde el panel;
     *  - pueden entrar en las pantallas de camarero y cocina y usar «Ver como».
     * Un admin normal solo ve las pantallas de administración de su restaurante.
     */
    public function esDesarrollador(): bool
    {
        return $this->rol === 'admin' && $this->puede_cambiar_restaurante;
    }

    /**
     * True si puede pasar de un restaurante a otro desde el panel: solo los
     * desarrolladores.
     */
    public function puedeCambiarDeRestaurante(): bool
    {
        return $this->esDesarrollador();
    }

    /**
     * Personal de un restaurante: quita las cuentas del equipo y del profesor
     * (las que pueden cambiar de restaurante). No son empleados de ningún
     * restaurante, así que no salen en Personal, su admin no las puede editar
     * ni borrar y no cuentan como "el admin" del restaurante.
     *
     * Uso: User::delPersonal()->where('restaurante_id', ...)
     * (Laravel convierte el método scopeDelPersonal en ->delPersonal()).
     */
    public function scopeDelPersonal(Builder $consulta): void
    {
        $consulta->where('puede_cambiar_restaurante', false);
    }

    /**
     * Restaurante en el que trabaja el usuario.
     */
    public function restaurante(): BelongsTo
    {
        return $this->belongsTo(Restaurante::class);
    }

    /**
     * Pedidos que ha tomado este usuario (comandas del camarero). Los que
     * hace el cliente desde el QR tienen user_id nulo.
     */
    public function pedidos(): HasMany
    {
        return $this->hasMany(Pedido::class, 'user_id');
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
            default => 'panel',
        };
    }
}
