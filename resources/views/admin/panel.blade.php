{{--
    Panel principal del admin (rama 17). Maqueta: panelAdmin/panel.
    Lo prepara Admin\PanelController; esta vista no hace consultas.

    Variables (todo el dinero en CÉNTIMOS; se pasa a euros solo al mostrarlo):
      $admin, $restaurante   el admin logueado y su restaurante
      $resumen               de EstadisticasRestaurante::resumenDeHoy():
                             ventas_hoy, ventas_ayer, variacion, pedidos_hoy, cobros_hoy, ticket_medio
      $mesas                 hasta 24 mesas [numero, estado]; $totalMesas, $mesasOcupadas, $mesasLibres, $ocupacion (%)
      $platos                5 platos (agotados primero) con sus alérgenos; $totalPlatos, $platosAgotados
      $pedidosEnCurso        ['pendiente' => n, 'preparando' => n, 'listo' => n]

    Diferencias con la maqueta (solo se enseña lo que la app hace de verdad):
    sin «PRO», buscador, notificaciones, ajustes, Configuración, Soporte,
    contadores del menú, camareros ni importes en las mesas, personal en turno,
    bloque del reglamento de alérgenos ni tiempo medio de cocina (no se guarda
    cuándo pasa un pedido a «listo»). La cuarta tarjeta es «Pedidos en curso».
--}}
@php
    $totalEnCurso = array_sum($pedidosEnCurso);
@endphp

