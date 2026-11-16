{{-- Página a la que se llega tras iniciar sesión. La rama 1 enviará a cada rol a su pantalla --}}
<x-app-layout>
    <x-slot name="header">
        <h1 class="text-headline-lg">{{ __('Dashboard') }}</h1>
    </x-slot>

    <x-tarjeta>
        <div class="flex items-center gap-3">
            <x-icono nombre="check_circle" class="text-tertiary" />
            <p class="text-body-lg">{{ __("You're logged in!") }}</p>
        </div>
    </x-tarjeta>
</x-app-layout>
