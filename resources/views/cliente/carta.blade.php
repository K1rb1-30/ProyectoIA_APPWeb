{{--
    Carta pública del QR (cliente, móvil, sin login). Maqueta: cliente/carta.
    Variables: $restaurante, $mesa y $categorias (cada una con sus platos
    disponibles y los alérgenos de cada plato).

    El carrito (rama 9) es el componente Alpine "carrito" (resources/js/carrito.js):
    cada plato tiene "Añadir" o el contador −/+, y abajo sale "Ver pedido".

    Diferencias con la maqueta:
    - Los platos agotados NO salen (golden test nº 5), en vez de verse apagados.
    - Sin buscador.
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
        <x-tarjeta class="text-center">
            <x-icono nombre="restaurant_menu" class="text-5xl text-secondary" />
            <p class="mt-2 text-body-lg">La carta todavía no está disponible.</p>
            <p class="text-body-md text-secondary">Pregunta al camarero, te atenderá enseguida.</p>
        </x-tarjeta>
    @else
        {{-- Cápsulas de categorías: enlaces que bajan a su sección (#categoria-3).
             scroll-mt deja hueco para que la cabecera fija no tape el título. --}}
        <nav class="sticky top-[60px] z-10 -mx-margin px-margin py-space-sm mb-space-md flex gap-2 overflow-x-auto bg-background" aria-label="Categorías de la carta">
            @foreach ($categorias as $categoria)
                <a href="#categoria-{{ $categoria->id }}"
                   class="shrink-0 inline-flex items-center min-h-[44px] px-5 rounded-full border border-outline-variant/50 bg-surface-container-lowest text-label-lg hover:border-primary-container hover:text-primary">
                    {{ $categoria->nombre }}
                </a>
            @endforeach
        </nav>

        <div class="space-y-space-xl">
            @foreach ($categorias as $categoria)
                <section id="categoria-{{ $categoria->id }}" class="scroll-mt-[130px]" aria-labelledby="titulo-categoria-{{ $categoria->id }}">
                    <div class="flex items-center justify-between gap-space-md mb-space-md">
                        <h2 id="titulo-categoria-{{ $categoria->id }}" class="text-headline-md">{{ $categoria->nombre }}</h2>
                        <span class="shrink-0 px-3 py-1 rounded-full bg-secondary-container text-label-md text-on-secondary-fixed-variant">
                            {{ $categoria->platos->count() }} {{ $categoria->platos->count() === 1 ? 'plato' : 'platos' }}
                        </span>
                    </div>

                    <div class="space-y-space-md">
                        @foreach ($categoria->platos as $plato)
                            <x-tarjeta>
                                <div class="flex gap-space-md">
                                    {{-- Izquierda: nombre, descripción y alérgenos --}}
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-headline-sm">{{ $plato->nombre }}</h3>
                                        @if ($plato->descripcion)
                                            <p class="mt-1 text-body-md text-secondary">{{ $plato->descripcion }}</p>
                                        @endif

                                        @if ($plato->alergenos->isNotEmpty())
                                            <div class="mt-2 flex flex-wrap gap-1" aria-label="Alérgenos">
                                                @foreach ($plato->alergenos as $alergeno)
                                                    <x-badge-alergeno>
                                                        <x-icono :nombre="$alergeno->icono" class="text-sm" />
                                                        {{ $alergeno->nombre }}
                                                    </x-badge-alergeno>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Derecha: foto de 88×88 px (DESIGN.md), solo si el plato tiene --}}
                                    @if ($plato->urlFoto())
                                        <img src="{{ $plato->urlFoto() }}" alt="{{ $plato->nombre }}"
                                             class="w-[88px] h-[88px] shrink-0 rounded object-cover" loading="lazy">
                                    @endif
                                </div>

                                <div class="mt-space-md pt-space-md border-t border-outline-variant/30 flex items-center justify-between gap-space-md">
                                    <x-precio :centimos="$plato->precio_centimos" class="text-price-display" />

                                    {{-- Si el plato no está en el carrito: botón "Añadir".
                                         Si ya está: contador −/+ (x-show elige cuál se ve). --}}
                                    <button type="button" x-show="cantidad({{ $plato->id }}) === 0" x-on:click="sumar({{ $plato->id }})"
                                            class="pulsable inline-flex items-center gap-1 min-h-[48px] px-5 rounded bg-primary-container text-on-primary text-label-lg font-bold">
                                        <x-icono nombre="add" class="text-xl" />
                                        Añadir
                                        <span class="sr-only">{{ $plato->nombre }}</span>
                                    </button>

                                    <div x-show="cantidad({{ $plato->id }}) > 0" x-cloak
                                         class="inline-flex items-center gap-1 p-1 rounded-full border border-outline-variant/50">
                                        <button type="button" x-on:click="restar({{ $plato->id }})" aria-label="Quitar una unidad de {{ $plato->nombre }}"
                                                class="w-10 h-10 rounded-full bg-surface-container-low text-headline-sm">−</button>
                                        <span class="w-8 text-center text-label-lg tabular-nums" x-text="cantidad({{ $plato->id }})"></span>
                                        <button type="button" x-on:click="sumar({{ $plato->id }})" aria-label="Añadir una unidad de {{ $plato->nombre }}"
                                                class="w-10 h-10 rounded-full bg-primary-container text-on-primary text-headline-sm">+</button>
                                    </div>
                                </div>
                            </x-tarjeta>
                        @endforeach
                    </div>
                </section>
            @endforeach
        </div>

        {{-- Aviso de intolerancias (como en la maqueta) --}}
        <div class="mt-space-xl flex gap-3 p-space-md rounded bg-surface-container-low text-body-md">
            <x-icono nombre="info" class="text-xl text-error" />
            <div>
                <p class="text-label-lg">¿Intolerancias o peticiones especiales?</p>
                <p class="text-secondary">Puedes indicarlo con una nota en cada plato al revisar tu pedido.</p>
            </div>
        </div>
    @endif

    {{-- Barra "Ver pedido": solo sale cuando hay algo en el carrito --}}
    <x-slot name="barraFija">
        <a href="{{ route('cliente.pedido', $rutaMesa) }}" x-show="articulos() > 0" x-cloak
           class="pulsable flex items-center justify-between gap-space-md min-h-[56px] px-space-md rounded-lg bg-primary-container text-on-primary shadow-nivel-2">
            <span class="flex items-center gap-3 text-label-lg font-bold">
                <span class="inline-flex items-center justify-center min-w-8 h-8 px-2 rounded-full bg-white/25 tabular-nums" x-text="articulos()"></span>
                Ver pedido
            </span>
            <span class="flex items-center gap-2 text-price-display">
                <span x-text="euros(totalCentimos())"></span>
                <x-icono nombre="arrow_forward" />
            </span>
        </a>
    </x-slot>
</x-layouts.cliente>
