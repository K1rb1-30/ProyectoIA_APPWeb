{{--
    Carta pública del QR (cliente, móvil, sin login). Maqueta: cliente/carta.
    Variables: $restaurante, $mesa y $categorias (cada una con sus platos
    disponibles y los alérgenos de cada plato).

    Diferencias con la maqueta:
    - Los platos agotados NO salen (golden test nº 5), en vez de verse apagados.
    - Sin botones "Añadir" ni barra "Ver pedido": llegan con la rama 9 (autopedido).
    - Sin buscador.
--}}
<x-layouts.cliente :restaurante="$restaurante->nombre" :mesa="$mesa->numero" seccion="carta"
                   :enlace-carta="route('cliente.carta', ['slug' => $restaurante->slug, 'numero' => $mesa->numero])"
                   :titulo="'Carta · '.$restaurante->nombre">

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

                                <div class="mt-space-md pt-space-md border-t border-outline-variant/30">
                                    <x-precio :centimos="$plato->precio_centimos" class="text-price-display" />
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
                <p class="text-secondary">Avisa al camarero y te asesoraremos.</p>
            </div>
        </div>
    @endif
</x-layouts.cliente>
