{{--
    "Mi pedido" del cliente (rama 9, rediseño en la rama 23).
    Variables: $restaurante, $mesa y $platos ({ id: { nombre, descripcion, precio, foto } },
    solo los disponibles).

    Los platos del carrito los pinta Alpine (componente "carrito", resources/js/carrito.js)
    con x-for. Al pulsar "Pagar y enviar a cocina" (rama 15) se envía un formulario normal con
    campos ocultos lineas[0][plato_id], lineas[0][cantidad], lineas[0][notas]...

    Rediseño «Comanda impresa»: los platos son filas con línea (no tarjetas), el
    total va grande y en letra de ticket, y hay un solo botón naranja abajo.

    Rama 26: si el restaurante no ha conectado su cuenta de Stripe, el pedido
    no se puede pagar desde el móvil: no hay botón y se pide llamar al camarero.
--}}
@php
    $rutaMesa = ['slug' => $restaurante->slug, 'numero' => $mesa->numero];
    $aceptaTarjeta = $restaurante->tieneStripeConfigurado();

    $datosCarrito = 'carrito('.\Illuminate\Support\Js::from([
        'clave' => 'carrito-'.$restaurante->slug.'-'.$mesa->numero,
        'precios' => $platos->map(fn ($plato) => $plato['precio']),
    ]).')';
@endphp

