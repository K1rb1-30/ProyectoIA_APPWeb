{{--
    Pantalla de inicio de sesión (rediseño en la rama 21).
    El título dice para quién es (el personal) en vez de un saludo genérico,
    y los campos no llevan iconos: la etiqueta ya dice lo que hay que escribir.
--}}
<x-guest-layout>
    <div class="mb-10">
        <h1 class="text-headline-lg">Acceso del personal</h1>
        <p class="mt-2 text-body-lg text-secondary">Camareros, cocina y administración.</p>
    </div>

    {{-- Mensaje de estado, por ejemplo tras cambiar la contraseña --}}
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        {{-- @csrf añade el token que protege el formulario contra envíos falsos --}}
        @csrf

        <x-campo-texto nombre="email" tipo="email" etiqueta="Correo electrónico"
                       :value="old('email')" placeholder="nombre@restaurante.es"
                       required autofocus autocomplete="username" />

        <x-campo-texto nombre="password" tipo="password" etiqueta="Contraseña"
                       required autocomplete="current-password">
            {{-- El enlace sale a la derecha de la etiqueta "Contraseña" --}}
            <x-slot name="accion">
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-label-md font-normal text-secondary underline underline-offset-2 hover:text-on-surface">
                        ¿La has olvidado?
                    </a>
                @endif
            </x-slot>
        </x-campo-texto>

        <label for="remember_me" class="flex items-center gap-2.5 min-h-[44px] text-body-md text-on-surface-variant cursor-pointer select-none">
            <input id="remember_me" name="remember" type="checkbox"
                   class="h-5 w-5 rounded-sm border-secondary-fixed-dim text-primary-container focus:ring-primary-container cursor-pointer">
            Mantener la sesión iniciada en este dispositivo
        </label>

        <x-boton-primario class="w-full min-h-[52px]">
            Entrar
        </x-boton-primario>
    </form>

    {{-- Las tablets y móviles del bar no inician sesión aquí: se emparejan con un código --}}
    <p class="mt-space-lg pt-space-md border-t border-outline-variant text-body-md text-secondary">
        ¿Es la tablet o el móvil del bar?
        <a href="{{ route('dispositivo') }}" class="text-on-surface underline underline-offset-2 hover:text-primary">Emparejar dispositivo</a>
    </p>
</x-guest-layout>
