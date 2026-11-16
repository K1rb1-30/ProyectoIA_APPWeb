{{-- Formulario para cambiar la contraseña --}}
<section>
    <header>
        <h2 class="text-headline-sm">{{ __('Update Password') }}</h2>
        <p class="mt-1 text-body-md text-secondary">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-4">
        @csrf
        @method('put')

        {{-- Este formulario guarda sus errores en la bolsa "updatePassword",
             por eso se los pasamos a mano a cada campo con :errores --}}
        <x-campo-texto nombre="current_password" id="update_password_current_password" tipo="password"
                       :etiqueta="__('Current Password')" icono="lock" autocomplete="current-password"
                       :errores="$errors->updatePassword->get('current_password')" />

        <x-campo-texto nombre="password" id="update_password_password" tipo="password"
                       :etiqueta="__('New Password')" icono="lock" autocomplete="new-password"
                       :errores="$errors->updatePassword->get('password')" />

        <x-campo-texto nombre="password_confirmation" id="update_password_password_confirmation" tipo="password"
                       :etiqueta="__('Confirm Password')" icono="lock" autocomplete="new-password"
                       :errores="$errors->updatePassword->get('password_confirmation')" />

        <div class="flex items-center gap-4 pt-2">
            <x-boton-primario>{{ __('Save') }}</x-boton-primario>

            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                   class="text-label-md text-tertiary">{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
