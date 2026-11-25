{{--
    Formulario para crear o editar un empleado (rama 18). No tiene maqueta:
    sigue el estilo de los formularios del panel (como el de mesas).
    Si el empleado ya existe en la base de datos, es "editar"; si no, "crear".

    Las contraseñas y los PIN no se rellenan nunca (ni con old()): no se muestran en claro.

    Según el rol se piden datos distintos (Alpine guarda el rol elegido en "rol"):
      - Administración: correo y contraseña, para entrar en /login.
      - Camarero: solo un PIN de 4 cifras, para entrar en los dispositivos.
    Los campos del otro rol se ocultan y se desactivan (disabled), así que no se envían.
--}}
@php
    $editando = $empleado->exists;
    // En tu propia ficha no puedes cambiarte el rol (lo comprueba también EmpleadoRequest).
    $soyYo = $editando && $empleado->is(auth()->user());
    $rolElegido = old('rol', $empleado->rol);
    $opciones = ['admin' => 'Administración', 'camarero' => 'Camarero'];
@endphp

<x-layouts.admin :titulo="$editando ? 'Editar a '.$empleado->name : 'Añadir empleado'">
    <div class="max-w-lg">
        <a href="{{ route('admin.personal') }}" class="inline-flex items-center gap-1 mb-space-md text-label-lg text-secondary hover:text-on-surface">
            Volver al personal
        </a>

        <x-tarjeta>
            <form method="POST" action="{{ $editando ? route('admin.personal.actualizar', $empleado) : route('admin.personal.guardar') }}" class="space-y-space-lg"
                  x-data="{ rol: @js($rolElegido) }">
                @csrf
                {{-- Los formularios HTML solo envían GET y POST: @method('PUT') le dice a Laravel que es una actualización --}}
                @if ($editando)
                    @method('PUT')
                @endif

                <x-campo-texto nombre="name" etiqueta="Nombre"
                               :value="old('name', $empleado->name)" maxlength="255" required autofocus />

                {{-- Rol --}}
                <div>
                    {{-- En tu propia ficha no hay select, así que la etiqueta no apunta a ningún campo --}}
                    <label @unless ($soyYo) for="rol" @endunless class="block mb-1.5 text-label-md text-on-surface">Rol</label>
                    @if ($soyYo)
                        {{-- Un select desactivado no se envía: mandamos el rol en un campo oculto --}}
                        <input type="hidden" name="rol" value="admin">
                        <p class="min-h-[48px] flex items-center px-4 rounded bg-surface-container-low text-body-md">Administración</p>
                        <p class="mt-1.5 text-body-md text-secondary">No puedes quitarte tu propio rol de administrador.</p>
                    @else
                        <select id="rol" name="rol" required x-model="rol"
                                @class([
                                    'block w-full min-h-[48px] py-3 pl-4 pr-10 rounded bg-surface-container-lowest text-on-surface border-[1.5px] text-body-lg focus:border-primary-container focus:ring-1 focus:ring-primary-container',
                                    'border-error' => $errors->has('rol'),
                                    'border-secondary-fixed-dim' => ! $errors->has('rol'),
                                ])>
                            @foreach ($opciones as $valor => $texto)
                                <option value="{{ $valor }}" @selected($rolElegido === $valor)>{{ $texto }}</option>
                            @endforeach
                        </select>
                        <p class="mt-1.5 text-body-md text-secondary">Camarero: mesas, comandas y cobros; entra con su PIN en los dispositivos del bar. Administración: la carta, las mesas, el personal y los dispositivos. La cocina no necesita cuenta: empareja su tablet en «Dispositivos».</p>
                    @endif
                    <x-errores-campo nombre="rol" />
                </div>

                {{-- Administración: correo y contraseña. La contraseña es obligatoria si todavía no tiene. --}}
                @php $tieneContrasena = $editando && $empleado->password; @endphp
                <fieldset class="space-y-space-md" x-show="rol === 'admin'" :disabled="rol !== 'admin'">
                    <x-campo-texto nombre="email" tipo="email" etiqueta="Correo electrónico"
                                   :value="old('email', $empleado->email)" maxlength="255"
                                   placeholder="ej. nombre@restaurante.es" />
                    <x-campo-texto nombre="password" tipo="password" autocomplete="new-password"
                                   :etiqueta="$tieneContrasena ? 'Nueva contraseña' : 'Contraseña'" />
                    <x-campo-texto nombre="password_confirmation" tipo="password" autocomplete="new-password"
                                   etiqueta="Repite la contraseña" />
                    <p class="text-body-md text-secondary">
                        @if ($tieneContrasena)
                            Déjala vacía para no cambiarla. Si la cambias, se cerrarán las sesiones abiertas de esta persona.
                        @else
                            Mínimo 8 caracteres. Dásela a la persona para que pueda entrar.
                        @endif
                    </p>
                </fieldset>

                {{-- Camarero: solo un PIN de 4 cifras. Obligatorio si todavía no tiene. --}}
                @php $tienePin = $editando && $empleado->pin; @endphp
                <fieldset class="space-y-space-md" x-show="rol === 'camarero'" :disabled="rol !== 'camarero'" x-cloak>
                    {{-- inputmode="numeric": en el móvil sale el teclado de números --}}
                    <x-campo-texto nombre="pin" tipo="password" inputmode="numeric" maxlength="4" autocomplete="off"
                                   class="font-mono tracking-[0.3em]"
                                   :etiqueta="$tienePin ? 'Nuevo PIN (4 cifras)' : 'PIN (4 cifras)'" />
                    <x-campo-texto nombre="pin_confirmation" tipo="password" inputmode="numeric" maxlength="4" autocomplete="off"
                                   class="font-mono tracking-[0.3em]" etiqueta="Repite el PIN" />
                    <p class="text-body-md text-secondary">
                        @if ($tienePin)
                            Déjalo vacío para no cambiarlo. Si lo cambias, se cerrará su sesión abierta.
                        @else
                            Con su nombre y este PIN entrará en los móviles y tablets de camarero del bar. No necesita correo.
                        @endif
                    </p>
                </fieldset>

                <div class="flex flex-wrap gap-3">
                    <x-boton-primario>
                        {{ $editando ? 'Guardar cambios' : 'Crear empleado' }}
                    </x-boton-primario>
                    <a href="{{ route('admin.personal') }}"
                       class="inline-flex items-center justify-center min-h-[48px] px-5 py-3 rounded border-[1.5px] border-on-surface text-label-lg hover:bg-surface-container-lowest">
                        Cancelar
                    </a>
                </div>
            </form>
        </x-tarjeta>
    </div>
</x-layouts.admin>
