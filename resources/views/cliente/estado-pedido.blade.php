{{--
    Estado de los pedidos del cliente (rama 9, rediseño en la rama 23).
    Rediseño «Comanda impresa»: cada pedido es un ticket blanco; el paso actual
    se marca con una raya naranja a la izquierda en vez de un fondo azul.
    Variables: $restaurante, $mesa y $pedidos (los que ha hecho ESTE navegador
    en esta mesa, guardados en la sesión; del más nuevo al más antiguo).

    Seguimiento en directo: cada 5 segundos, Alpine pide a /estado/datos los
    estados de los pedidos y actualiza los pasos sin recargar la página.
    Los pasos corresponden a los estados del pedido:
      pendiente → Recibido · preparando → En los fogones · listo → Listo · servido → En tu mesa
--}}
@php
    $rutaMesa = ['slug' => $restaurante->slug, 'numero' => $mesa->numero];

    $pasos = [
        ['estado' => 'pendiente', 'titulo' => 'Recibido en cocina', 'texto' => 'Tu pedido ha llegado a la pantalla de cocina.'],
        ['estado' => 'preparando', 'titulo' => 'En los fogones', 'texto' => 'La cocina está preparando tus platos.'],
        ['estado' => 'listo', 'titulo' => 'Listo para servir', 'texto' => 'El camarero lo recogerá enseguida.'],
        ['estado' => 'servido', 'titulo' => 'En tu mesa', 'texto' => '¡Buen provecho!'],
    ];

    // Estado de cada pedido { id: estado }; lo irá cambiando el polling.
    $estados = $pedidos->mapWithKeys(fn ($pedido) => [$pedido->id => $pedido->estado]);
@endphp

