{{--
    Estadísticas del restaurante (rama 13, rediseño en la rama 22).
    Variables (todo el dinero en CÉNTIMOS; se pasa a euros solo al mostrarlo):
      $resumen       ventas_hoy, ventas_ayer, variacion, pedidos_hoy, cobros_hoy, ticket_medio
      $ventasPorDia  30 filas [dia, total_centimos, pagos]
      $platos        top 10 [nombre, unidades, importe_centimos]
      $metodos       efectivo / stripe / tpv / tarjeta → [importe_centimos, numero]

    Los gráficos los pinta resources/js/estadisticas.js (Chart.js), que solo se
    carga en esta página. Cada <canvas> lleva sus datos en data-datos (JSON).

    Rediseño «Comanda impresa»: arriba, una franja con las ventas del día en
    grande y las otras tres cifras al lado (antes, cuatro tarjetas iguales).
    Debajo, cada bloque es una sección con una raya gruesa bajo el título, sin
    tarjetas. Los platos más pedidos son una lista con barras finas.
--}}
@php
    $hayVentas = collect($ventasPorDia)->sum('total_centimos') > 0;
    $totalMetodos = $metodos['tarjeta']['importe_centimos'] + $metodos['efectivo']['importe_centimos'];
    $porcentajeTarjeta = $totalMetodos > 0 ? (int) round($metodos['tarjeta']['importe_centimos'] * 100 / $totalMetodos) : 0;
    $maxUnidades = collect($platos)->max('unidades') ?: 1;
@endphp

