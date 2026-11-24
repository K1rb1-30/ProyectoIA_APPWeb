{{--
    Layout del cliente que entra por el QR (móvil, sin login): cabecera con
    el restaurante y la mesa, contenido y barra de navegación inferior.

    Uso:
        <x-layouts.cliente restaurante="Bar La Plaza" :mesa="5" seccion="carta">
            ...contenido...
            <x-slot name="barraFija"> ...botón "Ver pedido"... </x-slot>
        </x-layouts.cliente>

    - restaurante y mesa: lo que se muestra en la cabecera.
    - seccion: pestaña activa (carta, pedido o cuenta).
    - slot "barraFija" (opcional): barra pegada encima de la navegación,
      pensada para el botón del carrito.

    Rediseño «Comanda impresa» (rama 23): la mesa va pequeña en letra de ticket
    y el nombre del restaurante, en grande, como la cabecera de una carta
    impresa. La pestaña activa se marca con una raya naranja arriba.
--}}
@props(['restaurante' => null, 'mesa' => null, 'seccion' => 'carta', 'titulo' => null, 'enlaceCarta' => '#', 'enlacePedido' => '#'])

@php
    // Enlaces de las pestañas: carta (rama 8), "Mi pedido" (rama 9) y cuenta
    // (rama 10). Si la URL es de una mesa (/r/{slug}/mesa/{numero}/...), se
    // sacan de ella; si no, se usan los que se pasen (o "#").
    $rutaMesa = request()->route('slug') ? ['slug' => request()->route('slug'), 'numero' => request()->route('numero')] : null;

    $pestanas = [
        ['clave' => 'carta', 'texto' => 'Carta', 'icono' => 'restaurant_menu', 'enlace' => $rutaMesa ? route('cliente.carta', $rutaMesa) : $enlaceCarta],
        ['clave' => 'pedido', 'texto' => 'Mi pedido', 'icono' => 'receipt_long', 'enlace' => $rutaMesa ? route('cliente.pedido', $rutaMesa) : $enlacePedido],
        ['clave' => 'cuenta', 'texto' => 'Cuenta', 'icono' => 'payments', 'enlace' => $rutaMesa ? route('cliente.cuenta', $rutaMesa) : '#'],
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
            <div class="flex justify-end px-margin py-1 bg-surface-container">
                <x-selector-vista />
            </div>
        @endif

        <header class="px-5 pt-6 pb-2">
            @if ($mesa)
                <p class="font-mono text-label-md font-normal uppercase tracking-wide text-secondary">Mesa {{ $mesa }}</p>
            @endif
            {{-- En la carta el nombre es el título de la pantalla (grande); en las demás
                 pantallas, que ya tienen su propio título, va más pequeño --}}
            <p @class([
                'mt-1 font-extrabold tracking-tight',
                'text-[32px] leading-none tracking-[-0.04em]' => $seccion === 'carta',
                'text-headline-sm' => $seccion !== 'carta',
            ])>{{ $restaurante ?? config('app.name', 'MesaYa') }}</p>
        </header>

        {{-- pb-40 deja hueco para la barra fija y la navegación inferior --}}
        <main class="flex-1 px-5 pt-space-md pb-40">
            {{ $slot }}
        </main>

        <div class="fixed bottom-0 inset-x-0 z-20">
            <div class="max-w-[720px] mx-auto">
                @isset($barraFija)
                    <div class="px-4 pb-space-sm">
                        {{ $barraFija }}
                    </div>
                @endisset

                <nav class="grid grid-cols-3 bg-surface-container-lowest border-t border-outline-variant" aria-label="Navegación del cliente">
                    @foreach ($pestanas as $pestana)
                        @php $activa = $pestana['clave'] === $seccion; @endphp
                        <a href="{{ $pestana['enlace'] }}"
                           @class([
                               'flex flex-col items-center justify-center gap-0.5 min-h-[60px] -mt-px border-t-[3px] text-label-sm transition-colors',
                               'border-primary-container text-on-surface' => $activa,
                               'border-transparent text-secondary hover:text-on-surface' => ! $activa,
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