<x-layouts.cliente :restaurante="$restaurante->nombre" :mesa="$mesa->numero" seccion="pedido"
                   :enlace-carta="route('cliente.carta', $rutaMesa)"
                   :enlace-pedido="route('cliente.pedido', $rutaMesa)"
                   :titulo="'Estado del pedido · '.$restaurante->nombre">

    {{-- Si venimos de enviar el pedido, vaciamos el carrito de este navegador. --}}
    @if (session('vaciarCarrito'))
        <script>
            try { localStorage.removeItem(@js('carrito-'.$restaurante->slug.'-'.$mesa->numero)); } catch (error) {}
        </script>
    @endif

    @if ($pedidos->isEmpty())
        <div class="py-space-xl">
            <p class="text-headline-sm">Todavía no has hecho ningún pedido en esta mesa.</p>
            <a href="{{ route('cliente.carta', $rutaMesa) }}" class="mt-space-md inline-flex items-center min-h-[48px] px-5 rounded border-[1.5px] border-on-surface text-label-lg font-bold">Ver la carta</a>
        </div>
    @else
        <div x-data="{
                estados: @js($estados),
                pasos: @js(array_column($pasos, 'estado')),
                // Posición del estado en la lista de pasos (0 = pendiente ... 3 = servido).
                posicion(id) { return this.pasos.indexOf(this.estados[id]) },
                init() {
                    // Polling: cada 5 s preguntamos al servidor cómo van los pedidos.
                    setInterval(async () => {
                        try {
                            const respuesta = await fetch(@js(route('cliente.estado.datos', $rutaMesa)), { headers: { Accept: 'application/json' } });
                            if (! respuesta.ok) return;
                            const datos = await respuesta.json();
                            datos.pedidos.forEach(pedido => this.estados[pedido.id] = pedido.estado);
                        } catch (error) {
                            // Sin conexión: lo volvemos a intentar en 5 s.
                        }
                    }, 5000);
                },
             }" class="space-y-space-lg">

            @if (session('vaciarCarrito'))
                {{-- Confirmación: título grande alineado a la izquierda, sin icono ni tarjeta --}}
                <div>
                    <h1 class="text-headline-lg">¡Pedido pagado y enviado a cocina!</h1>
                    <p class="mt-2 font-mono text-label-md font-normal text-secondary">Mesa {{ $mesa->numero }} · Pedido #{{ $pedidos->first()->id }} · {{ $pedidos->first()->created_at->format('H:i') }} h</p>
                    {{-- Rama 15: ya está pagado, no se cobra otra vez en la cuenta --}}
                    <p class="mt-1 text-body-md font-semibold text-tertiary">Pagado con tarjeta: no se te volverá a cobrar en la cuenta.</p>
                </div>
            @else
                <h1 class="text-headline-lg">Tus pedidos</h1>
            @endif

            @foreach ($pedidos as $pedido)
                {{-- Cada pedido, con aspecto de ticket: fondo blanco y líneas discontinuas --}}
                <section class="bg-surface-container-lowest p-space-md shadow-nivel-1">
                    <div class="flex items-baseline justify-between gap-space-md mb-space-md">
                        <h2 class="text-headline-sm">Pedido <span class="font-mono">#{{ $pedido->id }}</span></h2>
                        <p class="font-mono text-label-md font-normal text-secondary">{{ $pedido->created_at->format('H:i') }} h · en directo</p>
                    </div>

                    {{-- Pasos del pedido. Un paso está "hecho" si el pedido ya pasó
                         por él, y "actual" si es el estado de ahora mismo. --}}
                    <ol class="space-y-1">
                        @foreach ($pasos as $indice => $paso)
                            {{-- El paso actual lleva una raya naranja a la izquierda; los hechos, una marca verde --}}
                            <li class="flex gap-3 py-2 pl-3 border-l-[3px]"
                                x-bind:class="posicion({{ $pedido->id }}) === {{ $indice }} ? 'border-primary-container' : 'border-transparent'">
                                <span class="mt-0.5 flex items-center justify-center w-7 h-7 shrink-0 rounded-full font-mono text-label-md"
                                      x-bind:class="posicion({{ $pedido->id }}) > {{ $indice }} ? 'bg-tertiary-container text-on-primary'
                                          : (posicion({{ $pedido->id }}) === {{ $indice }} ? 'bg-on-surface text-surface-container-lowest' : 'border-[1.5px] border-secondary-fixed-dim text-secondary')">
                                    <span x-show="posicion({{ $pedido->id }}) > {{ $indice }}" class="material-symbols-outlined text-lg" aria-hidden="true">check</span>
                                    <span x-show="posicion({{ $pedido->id }}) <= {{ $indice }}">{{ $indice + 1 }}</span>
                                </span>
                                <span x-bind:class="posicion({{ $pedido->id }}) < {{ $indice }} ? 'opacity-50' : ''">
                                    <span class="block text-label-lg">
                                        {{ $paso['titulo'] }}
                                        <span x-show="posicion({{ $pedido->id }}) === {{ $indice }}" class="ml-1 text-label-md text-primary-container">· Ahora</span>
                                    </span>
                                    <span class="block text-body-md text-secondary">{{ $paso['texto'] }}</span>
                                </span>
                            </li>
                        @endforeach
                    </ol>

                    {{-- Platos del pedido --}}
                    <ul class="mt-space-md pt-space-md border-t-[1.5px] border-dashed border-secondary-fixed-dim space-y-space-sm">
                        @foreach ($pedido->lineas as $linea)
                            <li class="flex items-start justify-between gap-space-md">
                                <span class="flex gap-2">
                                    <span class="font-mono text-label-lg tabular-nums">{{ $linea->cantidad }}×</span>
                                    <span>
                                        <span class="block text-label-lg">{{ $linea->plato?->nombre ?? 'Plato' }}</span>
                                        @if ($linea->notas)
                                            <span class="inline-block mt-1 px-2 py-0.5 bg-primary-fixed text-on-primary-fixed-variant text-label-md">Nota: {{ $linea->notas }}</span>
                                        @endif
                                    </span>
                                </span>
                                <x-precio :centimos="$linea->subtotalCentimos()" class="font-mono text-label-lg" />
                            </li>
                        @endforeach
                    </ul>

                    <div class="mt-space-md pt-space-md border-t-[1.5px] border-dashed border-secondary-fixed-dim flex items-baseline justify-between">
                        <span class="text-body-md text-secondary">Total de este pedido</span>
                        <x-precio :centimos="$pedido->totalCentimos()" class="font-mono text-headline-sm" />
                    </div>
                </section>
            @endforeach

            <a href="{{ route('cliente.carta', $rutaMesa) }}"
               class="pulsable flex items-center justify-center min-h-[56px] rounded bg-primary-container text-on-primary text-label-lg font-bold hover:bg-primary">
                Pedir algo más
            </a>
        </div>
    @endif
</x-layouts.cliente>
