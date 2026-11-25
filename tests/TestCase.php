<?php

namespace Tests;

use App\Models\Dispositivo;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Dispositivos de camarero creados en este test, por restaurante:
     * [restaurante_id => [id del dispositivo, token de su cookie]].
     *
     * @var array<int, array{0: int, 1: string}>
     */
    private array $dispositivosDeCamarero = [];

    /**
     * Un camarero solo puede estar dentro desde un dispositivo emparejado de su
     * restaurante (middleware IdentificarDispositivo). Así que, en los tests,
     * "actuar como camarero" es lo mismo que en la vida real: un camarero que
     * ha entrado con su PIN en un dispositivo de camarero. Se crea el
     * dispositivo, se manda su cookie y se guarda en la sesión con cuál entró.
     *
     * withCredentials(): para que getJson()/patchJson() también manden la
     * cookie (en los tests solo la mandan con esto).
     *
     * Con cualquier otro usuario (admin, desarrollador) no cambia nada.
     */
    public function actingAs(Authenticatable $user, $guard = null)
    {
        if ($user instanceof User && $user->rol === 'camarero') {
            if (! isset($this->dispositivosDeCamarero[$user->restaurante_id])) {
                $dispositivo = Dispositivo::create([
                    'restaurante_id' => $user->restaurante_id,
                    'tipo' => 'camarero',
                    'nombre' => 'Móvil de los tests',
                ]);
                $this->dispositivosDeCamarero[$user->restaurante_id] = [$dispositivo->id, $dispositivo->emparejar()];
            }

            [$idDispositivo, $token] = $this->dispositivosDeCamarero[$user->restaurante_id];

            $this->withCredentials()
                ->withCookie(Dispositivo::COOKIE, $token)
                ->withSession(['dispositivo_id' => $idDispositivo]);
        }

        return parent::actingAs($user, $guard);
    }
}
