{{-- Pantalla de inicio de sesión. Referencia: docs/design/DesignFrontend/landing/login --}}
<x-guest-layout>
    <div class="mb-8 space-y-2">
        <h2 class="text-headline-lg">Bienvenido de nuevo a MesaYa</h2>
        <p class="text-body-md text-secondary">
            Entra con tu cuenta del restaurante. Te llevaremos a tu pantalla de trabajo.
        </p>
    </div>

    {{-- Mensaje de estado, por ejemplo tras cambiar la contraseña --}}
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        {{-- @csrf añade el token que protege el formulario contra envíos falsos --}}
        @csrf

        <x-campo-texto nombre="email" tipo="email" etiqueta="Correo electrónico" icono="mail"
                       :value="old('email')" placeholder="ej. nombre@restaurante.es"
                       required autofocus autocomplete="username" />

        <x-campo-texto nombre="password" tipo="password" etiqueta="Contraseña" icono="lock"
                       placeholder="Introduce tu contraseña"
                       required autocomplete="current-password">
            {{-- El enlace sale a la derecha de la etiqueta "Contraseña" --}}
            <x-slot name="accion">
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-label-md text-primary underline-offset-2 hover:underline">
                        ¿Olvidaste tu contraseña?
                    </a>
                @endif
            </x-slot>
        </x-campo-texto>

        <div class="flex items-center pt-1">
            <input id="remember_me" name="remember" type="checkbox"
                   class="h-[18px] w-[18px] rounded-sm border-outline-variant text-primary-container focus:ring-primary-container cursor-pointer">
            <label for="remember_me" class="ml-2.5 text-label-md text-on-surface-variant cursor-pointer select-none">
                Mantener la sesión iniciada en este dispositivo
            </label>
        </div>

        <div class="pt-2">
            <x-boton-primario class="w-full">
                <x-icono nombre="login" class="text-xl" />
                Iniciar sesión en MesaYa
            </x-boton-primario>
        </div>
    </form>
</x-guest-layout>
