{{--
    Panel principal del admin (rama 17, rediseño en la rama 22).
    Lo prepara Admin\PanelController; esta vista no hace consultas.

    Variables (todo el dinero en CÉNTIMOS; se pasa a euros solo al mostrarlo):
      $admin, $restaurante   el admin logueado y su restaurante
      $resumen               de EstadisticasRestaurante::resumenDeHoy():
                             ventas_hoy, ventas_ayer, variacion, pedidos_hoy, cobros_hoy, ticket_medio
      $mesas                 hasta 24 mesas [numero, estado]; $totalMesas, $mesasOcupadas, $mesasLibres, $ocupacion (%)
      $platos                5 platos (agotados primero) con sus alérgenos; $totalPlatos, $platosAgotados
      $pedidosEnCurso        ['pendiente' => n, 'preparando' => n, 'listo' => n]

    Rediseño «Comanda impresa»: un solo dato principal (las ventas de hoy, en
    grande) y el resto de cifras al lado, más pequeñas. Debajo, una línea con
    los pedidos en cocina y, en dos columnas, la carta y las mesas como listas.
--}}
@php
    $totalEnCurso = array_sum($pedidosEnCurso);
@endphp

<x-layouts.admin :titulo="$restaurante->nombre">
    <x-slot name="subtitulo">Hola, {{ $admin->name }}. Así va hoy.</x-slot>
    <x-slot name="acciones">
        <a href="{{ route('admin.carta.crear') }}"
           class="pulsable inline-flex items-center justify-center min-h-[48px] px-6 rounded bg-primary-container text-on-primary text-label-lg font-bold hover:bg-primary">
            Añadir plato
        </a>
    </x-slot>

    {{-- 1. Resumen de hoy: ventas en grande y, al lado, el resto de cifras --}}
    <section aria-label="Resumen de hoy" class="flex flex-wrap bg-surface-container-lowest border border-outline-variant">
        <div class="flex-[2_1_320px] p-space-lg sm:p-8 border-b sm:border-b-0 sm:border-r border-outline-variant">
            <p class="text-label-lg text-secondary">Ventas cobradas hoy</p>
            <x-precio :centimos="$resumen['ventas_hoy']" class="block mt-2.5 font-mono text-[clamp(2.5rem,5vw,3.5rem)] leading-none font-semibold tracking-tight" />
            <p class="mt-3.5 text-body-md text-on-surface-variant">
                @if ($resumen['ventas_hoy'] === 0)
                    Todavía no hay ventas hoy.
                @elseif ($resumen['variacion'] !== null)
                    <span @class([
                        'font-bold',
                        'text-tertiary' => $resumen['variacion'] >= 0,
                        'text-error' => $resumen['variacion'] < 0,
                    ])>{{ $resumen['variacion'] >= 0 ? '+' : '' }}{{ $resumen['variacion'] }} %</span>
                    respecto a ayer (<x-precio :centimos="$resumen['ventas_ayer']" class="font-mono" />)
                @else
                    Ayer no hubo ventas.
                @endif
                · <a href="{{ route('admin.estadisticas') }}" class="font-semibold underline underline-offset-2 hover:text-primary">Estadísticas</a>
            </p>
        </div>

        <dl class="flex-[1_1_240px] flex flex-col divide-y divide-outline-variant">
            <div class="flex-1 flex items-baseline justify-between gap-3 px-space-lg py-4">
                <dt class="text-body-md text-secondary">Pedidos de hoy</dt>
                <dd class="font-mono text-headline-md">{{ $resumen['pedidos_hoy'] }}</dd>
            </div>
            <div class="flex-1 flex items-baseline justify-between gap-3 px-space-lg py-4">
                <dt class="text-body-md text-secondary">
                    @if ($resumen['pedidos_hoy'] > 0)
                        {{-- Mismo texto que antes: el test comprueba «Ticket medio: 12,50 €» --}}
                        <span class="sr-only">Ticket medio: <x-precio :centimos="$resumen['ticket_medio']" /></span>
                        <span aria-hidden="true">Ticket medio</span>
                    @else
                        Todavía no hay pedidos hoy.
                    @endif
                </dt>
                <dd class="font-mono text-headline-md" aria-hidden="true">
                    @if ($resumen['pedidos_hoy'] > 0)
                        <x-precio :centimos="$resumen['ticket_medio']" />
                    @else
                        —
                    @endif
                </dd>
            </div>
            <div class="flex-1 flex items-baseline justify-between gap-3 px-space-lg py-4">
                <dt class="text-body-md text-secondary">
                    Mesas ocupadas
                    @if ($totalMesas > 0)
                        <span class="block text-label-md font-normal">{{ $ocupacion }} % de ocupación</span>
                    @endif
                </dt>
                <dd class="font-mono text-headline-md whitespace-nowrap">{{ $mesasOcupadas }} de {{ $totalMesas }}</dd>
            </div>
        </dl>
    </section>

    {{-- 2. Pedidos en curso (no servidos), por estado, en una línea --}}
    <section aria-label="Pedidos en curso" class="flex flex-wrap items-center gap-x-8 gap-y-2 py-4 border-b border-outline-variant text-body-md">
        <span class="text-label-lg">En cocina ahora: <span class="font-mono">{{ $totalEnCurso }}</span> sin servir</span>
        @if ($totalEnCurso > 0)
            @foreach ($pedidosEnCurso as $estado => $numero)
                @if ($numero > 0)
                    <span class="inline-flex items-center gap-2">
                        <x-chip-estado :estado="$estado" />
                        <span class="font-mono text-label-lg">{{ $numero }}</span>
                    </span>
                @endif
            @endforeach
        @else
            <span class="text-secondary">No hay pedidos en cocina ahora mismo.</span>
        @endif
    </section>

    <div class="mt-12 flex flex-wrap items-start gap-x-14 gap-y-12">
        {{-- 3. Carta rápida --}}
        <section class="flex-[3_1_440px] min-w-0">
            <div class="flex flex-wrap items-baseline justify-between gap-space-md pb-3 border-b-2 border-on-surface">
                <h2 class="text-headline-md">
                    Carta
                    <span class="ml-1 font-mono text-body-md font-normal text-secondary">
                        {{ $totalPlatos }} {{ $totalPlatos === 1 ? 'plato' : 'platos' }} ·
                        {{ $platosAgotados }} {{ $platosAgotados === 1 ? 'agotado' : 'agotados' }}
                    </span>
                </h2>
                <a href="{{ route('admin.carta') }}" class="inline-flex items-center min-h-[44px] text-label-lg underline underline-offset-4 hover:text-primary">Gestionar la carta</a>
            </div>

            @if ($platos->isEmpty())
                <div class="py-space-xl text-center">
                    <p class="text-body-lg text-secondary">Todavía no hay platos en la carta.</p>
                    <a href="{{ route('admin.carta.crear') }}" class="mt-space-md inline-flex items-center min-h-[48px] px-6 rounded border-[1.5px] border-on-surface text-label-lg font-bold hover:bg-surface-container-lowest">Añadir el primer plato</a>
                </div>
            @else
                <ul>
                    @foreach ($platos as $plato)
                        <li class="flex items-center gap-space-md py-3.5 border-b border-outline-variant">
                            <div class="flex-1 min-w-0">
                                <p @class(['text-label-lg', 'text-secondary line-through' => ! $plato->disponible])>{{ $plato->nombre }}</p>
                                @unless ($plato->disponible)
                                    <p class="text-label-md text-error">Agotado</p>
                                @else
                                    <p class="text-body-md text-secondary">
                                        @if ($plato->alergenos->isEmpty())
                                            Sin alérgenos declarados
                                        @else
                                            Contiene {{ $plato->alergenos->pluck('nombre')->map(fn ($nombre) => mb_strtolower($nombre))->implode(', ') }}
                                        @endif
                                    </p>
                                @endunless
                            </div>

                            <x-precio :centimos="$plato->precio_centimos" @class(['font-mono text-label-lg', 'text-secondary' => ! $plato->disponible]) />

                            {{-- Interruptor: la misma ruta que en la carta. Vuelve aquí con back(). --}}
                            <form method="POST" action="{{ route('admin.carta.disponibilidad', $plato) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" role="switch" aria-checked="{{ $plato->disponible ? 'true' : 'false' }}"
                                        aria-label="Disponible: {{ $plato->nombre }}"
                                        class="inline-flex items-center min-h-[48px] min-w-[48px] justify-center">
                                    <span @class([
                                        'relative inline-block w-11 h-6 rounded-full transition-colors',
                                        'bg-on-surface' => $plato->disponible,
                                        'bg-secondary-fixed-dim' => ! $plato->disponible,
                                    ])>
                                        <span @class([
                                            'absolute top-0.5 w-5 h-5 rounded-full bg-white transition-all',
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
                    <p class="pt-3 text-body-md text-secondary">Mostrando {{ $platos->count() }} de {{ $totalPlatos }} platos (los agotados primero).</p>
                @endif
            @endif
        </section>

        {{-- 4. Mesas --}}
        <section class="flex-[2_1_300px] min-w-0">
            <div class="flex flex-wrap items-baseline justify-between gap-space-md pb-3 border-b-2 border-on-surface">
                <h2 class="text-headline-md">Mesas</h2>
                <a href="{{ route('admin.mesas') }}" class="inline-flex items-center min-h-[44px] text-label-lg underline underline-offset-4 hover:text-primary">Mesas y QR</a>
            </div>

            @if ($totalMesas === 0)
                <div class="py-space-xl text-center">
                    <p class="text-body-lg text-secondary">Todavía no hay mesas.</p>
                    <a href="{{ route('admin.mesas.crear') }}" class="mt-space-md inline-flex items-center min-h-[48px] px-6 rounded border-[1.5px] border-on-surface text-label-lg font-bold hover:bg-surface-container-lowest">Crear la primera mesa</a>
                </div>
            @else
                {{-- Cuadrícula: ocupada = cuadrado de tinta, libre = cuadrado con borde --}}
                <ul class="mt-space-md grid grid-cols-4 gap-2">
                    @foreach ($mesas as $mesa)
                        <li @class([
                            'aspect-square flex items-center justify-center font-mono text-headline-sm',
                            'bg-on-surface text-surface-container-lowest' => $mesa->estado === 'ocupada',
                            'border-[1.5px] border-secondary-fixed-dim' => $mesa->estado !== 'ocupada',
                        ])>
                            <span class="sr-only">Mesa {{ $mesa->numero }}, {{ $mesa->estado }}</span>
                            <span aria-hidden="true">{{ $mesa->numero }}</span>
                        </li>
                    @endforeach
                </ul>
                <p class="mt-3 flex flex-wrap gap-x-5 gap-y-1 text-body-md text-on-surface-variant">
                    <span class="inline-flex items-center gap-2"><span class="w-3 h-3 bg-on-surface"></span> {{ $mesasOcupadas }} {{ $mesasOcupadas === 1 ? 'ocupada' : 'ocupadas' }}</span>
                    <span class="inline-flex items-center gap-2"><span class="w-3 h-3 border-[1.5px] border-secondary-fixed-dim"></span> {{ $mesasLibres }} {{ $mesasLibres === 1 ? 'libre' : 'libres' }}</span>
                </p>

                @if ($totalMesas > $mesas->count())
                    <p class="pt-2 text-body-md text-secondary">Y {{ $totalMesas - $mesas->count() }} más en Mesas y QR.</p>
                @endif

                {{-- target="_blank": la página para imprimir se abre en otra pestaña, como en Mesas y QR --}}
                <a href="{{ route('admin.mesas.imprimir') }}" target="_blank"
                   class="mt-space-lg w-full pulsable inline-flex items-center justify-center min-h-[48px] px-5 rounded border-[1.5px] border-on-surface text-label-lg font-bold hover:bg-surface-container-lowest">
                    Imprimir los QR
                </a>
            @endif
        </section>
    </div>
</x-layouts.admin>
