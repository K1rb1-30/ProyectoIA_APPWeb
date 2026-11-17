{{--
    Nueva comanda del camarero (rama 5). Referencia: docs/design/DesignFrontend/camarero/nueva-comanda.
    No están (de momento) los comensales, las notas rápidas en chips ni el buscador.

    Cómo funciona:
    - Alpine guarda en "cantidades" cuántas unidades lleva cada plato ({ idPlato: cantidad }).
    - Cada plato tiene campos ocultos lineas[id][plato_id] y lineas[id][cantidad].
      Si la cantidad es 0 se desactivan (:disabled) y el navegador no los envía,
      así solo llegan al servidor los platos pedidos.
    - El total de abajo lo calcula Alpine solo para enseñarlo. El servidor no se
      fía de él: vuelve a coger los precios de la base de datos.
--}}
@php
    // Precio de cada plato disponible, para que Alpine calcule el total.
    $precios = $categorias->flatMap->platos
        ->where('disponible', true)
        ->mapWithKeys(fn ($plato) => [$plato->id => $plato->precio_centimos]);

    // Si la validación falla, Laravel vuelve aquí con lo que se había enviado
    // (old). Así el camarero no pierde la comanda que llevaba.
    $lineasAnteriores = old('lineas', []);
    $cantidades = collect($lineasAnteriores)->mapWithKeys(fn ($linea, $id) => [$id => (int) ($linea['cantidad'] ?? 0)]);
@endphp

