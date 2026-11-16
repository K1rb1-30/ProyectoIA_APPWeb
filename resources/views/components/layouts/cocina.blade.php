{{--
    Layout de cocina (tablet en horizontal): una cabecera y todo el ancho
    de la pantalla para el tablero de pedidos. Referencia: cocina/pedidos.

    Uso:
        <x-layouts.cocina titulo="Pedidos">
            <x-slot name="acciones"> ...contadores, botones... </x-slot>
            ...columnas del tablero...
        </x-layouts.cocina>
--}}
@props(['titulo' => 'Pedidos'])

<x-layouts.base :titulo="$titulo">
    <div class="min-h-screen flex flex-col">
        <header class="flex flex-wrap items-center justify-between gap-space-md px-margin py-space-sm bg-surface-container-lowest border-b border-outline-variant/30">
            <div class="flex items-center gap-3">
                <x-logo class="w-12 h-12" />
                <div>
                    <h1 class="text-headline-md">{{ $titulo }}</h1>
                    <p class="text-label-md font-normal text-secondary">Cocina</p>
                </div>
            </div>

            <div class="flex items-center gap-space-sm">
                {{ $acciones ?? '' }}

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-boton-secundario type="submit">
                        <x-icono nombre="logout" class="text-xl" />
                        Salir
                    </x-boton-secundario>
                </form>
            </div>
        </header>

        {{-- Sin ancho máximo: el tablero aprovecha toda la tablet --}}
        <main class="flex-1 p-margin bg-surface-container-low">
            {{ $slot }}
        </main>
    </div>
</x-layouts.base>
