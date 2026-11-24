{{--
    Layout de cocina (tablet en horizontal): una barra oscura arriba y todo el
    ancho de la pantalla para el tablero de pedidos.

    Uso:
        <x-layouts.cocina titulo="Pedidos">
            <x-slot name="acciones"> ...contadores, botones... </x-slot>
            ...columnas del tablero...
        </x-layouts.cocina>

    Rediseño «Comanda impresa»: la barra es de tinta (oscura) para que el
    tablero, con los tickets en blanco, sea lo que más destaque.
--}}
@props(['titulo' => 'Pedidos'])

<x-layouts.base :titulo="$titulo">
    <div class="min-h-screen flex flex-col">
        <header class="flex flex-wrap items-center justify-between gap-space-md px-margin py-space-sm bg-inverse-surface text-inverse-on-surface">
            <div class="flex items-baseline gap-3">
                <h1 class="text-headline-md">{{ $titulo }}</h1>
                <p class="text-body-md text-secondary-fixed-dim">Cocina</p>
            </div>

            <div class="flex items-center gap-space-sm">
                {{ $acciones ?? '' }}

                {{-- Solo lo ve el admin. Va sobre fondo claro porque el selector es de color oscuro. --}}
                @if (Auth::user()?->rol === 'admin')
                    <div class="rounded bg-surface-container-lowest text-on-surface px-2">
                        <x-selector-vista />
                    </div>
                @endif

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="pulsable inline-flex items-center gap-2 min-h-[48px] px-4 rounded border-[1.5px] border-inverse-on-surface/60 text-label-lg hover:bg-inverse-on-surface/10">
                        <x-icono nombre="logout" class="text-xl" />
                        Salir
                    </button>
                </form>
            </div>
        </header>

        {{-- Sin ancho máximo: el tablero aprovecha toda la tablet --}}
        <main class="flex-1 bg-surface-container">
            {{ $slot }}
        </main>
    </div>
</x-layouts.base>
