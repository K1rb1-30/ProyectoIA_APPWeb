{{--
    Layout del panel de administración (escritorio): menú lateral fijo a la
    izquierda, barra superior y contenido. Referencia: panelAdmin/panel.

    Uso:
        <x-layouts.admin titulo="Carta">
            <x-slot name="acciones"> <x-boton-primario>Añadir plato</x-boton-primario> </x-slot>
            ...contenido de la página...
        </x-layouts.admin>

    El slot "acciones" (opcional) pone botones a la derecha de la barra superior.
--}}
@props(['titulo' => 'Panel'])

@php
    // Opciones del menú lateral. "ruta" es el nombre de la ruta de Laravel.
    // Las ramas 3, 4 y 13 crearán esas rutas: mientras no existan (null),
    // el enlace apunta a "#". Al crear una ruta, poned aquí su nombre.
    $menu = [
        ['texto' => 'Carta', 'icono' => 'restaurant_menu', 'ruta' => null],
        ['texto' => 'Alérgenos', 'icono' => 'nutrition', 'ruta' => null],
        ['texto' => 'Mesas y QR', 'icono' => 'qr_code_2', 'ruta' => 'mesas.index'],
        ['texto' => 'Personal', 'icono' => 'badge', 'ruta' => null],
        ['texto' => 'Estadísticas', 'icono' => 'bar_chart', 'ruta' => null],
    ];
@endphp

<x-layouts.base :titulo="$titulo">
    <div class="min-h-screen lg:flex">
        {{-- Menú lateral --}}
        <aside class="lg:w-72 lg:shrink-0 lg:min-h-screen flex flex-col gap-space-lg p-space-md bg-surface-container-lowest border-b lg:border-b-0 lg:border-r border-outline-variant/30">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-2 pt-2">
                <x-logo />
                <span>
                    <span class="block text-headline-sm font-bold">{{ config('app.name', 'MesaYa') }}</span>
                    <span class="block text-label-md font-normal text-secondary">Panel de administración</span>
                </span>
            </a>

            <nav class="flex flex-row flex-wrap lg:flex-col gap-1" aria-label="Menú principal">
                @foreach ($menu as $opcion)
                    @php
                        // La opción está activa si estamos en cualquier ruta de su grupo:
                        // para "mesas.index" vale también "mesas.create", "mesas.edit"...
                        $activa = $opcion['ruta'] && request()->routeIs(strtok($opcion['ruta'], '.').'.*');
                    @endphp
                    <a href="{{ $opcion['ruta'] ? route($opcion['ruta']) : '#' }}"
                       @class([
                           'flex items-center gap-3 min-h-[48px] px-4 rounded text-label-lg transition-colors',
                           'bg-primary text-on-primary' => $activa,
                           'text-on-surface hover:bg-surface-container-low' => ! $activa,
                       ])
                       @if ($activa) aria-current="page" @endif>
                        <x-icono :nombre="$opcion['icono']" class="text-xl" />
                        {{ $opcion['texto'] }}
                    </a>
                @endforeach
            </nav>

            {{-- Parte de abajo: perfil y cerrar sesión --}}
            <div class="lg:mt-auto flex flex-row flex-wrap lg:flex-col gap-1 pt-space-md border-t border-outline-variant/30">
                <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 min-h-[48px] px-4 rounded text-label-lg text-secondary hover:bg-surface-container-low transition-colors">
                    <x-icono nombre="account_circle" class="text-xl" />
                    Perfil
                </a>
                {{-- Cerrar sesión va por POST (con @csrf) para que nadie pueda cerrarla con un simple enlace --}}
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 min-h-[48px] px-4 rounded text-label-lg text-secondary hover:bg-surface-container-low transition-colors">
                        <x-icono nombre="logout" class="text-xl" />
                        Cerrar sesión
                    </button>
                </form>
            </div>
        </aside>

        <div class="flex-1 min-w-0 flex flex-col">
            {{-- Barra superior --}}
            <header class="flex flex-wrap items-center justify-between gap-space-md px-margin-md py-space-md bg-surface-container-lowest border-b border-outline-variant/30">
                <h1 class="text-headline-md">{{ $titulo }}</h1>

                <div class="flex items-center gap-space-md">
                    {{ $acciones ?? '' }}

                    @auth
                        <div class="text-right">
                            <span class="block text-label-lg">{{ Auth::user()->name }}</span>
                            <span class="block text-label-md font-normal text-secondary">Administración</span>
                        </div>
                    @endauth
                </div>
            </header>

            {{-- Contenido: ancho máximo de 1280 px, como indica DESIGN.md --}}
            <main class="flex-1 w-full max-w-[1280px] mx-auto p-margin-md lg:p-margin-lg">
                {{ $slot }}
            </main>
        </div>
    </div>
</x-layouts.base>
