{{--
    Estadísticas del restaurante (rama 13). Maqueta: panelAdmin/estadisticas.
    Variables (todo el dinero en CÉNTIMOS; se pasa a euros solo al mostrarlo):
      $resumen       ventas_hoy, ventas_ayer, variacion, pedidos_hoy, cobros_hoy, ticket_medio
      $ventasPorDia  30 filas [dia, total_centimos, pagos]
      $platos        top 10 [nombre, unidades, importe_centimos]
      $metodos       efectivo / stripe / tpv / tarjeta → [importe_centimos, numero]

    Los gráficos los pinta resources/js/estadisticas.js (Chart.js), que solo se
    carga en esta página. Cada <canvas> lleva sus datos en data-datos (JSON).

    Diferencias con la maqueta: ventas por DÍA (30 días) en vez de por franja
    horaria; «Cobros de hoy» en vez de «Rotación»; sin selector de periodo,
    exportar, objetivos, márgenes ni propinas. Los platos son una lista con
    barras en HTML, como en la maqueta.
--}}
@php
    $hayVentas = collect($ventasPorDia)->sum('total_centimos') > 0;
    $totalMetodos = $metodos['tarjeta']['importe_centimos'] + $metodos['efectivo']['importe_centimos'];
    $porcentajeTarjeta = $totalMetodos > 0 ? (int) round($metodos['tarjeta']['importe_centimos'] * 100 / $totalMetodos) : 0;
    $maxUnidades = collect($platos)->max('unidades') ?: 1;
@endphp