<x-layouts.admin :titulo="$restaurante->nombre">
    <x-slot name="acciones">
        {{-- Enlaces con aspecto de botón (las mismas clases que x-boton-primario, que es un <button>) --}}
        <a href="{{ route('admin.carta.crear') }}" class="pulsable inline-flex items-center justify-center gap-2 min-h-[48px] px-5 py-3 rounded bg-primary-container text-on-primary text-label-lg font-bold shadow-sm transition hover:bg-primary">
            <x-icono nombre="add_circle" class="text-xl" />
            Añadir plato
        </a>
    </x-slot>

    <div class="space-y-space-lg">
        <div>
            <h2 class="text-headline-lg font-extrabold">Hola, {{ $admin->name }}</h2>
            <p class="text-body-md text-secondary">Así va hoy {{ $restaurante->nombre }}.</p>
        </div>

        {{-- 1. Tarjetas de resumen --}}
        <div class="grid gap-space-md sm:grid-cols-2 xl:grid-cols-4">
            {{-- Ventas de hoy (pagos cobrados) --}}
            <x-tarjeta class="flex flex-col">
                <p class="flex items-center justify-between text-label-lg text-secondary">Ventas de hoy <x-icono nombre="euro" class="text-primary" /></p>
                <x-precio :centimos="$resumen['ventas_hoy']" class="block mt-2 text-headline-lg font-extrabold" />
                @if ($resumen['ventas_hoy'] === 0)
                    <p class="mt-1 text-label-md text-secondary">Todavía no hay ventas hoy.</p>
                @elseif ($resumen['variacion'] !== null)
                    <p class="mt-1 text-label-md">
                        <span @class([
                            'px-2 py-0.5 rounded',
                            'bg-tertiary-fixed/40 text-tertiary' => $resumen['variacion'] >= 0,
                            'bg-error-container text-on-error-container' => $resumen['variacion'] < 0,
                        ])>{{ $resumen['variacion'] >= 0 ? '+' : '' }}{{ $resumen['variacion'] }} %</span>
                        <span class="text-secondary">vs. ayer (<x-precio :centimos="$resumen['ventas_ayer']" />)</span>
                    </p>
                @else
                    <p class="mt-1 text-label-md text-secondary">Ayer no hubo ventas.</p>
                @endif
                <a href="{{ route('admin.estadisticas') }}" class="mt-auto pt-space-md text-label-md text-primary hover:underline">Ver estadísticas →</a>
            </x-tarjeta>

            {{-- Pedidos de hoy y ticket medio --}}
            <x-tarjeta>
                <p class="flex items-center justify-between text-label-lg text-secondary">Pedidos de hoy <x-icono nombre="receipt_long" class="text-primary" /></p>
                <p class="mt-2"><span class="text-headline-lg font-extrabold">{{ $resumen['pedidos_hoy'] }}</span> <span class="text-body-md text-secondary">{{ $resumen['pedidos_hoy'] === 1 ? 'pedido' : 'pedidos' }}</span></p>
                <p class="mt-1 text-label-md text-secondary">
                    @if ($resumen['pedidos_hoy'] > 0)
                        Ticket medio: <x-precio :centimos="$resumen['ticket_medio']" />
                    @else
                        Todavía no hay pedidos hoy.
                    @endif
                </p>
            </x-tarjeta>

            {{-- Mesas ocupadas --}}
            <x-tarjeta>
                <p class="flex items-center justify-between text-label-lg text-secondary">Mesas ocupadas <x-icono nombre="table_restaurant" class="text-primary" /></p>
                @if ($totalMesas > 0)
                    <p class="mt-2"><span class="text-headline-lg font-extrabold">{{ $mesasOcupadas }} de {{ $totalMesas }}</span></p>
                    <p class="mt-1 text-label-md text-secondary">{{ $ocupacion }} % de ocupación · {{ $mesasLibres }} {{ $mesasLibres === 1 ? 'libre' : 'libres' }}</p>
                @else
                    <p class="mt-2 text-headline-lg font-extrabold">0</p>
                    <p class="mt-1 text-label-md text-secondary">Todavía no hay mesas.</p>
                @endif
            </x-tarjeta>

            {{-- Pedidos en curso (no servidos), por estado --}}
            <x-tarjeta>
                <p class="flex items-center justify-between text-label-lg text-secondary">Pedidos en curso <x-icono nombre="skillet" class="text-primary" /></p>
                <p class="mt-2"><span class="text-headline-lg font-extrabold">{{ $totalEnCurso }}</span> <span class="text-body-md text-secondary">sin servir</span></p>
                @if ($totalEnCurso > 0)
                    <div class="mt-2 flex flex-wrap gap-1">
                        @foreach ($pedidosEnCurso as $estado => $numero)
                            @if ($numero > 0)
                                <span class="inline-flex items-center gap-1">
                                    <x-chip-estado :estado="$estado" />
                                    <span class="text-label-md tabular-nums">{{ $numero }}</span>
                                </span>
                            @endif
                        @endforeach
                    </div>
                @else
                    <p class="mt-1 text-label-md text-secondary">No hay pedidos en cocina ahora mismo.</p>
                @endif
            </x-tarjeta>
        </div>

        <div class="grid gap-space-lg lg:grid-cols-5 items-start">
            {{-- 2. Carta rápida --}}
            <x-tarjeta class="lg:col-span-3">
                <div class="flex flex-wrap items-start justify-between gap-space-md">
                    <div>
                        <h2 class="text-headline-md">Carta</h2>
                        <p class="text-body-md text-secondary">
                            {{ $totalPlatos }} {{ $totalPlatos === 1 ? 'plato' : 'platos' }} ·
                            {{ $platosAgotados }} {{ $platosAgotados === 1 ? 'agotado' : 'agotados' }}
                        </p>
                    </div>
                    <a href="{{ route('admin.carta') }}" class="min-h-[48px] inline-flex items-center text-label-lg text-primary hover:underline">Gestionar la carta completa →</a>
                </div>

                @if ($platos->isEmpty())
                    <div class="flex flex-col items-center gap-space-md py-space-lg text-center text-secondary">
                        <x-icono nombre="restaurant_menu" class="text-5xl" />
                        <p class="text-body-md">Todavía no hay platos en la carta.</p>
                        <a href="{{ route('admin.carta.crear') }}" class="pulsable inline-flex items-center justify-center gap-2 min-h-[48px] px-5 py-3 rounded bg-primary-container text-on-primary text-label-lg font-bold shadow-sm transition hover:bg-primary">Añadir el primer plato</a>
                    </div>
                @else
                    <ul class="mt-space-md divide-y divide-outline-variant/30">
                        @foreach ($platos as $plato)
                            <li @class(['flex items-center gap-space-md py-space-md', 'opacity-60' => ! $plato->disponible])>
                                {{-- Miniatura o, si no hay foto, un icono (como en la carta) --}}
                                @if ($plato->urlFoto())
                                    <img src="{{ $plato->urlFoto() }}" alt="" class="w-14 h-14 shrink-0 rounded object-cover">
                                @else
                                    <div class="w-14 h-14 shrink-0 rounded bg-surface-container flex items-center justify-center text-secondary">
                                        <x-icono nombre="restaurant" class="text-2xl" />
                                    </div>
                                @endif

                                <div class="flex-1 min-w-0">
                                    <p class="text-label-lg">
                                        {{ $plato->nombre }}
                                        @unless ($plato->disponible)
                                            <span class="ml-1 px-2 py-0.5 rounded-full bg-error-container text-on-error-container text-label-sm">Agotado</span>
                                        @endunless
                                    </p>
                                    <div class="mt-1 flex flex-wrap gap-1">
                                        @foreach ($plato->alergenos as $alergeno)
                                            <x-badge-alergeno title="{{ $alergeno->nombre }}">
                                                <x-icono :nombre="$alergeno->icono" class="text-sm" />
                                                {{ $alergeno->nombre }}
                                            </x-badge-alergeno>
                                        @endforeach
                                    </div>
                                </div>

                                <x-precio :centimos="$plato->precio_centimos" class="text-price-display" />

                                {{-- Interruptor: la misma ruta que en la carta. Vuelve aquí con back(). --}}
                                <form method="POST" action="{{ route('admin.carta.disponibilidad', $plato) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" role="switch" aria-checked="{{ $plato->disponible ? 'true' : 'false' }}"
                                            aria-label="Disponible: {{ $plato->nombre }}"
                                            class="inline-flex items-center min-h-[48px] min-w-[48px] justify-center">
                                        <span @class([
                                            'relative inline-block w-11 h-6 rounded-full transition-colors',
                                            'bg-tertiary-container' => $plato->disponible,
                                            'bg-outline-variant' => ! $plato->disponible,
                                        ])>
                                            <span @class([
                                                'absolute top-0.5 w-5 h-5 rounded-full bg-white shadow transition-all',
                                                'left-[22px]' => $plato->disponible,
                                                'left-0.5' => ! $plato->disponible,
                                            ])></span>
                                        </span>
                                    </button>
                                </form>
                            </li>
                        @endforeach
                    </ul>

                    @if ($totalPlatos > $platos->count())
                        <p class="pt-space-md text-body-md text-secondary">Mostrando {{ $platos->count() }} de {{ $totalPlatos }} platos (los agotados primero).</p>
                    @endif
                @endif
            </x-tarjeta>

            {{-- 3. Mesas --}}
            <x-tarjeta class="lg:col-span-2">
                <div class="flex flex-wrap items-start justify-between gap-space-md">
                    <div>
                        <h2 class="text-headline-md">Mesas</h2>
                        <p class="text-body-md text-secondary">
                            {{ $mesasLibres }} {{ $mesasLibres === 1 ? 'libre' : 'libres' }} ·
                            {{ $mesasOcupadas }} {{ $mesasOcupadas === 1 ? 'ocupada' : 'ocupadas' }}
                        </p>
                    </div>
                    <a href="{{ route('admin.mesas') }}" class="min-h-[48px] inline-flex items-center text-label-lg text-primary hover:underline">Mesas y QR →</a>
                </div>

                @if ($totalMesas === 0)
                    <div class="flex flex-col items-center gap-space-md py-space-lg text-center text-secondary">
                        <x-icono nombre="qr_code_2" class="text-5xl" />
                        <p class="text-body-md">Todavía no hay mesas.</p>
                        <a href="{{ route('admin.mesas.crear') }}" class="pulsable inline-flex items-center justify-center gap-2 min-h-[48px] px-5 py-3 rounded bg-primary-container text-on-primary text-label-lg font-bold shadow-sm transition hover:bg-primary">Crear la primera mesa</a>
                    </div>
                @else
                    <ul class="mt-space-md grid grid-cols-2 sm:grid-cols-3 gap-space-sm">
                        @foreach ($mesas as $mesa)
                            {{-- Barra de color a la izquierda según el estado (DESIGN.md) --}}
                            <li @class([
                                'flex flex-col gap-1 p-space-sm rounded border border-outline-variant/30 border-l-4',
                                'border-l-estado-listo' => $mesa->estado === 'libre',
                                'border-l-primary-container' => $mesa->estado === 'ocupada',
                            ])>
                                <span class="text-label-lg">Mesa {{ $mesa->numero }}</span>
                                <x-chip-estado :estado="$mesa->estado" class="self-start" />
                            </li>
                        @endforeach
                    </ul>

                    @if ($totalMesas > $mesas->count())
                        <p class="pt-space-sm text-body-md text-secondary">Y {{ $totalMesas - $mesas->count() }} más en Mesas y QR.</p>
                    @endif

                    {{-- target="_blank": la página para imprimir se abre en otra pestaña, como en Mesas y QR --}}
                    <a href="{{ route('admin.mesas.imprimir') }}" target="_blank" class="mt-space-md w-full pulsable inline-flex items-center justify-center gap-2 min-h-[48px] px-5 py-3 rounded bg-surface-container-lowest border-[1.5px] border-outline-variant/60 text-on-surface text-label-lg shadow-sm transition hover:border-primary-container">
                        <x-icono nombre="print" class="text-xl text-primary" />
                        Imprimir los QR
                    </a>
                @endif
            </x-tarjeta>
        </div>
    </div>
</x-layouts.admin>
