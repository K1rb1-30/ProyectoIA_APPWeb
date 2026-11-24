{{-- Aviso de que hay que verificar el correo, con botón para reenviar el enlace --}}
<x-guest-layout>
    <div class="mb-10 space-y-2">
        <h1 class="text-headline-lg">Verifica tu correo</h1>
        <p class="text-body-lg text-secondary">
            {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
        </p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <x-auth-session-status class="mb-4" :status="__('A new verification link has been sent to the email address you provided during registration.')" />
    @endif

    <div class="space-y-3">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <x-boton-primario class="w-full">{{ __('Resend Verification Email') }}</x-boton-primario>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <x-boton-secundario type="submit" class="w-full">{{ __('Log Out') }}</x-boton-secundario>
        </form>
    </div>
</x-guest-layout>
