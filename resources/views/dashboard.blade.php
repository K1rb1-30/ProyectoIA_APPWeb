{{--
    Panel del administrador (rol admin). Camarero y cocina van a sus propias pantallas tras el login.
    Usa el layout de admin para que el menú lateral (Carta, Alérgenos, Mesas y QR...) esté
    disponible nada más entrar. El panel con cifras (maqueta panelAdmin/panel) está por hacer.
--}}
<x-layouts.admin :titulo="__('Dashboard')">
    <x-tarjeta>
        <div class="flex items-center gap-3">
            <x-icono nombre="check_circle" class="text-tertiary" />
            <p class="text-body-lg">{{ __("You're logged in!") }}</p>
        </div>
    </x-tarjeta>
</x-layouts.admin>
