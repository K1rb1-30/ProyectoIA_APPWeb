{{-- Elegir una contraseña nueva. Se llega aquí desde el enlace del correo de recuperación --}}
<x-guest-layout>
    <div class="mb-8 space-y-2">
        <h2 class="text-headline-lg">Elige una contraseña nueva</h2>
        <p class="text-body-md text-secondary">Escríbela dos veces para confirmar que no hay errores.</p>
    </div>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
        @csrf

        {{-- Token del enlace del correo: demuestra que la petición es legítima --}}
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <x-campo-texto nombre="email" tipo="email" :etiqueta="__('Email')" icono="mail"
                       :value="old('email', $request->email)" required autofocus autocomplete="username" />

        <x-campo-texto nombre="password" tipo="password" :etiqueta="__('Password')" icono="lock"
                       required autocomplete="new-password" />

        <x-campo-texto nombre="password_confirmation" tipo="password" :etiqueta="__('Confirm Password')" icono="lock"
                       required autocomplete="new-password" />

        <div class="pt-2">
            <x-boton-primario class="w-full">{{ __('Reset Password') }}</x-boton-primario>
        </div>
    </form>
</x-guest-layout>
