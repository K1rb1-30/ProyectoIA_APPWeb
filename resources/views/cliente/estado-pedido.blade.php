{{--
    Estado de los pedidos del cliente (rama 9). Maqueta: cliente/estado-pedido.
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
        <x-tarjeta class="text-center">
            <x-icono nombre="receipt_long" class="text-5xl text-secondary" />
            <p class="mt-2 text-body-lg">Todavía no has hecho ningún pedido en esta mesa.</p>
            <a href="{{ route('cliente.carta', $rutaMesa) }}" class="mt-2 inline-block text-label-lg text-primary hover:underline">Ver la carta</a>
        </x-tarjeta>
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
                <x-tarjeta class="text-center">
                    <span class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-tertiary-fixed/50 text-tertiary">
                        <x-icono nombre="check_circle" class="text-4xl" />
                    </span>
                    <h1 class="mt-space-sm text-headline-lg">¡Pedido pagado y enviado a cocina!</h1>
                    <p class="mt-1 text-body-md text-secondary">Mesa {{ $mesa->numero }} · Pedido #{{ $pedidos->first()->id }} a las {{ $pedidos->first()->created_at->format('H:i') }} h</p>
                    {{-- Rama 15: ya está pagado, no se cobra otra vez en la cuenta --}}
                    <p class="mt-1 text-body-md text-tertiary">Pagado con tarjeta: no se te volverá a cobrar en la cuenta.</p>
                </x-tarjeta>
            @else
                <h1 class="text-headline-lg">Tus pedidos</h1>
            @endif

            @foreach ($pedidos as $pedido)
                <x-tarjeta>
                    <div class="flex items-center justify-between gap-space-md mb-space-md">
                        <div>
                            <p class="text-label-sm uppercase text-secondary">Seguimiento en directo</p>
                            <h2 class="text-headline-sm">Pedido #{{ $pedido->id }} · {{ $pedido->created_at->format('H:i') }} h</h2>
                        </div>
                    </div>

                    {{-- Pasos del pedido. Un paso está "hecho" si el pedido ya pasó
                         por él, y "actual" si es el estado de ahora mismo. --}}
                    <ol class="space-y-space-sm">
                        @foreach ($pasos as $indice => $paso)
                            <li class="flex gap-3 p-2 rounded"
                                x-bind:class="posicion({{ $pedido->id }}) === {{ $indice }} ? 'bg-blue-50 ring-1 ring-blue-200' : ''">
                                <span class="mt-0.5 flex items-center justify-center w-7 h-7 shrink-0 rounded-full text-label-md"
                                      x-bind:class="posicion({{ $pedido->id }}) > {{ $indice }} ? 'bg-tertiary-container text-on-primary'
                                          : (posicion({{ $pedido->id }}) === {{ $indice }} ? 'bg-estado-preparando text-on-primary' : 'bg-surface-container text-secondary')">
                                    <span x-show="posicion({{ $pedido->id }}) > {{ $indice }}" class="material-symbols-outlined text-lg" aria-hidden="true">check</span>
                                    <span x-show="posicion({{ $pedido->id }}) <= {{ $indice }}">{{ $indice + 1 }}</span>
                                </span>
                                <span x-bind:class="posicion({{ $pedido->id }}) < {{ $indice }} ? 'opacity-50' : ''">
                                    <span class="block text-label-lg">
                                        {{ $paso['titulo'] }}
                                        <span x-show="posicion({{ $pedido->id }}) === {{ $indice }}" class="ml-1 px-2 py-0.5 rounded-full bg-estado-preparando text-on-primary text-label-sm">Ahora</span>
                                    </span>
                                    <span class="block text-body-md text-secondary">{{ $paso['texto'] }}</span>
                                </span>
                            </li>
                        @endforeach
                    </ol>

                    {{-- Platos del pedido --}}
                    <ul class="mt-space-md pt-space-md border-t border-outline-variant/30 space-y-space-sm">
                        @foreach ($pedido->lineas as $linea)
                            <li class="flex items-start justify-between gap-space-md">
                                <span class="flex gap-2">
                                    <span class="px-2 py-0.5 h-fit rounded bg-primary-fixed/50 text-label-md tabular-nums">{{ $linea->cantidad }}×</span>
                                    <span>
                                        <span class="block text-label-lg">{{ $linea->plato?->nombre ?? 'Plato' }}</span>
                                        @if ($linea->notas)
                                            <span class="inline-flex items-center gap-1 mt-0.5 px-2 py-0.5 rounded bg-secondary-container text-label-sm">
                                                <x-icono nombre="edit_note" class="text-sm" />
                                                Nota: {{ $linea->notas }}
                                            </span>
                                        @endif
                                    </span>
                                </span>
                                <x-precio :centimos="$linea->subtotalCentimos()" class="text-label-lg" />
                            </li>
                        @endforeach
                    </ul>

                    <div class="mt-space-md pt-space-md border-t border-outline-variant/30 flex items-center justify-between">
                        <span class="text-body-md text-secondary">Total de este pedido</span>
                        <x-precio :centimos="$pedido->totalCentimos()" class="text-headline-sm text-primary" />
                    </div>
                </x-tarjeta>
            @endforeach

            <a href="{{ route('cliente.carta', $rutaMesa) }}"
               class="pulsable flex items-center justify-center gap-2 min-h-[56px] rounded-lg bg-primary-container text-on-primary text-label-lg font-bold">
                <x-icono nombre="add_circle" />
                Pedir algo más
            </a>
        </div>
    @endif
</x-layouts.cliente>
