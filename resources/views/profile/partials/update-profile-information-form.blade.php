{{-- Formulario para cambiar el nombre y el correo --}}
<section>
    <header>
        <h2 class="text-headline-md">{{ __('Profile Information') }}</h2>
        <p class="mt-1 text-body-md text-secondary">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    {{-- Formulario aparte, vacío, que usa el botón "reenviar verificación" de más abajo --}}
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('perfil.actualizar') }}" class="mt-6 space-y-4">
        @csrf
        {{-- Los formularios HTML solo envían GET o POST: @method simula el PATCH --}}
        @method('patch')

        <x-campo-texto nombre="name" :etiqueta="__('Name')"
                       :value="old('name', $user->name)" required autofocus autocomplete="name" />

        <div>
            <x-campo-texto nombre="email" tipo="email" :etiqueta="__('Email')"
                           :value="old('email', $user->email)" required autocomplete="username" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="mt-2 text-body-md text-on-surface">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="text-label-md text-primary underline-offset-2 hover:underline">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 text-label-md text-tertiary">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4 pt-2">
            <x-boton-primario>{{ __('Save') }}</x-boton-primario>

            {{-- Mensaje "Guardado." que Alpine oculta solo a los 2 segundos --}}
            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                   class="text-label-md text-tertiary">{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
