{{--
    "Pago completado" del cliente (rama 11). Maqueta: cliente/pago-completado.
    Solo se llega aquí después de comprobar con Stripe que la sesión está pagada.
    Variables: $restaurante, $mesa, $parte, $pago (puede ser null), $cuenta (con sus partes).

    Diferencias con la maqueta: sin propina, camarero asignado, últimos dígitos
    de la tarjeta, lista de platos, ticket por email, factura ni PDF.
--}}
@php
    $rutaMesa = ['slug' => $restaurante->slug, 'numero' => $mesa->numero];

    // Estado global de la mesa: cuánto está pagado ya y qué porcentaje es.
    $partesPagadas = $cuenta->partes->where('estado', 'pagado');
    $pagadoCentimos = $partesPagadas->sum('importe_centimos');
    $porcentaje = $cuenta->total_centimos > 0 ? (int) floor($pagadoCentimos * 100 / $cuenta->total_centimos) : 0;
@endphp

<x-layouts.cliente :restaurante="$restaurante->nombre" :mesa="$mesa->numero" seccion="cuenta"
                   :titulo="'Pago completado · '.$restaurante->nombre">

    {{-- Confirmación --}}
    <x-tarjeta class="mb-space-md text-center">
        <span class="mx-auto flex items-center justify-center w-20 h-20 rounded-full bg-tertiary-container/20">
            <span class="flex items-center justify-center w-14 h-14 rounded-full bg-tertiary text-white">
                <x-icono nombre="check_circle" class="text-4xl" />
            </span>
        </span>
        <span class="mt-space-sm inline-flex items-center gap-1 px-3 py-1 rounded-full bg-tertiary-fixed/40 text-label-sm text-on-tertiary-container">
            <x-icono nombre="verified" class="text-base" /> Pago verificado con Stripe
        </span>
        <h1 class="mt-space-sm text-headline-lg">¡Pago completado con éxito!</h1>
        <p class="mt-1 text-body-md text-secondary">
            Tu parte ha quedado pagada y registrada en la cuenta de la Mesa {{ $mesa->numero }}.
        </p>
    </x-tarjeta>

    {{-- Recibo --}}
    <x-tarjeta class="mb-space-md border-t-4 border-t-tertiary">
        <p class="text-label-sm uppercase text-secondary">Recibo digital</p>
        <div class="flex flex-wrap items-center gap-2">
            <x-precio :centimos="$parte->importe_centimos" class="text-headline-md font-extrabold" />
            <span class="px-2 py-0.5 rounded-full bg-tertiary-fixed text-label-sm text-on-tertiary-container">Pagado</span>
        </div>
        <p class="text-body-md text-secondary">Persona {{ $parte->persona }} de {{ $cuenta->partes->count() }}</p>

        <dl class="mt-space-md pt-space-md border-t border-outline-variant/40 space-y-space-sm text-body-md">
            <div>
                <dt class="text-label-sm text-secondary">Fecha y hora</dt>
                <dd class="flex items-center gap-2"><x-icono nombre="schedule" class="text-lg" />
                    {{ ($pago?->created_at ?? now())->format('d/m/Y \a \l\a\s H:i') }} h</dd>
            </div>
            <div>
                <dt class="text-label-sm text-secondary">Método de pago</dt>
                <dd class="flex items-center gap-2"><x-icono nombre="credit_card" class="text-lg" /> Tarjeta (Stripe)</dd>
            </div>
            @if ($pago?->referencia_stripe)
                <div>
                    <dt class="text-label-sm text-secondary">Referencia de la transacción</dt>
                    {{-- break-all: el id de Stripe es largo y no tiene espacios --}}
                    <dd class="flex items-center gap-2 break-all"><x-icono nombre="tag" class="text-lg" /> {{ $pago->referencia_stripe }}</dd>
                </div>
            @endif
        </dl>
    </x-tarjeta>

    {{-- Estado global de la mesa --}}
    <x-tarjeta class="mb-space-md">
        <div class="flex items-center justify-between gap-space-md">
            <h2 class="text-headline-sm">Estado de la Mesa {{ $mesa->numero }}</h2>
            <span class="text-headline-sm text-primary">{{ $porcentaje }}%</span>
        </div>
        <p class="text-body-md text-secondary">
            {{ $partesPagadas->count() }} de {{ $cuenta->partes->count() }} personas han pagado
            (<x-precio :centimos="$pagadoCentimos" /> de <x-precio :centimos="$cuenta->total_centimos" />)
        </p>

        <div class="mt-space-sm h-2 rounded-full bg-surface-container-high overflow-hidden" role="progressbar"
             aria-valuenow="{{ $porcentaje }}" aria-valuemin="0" aria-valuemax="100">
            <div class="h-full bg-tertiary" style="width: {{ $porcentaje }}%"></div>
        </div>

        <ul class="mt-space-md space-y-2">
            @foreach ($cuenta->partes as $otraParte)
                @php $esLaMia = $otraParte->id === $parte->id; @endphp
                <li class="flex items-center gap-space-sm p-2 rounded {{ $esLaMia ? 'bg-tertiary-fixed/20' : '' }}">
                    <span class="flex items-center justify-center w-10 h-10 rounded-full text-label-md {{ $esLaMia ? 'bg-tertiary text-white' : 'bg-surface-container-high' }}">
                        {{ $esLaMia ? 'TÚ' : 'P'.$otraParte->persona }}
                    </span>
                    <span class="flex-1">
                        <span class="block text-label-lg">{{ $esLaMia ? 'Tú (Persona '.$otraParte->persona.')' : 'Persona '.$otraParte->persona }}</span>
                        <span class="block text-label-sm {{ $otraParte->estado === 'pagado' ? 'text-tertiary' : 'text-secondary' }}">
                            {{ $otraParte->estado === 'pagado' ? 'Pagado' : 'Pendiente de pago' }}
                        </span>
                    </span>
                    <x-precio :centimos="$otraParte->importe_centimos" class="text-label-lg" />
                </li>
            @endforeach
        </ul>

        <p class="mt-space-md flex gap-2 text-body-md text-secondary">
            <x-icono nombre="info" class="text-lg" />
            La mesa quedará libre cuando todos hayan pagado su parte.
        </p>
    </x-tarjeta>

    <a href="{{ route('cliente.carta', $rutaMesa) }}"
       class="pulsable w-full flex items-center justify-center gap-2 min-h-[56px] rounded-lg bg-primary-container text-on-primary text-label-lg font-bold shadow-nivel-2">
        <x-icono nombre="bakery_dining" /> Pedir cafés o postres (volver a la carta)
    </a>
    <a href="{{ route('cliente.cuenta', $rutaMesa) }}"
       class="mt-space-sm w-full flex items-center justify-center gap-2 min-h-[48px] rounded-lg border border-outline-variant/50 bg-surface-container-lowest text-label-lg">
        <x-icono nombre="receipt_long" /> Ver la cuenta
    </a>
</x-layouts.cliente>
