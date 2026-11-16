{{--
    Campo de formulario completo: etiqueta, icono opcional, input y errores.
    Uso:
        <x-campo-texto nombre="email" tipo="email" etiqueta="Correo electrónico"
                       icono="mail" :value="old('email')" required />

    Propiedades:
    - nombre:   el name del input. También se usa para buscar sus errores.
    - etiqueta: texto del <label> (opcional).
    - tipo:     text, email, password, number...
    - icono:    nombre de un icono de Material Symbols (opcional).
    - id:       por defecto es igual que el nombre.
    - errores:  lista de mensajes. Por defecto se cogen los errores de
                validación de ese campo; solo hay que pasarla cuando el
                formulario usa otra "bolsa" de errores (como en el perfil).
    Los demás atributos (value, required, autofocus, placeholder...) van al <input>.
    El slot "accion" pone un enlace a la derecha de la etiqueta.

    Si el tipo es password, aparece un botón con un ojo para ver u ocultar
    lo escrito. Lo hace Alpine.js con la variable "visible".
--}}
@props([
    'nombre',
    'etiqueta' => null,
    'tipo' => 'text',
    'icono' => null,
    'id' => null,
    'errores' => null,
])

@php
    $id = $id ?? $nombre;
    $errores = $errores ?? $errors->get($nombre);
    $esContrasena = $tipo === 'password';
@endphp

<div @if ($esContrasena) x-data="{ visible: false }" @endif>
    @if ($etiqueta)
        <div class="flex items-center justify-between mb-1.5">
            <label for="{{ $id }}" class="block text-label-md text-on-surface">{{ $etiqueta }}</label>
            {{ $accion ?? '' }}
        </div>
    @endif

    <div class="relative">
        @if ($icono)
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-secondary">
                <x-icono :nombre="$icono" class="text-xl" />
            </div>
        @endif

        <input
            id="{{ $id }}"
            name="{{ $nombre }}"
            type="{{ $tipo }}"
            @if ($esContrasena) x-bind:type="visible ? 'text' : 'password'" @endif
            {{ $attributes->class([
                'block w-full min-h-[48px] py-3 rounded bg-surface-container-lowest text-on-surface text-body-md shadow-sm transition-colors placeholder:text-secondary/60 focus:border-primary-container focus:ring-1 focus:ring-primary-container',
                'pl-11' => $icono,
                'pl-4' => ! $icono,
                'pr-11' => $esContrasena,
                'pr-4' => ! $esContrasena,
                'border-error' => $errores,
                'border-outline-variant/50' => ! $errores,
            ]) }}
        />

        @if ($esContrasena)
            <button type="button" x-on:click="visible = ! visible" aria-label="Mostrar u ocultar la contraseña"
                    class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-secondary hover:text-on-surface transition-colors">
                {{-- x-text cambia el icono según se vea o no la contraseña --}}
                <span class="material-symbols-outlined text-xl" aria-hidden="true" x-text="visible ? 'visibility_off' : 'visibility'">visibility</span>
            </button>
        @endif
    </div>

    @if ($errores)
        <ul class="mt-1.5 space-y-1 text-body-md text-error">
            @foreach ((array) $errores as $mensaje)
                <li>{{ $mensaje }}</li>
            @endforeach
        </ul>
    @endif
</div>
