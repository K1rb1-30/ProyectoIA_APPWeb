{{--
    Layout del panel de administración (escritorio): menú lateral a la
    izquierda y contenido a la derecha.

    Uso:
        <x-layouts.admin titulo="Carta">
            <x-slot name="acciones"> <x-boton-primario>Añadir plato</x-boton-primario> </x-slot>
            <x-slot name="subtitulo">24 platos</x-slot>   (opcional)
            ...contenido de la página...
        </x-layouts.admin>

    - "acciones" (opcional) pone botones a la derecha del título.
    - "subtitulo" (opcional) pone una línea pequeña debajo del título.

    Rediseño «Comanda impresa» (rama 22): el título de la página va en grande
    encima del contenido (antes iba en una barra superior), el menú es solo
    texto (sin iconos) y la opción activa es un bloque de tinta.
--}}
@props(['titulo' => 'Panel'])

@php
    // Opciones del menú lateral. "ruta" es el nombre de la ruta de Laravel.
    $menu = [
        ['texto' => 'Panel', 'ruta' => 'panel'],
        ['texto' => 'Carta', 'ruta' => 'admin.carta'],
        ['texto' => 'Alérgenos', 'ruta' => 'admin.alergenos'],
        ['texto' => 'Mesas y QR', 'ruta' => 'admin.mesas'],
        ['texto' => 'Personal', 'ruta' => 'admin.personal'],
        ['texto' => 'Dispositivos', 'ruta' => 'admin.dispositivos'],
        ['texto' => 'Estadísticas', 'ruta' => 'admin.estadisticas'],
        // Rama 24: estado de la suscripción mensual y portal de Stripe.
        ['texto' => 'Suscripción', 'ruta' => 'admin.suscripcion'],
    ];
    $usuario = Auth::user();
@endphp

<x-layouts.base :titulo="$titulo">
    <div class="min-h-screen lg:flex">
        {{-- Menú lateral. En pantallas pequeñas se queda arriba y las opciones bajan de línea --}}
        <aside class="lg:w-60 lg:shrink-0 lg:min-h-screen flex flex-col gap-space-lg px-space-md py-space-lg border-b lg:border-b-0 lg:border-r border-outline-variant">
            <div class="px-3">
                <a href="{{ route('panel') }}" class="text-headline-md font-extrabold tracking-tight">{{ config('app.name', 'MesaYa') }}<span class="text-primary-container">.</span></a>
                {{-- Las cuentas que pueden cambiar de restaurante ven un desplegable;
                     el resto, solo el nombre de su restaurante. --}}
                @if ($usuario?->puedeCambiarDeRestaurante())
                    <x-selector-restaurante class="mt-1" />
                @elseif ($usuario?->restaurante)
                    <p class="mt-0.5 text-body-md text-secondary">{{ $usuario->restaurante->nombre }}</p>
                @endif
            </div>

            <nav class="flex flex-row flex-wrap lg:flex-col gap-0.5" aria-label="Menú principal">
                @foreach ($menu as $opcion)
                    @php
                        // La opción está activa si estamos en su ruta o en una de sus subrutas.
                        $activa = request()->routeIs($opcion['ruta'].'*');
                    @endphp
                    <a href="{{ route($opcion['ruta']) }}"
                       @class([
                           'flex items-center min-h-[44px] px-3 rounded text-label-lg transition-colors',
                           'bg-on-surface text-surface-container-lowest' => $activa,
                           'font-medium text-on-surface-variant hover:bg-surface-container-low hover:text-on-surface' => ! $activa,
                       ])
                       @if ($activa) aria-current="page" @endif>
                        {{ $opcion['texto'] }}
                    </a>
                @endforeach
            </nav>

            {{-- Parte de abajo: quién eres, vista, perfil y cerrar sesión --}}
            <div class="lg:mt-auto px-3 pt-space-md border-t border-outline-variant text-body-md">
                @if ($usuario)
                    <p class="text-label-lg">{{ $usuario->name }}</p>
                    <p class="text-secondary">Administración</p>
                @endif
                <div class="mt-space-sm">
                    {{-- apilado: en los 240 px del menú no caben la etiqueta y el desplegable en una fila --}}
                    <x-selector-vista apilado />
                </div>
                <div class="mt-space-sm flex flex-wrap items-center gap-x-3">
                    <a href="{{ route('perfil.editar') }}" class="inline-flex items-center min-h-[44px] text-secondary hover:text-on-surface">Perfil</a>
                    <span class="text-outline-variant" aria-hidden="true">·</span>
                    {{-- Cerrar sesión va por POST (con @csrf) para que nadie pueda cerrarla con un simple enlace --}}
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="inline-flex items-center min-h-[44px] text-secondary hover:text-on-surface">Cerrar sesión</button>
                    </form>
                </div>
            </div>
        </aside>

        {{-- Contenido: ancho máximo de 1180 px --}}
        <main class="flex-1 min-w-0 px-4 sm:px-8 lg:px-12 py-space-xl">
            <div class="max-w-[1180px]">
                {{-- Título de la página en grande y, a la derecha, sus acciones --}}
                <header class="flex flex-wrap items-end justify-between gap-space-md mb-space-xl">
                    <div>
                        <h1 class="text-[clamp(2rem,3.5vw,2.75rem)] leading-none tracking-[-0.04em] font-extrabold">{{ $titulo }}</h1>
                        @isset($subtitulo)
                            <p class="mt-2.5 text-body-lg text-secondary">{{ $subtitulo }}</p>
                        @endisset
                    </div>
                    @isset($acciones)
                        {{-- flex-wrap: en el móvil los botones bajan a otra línea en vez de salirse de la pantalla --}}
                        <div class="flex flex-wrap items-center gap-space-sm">
                            {{ $acciones }}
                        </div>
                    @endisset
                </header>

                <x-mensajes-sesion />
                {{ $slot }}
            </div>
        </main>
    </div>
</x-layouts.base>
