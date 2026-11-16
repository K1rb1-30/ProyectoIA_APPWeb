{{-- Eliminar la cuenta: el botón abre una ventana que pide la contraseña para confirmar --}}
<section class="space-y-6">
    <header>
        <h2 class="text-headline-sm">{{ __('Delete Account') }}</h2>
        <p class="mt-1 text-body-md text-secondary">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
        </p>
    </header>

    {{-- $dispatch lanza el evento "open-modal", que escucha el componente <x-modal> --}}
    <x-boton-peligro type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')">
        {{ __('Delete Account') }}
    </x-boton-peligro>

    {{-- Si la contraseña era incorrecta, la ventana se vuelve a abrir sola para enseñar el error --}}
    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-space-lg">
            @csrf
            @method('delete')

            <h2 class="text-headline-sm">{{ __('Are you sure you want to delete your account?') }}</h2>

            <p class="mt-1 text-body-md text-secondary">
                {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
            </p>

            <div class="mt-6">
                <x-campo-texto nombre="password" id="delete_user_password" tipo="password" icono="lock"
                               placeholder="{{ __('Password') }}" aria-label="{{ __('Password') }}"
                               :errores="$errors->userDeletion->get('password')" />
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <x-boton-secundario x-on:click="$dispatch('close')">{{ __('Cancel') }}</x-boton-secundario>
                <x-boton-peligro>{{ __('Delete Account') }}</x-boton-peligro>
            </div>
        </form>
    </x-modal>
</section>
