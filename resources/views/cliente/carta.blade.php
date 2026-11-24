{{--
    Carta pública del QR (cliente, móvil, sin login). Rediseño en la rama 23.
    Variables: $restaurante, $mesa y $categorias (cada una con sus platos
    disponibles y los alérgenos de cada plato).
    El carrito (rama 9) es el componente Alpine "carrito" (resources/js/carrito.js):
    cada plato tiene "Añadir" o el contador −/+, y abajo sale "Ver pedido".

    Los platos agotados NO salen (golden test nº 5).

    Rediseño «Comanda impresa»: se lee como una carta impresa. Cada categoría
    es un título grande y cada plato una fila con una línea de puntos entre el
    nombre y el precio. Los alérgenos van como texto («Contiene: ...»).
--}}
@php
    $rutaMesa = ['slug' => $restaurante->slug, 'numero' => $mesa->numero];

    // Datos para el carrito: clave de localStorage (una por restaurante y mesa)
    // y precio de cada plato disponible { id: céntimos }.
    // Js::from convierte el array de PHP en un objeto de JavaScript seguro.
    $datosCarrito = 'carrito('.\Illuminate\Support\Js::from([
        'clave' => 'carrito-'.$restaurante->slug.'-'.$mesa->numero,
        'precios' => $categorias->flatMap->platos->pluck('precio_centimos', 'id'),
    ]).')';
@endphp

<x-layouts.cliente :restaurante="$restaurante->nombre" :mesa="$mesa->numero" seccion="carta"
                   :enlace-carta="route('cliente.carta', $rutaMesa)"
                   :enlace-pedido="route('cliente.pedido', $rutaMesa)"
                   :titulo="'Carta · '.$restaurante->nombre"
                   :x-data="$datosCarrito">
    @if ($categorias->isEmpty())
        <div class="py-space-xl">
            <p class="text-headline-sm">La carta todavía no está disponible.</p>
            <p class="mt-1 text-body-md text-secondary">Pregunta al camarero, te atenderá enseguida.</p>
        </div>
    @else
        {{-- Categorías: enlaces que bajan a su sección (#categoria-3).
             Se quedan pegadas arriba al bajar; scroll-mt deja hueco para que no tapen el título. --}}
        <nav class="sticky top-0 z-10 -mx-5 px-5 flex gap-space-lg overflow-x-auto bg-background border-b border-outline-variant" aria-label="Categorías de la carta">
            @foreach ($categorias as $categoria)
                <a href="#categoria-{{ $categoria->id }}"
                   class="shrink-0 inline-flex items-center min-h-[48px] text-label-lg text-on-surface-variant hover:text-on-surface">
                    {{ $categoria->nombre }}
                </a>
            @endforeach
        </nav>

        @foreach ($categorias as $categoria)
            <section id="categoria-{{ $categoria->id }}" class="scroll-mt-16 pt-space-xl" aria-labelledby="titulo-categoria-{{ $categoria->id }}">
                <h2 id="titulo-categoria-{{ $categoria->id }}" class="text-[26px] leading-tight tracking-[-0.03em] font-extrabold">{{ $categoria->nombre }}</h2>

                <ul class="mt-2">
                    @foreach ($categoria->platos as $plato)
                        <li class="flex gap-space-md py-4 border-b border-outline-variant">
                            <div class="flex-1 min-w-0">
                                {{-- Nombre ····· precio, como en una carta impresa --}}
                                <div class="flex items-baseline gap-2.5">
                                    <h3 class="text-headline-sm">{{ $plato->nombre }}</h3>
                                    <span class="flex-1 min-w-[16px] border-b-[1.5px] border-dotted border-secondary-fixed-dim -translate-y-1" aria-hidden="true"></span>
                                    <x-precio :centimos="$plato->precio_centimos" class="font-mono text-label-lg" />
                                </div>
                                @if ($plato->descripcion)
                                    <p class="mt-1.5 text-body-md text-on-surface-variant">{{ $plato->descripcion }}</p>
                                @endif
                                @if ($plato->alergenos->isNotEmpty())
                                    <p class="mt-1.5 text-label-md font-normal text-secondary">
                                        Contiene:
                                        @foreach ($plato->alergenos as $alergeno)
                                            <span class="font-semibold text-on-surface">{{ $alergeno->nombre }}</span>@if (! $loop->last) · @endif
                                        @endforeach
                                    </p>
                                @endif

                                <div class="mt-3 flex items-center justify-between gap-space-md">
                                    {{-- Si el plato no está en el carrito: botón "Añadir".
                                         Si ya está: contador −/+ (x-show elige cuál se ve). --}}
                                    <button type="button" x-show="cantidad({{ $plato->id }}) === 0" x-on:click="sumar({{ $plato->id }})"
                                            class="pulsable inline-flex items-center min-h-[44px] px-4 rounded border-[1.5px] border-on-surface text-label-lg font-bold hover:bg-surface-container-lowest">
                                        Añadir
                                        <span class="sr-only">{{ $plato->nombre }}</span>
                                    </button>
                                    <div x-show="cantidad({{ $plato->id }}) > 0" x-cloak
                                         class="inline-flex items-center rounded border-[1.5px] border-on-surface">
                                        <button type="button" x-on:click="restar({{ $plato->id }})" aria-label="Quitar una unidad de {{ $plato->nombre }}"
                                                class="w-11 h-11 text-headline-sm">−</button>
                                        <span class="w-8 text-center font-mono text-label-lg tabular-nums" x-text="cantidad({{ $plato->id }})"></span>
                                        <button type="button" x-on:click="sumar({{ $plato->id }})" aria-label="Añadir una unidad de {{ $plato->nombre }}"
                                                class="w-11 h-11 bg-on-surface text-surface-container-lowest text-headline-sm">+</button>
                                    </div>
                                </div>
                            </div>

                            {{-- Foto a la derecha, solo si el plato tiene --}}
                            @if ($plato->urlFoto())
                                <img src="{{ $plato->urlFoto() }}" alt="{{ $plato->nombre }}"
                                     class="w-[72px] h-[72px] shrink-0 rounded-sm object-cover" loading="lazy">
                            @endif
                        </li>
                    @endforeach
                </ul>
            </section>
        @endforeach

        {{-- Aviso de intolerancias --}}
        <p class="mt-space-xl py-space-md border-t border-outline-variant text-body-md text-secondary">
            <span class="font-semibold text-on-surface">¿Intolerancias o peticiones especiales?</span>
            Puedes indicarlo con una nota en cada plato al revisar tu pedido.
        </p>
    @endif

    {{-- Barra "Ver pedido": solo sale cuando hay algo en el carrito --}}
    <x-slot name="barraFija">
        <a href="{{ route('cliente.pedido', $rutaMesa) }}" x-show="articulos() > 0" x-cloak
           class="pulsable flex items-center justify-between gap-space-md min-h-[56px] px-5 rounded bg-primary-container text-on-primary shadow-nivel-2 hover:bg-primary">
            <span class="text-label-lg font-bold">Ver pedido · <span class="font-mono" x-text="articulos()"></span></span>
            <span class="font-mono text-label-lg" x-text="euros(totalCentimos())"></span>
        </a>
    </x-slot>
</x-layouts.cliente>
