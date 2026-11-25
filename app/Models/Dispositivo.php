<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Tablet o móvil del bar emparejado por el admin (ver la migración).
 *
 * Extiende Authenticatable (como User) para que un dispositivo de COCINA pueda
 * "iniciar sesión" él mismo: la cocina no tiene usuarios, tiene dispositivos.
 * Lo hace el middleware IdentificarDispositivo con el guard "dispositivo"
 * (config/auth.php). Mientras tanto, Auth::user() devuelve el dispositivo, y
 * como tiene restaurante_id y rol, el filtro PerteneceARestaurante y el
 * middleware ComprobarRol funcionan igual que con una persona.
 *
 * Ojo: NO usa el trait PerteneceARestaurante, por lo mismo que User: Laravel
 * busca el dispositivo para saber quién ha "iniciado sesión", y si el filtro
 * preguntara a su vez por quién ha iniciado sesión, se llamaría a sí mismo sin
 * fin. Las pantallas del admin filtran a mano por su restaurante.
 */
#[Fillable(['restaurante_id', 'tipo', 'nombre'])]
#[Hidden(['codigo_hash', 'token_hash'])]
class Dispositivo extends Authenticatable
{
    protected $table = 'dispositivos';

    /** Nombre de la cookie que guarda el token del dispositivo. */
    public const COOKIE = 'mesaya_dispositivo';

    /** Minutos que vale un código de emparejar. */
    public const MINUTOS_CODIGO = 10;

    /** Una cookie de un año: el dispositivo no tiene que volver a emparejarse. */
    public const MINUTOS_COOKIE = 60 * 24 * 365;

    /**
     * Letras y números del código, sin los que se confunden al leerlos
     * (0/O, 1/I/L).
     */
    private const CARACTERES_CODIGO = 'ABCDEFGHJKMNPQRSTUVWXYZ23456789';

    protected function casts(): array
    {
        return [
            'codigo_caduca_en' => 'datetime',
            'emparejado_en' => 'datetime',
            'ultimo_uso_en' => 'datetime',
        ];
    }

    public function restaurante(): BelongsTo
    {
        return $this->belongsTo(Restaurante::class);
    }

    /**
     * Rol con el que el dispositivo pasa por ComprobarRol. Solo los de cocina
     * inician sesión como dispositivo; los de camarero los usa cada camarero
     * con su PIN (rama siguiente).
     */
    public function getRolAttribute(): string
    {
        return $this->tipo;
    }

    /** Lo pide ComprobarRol: un dispositivo nunca es desarrollador. */
    public function esDesarrollador(): bool
    {
        return false;
    }

    public function estaEmparejado(): bool
    {
        return $this->token_hash !== null;
    }

    /** True si tiene un código de emparejar que todavía vale. */
    public function codigoVigente(): bool
    {
        return $this->codigo_hash !== null && $this->codigo_caduca_en?->isFuture();
    }

    /**
     * Crea un código nuevo de 6 caracteres que vale MINUTOS_CODIGO minutos.
     * Guarda solo su hash y devuelve el código para enseñárselo al admin una vez.
     */
    public function generarCodigo(): string
    {
        $codigo = '';
        for ($i = 0; $i < 6; $i++) {
            // random_int es aleatorio de verdad (sirve para seguridad); rand() no.
            $codigo .= self::CARACTERES_CODIGO[random_int(0, strlen(self::CARACTERES_CODIGO) - 1)];
        }

        $this->forceFill([
            'codigo_hash' => self::hashCodigo($codigo),
            'codigo_caduca_en' => now()->addMinutes(self::MINUTOS_CODIGO),
        ])->save();

        return $codigo;
    }

    /**
     * Hash de un código tal como lo escribe alguien: sin guiones ni espacios
     * y en mayúsculas, para que «k7q-4zp» y «K7Q4ZP» den lo mismo.
     */
    public static function hashCodigo(string $codigo): string
    {
        $limpio = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $codigo));

        return hash('sha256', $limpio);
    }

    /** Código con un guion en medio para leerlo mejor: K7Q4ZP → K7Q-4ZP. */
    public static function formatearCodigo(string $codigo): string
    {
        return substr($codigo, 0, 3).'-'.substr($codigo, 3);
    }

    /**
     * Empareja el dispositivo: borra el código y crea el token. Devuelve el
     * token para meterlo en la cookie (en la base de datos solo queda su hash).
     */
    public function emparejar(): string
    {
        $token = Str::random(60);

        $this->forceFill([
            'codigo_hash' => null,
            'codigo_caduca_en' => null,
            'token_hash' => hash('sha256', $token),
            'emparejado_en' => now(),
            'ultimo_uso_en' => now(),
        ])->save();

        return $token;
    }

    /**
     * Dispositivo emparejado al que pertenece la cookie de esta petición, o
     * null si no hay cookie o ya no vale (por ejemplo, el admin lo desvinculó).
     */
    public static function deLaPeticion(Request $request): ?self
    {
        $token = $request->cookie(self::COOKIE);

        if (! is_string($token) || $token === '') {
            return null;
        }

        return static::where('token_hash', hash('sha256', $token))->first();
    }

    /**
     * Apunta que se ha usado. Como la cocina pregunta cada 5 segundos, solo se
     * guarda si ha pasado más de un minuto, para no escribir sin parar.
     */
    public function apuntarUso(): void
    {
        if ($this->ultimo_uso_en === null || $this->ultimo_uso_en->lt(now()->subMinute())) {
            $this->forceFill(['ultimo_uso_en' => now()])->saveQuietly();
        }
    }
}
