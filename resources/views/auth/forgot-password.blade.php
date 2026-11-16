{{-- Recuperar contraseña: se pide el correo y se envía un enlace para elegir una nueva --}}
<x-guest-layout>
    <div class="mb-8 space-y-2">
        <h2 class="text-headline-lg">Recuperar la contraseña</h2>
        <p class="text-body-md text-secondary">
            {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
        </p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf

        <x-campo-texto nombre="email" tipo="email" :etiqueta="__('Email')" icono="mail"
                       :value="old('email')" required autofocus />

        <div class="pt-2 space-y-3">
            <x-boton-primario class="w-full">
                <x-icono nombre="send" class="text-xl" />
                {{ __('Email Password Reset Link') }}
            </x-boton-primario>

            <a href="{{ route('login') }}" class="block text-center text-label-md text-primary underline-offset-2 hover:underline">
                Volver al inicio de sesión
            </a>
        </div>
    </form>
</x-guest-layout>