<x-layouts.camarero titulo="Nueva comanda" seccion="mesas">
    <x-slot:acciones>
        <a href="{{ route('camarero.mesas') }}" aria-label="Volver a mesas"
           class="pulsable inline-flex items-center justify-center w-10 h-10 rounded bg-surface-container-low hover:bg-surface-container">
            <x-icono nombre="arrow_back" />
        </a>
    </x-slot:acciones>

    {{-- Selector de mesa: cada ficha lleva a la comanda de esa mesa --}}
    <section class="mb-space-md">
        <p class="text-label-md text-secondary uppercase tracking-wider mb-2">Seleccionar mesa</p>
        <div class="flex gap-2 overflow-x-auto pb-1.5 -mx-margin px-margin">
            @foreach ($mesas as $otraMesa)
                @php $seleccionada = $otraMesa->is($mesa); @endphp
                <a href="{{ route('camarero.comanda.crear', $otraMesa) }}"
                   @if ($seleccionada) aria-current="page" @endif
                   @class([
                       'pulsable shrink-0 px-3.5 py-2 rounded-md border shadow-sm',
                       'bg-on-surface text-surface-container-lowest border-on-surface ring-2 ring-primary-container' => $seleccionada,
                       'bg-surface-container-lowest border-surface-container-highest' => ! $seleccionada,
                   ])>
                    <span class="block text-headline-sm font-bold leading-none">M{{ $otraMesa->numero }}</span>
                    <span @class([
                        'block text-label-sm mt-1',
                        'text-surface-container-lowest' => $seleccionada,
                        'text-tertiary' => ! $seleccionada && $otraMesa->estado === 'libre',
                        'text-primary' => ! $seleccionada && $otraMesa->estado === 'ocupada',
                    ])>{{ $seleccionada ? 'Seleccionada' : ucfirst($otraMesa->estado) }}</span>
                </a>
            @endforeach
        </div>
    </section>

    {{-- Tarjeta de la mesa elegida --}}
    <x-tarjeta class="flex items-center gap-3 mb-space-md">
        <span class="flex items-center justify-center w-12 h-12 rounded-md bg-primary-fixed text-primary text-headline-md font-bold">{{ $mesa->numero }}</span>
        <div>
            <h2 class="text-headline-md">Mesa {{ $mesa->numero }}</h2>
            <x-chip-estado :estado="$mesa->estado" class="mt-1" />
        </div>
    </x-tarjeta>

    {{-- Errores de validación (por ejemplo, un plato que se ha agotado mientras tanto) --}}
    @if ($errors->any())
        <div role="alert" class="mb-space-md px-4 py-3 rounded bg-error-container text-on-error-container text-body-md">
            <ul class="space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if ($categorias->isEmpty())
        <x-tarjeta>
            <p class="text-body-lg text-secondary">La carta está vacía. El administrador tiene que añadir platos.</p>
        </x-tarjeta>
    @else
        {{-- @js pasa los datos de PHP a JavaScript de forma segura (escapa comillas) --}}
        <form method="POST" action="{{ route('camarero.comanda.guardar', $mesa) }}"
              x-data="{
                  categoria: @js($categorias->first()->id),
                  cantidades: @js((object) $cantidades->all()),
                  precios: @js((object) $precios->all()),
                  cantidad(id) { return this.cantidades[id] ?? 0 },
                  sumar(id) { if (this.cantidad(id) < 99) this.cantidades[id] = this.cantidad(id) + 1 },
                  restar(id) { if (this.cantidad(id) > 0) this.cantidades[id] = this.cantidad(id) - 1 },
                  articulos() { return Object.values(this.cantidades).reduce((suma, n) => suma + n, 0) },
                  totalCentimos() {
                      return Object.entries(this.cantidades)
                          .reduce((suma, [id, n]) => suma + n * (this.precios[id] ?? 0), 0)
                  },
                  euros(centimos) {
                      return (centimos / 100).toLocaleString('es-ES', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' €'
                  },
              }">
            @csrf

            {{-- Pestañas de categorías --}}
            <nav class="flex gap-2 overflow-x-auto pb-2 mb-space-sm -mx-margin px-margin" aria-label="Categorías">
                @foreach ($categorias as $categoria)
                    <button type="button" x-on:click="categoria = {{ $categoria->id }}"
                            class="pulsable shrink-0 px-4 py-2 rounded-full border text-label-md shadow-sm"
                            :class="categoria === {{ $categoria->id }}
                                ? 'bg-on-surface text-surface-container-lowest border-on-surface'
                                : 'bg-surface-container-lowest text-secondary border-surface-container-highest'">
                        {{ $categoria->nombre }}
                    </button>
                @endforeach
            </nav>

            {{-- Platos. Los de otras categorías se ocultan con x-show, pero
                 siguen dentro del formulario y se envían igual. --}}
            @foreach ($categorias as $categoria)
                <div x-show="categoria === {{ $categoria->id }}" class="space-y-2.5">
                    @foreach ($categoria->platos as $plato)
                        @if ($plato->disponible)
                            <article class="relative overflow-hidden bg-surface-container-lowest rounded-lg border border-surface-container-highest shadow-sm p-3">
                                {{-- Franja naranja a la izquierda cuando el plato está en la comanda --}}
                                <div class="absolute inset-y-0 left-0 w-1 bg-primary-container" x-show="cantidad({{ $plato->id }}) > 0"></div>

                                <div class="flex items-center justify-between gap-3 pl-1">
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-headline-sm truncate">{{ $plato->nombre }}</h3>
                                        <div class="flex flex-wrap items-center gap-1.5 mt-1">
                                            <x-precio :centimos="$plato->precio_centimos" class="text-price-display text-primary" />
                                            @foreach ($plato->alergenos as $alergeno)
                                                <x-badge-alergeno>{{ $alergeno->nombre }}</x-badge-alergeno>
                                            @endforeach
                                        </div>
                                    </div>

                                    {{-- Botones − / + --}}
                                    <div class="flex items-center bg-surface-container-low rounded-md p-1 border border-surface-container-highest shrink-0">
                                        <button type="button" x-on:click="restar({{ $plato->id }})" aria-label="Quitar uno de {{ $plato->nombre }}"
                                                class="pulsable w-10 h-10 rounded bg-surface-container-lowest font-bold text-xl shadow-sm">−</button>
                                        <span class="w-8 text-center text-headline-sm font-bold tabular-nums" x-text="cantidad({{ $plato->id }})" aria-live="polite">0</span>
                                        <button type="button" x-on:click="sumar({{ $plato->id }})" aria-label="Añadir uno de {{ $plato->nombre }}"
                                                class="pulsable w-10 h-10 rounded bg-primary-container text-on-primary font-bold text-xl shadow-sm">+</button>
                                    </div>
                                </div>

                                {{-- Campos que se envían (desactivados si la cantidad es 0) --}}
                                <input type="hidden" name="lineas[{{ $plato->id }}][plato_id]" value="{{ $plato->id }}" :disabled="cantidad({{ $plato->id }}) === 0">
                                <input type="hidden" name="lineas[{{ $plato->id }}][cantidad]" :value="cantidad({{ $plato->id }})" :disabled="cantidad({{ $plato->id }}) === 0">

                                {{-- Nota para cocina: solo aparece si el plato está en la comanda --}}
                                <div x-show="cantidad({{ $plato->id }}) > 0" class="mt-2 pl-1">
                                    <label for="notas-{{ $plato->id }}" class="sr-only">Notas para cocina de {{ $plato->nombre }}</label>
                                    <input type="text" id="notas-{{ $plato->id }}" name="lineas[{{ $plato->id }}][notas]" maxlength="255"
                                           value="{{ $lineasAnteriores[$plato->id]['notas'] ?? '' }}"
                                           :disabled="cantidad({{ $plato->id }}) === 0"
                                           placeholder="Nota para cocina (sin cebolla, muy hecho...)"
                                           class="w-full rounded border-outline-variant/60 bg-surface-container-low text-body-md focus:border-primary-container focus:ring-primary-container">
                                </div>
                            </article>
                        @else
                            {{-- Plato agotado: en gris, tachado y sin botones --}}
                            <article class="bg-surface-container-low rounded-lg border border-surface-container-highest/60 opacity-60 p-3">
                                <div class="flex items-center justify-between gap-3 pl-1">
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-headline-sm text-secondary line-through truncate">{{ $plato->nombre }}</h3>
                                        <div class="flex items-center gap-2 mt-1">
                                            <x-precio :centimos="$plato->precio_centimos" class="text-price-display text-secondary" />
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-label-sm bg-error-container text-on-error-container">
                                                <x-icono nombre="block" class="text-sm" /> Agotado
                                            </span>
                                        </div>
                                    </div>
                                    <span class="h-10 px-4 inline-flex items-center rounded bg-surface-container-highest text-secondary text-label-md">No disp.</span>
                                </div>
                            </article>
                        @endif
                    @endforeach
                </div>
            @endforeach

            {{-- Hueco para que la barra de envío no tape el último plato --}}
            <div class="h-24"></div>

            {{-- Barra fija de abajo con el total y el botón de enviar. Va justo
                 encima de la barra de navegación del layout (77 px de alto). --}}
            <div class="fixed inset-x-0 bottom-[77px] z-10">
                <div class="max-w-md mx-auto px-margin pb-2">
                    <div class="flex items-center justify-between gap-3 bg-surface-container-lowest border border-surface-container-highest rounded-lg p-2.5 shadow-nivel-2">
                        <div class="pl-2">
                            <p class="flex items-center gap-1.5 text-headline-sm font-bold tabular-nums">
                                <span class="w-2.5 h-2.5 rounded-full bg-primary-container"></span>
                                <span x-text="euros(totalCentimos())">0,00 €</span>
                            </p>
                            <p class="text-label-sm text-secondary">
                                <span x-text="articulos()">0</span> artículos · Mesa {{ $mesa->numero }}
                            </p>
                        </div>
                        <x-boton-primario class="flex-1 max-w-[220px]" x-bind:disabled="articulos() === 0">
                            <x-icono nombre="local_fire_department" class="text-xl" />
                            Enviar a cocina
                        </x-boton-primario>
                    </div>
                </div>
            </div>
        </form>
    @endif
</x-layouts.camarero>
