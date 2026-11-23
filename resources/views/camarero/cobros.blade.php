{{--
    Pestaña «Cobros» del camarero (rama 12). SIN maqueta propia: sigue el estilo
    de camarero/mesas (tarjetas de contadores) y camarero/detalle-mesa.
    Variables:
      $esperando      partes en pendiente_efectivo agrupadas por número de mesa
      $pagosHoy       pagos de hoy del restaurante (con parte, cuenta y mesa)
      $totalEfectivo, $totalStripe, $totalTpv   totales de hoy en céntimos
--}}
<x-layouts.camarero titulo="Cobros" seccion="cobros">
    <x-mensajes-sesion />

    {{-- Esperando cobro en efectivo --}}
    <h2 class="text-headline-sm font-bold mb-space-sm">Esperando cobro en efectivo</h2>
    @forelse ($esperando as $numeroMesa => $partes)
        @php $mesa = $partes->first()->cuenta->mesa; @endphp
        <x-tarjeta class="mb-space-sm border-l-4 border-l-primary-container">
            <div class="flex items-center justify-between gap-space-md">
                <h3 class="text-headline-sm font-extrabold">Mesa {{ $numeroMesa }}</h3>
                <x-precio :centimos="$partes->sum('importe_centimos')" class="text-headline-sm font-bold text-primary" />
            </div>
            <ul class="mt-1 text-body-md text-secondary">
                @foreach ($partes as $parte)
                    <li>Persona {{ $parte->persona }}: <x-precio :centimos="$parte->importe_centimos" /></li>
                @endforeach
            </ul>
            <a href="{{ route('camarero.mesas.detalle', $mesa) }}"
               class="pulsable mt-space-sm w-full inline-flex items-center justify-center gap-1.5 min-h-[48px] rounded bg-primary-container text-on-primary text-label-lg font-bold">
                <x-icono nombre="point_of_sale" class="text-xl" /> Ir a cobrar
            </a>
        </x-tarjeta>
    @empty
        <x-tarjeta class="mb-space-sm">
            <p class="text-body-md text-secondary">Nadie está esperando para pagar en efectivo.</p>
        </x-tarjeta>
    @endforelse

    {{-- Cobrado hoy --}}
    <h2 class="mt-space-lg text-headline-sm font-bold mb-space-sm">Cobrado hoy</h2>
    <div class="grid grid-cols-3 gap-2 mb-space-md">
        @foreach ([['Efectivo', 'payments', $totalEfectivo], ['Stripe', 'credit_card', $totalStripe], ['TPV', 'contactless', $totalTpv]] as [$texto, $icono, $total])
            <div class="bg-surface-container-lowest rounded-lg border border-outline-variant/30 shadow-nivel-1 p-3">
                <p class="flex items-center gap-1 text-label-md text-secondary"><x-icono :nombre="$icono" class="text-lg" /> {{ $texto }}</p>
                <x-precio :centimos="$total" class="block mt-1 text-label-lg font-bold" />
            </div>
        @endforeach
    </div>

    @if ($pagosHoy->isEmpty())
        <x-tarjeta>
            <p class="text-body-md text-secondary">Todavía no se ha cobrado nada hoy.</p>
        </x-tarjeta>
    @else
        <x-tarjeta>
            <ul class="divide-y divide-outline-variant/30">
                @foreach ($pagosHoy as $pago)
                    @php
                        $metodo = $pago->metodo === 'efectivo' ? 'Efectivo' : ($pago->referencia_stripe ? 'Stripe' : 'TPV');
                    @endphp
                    <li class="flex items-center justify-between gap-space-md py-2">
                        <span>
                            <span class="block text-label-lg">Mesa {{ $pago->parteCuenta->cuenta->mesa->numero }} · Persona {{ $pago->parteCuenta->persona }}</span>
                            <span class="block text-label-sm text-secondary">{{ $pago->created_at->format('H:i') }} · {{ $metodo }}</span>
                        </span>
                        <x-precio :centimos="$pago->importe_centimos" class="text-label-lg" />
                    </li>
                @endforeach
            </ul>
        </x-tarjeta>
    @endif
</x-layouts.camarero>
