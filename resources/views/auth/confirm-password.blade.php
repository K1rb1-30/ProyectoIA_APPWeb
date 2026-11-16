{{-- Laravel pide confirmar la contraseña antes de entrar en zonas sensibles --}}
<x-guest-layout>
    <div class="mb-8 space-y-2">
        <h2 class="text-headline-lg">Confirma tu contraseña</h2>
        <p class="text-body-md text-secondary">
            {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
        </p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-4">
        @csrf

        <x-campo-texto nombre="password" tipo="password" :etiqueta="__('Password')" icono="lock"
                       required autocomplete="current-password" />

        <div class="pt-2">
            <x-boton-primario class="w-full">{{ __('Confirm') }}</x-boton-primario>
        </div>
    </form>
</x-guest-layout>
