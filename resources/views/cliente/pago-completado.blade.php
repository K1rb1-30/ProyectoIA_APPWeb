{{--
    "Pago completado" del cliente (rama 11, rediseño en la rama 23).
    Solo se llega aquí después de comprobar con Stripe que la sesión está pagada.
    Variables: $restaurante, $mesa, $parte, $pago (puede ser null), $cuenta (con sus partes).

    Rediseño «Comanda impresa»: un título corto y grande, un justificante con
    aspecto de ticket (líneas discontinuas y letra de máquina) y, debajo, cómo
    va el resto de la mesa en filas. Sin círculos ni cápsulas de colores.
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
    <svg width="48" height="48" viewBox="0 0 48 48" fill="none" aria-hidden="true" class="text-tertiary">
        <circle cx="24" cy="24" r="23" stroke="currentColor" stroke-width="2"></circle>
        <path d="M14 25l7 7 13-14" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"></path>
    </svg>
    <h1 class="mt-space-md text-headline-lg">¡Pago completado con éxito!</h1>
    <p class="mt-2 text-body-lg text-on-surface-variant">
        Tu parte ha quedado pagada y registrada en la cuenta de la Mesa {{ $mesa->numero }}. Pago verificado con Stripe.
    </p>

    {{-- Justificante, como un ticket impreso --}}
    <figure class="mt-space-xl p-space-lg bg-surface-container-lowest shadow-nivel-1" aria-label="Justificante del pago">
        <div class="flex justify-between gap-space-md font-mono text-label-md font-normal text-secondary">
            <span class="uppercase">{{ $restaurante->nombre }} · Mesa {{ $mesa->numero }}</span>
            <span>{{ ($pago?->created_at ?? now())->format('d/m/Y H:i') }}</span>
        </div>
        <dl class="mt-space-md pt-space-md border-t-[1.5px] border-dashed border-secondary-fixed-dim space-y-2 font-mono text-body-md">
            <div class="flex justify-between gap-space-md"><dt>Persona {{ $parte->persona }} de {{ $cuenta->numeroDePersonas() }}</dt><dd><x-precio :centimos="$parte->importe_centimos" /></dd></div>
            <div class="flex justify-between gap-space-md text-secondary"><dt>Método</dt><dd>Tarjeta (Stripe)</dd></div>
            @if ($pago?->referencia_stripe)
                <div class="text-secondary">
                    <dt>Referencia</dt>
                    {{-- break-all: el id de Stripe es largo y no tiene espacios --}}
                    <dd class="break-all text-label-md font-normal">{{ $pago->referencia_stripe }}</dd>
                </div>
            @endif
        </dl>
        <div class="mt-space-md pt-space-md border-t-[1.5px] border-dashed border-secondary-fixed-dim flex items-baseline justify-between">
            <span class="text-label-lg">Pagado</span>
            <x-precio :centimos="$parte->importe_centimos" class="font-mono text-headline-md" />
        </div>
    </figure>

    {{-- Estado global de la mesa --}}
    <section class="mt-space-xl">
        <div class="flex items-baseline justify-between gap-space-md pb-3 border-b-2 border-on-surface">
            <h2 class="text-headline-sm">Mesa {{ $mesa->numero }}</h2>
            <span class="font-mono text-label-lg">{{ $porcentaje }}% pagado</span>
        </div>
        <p class="mt-2 text-body-md text-secondary">
            {{ $partesPagadas->count() }} de {{ $cuenta->partes->count() }} partes pagadas
            (<x-precio :centimos="$pagadoCentimos" class="font-mono" /> de <x-precio :centimos="$cuenta->total_centimos" class="font-mono" />)
        </p>
        <div class="mt-2 h-2 bg-surface-container-high" role="progressbar"
             aria-valuenow="{{ $porcentaje }}" aria-valuemin="0" aria-valuemax="100" aria-label="Porcentaje pagado de la mesa">
            <div class="h-full bg-tertiary-container" style="width: {{ $porcentaje }}%"></div>
        </div>

        <ul class="mt-space-md">
            @foreach ($cuenta->partes as $otraParte)
                @php $esLaMia = $otraParte->id === $parte->id; @endphp
                <li class="flex items-center gap-space-md py-3 border-b border-outline-variant">
                    <span class="flex-1">
                        <span class="block text-label-lg">{{ $esLaMia ? 'Tú ('.$otraParte->nombre().')' : $otraParte->nombre() }}</span>
                        <span class="block text-label-md {{ $otraParte->estado === 'pagado' ? 'text-tertiary' : 'font-normal text-secondary' }}">
                            {{ $otraParte->estado === 'pagado' ? 'Pagado' : 'Pendiente de pago' }}
                        </span>
                    </span>
                    <x-precio :centimos="$otraParte->importe_centimos" class="font-mono text-label-lg" />
                </li>
            @endforeach
        </ul>

        <p class="mt-space-md text-body-md text-secondary">La mesa quedará libre cuando todos hayan pagado su parte.</p>
    </section>

    <div class="mt-space-xl space-y-space-sm">
        <a href="{{ route('cliente.carta', $rutaMesa) }}"
           class="pulsable w-full flex items-center justify-center min-h-[56px] rounded bg-primary-container text-on-primary text-label-lg font-bold hover:bg-primary">
            Pedir cafés o postres
        </a>
        <a href="{{ route('cliente.cuenta', $rutaMesa) }}"
           class="w-full flex items-center justify-center min-h-[48px] rounded border-[1.5px] border-on-surface text-label-lg">
            Ver la cuenta
        </a>
    </div>
</x-layouts.cliente>
