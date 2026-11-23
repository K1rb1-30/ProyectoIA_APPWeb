{{--
    "Mi pedido" del cliente (rama 9). Maqueta: cliente/mi-pedido.
    Variables: $restaurante, $mesa y $platos ({ id: { nombre, descripcion, precio, foto } },
    solo los disponibles).

    Los platos del carrito los pinta Alpine (componente "carrito", resources/js/carrito.js)
    con x-for. Al pulsar "Pagar y enviar a cocina" (rama 15) se envía un formulario normal con
    campos ocultos lineas[0][plato_id], lineas[0][cantidad], lineas[0][notas]...

    Diferencias con la maqueta: sin comentarios para toda la mesa, comensales
    ni desglose del IVA (no están en los requisitos).
--}}
@php
    $rutaMesa = ['slug' => $restaurante->slug, 'numero' => $mesa->numero];

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

    <h1 class="text-headline-lg mb-space-md">Mi pedido</h1>

    {{-- Aviso al volver de Stripe sin pagar (rama 15) --}}
    @if (session('estado'))
        <div role="status" class="mb-space-md flex items-center gap-2 px-4 py-3 rounded bg-amber-50 text-amber-900 text-body-md">
            <x-icono nombre="info" class="text-xl" />
            {{ session('estado') }}
        </div>
    @endif

    {{-- Errores al enviar (por ejemplo, un plato que se ha agotado mientras tanto) --}}
    @if ($errors->any())
        <div role="alert" class="mb-space-md flex gap-2 px-4 py-3 rounded bg-error-container text-on-error-container text-body-md">
            <x-icono nombre="error" class="text-xl" />
            <ul>
                @foreach ($errors->all() as $mensaje)
                    <li>{{ $mensaje }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Platos que estaban en el carrito pero ya no están disponibles --}}
    <div x-show="quitados > 0" x-cloak role="status"
         class="mb-space-md flex gap-2 px-4 py-3 rounded bg-amber-50 text-amber-900 text-body-md">
        <x-icono nombre="warning" class="text-xl" />
        <span>Hemos quitado de tu pedido algún plato que ya no está disponible.</span>
    </div>

    {{-- Carrito vacío --}}
    <x-tarjeta x-show="articulos() === 0" class="text-center">
        <x-icono nombre="shopping_bag" class="text-5xl text-secondary" />
        <p class="mt-2 text-body-lg">Tu pedido está vacío.</p>
        <a href="{{ route('cliente.carta', $rutaMesa) }}" class="mt-2 inline-block text-label-lg text-primary hover:underline">Ver la carta</a>
    </x-tarjeta>

    {{-- "enviando" desactiva el botón al enviar, para no mandar el pedido dos veces. --}}
    <form method="POST" action="{{ route('cliente.pedido.enviar', $rutaMesa) }}" x-show="articulos() > 0" x-cloak
          x-data="{ enviando: false }" x-on:submit="enviando = true">
        @csrf

        <div class="space-y-space-md">
            {{-- Una tarjeta por plato del carrito. "indice" numera los campos ocultos. --}}
            <template x-for="(id, indice) in ids()" :key="id">
                <x-tarjeta>
                    <div class="flex gap-space-md">
                        <div class="flex-1 min-w-0">
                            <h2 class="text-headline-sm" x-text="window.platosCarta[id].nombre"></h2>
                            <p class="mt-1 text-body-md text-secondary" x-show="window.platosCarta[id].descripcion" x-text="window.platosCarta[id].descripcion"></p>
                            <p class="mt-1 text-price-display" x-text="euros(window.platosCarta[id].precio)"></p>
                        </div>
                        <template x-if="window.platosCarta[id].foto">
                            <img :src="window.platosCarta[id].foto" alt="" class="w-[88px] h-[88px] shrink-0 rounded object-cover">
                        </template>
                    </div>

                    {{-- Nota para cocina (se guarda en el carrito al escribir) --}}
                    <label class="block mt-space-md">
                        <span class="flex items-center gap-1 mb-1 text-label-md text-secondary">
                            <x-icono nombre="edit_note" class="text-lg" />
                            Nota para cocina
                        </span>
                        <input type="text" maxlength="255" placeholder="Ej. sin cebolla, salsa aparte…"
                               x-model="items[id].notas" x-on:input.debounce.300ms="guardar()"
                               class="block w-full min-h-[44px] px-3 rounded border-outline-variant/50 text-body-md focus:border-primary-container focus:ring-1 focus:ring-primary-container">
                    </label>

                    <div class="mt-space-md pt-space-md border-t border-outline-variant/30 flex items-center justify-between gap-space-md">
                        <span class="text-body-md text-secondary">
                            Subtotal: <span class="text-label-lg text-on-surface" x-text="euros(items[id].cantidad * window.platosCarta[id].precio)"></span>
                        </span>
                        <div class="inline-flex items-center gap-1 p-1 rounded-full border border-outline-variant/50">
                            <button type="button" x-on:click="restar(id)" aria-label="Quitar una unidad"
                                    class="w-10 h-10 rounded-full bg-surface-container-low text-headline-sm">−</button>
                            <span class="w-8 text-center text-label-lg tabular-nums" x-text="items[id].cantidad"></span>
                            <button type="button" x-on:click="sumar(id)" aria-label="Añadir una unidad"
                                    class="w-10 h-10 rounded-full bg-primary-container text-on-primary text-headline-sm">+</button>
                        </div>
                    </div>

                    {{-- Campos ocultos que se envían al servidor --}}
                    <input type="hidden" :name="`lineas[${indice}][plato_id]`" :value="id">
                    <input type="hidden" :name="`lineas[${indice}][cantidad]`" :value="items[id].cantidad">
                    <input type="hidden" :name="`lineas[${indice}][notas]`" :value="items[id].notas">
                </x-tarjeta>
            </template>
        </div>

        <a href="{{ route('cliente.carta', $rutaMesa) }}"
           class="mt-space-md flex items-center justify-center gap-2 min-h-[56px] rounded-lg border-2 border-dashed border-primary-container text-label-lg text-primary">
            <x-icono nombre="add_circle" />
            Añadir más platos de la carta
        </a>

        {{-- Resumen --}}
        <x-tarjeta class="mt-space-md">
            <div class="flex items-center justify-between text-body-md text-secondary">
                <span>Productos (<span x-text="articulos()"></span> uds.)</span>
                <span x-text="euros(totalCentimos())"></span>
            </div>
            <div class="mt-2 flex items-center justify-between">
                <span class="text-headline-sm">Total</span>
                <span class="text-headline-md text-primary" x-text="euros(totalCentimos())"></span>
            </div>
            <p class="mt-1 text-label-md font-normal text-secondary">IVA incluido. Se paga ahora con tarjeta.</p>
        </x-tarjeta>

        {{-- Rama 15: el pedido se paga al hacerlo. En efectivo, con el camarero. --}}
        <p class="mt-space-md flex items-start gap-2 text-body-md text-secondary">
            <x-icono nombre="verified_user" class="text-tertiary" />
            <span>Pagas con tarjeta en la página segura de Stripe (modo de prueba) y, en cuanto se confirma el pago, tu pedido llega a cocina.</span>
        </p>
        <p class="mt-space-sm flex items-start gap-2 text-body-md text-secondary">
            <x-icono nombre="payments" class="text-primary" />
            <span>¿Prefieres pagar en efectivo? <strong class="text-on-surface">Llama al camarero</strong> y él te toma nota.</span>
        </p>

        <div class="sticky bottom-[72px] mt-space-md">
            <button type="submit" x-bind:disabled="enviando"
                    class="pulsable w-full flex items-center justify-between gap-space-md min-h-[56px] px-space-md rounded-lg bg-primary-container text-on-primary shadow-nivel-2 disabled:opacity-60">
                <span class="flex items-center gap-2 text-label-lg font-bold">
                    <x-icono nombre="credit_card" />
                    <span x-text="enviando ? 'Abriendo el pago…' : 'Pagar y enviar a cocina'">Pagar y enviar a cocina</span>
                </span>
                <span class="px-3 py-1 rounded-full bg-white/25 text-price-display" x-text="euros(totalCentimos())"></span>
            </button>
        </div>
    </form>
</x-layouts.cliente>
