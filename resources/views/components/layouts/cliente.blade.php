{{--
    Layout del cliente que entra por el QR (móvil, sin login): cabecera con
    el restaurante y la mesa, contenido y barra de navegación inferior.
    Referencia: cliente/carta.

    Uso:
        <x-layouts.cliente restaurante="Bar La Plaza" :mesa="5" seccion="carta">
            ...contenido...
            <x-slot name="barraFija"> ...botón "Ver pedido"... </x-slot>
        </x-layouts.cliente>

    - restaurante y mesa: lo que se muestra en la cabecera.
    - seccion: pestaña activa (carta, pedido o cuenta).
    - slot "barraFija" (opcional): barra pegada encima de la navegación,
      pensada para el botón del carrito.
--}}
@props(['restaurante' => null, 'mesa' => null, 'seccion' => 'carta', 'titulo' => null, 'enlaceCarta' => '#', 'enlacePedido' => '#'])

@php
    // Enlaces de la carta (rama 8) y de "Mi pedido" (rama 9) de esta mesa.
    // "Cuenta" llega con la rama 10; hasta entonces, "#".
    $pestanas = [
        ['clave' => 'carta', 'texto' => 'Carta', 'icono' => 'restaurant_menu', 'enlace' => $enlaceCarta],
        ['clave' => 'pedido', 'texto' => 'Mi pedido', 'icono' => 'receipt_long', 'enlace' => $enlacePedido],
        ['clave' => 'cuenta', 'texto' => 'Cuenta', 'icono' => 'payments', 'enlace' => '#'],
    ];
@endphp

<x-layouts.base :titulo="$titulo ?? $restaurante">
    {{-- 720 px es el ancho máximo de lectura que marca DESIGN.md.
         $attributes pasa aquí los atributos extra, como el x-data del carrito:
         así el contenido y la barra fija de abajo comparten el mismo carrito. --}}
    <div {{ $attributes->merge(['class' => 'min-h-screen max-w-[720px] mx-auto flex flex-col bg-background']) }}>
        {{-- Solo lo ve un admin que está probando la carta: franja para volver a su panel.
             El cliente real no inicia sesión, así que nunca la ve. --}}
        @if (Auth::user()?->rol === 'admin')
            <div class="flex justify-end px-margin py-1 bg-primary-fixed/40">
                <x-selector-vista />
            </div>
        @endif

        <header class="sticky top-0 z-20 flex items-center gap-3 px-margin py-space-sm bg-surface-container-lowest border-b border-outline-variant/30">
            <x-logo />
            <div class="min-w-0">
                <p class="text-headline-sm font-bold truncate">{{ $restaurante ?? config('app.name', 'MesaYa') }}</p>
                @if ($mesa)
                    <p class="text-label-md text-tertiary">Mesa {{ $mesa }}</p>
                @endif
            </div>
        </header>

        {{-- pb-40 deja hueco para la barra fija y la navegación inferior --}}
        <main class="flex-1 p-margin pb-40">
            {{ $slot }}
        </main>

        <div class="fixed bottom-0 inset-x-0 z-20">
            <div class="max-w-[720px] mx-auto">
                @isset($barraFija)
                    <div class="px-margin pb-space-sm">
                        {{ $barraFija }}
                    </div>
                @endisset

                <nav class="grid grid-cols-3 bg-surface-container-lowest border-t border-outline-variant/30 shadow-nivel-2" aria-label="Navegación del cliente">
                    @foreach ($pestanas as $pestana)
                        @php $activa = $pestana['clave'] === $seccion; @endphp
                        <a href="{{ $pestana['enlace'] }}"
                           @class([
                               'flex flex-col items-center justify-center gap-0.5 min-h-[56px] m-1.5 rounded text-label-sm transition-colors',
                               'bg-primary-container text-on-primary' => $activa,
                               'text-on-surface hover:bg-surface-container-low' => ! $activa,
                           ])
                           @if ($activa) aria-current="page" @endif>
                            <x-icono :nombre="$pestana['icono']" />
                            {{ $pestana['texto'] }}
                        </a>
                    @endforeach
                </nav>
            </div>
        </div>
    </div>
</x-layouts.base>