<x-layouts.admin titulo="Estadísticas">
    {{-- Chart.js solo en esta página (entrada aparte de Vite) --}}
    @vite('resources/js/estadisticas.js')

    <div class="space-y-space-lg">
        <div>
            <h2 class="text-headline-lg font-extrabold">Informes y analítica</h2>
            <p class="text-body-md text-secondary">Ventas cobradas, platos más pedidos y métodos de pago de los últimos 30 días.</p>
        </div>

        {{-- Tarjetas de resumen de hoy --}}
        <div class="grid gap-space-md sm:grid-cols-2 xl:grid-cols-4">
            <x-tarjeta>
                <p class="flex items-center justify-between text-label-lg text-secondary">Ventas del día <x-icono nombre="payments" class="text-primary" /></p>
                <x-precio :centimos="$resumen['ventas_hoy']" class="block mt-2 text-headline-lg font-extrabold" />
                @if ($resumen['variacion'] !== null)
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
            </x-tarjeta>

            <x-tarjeta>
                <p class="flex items-center justify-between text-label-lg text-secondary">Comandas de hoy <x-icono nombre="receipt_long" class="text-primary" /></p>
                <p class="mt-2"><span class="text-headline-lg font-extrabold">{{ $resumen['pedidos_hoy'] }}</span> <span class="text-body-md text-secondary">pedidos</span></p>
            </x-tarjeta>

            <x-tarjeta>
                <p class="flex items-center justify-between text-label-lg text-secondary">Ticket medio <x-icono nombre="query_stats" class="text-primary" /></p>
                <x-precio :centimos="$resumen['ticket_medio']" class="block mt-2 text-headline-lg font-extrabold" />
                <p class="mt-1 text-label-md text-secondary">Ventas de hoy entre pedidos de hoy.</p>
            </x-tarjeta>

            <x-tarjeta>
                <p class="flex items-center justify-between text-label-lg text-secondary">Cobros de hoy <x-icono nombre="point_of_sale" class="text-primary" /></p>
                <p class="mt-2"><span class="text-headline-lg font-extrabold">{{ $resumen['cobros_hoy'] }}</span> <span class="text-body-md text-secondary">pagos</span></p>
            </x-tarjeta>
        </div>

        {{-- Ventas por día --}}
        <x-tarjeta>
            <h3 class="text-headline-sm font-bold">Ventas por día</h3>
            <p class="text-body-md text-secondary mb-space-md">Lo cobrado cada día de los últimos 30 días. En naranja, los días por encima de la media.</p>
            @if ($hayVentas)
                {{-- h-72 + relative: Chart.js necesita un contenedor con altura fija --}}
                <div class="relative h-72">
                    <canvas data-grafico="ventas-por-dia" data-datos='@json($ventasPorDia)'
                            aria-label="Gráfico de barras de las ventas por día" role="img"></canvas>
                </div>
            @else
                <x-estadisticas-vacio texto="Todavía no hay ventas cobradas en los últimos 30 días." />
            @endif
        </x-tarjeta>

        <div class="grid gap-space-lg xl:grid-cols-[3fr_2fr]">
            {{-- Platos más pedidos --}}
            <x-tarjeta>
                <h3 class="text-headline-sm font-bold">Platos más pedidos</h3>
                <p class="text-body-md text-secondary mb-space-md">Top 10 por unidades en los últimos 30 días.</p>
                @forelse ($platos as $posicion => $plato)
                    <div class="flex items-center gap-space-md py-3 border-b border-outline-variant/30 last:border-0">
                        <span class="w-6 text-headline-sm font-bold text-secondary">{{ $posicion + 1 }}</span>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-baseline justify-between gap-space-sm">
                                <p class="text-label-lg truncate">{{ $plato['nombre'] }}</p>
                                <x-precio :centimos="$plato['importe_centimos']" class="text-label-lg" />
                            </div>
                            <p class="text-label-sm text-secondary">{{ $plato['unidades'] }} {{ $plato['unidades'] === 1 ? 'unidad' : 'unidades' }}</p>
                            {{-- Barra proporcional al plato que más se ha pedido --}}
                            <div class="mt-1 h-1.5 rounded-full bg-secondary-container/60 overflow-hidden">
                                <div class="h-full rounded-full bg-primary-container" style="width: {{ (int) round($plato['unidades'] * 100 / $maxUnidades) }}%"></div>
                            </div>
                        </div>
                    </div>
                @empty
                    <x-estadisticas-vacio texto="Todavía no hay pedidos en los últimos 30 días." />
                @endforelse
            </x-tarjeta>

            {{-- Métodos de pago --}}
            <x-tarjeta>
                <h3 class="flex items-center justify-between text-headline-sm font-bold">Métodos de pago <x-icono nombre="credit_card" class="text-secondary" /></h3>
                <p class="text-body-md text-secondary mb-space-md">Tarjeta (Stripe y TPV) frente a efectivo, últimos 30 días.</p>
                @if ($totalMetodos > 0)
                    <div class="relative mx-auto h-56 w-56">
                        <canvas data-grafico="metodos-de-pago"
                                data-datos='@json(['tarjeta' => $metodos['tarjeta']['importe_centimos'], 'efectivo' => $metodos['efectivo']['importe_centimos']])'
                                aria-label="Gráfico circular de tarjeta frente a efectivo" role="img"></canvas>
                        {{-- Porcentaje en el centro del círculo --}}
                        <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                            <span class="text-headline-lg font-extrabold">{{ $porcentajeTarjeta }}%</span>
                            <span class="text-label-sm text-primary">con tarjeta</span>
                        </div>
                    </div>

                    <div class="mt-space-md space-y-space-sm">
                        <div class="flex items-start justify-between gap-space-md p-3 rounded bg-surface-container-low">
                            <div class="flex gap-2">
                                <span class="mt-1.5 w-3 h-3 rounded-full bg-primary-container"></span>
                                <div>
                                    <p class="text-label-lg">Tarjeta</p>
                                    <p class="text-label-sm text-secondary">
                                        Stripe: <x-precio :centimos="$metodos['stripe']['importe_centimos']" /> ({{ $metodos['stripe']['numero'] }})
                                        · TPV: <x-precio :centimos="$metodos['tpv']['importe_centimos']" /> ({{ $metodos['tpv']['numero'] }})
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <x-precio :centimos="$metodos['tarjeta']['importe_centimos']" class="text-label-lg font-bold" />
                                <p class="text-label-sm text-secondary">{{ $metodos['tarjeta']['numero'] }} pagos</p>
                            </div>
                        </div>
                        <div class="flex items-start justify-between gap-space-md p-3 rounded bg-surface-container-low">
                            <div class="flex gap-2">
                                <span class="mt-1.5 w-3 h-3 rounded-full bg-inverse-surface"></span>
                                <div>
                                    <p class="text-label-lg">Efectivo</p>
                                    <p class="text-label-sm text-secondary">Cobrado por los camareros en la mesa</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <x-precio :centimos="$metodos['efectivo']['importe_centimos']" class="text-label-lg font-bold" />
                                <p class="text-label-sm text-secondary">{{ $metodos['efectivo']['numero'] }} pagos</p>
                            </div>
                        </div>
                    </div>
                @else
                    <x-estadisticas-vacio texto="Todavía no hay pagos en los últimos 30 días." />
                @endif
            </x-tarjeta>
        </div>
    </div>
</x-layouts.admin>
