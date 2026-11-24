{{--
    Nueva comanda del camarero (rama 5, rediseño en la rama 20).

    Cómo funciona:
    - Alpine guarda en "cantidades" cuántas unidades lleva cada plato ({ idPlato: cantidad }).
    - Cada plato tiene campos ocultos lineas[id][plato_id] y lineas[id][cantidad].
      Si la cantidad es 0 se desactivan (:disabled) y el navegador no los envía,
      así solo llegan al servidor los platos pedidos.
    - El total de abajo lo calcula Alpine solo para enseñarlo. El servidor no se
      fía de él: vuelve a coger los precios de la base de datos.

    Rediseño «Comanda impresa»: la mesa elegida va como título; los platos,
    en filas separadas por líneas (no en tarjetas) para que quepan más en
    la pantalla; las pestañas de categoría se marcan con una raya naranja.
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

{{-- El título de la pantalla es la mesa: es lo que el camarero tiene que comprobar antes de enviar --}}
<x-layouts.camarero :titulo="'Mesa '.$mesa->numero" seccion="mesas">
    <x-slot:acciones>
        <a href="{{ route('camarero.mesas') }}" aria-label="Volver a mesas"
           class="pulsable inline-flex items-center justify-center w-11 h-11 rounded border-[1.5px] border-secondary-fixed-dim hover:bg-surface-container-low">
            <x-icono nombre="arrow_back" />
        </a>
    </x-slot:acciones>

    {{-- Subtítulo: qué se está haciendo y el estado de la mesa --}}
    <div class="flex items-baseline justify-between gap-space-md mb-space-md">
        <h2 class="text-headline-sm">Nueva comanda</h2>
        <x-chip-estado :estado="$mesa->estado" />
    </div>

    {{-- Selector de mesa: cada ficha lleva a la comanda de esa mesa --}}
    <section class="mb-space-md">
        <p class="text-label-md text-secondary mb-2">Cambiar de mesa</p>
        <div class="flex gap-2 overflow-x-auto pb-1.5 -mx-margin px-margin">
            @foreach ($mesas as $otraMesa)
                @php $seleccionada = $otraMesa->is($mesa); @endphp
                <a href="{{ route('camarero.comanda.crear', $otraMesa) }}"
                   @if ($seleccionada) aria-current="page" @endif
                   aria-label="Mesa {{ $otraMesa->numero }}, {{ $otraMesa->estado }}"
                   @class([
                       'pulsable shrink-0 inline-flex items-center justify-center min-w-[48px] h-12 px-2 rounded font-mono text-label-lg',
                       'bg-on-surface text-surface-container-lowest' => $seleccionada,
                       'border-[1.5px] border-secondary-fixed-dim bg-surface-container-lowest' => ! $seleccionada && $otraMesa->estado === 'libre',
                       'bg-surface-container-high' => ! $seleccionada && $otraMesa->estado === 'ocupada',
                   ])>{{ $otraMesa->numero }}</a>
            @endforeach
        </div>
    </section>

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
                      return (centimos / 100).toLocaleString('es-ES', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' €'
                  },
              }">
            @csrf

            {{-- Pestañas de categorías: la activa lleva una raya naranja debajo --}}
            <nav class="flex gap-space-lg overflow-x-auto -mx-margin px-margin border-b border-outline-variant" aria-label="Categorías">
                @foreach ($categorias as $categoria)
                    <button type="button" x-on:click="categoria = {{ $categoria->id }}"
                            class="shrink-0 min-h-[44px] -mb-px border-b-[3px] text-label-lg"
                            :class="categoria === {{ $categoria->id }}
                                ? 'border-primary-container text-on-surface'
                                : 'border-transparent text-secondary'"
                            :aria-pressed="categoria === {{ $categoria->id }}">
                        {{ $categoria->nombre }}
                    </button>
                @endforeach
            </nav>

            {{-- Platos. Los de otras categorías se ocultan con x-show, pero
                 siguen dentro del formulario y se envían igual. --}}
            @foreach ($categorias as $categoria)
                <ul x-show="categoria === {{ $categoria->id }}">
                    @foreach ($categoria->platos as $plato)
                        @if ($plato->disponible)
                            <li class="py-3 border-b border-outline-variant">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-headline-sm truncate">{{ $plato->nombre }}</h3>
                                        <p class="mt-0.5 flex flex-wrap items-center gap-1.5">
                                            <x-precio :centimos="$plato->precio_centimos" class="font-mono text-body-md text-secondary" />
                                            @foreach ($plato->alergenos as $alergeno)
                                                <x-badge-alergeno>{{ $alergeno->nombre }}</x-badge-alergeno>
                                            @endforeach
                                        </p>
                                    </div>

                                    {{-- Botones − / +. El borde se oscurece cuando el plato ya está en la comanda --}}
                                    <div class="flex items-center rounded border-[1.5px] shrink-0"
                                         :class="cantidad({{ $plato->id }}) > 0 ? 'border-on-surface' : 'border-secondary-fixed-dim'">
                                        <button type="button" x-on:click="restar({{ $plato->id }})" aria-label="Quitar uno de {{ $plato->nombre }}"
                                                class="pulsable w-11 h-11 text-headline-sm">−</button>
                                        <span class="w-8 text-center font-mono text-label-lg tabular-nums" x-text="cantidad({{ $plato->id }})" aria-live="polite">0</span>
                                        <button type="button" x-on:click="sumar({{ $plato->id }})" aria-label="Añadir uno de {{ $plato->nombre }}"
                                                class="pulsable w-11 h-11 bg-on-surface text-surface-container-lowest text-headline-sm">+</button>
                                    </div>
                                </div>

                                {{-- Campos que se envían (desactivados si la cantidad es 0) --}}
                                <input type="hidden" name="lineas[{{ $plato->id }}][plato_id]" value="{{ $plato->id }}" :disabled="cantidad({{ $plato->id }}) === 0">
                                <input type="hidden" name="lineas[{{ $plato->id }}][cantidad]" :value="cantidad({{ $plato->id }})" :disabled="cantidad({{ $plato->id }}) === 0">

                                {{-- Nota para cocina: solo aparece si el plato está en la comanda --}}
                                <div x-show="cantidad({{ $plato->id }}) > 0" class="mt-2">
                                    <label for="notas-{{ $plato->id }}" class="sr-only">Notas para cocina de {{ $plato->nombre }}</label>
                                    <input type="text" id="notas-{{ $plato->id }}" name="lineas[{{ $plato->id }}][notas]" maxlength="255"
                                           value="{{ $lineasAnteriores[$plato->id]['notas'] ?? '' }}"
                                           :disabled="cantidad({{ $plato->id }}) === 0"
                                           placeholder="Nota para cocina (sin cebolla, muy hecho...)"
                                           class="w-full min-h-[44px] rounded border-0 bg-primary-fixed text-on-primary-fixed-variant text-body-md placeholder:text-on-primary-fixed-variant/60 focus:ring-2 focus:ring-primary-container">
                                </div>
                            </li>
                        @else
                            {{-- Plato agotado: tachado, en gris y sin botones --}}
                            <li class="py-3 border-b border-outline-variant">
                                <h3 class="text-headline-sm text-secondary line-through truncate">{{ $plato->nombre }}</h3>
                                <p class="mt-0.5 flex items-center gap-2 text-body-md">
                                    <x-precio :centimos="$plato->precio_centimos" class="font-mono text-secondary" />
                                    <span class="font-semibold text-error">Agotado</span>
                                </p>
                            </li>
                        @endif
                    @endforeach
                </ul>
            @endforeach

            {{-- Hueco para que la barra de envío no tape el último plato --}}
            <div class="h-24"></div>

            {{-- Barra fija de abajo: un solo botón naranja con el número de platos y el total.
                 Va justo encima de la barra de navegación del layout (65 px de alto). --}}
            <div class="fixed inset-x-0 bottom-[65px] z-10">
                <div class="max-w-md mx-auto px-margin pb-2">
                    <x-boton-primario class="w-full justify-between shadow-nivel-2" x-bind:disabled="articulos() === 0">
                        <span>Enviar a cocina · <span class="font-mono" x-text="articulos()">0</span></span>
                        <span class="font-mono" x-text="euros(totalCentimos())">0,00 €</span>
                    </x-boton-primario>
                </div>
            </div>
        </form>
    @endif
</x-layouts.camarero>
