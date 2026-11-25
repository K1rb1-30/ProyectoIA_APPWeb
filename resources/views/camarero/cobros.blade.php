{{--
    Pestaña «Cobros» del camarero (rama 12, rediseño en la rama 20).
    Variables:
      $esperando      partes en pendiente_efectivo agrupadas por número de mesa
      $pagosHoy       pagos de hoy del restaurante (con parte, cuenta y mesa)
      $totalEfectivo, $totalStripe, $totalTpv   totales de hoy en céntimos

    Rediseño «Comanda impresa»: lo urgente (quién espera para pagar) va arriba;
    lo cobrado hoy, abajo, como una lista con líneas, sin tarjetas apiladas.
--}}
<x-layouts.camarero titulo="Cobros" seccion="cobros">
    <x-mensajes-sesion />

    {{-- Esperando cobro en efectivo --}}
    <h2 class="pb-2 border-b-2 border-on-surface text-headline-sm">Esperando cobro en efectivo</h2>
    <ul class="mb-space-xl">
        @forelse ($esperando as $numeroMesa => $partes)
            @php $mesa = $partes->first()->cuenta->mesa; @endphp
            <li class="py-space-md border-b border-outline-variant">
                <div class="flex items-baseline justify-between gap-space-md">
                    <h3 class="text-headline-md">Mesa {{ $numeroMesa }}</h3>
                    <x-precio :centimos="$partes->sum('importe_centimos')" class="font-mono text-headline-sm" />
                </div>
                <ul class="mt-1 text-body-md text-secondary">
                    @foreach ($partes as $parte)
                        <li>{{ $parte->nombre() }}: <x-precio :centimos="$parte->importe_centimos" class="font-mono" /></li>
                    @endforeach
                </ul>
                <a href="{{ route('camarero.mesas.detalle', $mesa) }}"
                   class="pulsable mt-space-sm w-full inline-flex items-center justify-center min-h-[48px] rounded bg-on-surface text-surface-container-lowest text-label-lg font-bold hover:bg-on-surface-variant">
                    Ir a cobrar
                </a>
            </li>
        @empty
            <li class="py-space-md text-body-md text-secondary">Nadie está esperando para pagar en efectivo.</li>
        @endforelse
    </ul>

    {{-- Cobrado hoy: tres totales en una franja con separadores --}}
    <h2 class="pb-2 border-b-2 border-on-surface text-headline-sm">Cobrado hoy</h2>
    <dl class="grid grid-cols-3 divide-x divide-outline-variant border-b border-outline-variant">
        @foreach ([['Efectivo', $totalEfectivo], ['Stripe', $totalStripe], ['TPV', $totalTpv]] as [$texto, $total])
            <div class="py-3 px-2 first:pl-0">
                <dt class="text-label-md text-secondary">{{ $texto }}</dt>
                <dd><x-precio :centimos="$total" class="font-mono text-label-lg" /></dd>
            </div>
        @endforeach
    </dl>

    @if ($pagosHoy->isEmpty())
        <p class="py-space-md text-body-md text-secondary">Todavía no se ha cobrado nada hoy.</p>
    @else
        <ul>
            @foreach ($pagosHoy as $pago)
                @php
                    $metodo = $pago->metodo === 'efectivo' ? 'Efectivo' : ($pago->referencia_stripe ? 'Stripe' : 'TPV');
                @endphp
                <li class="flex items-center justify-between gap-space-md py-3 border-b border-outline-variant">
                    <span>
                        <span class="block text-label-lg">Mesa {{ $pago->parteCuenta->cuenta->mesa->numero }} · {{ $pago->parteCuenta->nombre() }}</span>
                        <span class="block text-body-md text-secondary"><span class="font-mono">{{ $pago->created_at->format('H:i') }}</span> · {{ $metodo }}</span>
                    </span>
                    <x-precio :centimos="$pago->importe_centimos" class="font-mono text-label-lg" />
                </li>
            @endforeach
        </ul>
    @endif
</x-layouts.camarero>
