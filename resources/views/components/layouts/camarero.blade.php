{{--
    Layout del camarero (móvil): cabecera fija arriba, contenido en una
    columna y barra de navegación fija abajo.

    Uso:
        <x-layouts.camarero titulo="Mesas" seccion="mesas">
            ...contenido...
        </x-layouts.camarero>

    "seccion" marca qué pestaña de abajo está activa: mesas, avisos o cobros.

    El div principal lleva el componente Alpine "avisosCamarero" (rama 7): cada
    5 s pide los pedidos listos y pinta el contador de la pestaña «Avisos».
    Como todo el contenido está dentro, la pantalla de avisos usa esos mismos
    datos sin hacer otra consulta.
    El slot "acciones" (opcional) pone botones a la derecha de la cabecera.

    Rediseño «Comanda impresa»: el título de la pantalla va en grande (es lo
    primero que se lee) y la pestaña activa se marca con una raya naranja
    arriba, en vez de un bloque de color.
--}}
@props(['titulo' => 'Mesas', 'seccion' => 'mesas'])

@php
    // Pestañas de la barra inferior.
    $pestanas = [
        ['clave' => 'mesas', 'texto' => 'Mesas', 'icono' => 'table_restaurant', 'ruta' => 'camarero.mesas'],
        ['clave' => 'avisos', 'texto' => 'Avisos', 'icono' => 'room_service', 'ruta' => 'camarero.avisos'],
        ['clave' => 'cobros', 'texto' => 'Cobros', 'icono' => 'payments', 'ruta' => 'camarero.cobros'],
        ['clave' => 'perfil', 'texto' => 'Perfil', 'icono' => 'account_circle', 'ruta' => 'perfil.editar'],
    ];

    // Globo de «Cobros»: partes que esperan el cobro en efectivo. Se cuenta al
    // cargar la página (sin polling). Con login, el filtro del trait deja solo
    // las del restaurante del camarero.
    $esperandoEfectivo = Auth::check() ? \App\Models\ParteCuenta::esperandoEfectivo()->count() : 0;
@endphp

<x-layouts.base :titulo="$titulo">
    {{-- max-w-md centra la columna si se abre en una pantalla grande --}}
    <div class="min-h-screen max-w-md mx-auto flex flex-col bg-background"
         x-data="avisosCamarero({ urlDatos: @js(route('camarero.avisos.datos')), urlServido: @js(route('camarero.avisos.servido', '__ID__')) })">
        {{-- Solo lo ven los desarrolladores: una franja encima de la cabecera para volver al panel o ir a otra vista --}}
        @if (Auth::user()?->esDesarrollador())
            <div class="flex justify-end px-margin py-1 bg-surface-container">
                <x-selector-vista />
            </div>
        @endif

        <header class="sticky top-0 z-20 flex items-end justify-between gap-space-sm px-margin pt-space-md pb-space-sm bg-background">
            <div class="min-w-0">
                <h1 class="text-headline-lg truncate">{{ $titulo }}</h1>
                @auth
                    <p class="text-body-md text-secondary truncate">{{ Auth::user()->name }}</p>
                @endauth
            </div>

            <div class="flex items-center gap-space-sm shrink-0">
                {{ $acciones ?? '' }}
            </div>
        </header>

        {{-- pb-24 deja hueco para que la barra inferior no tape el final --}}
        <main class="flex-1 px-margin pt-space-sm pb-24">
            {{ $slot }}
        </main>

        <nav class="fixed bottom-0 inset-x-0 z-20 bg-surface-container-lowest border-t border-outline-variant" aria-label="Navegación del camarero">
            <div class="max-w-md mx-auto grid grid-cols-4">
                @foreach ($pestanas as $pestana)
                    @php $activa = $pestana['clave'] === $seccion; @endphp
                    <a href="{{ $pestana['ruta'] ? route($pestana['ruta']) : '#' }}"
                       @class([
                           'flex flex-col items-center justify-center gap-0.5 min-h-[64px] -mt-px border-t-[3px] text-label-sm transition-colors',
                           'border-primary-container text-on-surface' => $activa,
                           'border-transparent text-secondary hover:text-on-surface' => ! $activa,
                       ])
                       @if ($activa) aria-current="page" @endif>
                        {{-- relative: para colocar el globo del contador encima del icono --}}
                        <span class="relative">
                            <x-icono :nombre="$pestana['icono']" />
                            @if ($pestana['clave'] === 'avisos')
                                <span x-show="pedidos.length > 0" x-cloak x-text="pedidos.length"
                                      class="absolute -top-1.5 -right-2.5 min-w-[20px] h-5 px-1 rounded-full bg-primary-container text-on-primary font-mono text-[11px] font-semibold leading-5 text-center"
                                      aria-label="Pedidos listos para servir"></span>
                            @endif
                            @if ($pestana['clave'] === 'cobros' && $esperandoEfectivo > 0)
                                <span class="absolute -top-1.5 -right-2.5 min-w-[20px] h-5 px-1 rounded-full bg-primary-container text-on-primary font-mono text-[11px] font-semibold leading-5 text-center"
                                      aria-label="Personas esperando para pagar en efectivo">{{ $esperandoEfectivo }}</span>
                            @endif
                        </span>
                        {{ $pestana['texto'] }}
                    </a>
                @endforeach
            </div>
        </nav>
    </div>
</x-layouts.base>
