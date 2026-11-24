{{-- Perfil del usuario: tres tarjetas, una por formulario (cada uno está en profile/partials) --}}
<x-app-layout>
    <x-slot name="header">
        <h1 class="text-[clamp(2rem,3.5vw,2.75rem)] leading-none tracking-[-0.04em] font-extrabold">{{ __('Profile') }}</h1>
    </x-slot>

    <div class="space-y-space-lg">
        <x-tarjeta>
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </x-tarjeta>

        <x-tarjeta>
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </x-tarjeta>

        <x-tarjeta>
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </x-tarjeta>
    </div>
</x-app-layout>
