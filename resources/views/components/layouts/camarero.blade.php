{{--
    Layout del camarero (móvil): cabecera fija arriba, contenido en una
    columna y barra de navegación fija abajo. Referencia: camarero/mesas.

    Uso:
        <x-layouts.camarero titulo="Mesas" seccion="mesas">
            ...contenido...
        </x-layouts.camarero>

    "seccion" marca qué pestaña de abajo está activa: mesas, avisos o cobros.
    El slot "acciones" (opcional) pone botones a la derecha de la cabecera.
--}}
@props(['titulo' => 'Mesas', 'seccion' => 'mesas'])

@php
    // Pestañas de la barra inferior. Las rutas llegan con las ramas 5, 7 y 12;
    // hasta entonces (null) apuntan a "#".
    $pestanas = [
        ['clave' => 'mesas', 'texto' => 'Mesas', 'icono' => 'table_restaurant', 'ruta' => null],
        ['clave' => 'avisos', 'texto' => 'Avisos', 'icono' => 'room_service', 'ruta' => null],
        ['clave' => 'cobros', 'texto' => 'Cobros', 'icono' => 'payments', 'ruta' => null],
        ['clave' => 'perfil', 'texto' => 'Perfil', 'icono' => 'account_circle', 'ruta' => 'profile.edit'],
    ];
@endphp

<x-layouts.base :titulo="$titulo">
    {{-- max-w-md centra la columna si se abre en una pantalla grande --}}
    <div class="min-h-screen max-w-md mx-auto flex flex-col bg-background">
        {{-- Solo lo ve el admin: una franja encima de la cabecera para volver al panel o ir a otra vista --}}
        @if (Auth::user()?->rol === 'admin')
            <div class="flex justify-end px-margin py-1 bg-primary-fixed/40">
                <x-selector-vista />
            </div>
        @endif

        <header class="sticky top-0 z-20 flex items-center justify-between gap-space-sm px-margin py-space-sm bg-surface-container-lowest border-b border-outline-variant/30">
            <div class="flex items-center gap-3 min-w-0">
                <x-logo />
                <div class="min-w-0">
                    <h1 class="text-headline-sm font-bold truncate">{{ $titulo }}</h1>
                    @auth
                        <p class="text-label-md font-normal text-secondary truncate">Camarero: {{ Auth::user()->name }}</p>
                    @endauth
                </div>
            </div>

            <div class="flex items-center gap-space-sm shrink-0">
                {{ $acciones ?? '' }}
            </div>
        </header>

        {{-- pb-24 deja hueco para que la barra inferior no tape el final --}}
        <main class="flex-1 p-margin pb-24">
            {{ $slot }}
        </main>

        <nav class="fixed bottom-0 inset-x-0 z-20 bg-surface-container-lowest border-t border-outline-variant/30 shadow-nivel-2" aria-label="Navegación del camarero">
            <div class="max-w-md mx-auto grid grid-cols-4">
                @foreach ($pestanas as $pestana)
                    @php $activa = $pestana['clave'] === $seccion; @endphp
                    <a href="{{ $pestana['ruta'] ? route($pestana['ruta']) : '#' }}"
                       @class([
                           'flex flex-col items-center justify-center gap-0.5 min-h-[64px] m-1.5 rounded text-label-sm transition-colors',
                           'bg-primary-container text-on-primary' => $activa,
                           'text-on-surface hover:bg-surface-container-low' => ! $activa,
                       ])
                       @if ($activa) aria-current="page" @endif>
                        <x-icono :nombre="$pestana['icono']" />
                        {{ $pestana['texto'] }}
                    </a>
                @endforeach
            </div>
        </nav>
    </div>
</x-layouts.base>
