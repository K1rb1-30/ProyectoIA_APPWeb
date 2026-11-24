{{--
    Desplegable "Ver como" para el admin: salta a las pantallas de cada rol
    (administración, camarero, cocina) para probarlas sin cerrar sesión.
    Uso: <x-selector-vista />  (ya está puesto en las cabeceras de los layouts)

    - Solo se muestra si el usuario logueado es admin. Los demás roles no lo ven.
    - No cambia el rol de nadie en la base de datos: solo navega. El admin
      puede entrar en esas pantallas porque ComprobarRol siempre le deja pasar.
    - Al elegir una opción, el onchange lleva el navegador a esa URL.
    - "Cliente" abre la carta pública (rama 8) de la primera mesa del
      restaurante del admin. Si no tiene mesas, sale desactivado.
--}}
@php
    // Cada vista: texto, ruta y patrón de rutas que la marcan como elegida.
    $vistas = [
        ['texto' => 'Administración', 'ruta' => 'panel', 'patron' => ['panel', 'admin.*']],
        ['texto' => 'Camarero', 'ruta' => 'camarero.mesas', 'patron' => ['camarero.*']],
        ['texto' => 'Cocina', 'ruta' => 'cocina.pedidos', 'patron' => ['cocina.*']],
    ];

    // Primera mesa del restaurante del admin (el trait PerteneceARestaurante
    // ya filtra por su restaurante). Solo se consulta si el usuario es admin.
    $primeraMesa = Auth::user()?->rol === 'admin'
        ? \App\Models\Mesa::orderBy('numero')->first()
        : null;
@endphp

@if (Auth::user()?->rol === 'admin')
    <label {{ $attributes->merge(['class' => 'inline-flex items-center gap-2 text-label-md text-secondary']) }}>
        <span class="whitespace-nowrap">Ver como</span>
        <select onchange="if (this.value) window.location.href = this.value"
                class="min-h-[44px] py-2 pl-3 pr-9 rounded border-[1.5px] border-secondary-fixed-dim bg-surface-container-lowest text-label-md text-on-surface focus:border-primary-container focus:ring-1 focus:ring-primary-container">
            @foreach ($vistas as $vista)
                <option value="{{ route($vista['ruta']) }}" @selected(request()->routeIs(...$vista['patron']))>
                    {{ $vista['texto'] }}
                </option>
            @endforeach
            @if ($primeraMesa)
                <option value="{{ $primeraMesa->urlQr() }}" @selected(request()->routeIs('cliente.*'))>
                    Cliente (mesa {{ $primeraMesa->numero }})
                </option>
            @else
                <option value="" disabled>Cliente (crea una mesa)</option>
            @endif
        </select>
    </label>
@endif