<x-layouts.cliente :restaurante="$restaurante->nombre" :mesa="$mesa->numero" seccion="pedido"
                   :enlace-carta="route('cliente.carta', $rutaMesa)"
                   :enlace-pedido="route('cliente.pedido', $rutaMesa)"
                   :titulo="'Mi pedido · '.$restaurante->nombre"
                   :x-data="$datosCarrito">

    {{-- Datos de los platos para pintarlos con Alpine (x-for). --}}
    <script>
        window.platosCarta = @js($platos);
    </script>

    <h1 class="text-headline-lg">Mi pedido</h1>

    {{-- Aviso al volver de Stripe sin pagar (rama 15) --}}
    @if (session('estado'))
        <div role="status" class="mt-space-md px-4 py-3 rounded bg-amber-50 text-amber-900 text-body-md">
            {{ session('estado') }}
        </div>
    @endif

    {{-- Errores al enviar (por ejemplo, un plato que se ha agotado mientras tanto) --}}
    @if ($errors->any())
        <div role="alert" class="mt-space-md px-4 py-3 rounded bg-error-container text-on-error-container text-body-md">
            <ul>
                @foreach ($errors->all() as $mensaje)
                    <li>{{ $mensaje }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Platos que estaban en el carrito pero ya no están disponibles --}}
    <div x-show="quitados > 0" x-cloak role="status"
         class="mt-space-md px-4 py-3 rounded bg-amber-50 text-amber-900 text-body-md">
        Hemos quitado de tu pedido algún plato que ya no está disponible.
    </div>

    {{-- Carrito vacío --}}
    <div x-show="articulos() === 0" class="py-space-xl">
        <p class="text-headline-sm">Tu pedido está vacío.</p>
        <a href="{{ route('cliente.carta', $rutaMesa) }}" class="mt-space-md inline-flex items-center min-h-[48px] px-5 rounded border-[1.5px] border-on-surface text-label-lg font-bold">Ver la carta</a>
    </div>

    {{-- "enviando" desactiva el botón al enviar, para no mandar el pedido dos veces. --}}
    <form method="POST" action="{{ route('cliente.pedido.enviar', $rutaMesa) }}" x-show="articulos() > 0" x-cloak
          x-data="{ enviando: false }" x-on:submit="enviando = true">
        @csrf

        <ul class="mt-space-md border-t-2 border-on-surface">
            {{-- Una fila por plato del carrito. "indice" numera los campos ocultos. --}}
            <template x-for="(id, indice) in ids()" :key="id">
                <li class="py-4 border-b border-outline-variant">
                    <div class="flex items-baseline justify-between gap-space-md">
                        <div class="min-w-0">
                            <h2 class="text-headline-sm" x-text="window.platosCarta[id].nombre"></h2>
                            <p class="font-mono text-label-md font-normal text-secondary"
                               x-text="items[id].cantidad + ' × ' + euros(window.platosCarta[id].precio)"></p>
                        </div>
                        <span class="font-mono text-label-lg" x-text="euros(items[id].cantidad * window.platosCarta[id].precio)"></span>
                    </div>

                    <div class="mt-3 flex items-center gap-3">
                        <div class="inline-flex items-center shrink-0 rounded border-[1.5px] border-on-surface">
                            <button type="button" x-on:click="restar(id)" aria-label="Quitar una unidad"
                                    class="w-11 h-11 text-headline-sm">−</button>
                            <span class="w-8 text-center font-mono text-label-lg tabular-nums" x-text="items[id].cantidad"></span>
                            <button type="button" x-on:click="sumar(id)" aria-label="Añadir una unidad"
                                    class="w-11 h-11 bg-on-surface text-surface-container-lowest text-headline-sm">+</button>
                        </div>

                        {{-- Nota para cocina (se guarda en el carrito al escribir) --}}
                        <label class="flex-1 min-w-0">
                            <span class="sr-only">Nota para cocina</span>
                            <input type="text" maxlength="255" placeholder="Nota para cocina (sin cebolla…)"
                                   x-model="items[id].notas" x-on:input.debounce.300ms="guardar()"
                                   class="block w-full min-h-[44px] px-3 rounded border-[1.5px] border-secondary-fixed-dim bg-surface-container-lowest text-body-md focus:border-primary-container focus:ring-1 focus:ring-primary-container">
                        </label>
                    </div>

                    {{-- Campos ocultos que se envían al servidor --}}
                    <input type="hidden" :name="`lineas[${indice}][plato_id]`" :value="id">
                    <input type="hidden" :name="`lineas[${indice}][cantidad]`" :value="items[id].cantidad">
                    <input type="hidden" :name="`lineas[${indice}][notas]`" :value="items[id].notas">
                </li>
            </template>
        </ul>

        <a href="{{ route('cliente.carta', $rutaMesa) }}"
           class="inline-flex items-center min-h-[48px] mt-2 text-label-lg underline underline-offset-4">
            Añadir más platos de la carta
        </a>

        {{-- Total, en grande --}}
        <div class="mt-space-md flex items-baseline justify-between gap-space-md">
            <span class="text-headline-sm">Total</span>
            <span class="font-mono text-[32px] leading-none font-semibold tracking-tight" x-text="euros(totalCentimos())"></span>
        </div>
        <p class="mt-1 text-right text-label-md font-normal text-secondary">
            IVA incluido · <span x-text="articulos()"></span> uds.{{ $aceptaTarjeta ? ' · Se paga ahora con tarjeta.' : '' }}
        </p>

        @if ($aceptaTarjeta)
            {{-- Rama 15: el pedido se paga al hacerlo. En efectivo, con el camarero. --}}
            <div class="mt-space-lg pt-space-md border-t border-outline-variant space-y-2 text-body-md text-on-surface-variant">
                <p>Pagas con tarjeta en la página segura de Stripe (modo de prueba) y, en cuanto se confirma el pago, tu pedido llega a cocina.</p>
                <p>¿Prefieres pagar en efectivo? <strong class="text-on-surface">Llama al camarero</strong> y él te toma nota.</p>
            </div>

            <div class="sticky bottom-[72px] mt-space-lg">
                <button type="submit" x-bind:disabled="enviando"
                        class="pulsable w-full flex items-center justify-between gap-space-md min-h-[56px] px-5 rounded bg-primary-container text-on-primary shadow-nivel-2 hover:bg-primary disabled:opacity-60">
                    <span class="text-label-lg font-bold" x-text="enviando ? 'Abriendo el pago…' : 'Pagar y enviar a cocina'">Pagar y enviar a cocina</span>
                    <span class="font-mono text-label-lg" x-text="euros(totalCentimos())"></span>
                </button>
            </div>
        @else
            {{-- Rama 26: sin Stripe no hay pedidos desde el móvil (no existen pedidos sin pagar). --}}
            <div class="mt-space-lg pt-space-md border-t border-outline-variant space-y-2 text-body-md text-on-surface-variant">
                <p class="text-headline-sm text-on-surface">Este restaurante no acepta pagos con tarjeta desde el móvil.</p>
                <p><strong class="text-on-surface">Llama al camarero</strong> y él te toma nota. Puedes enseñarle esta lista.</p>
            </div>
        @endif
    </form>
</x-layouts.cliente>
