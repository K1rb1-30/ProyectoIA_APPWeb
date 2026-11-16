{{--
    Desplegable "Ver como" para el admin: salta a las pantallas de cada rol
    (administración, camarero, cocina) para probarlas sin cerrar sesión.
    Uso: <x-selector-vista />  (ya está puesto en las cabeceras de los layouts)

    - Solo se muestra si el usuario logueado es admin. Los demás roles no lo ven.
    - No cambia el rol de nadie en la base de datos: solo navega. El admin
      puede entrar en esas pantallas porque ComprobarRol siempre le deja pasar.
    - Al elegir una opción, el onchange lleva el navegador a esa URL.
    - "Cliente" sale desactivado: la carta pública por QR llega con la rama 8.
--}}
@php
    // Cada vista: texto, ruta y patrón de rutas que la marcan como elegida.
    $vistas = [
        ['texto' => 'Administración', 'ruta' => 'dashboard', 'patron' => ['dashboard', 'admin.*']],
        ['texto' => 'Camarero', 'ruta' => 'camarero.mesas', 'patron' => ['camarero.*']],
        ['texto' => 'Cocina', 'ruta' => 'cocina.pedidos', 'patron' => ['cocina.*']],
    ];
@endphp

@if (Auth::user()?->rol === 'admin')
    <label {{ $attributes->merge(['class' => 'inline-flex items-center gap-2 text-label-md text-secondary']) }}>
        <x-icono nombre="visibility" class="text-xl" />
        <span class="whitespace-nowrap">Ver como</span>
        <select onchange="if (this.value) window.location.href = this.value"
                class="min-h-[44px] py-2 pl-3 pr-9 rounded border-outline-variant/50 bg-surface-container-lowest text-label-md text-on-surface focus:border-primary-container focus:ring-1 focus:ring-primary-container">
            @foreach ($vistas as $vista)
                <option value="{{ route($vista['ruta']) }}" @selected(request()->routeIs(...$vista['patron']))>
                    {{ $vista['texto'] }}
                </option>
            @endforeach
            <option value="" disabled>Cliente (próximamente)</option>
        </select>
    </label>
@endif