<x-layouts.admin titulo="Estadísticas">
    <x-slot name="subtitulo">Ventas cobradas, platos más pedidos y métodos de pago de los últimos 30 días.</x-slot>

    {{-- Chart.js solo en esta página (entrada aparte de Vite) --}}
    @vite('resources/js/estadisticas.js')

    {{-- 1. Hoy: ventas en grande y, al lado, el resto de cifras --}}
    <section aria-label="Resumen de hoy" class="flex flex-wrap bg-surface-container-lowest border border-outline-variant">
        <div class="flex-[2_1_320px] p-space-lg sm:p-8 border-b sm:border-b-0 sm:border-r border-outline-variant">
            <p class="text-label-lg text-secondary">Ventas del día</p>
            <x-precio :centimos="$resumen['ventas_hoy']" class="block mt-2.5 font-mono text-[clamp(2.5rem,5vw,3.5rem)] leading-none font-semibold tracking-tight" />
            <p class="mt-3.5 text-body-md text-on-surface-variant">
                @if ($resumen['variacion'] !== null)
                    <span @class([
                        'font-bold',
                        'text-tertiary' => $resumen['variacion'] >= 0,
                        'text-error' => $resumen['variacion'] < 0,
                    ])>{{ $resumen['variacion'] >= 0 ? '+' : '' }}{{ $resumen['variacion'] }} %</span>
                    respecto a ayer (<x-precio :centimos="$resumen['ventas_ayer']" class="font-mono" />)
                @else
                    Ayer no hubo ventas.
                @endif
            </p>
        </div>

        <dl class="flex-[1_1_240px] flex flex-col divide-y divide-outline-variant">
            <div class="flex-1 flex items-baseline justify-between gap-3 px-space-lg py-4">
                <dt class="text-body-md text-secondary">Comandas de hoy</dt>
                <dd class="font-mono text-headline-md">{{ $resumen['pedidos_hoy'] }}</dd>
            </div>
            <div class="flex-1 flex items-baseline justify-between gap-3 px-space-lg py-4">
                <dt class="text-body-md text-secondary">Ticket medio <span class="block text-label-md font-normal">ventas entre pedidos de hoy</span></dt>
                <dd><x-precio :centimos="$resumen['ticket_medio']" class="font-mono text-headline-md" /></dd>
            </div>
            <div class="flex-1 flex items-baseline justify-between gap-3 px-space-lg py-4">
                <dt class="text-body-md text-secondary">Cobros de hoy</dt>
                <dd class="font-mono text-headline-md">{{ $resumen['cobros_hoy'] }}</dd>
            </div>
        </dl>
    </section>

    {{-- 2. Ventas por día --}}
    <section class="mt-12">
        <div class="flex flex-wrap items-baseline justify-between gap-x-space-lg gap-y-1 pb-3 border-b-2 border-on-surface">
            <h2 class="text-headline-md">Ventas por día</h2>
            <p class="text-body-md text-secondary">Últimos 30 días · en naranja, los días por encima de la media</p>
        </div>
        @if ($hayVentas)
            {{-- h-72 + relative: Chart.js necesita un contenedor con altura fija --}}
            <div class="relative h-72 mt-space-lg">
                <canvas data-grafico="ventas-por-dia" data-datos='@json($ventasPorDia)'
                        aria-label="Gráfico de barras de las ventas por día" role="img"></canvas>
            </div>
        @else
            <x-estadisticas-vacio texto="Todavía no hay ventas cobradas en los últimos 30 días." />
        @endif
    </section>

    <div class="mt-12 flex flex-wrap items-start gap-x-14 gap-y-12">
        {{-- 3. Platos más pedidos --}}
        <section class="flex-[3_1_440px] min-w-0">
            <div class="flex flex-wrap items-baseline justify-between gap-x-space-lg gap-y-1 pb-3 border-b-2 border-on-surface">
                <h2 class="text-headline-md">Platos más pedidos</h2>
                <p class="text-body-md text-secondary">Top 10 por unidades</p>
            </div>
            @forelse ($platos as $posicion => $plato)
                <div class="flex items-center gap-space-md py-3 border-b border-outline-variant">
                    <span class="w-6 font-mono text-label-lg text-secondary">{{ $posicion + 1 }}</span>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-baseline justify-between gap-space-sm">
                            <p class="text-label-lg truncate">{{ $plato['nombre'] }}</p>
                            <x-precio :centimos="$plato['importe_centimos']" class="font-mono text-label-lg" />
                        </div>
                        <p class="text-body-md text-secondary"><span class="font-mono">{{ $plato['unidades'] }}</span> {{ $plato['unidades'] === 1 ? 'unidad' : 'unidades' }}</p>
                        {{-- Barra proporcional al plato que más se ha pedido --}}
                        <div class="mt-1.5 h-1 bg-surface-container-high" aria-hidden="true">
                            <div @class(['h-full', 'bg-primary-container' => $loop->first, 'bg-on-surface' => ! $loop->first])
                                 style="width: {{ (int) round($plato['unidades'] * 100 / $maxUnidades) }}%"></div>
                        </div>
                    </div>
                </div>
            @empty
                <x-estadisticas-vacio texto="Todavía no hay pedidos en los últimos 30 días." />
            @endforelse
        </section>

        {{-- 4. Métodos de pago --}}
        <section class="flex-[2_1_300px] min-w-0">
            <div class="pb-3 border-b-2 border-on-surface">
                <h2 class="text-headline-md">Métodos de pago</h2>
                <p class="text-body-md text-secondary">Tarjeta (Stripe y TPV) frente a efectivo</p>
            </div>
            @if ($totalMetodos > 0)
                <div class="relative mx-auto mt-space-lg h-52 w-52">
                    <canvas data-grafico="metodos-de-pago"
                            data-datos='@json(['tarjeta' => $metodos['tarjeta']['importe_centimos'], 'efectivo' => $metodos['efectivo']['importe_centimos']])'
                            aria-label="Gráfico circular de tarjeta frente a efectivo" role="img"></canvas>
                    {{-- Porcentaje en el centro del círculo --}}
                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                        <span class="font-mono text-headline-lg">{{ $porcentajeTarjeta }}%</span>
                        <span class="text-label-md font-normal text-secondary">con tarjeta</span>
                    </div>
                </div>

                <dl class="mt-space-lg">
                    <div class="flex items-start justify-between gap-space-md py-3 border-t border-outline-variant">
                        <dt class="flex gap-2.5">
                            <span class="mt-1.5 w-3 h-3 shrink-0 bg-primary-container" aria-hidden="true"></span>
                            <span>
                                <span class="block text-label-lg">Tarjeta</span>
                                <span class="block text-body-md text-secondary">
                                    Stripe: <x-precio :centimos="$metodos['stripe']['importe_centimos']" class="font-mono" /> ({{ $metodos['stripe']['numero'] }})
                                    · TPV: <x-precio :centimos="$metodos['tpv']['importe_centimos']" class="font-mono" /> ({{ $metodos['tpv']['numero'] }})
                                </span>
                            </span>
                        </dt>
                        <dd class="text-right">
                            <x-precio :centimos="$metodos['tarjeta']['importe_centimos']" class="font-mono text-label-lg" />
                            <span class="block text-body-md text-secondary">{{ $metodos['tarjeta']['numero'] }} pagos</span>
                        </dd>
                    </div>
                    <div class="flex items-start justify-between gap-space-md py-3 border-t border-outline-variant">
                        <dt class="flex gap-2.5">
                            <span class="mt-1.5 w-3 h-3 shrink-0 bg-on-surface" aria-hidden="true"></span>
                            <span>
                                <span class="block text-label-lg">Efectivo</span>
                                <span class="block text-body-md text-secondary">Cobrado por los camareros en la mesa</span>
                            </span>
                        </dt>
                        <dd class="text-right">
                            <x-precio :centimos="$metodos['efectivo']['importe_centimos']" class="font-mono text-label-lg" />
                            <span class="block text-body-md text-secondary">{{ $metodos['efectivo']['numero'] }} pagos</span>
                        </dd>
                    </div>
                </dl>
            @else
                <x-estadisticas-vacio texto="Todavía no hay pagos en los últimos 30 días." />
            @endif
        </section>
    </div>
</x-layouts.admin>
