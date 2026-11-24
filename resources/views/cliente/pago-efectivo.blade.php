{{--
    "Pago en efectivo" del cliente (rama 12, rediseño en la rama 23).
    Se llega aquí cuando la parte está en "pendiente_efectivo".
    Variables: $restaurante, $mesa, $parte, $cuenta.

    El aviso al camarero es que la parte le sale como "Efectivo sin confirmar"
    en el detalle de la mesa.

    Rediseño «Comanda impresa»: la instrucción («Avisa al camarero…») es el
    título grande; debajo, el importe en letra de ticket. Sin iconos ni cajas.
--}}
@php
    $rutaMesa = ['slug' => $restaurante->slug, 'numero' => $mesa->numero];
@endphp

<x-layouts.cliente :restaurante="$restaurante->nombre" :mesa="$mesa->numero" seccion="cuenta"
                   :titulo="'Pago en efectivo · '.$restaurante->nombre">

    <a href="{{ route('cliente.cuenta', $rutaMesa) }}" class="inline-flex items-center min-h-[44px] text-label-lg text-secondary underline underline-offset-2 hover:text-on-surface">
        Volver a la cuenta
    </a>

    <p class="mt-space-md inline-flex items-center gap-2 text-label-md text-amber-800">
        <span class="w-2 h-2 rounded-full bg-estado-pendiente" aria-hidden="true"></span>
        Pendiente de cobro en efectivo
    </p>
    <h1 class="mt-2 text-headline-lg">Avisa al camarero, vendrá a cobrarte</h1>
    <p class="mt-2 text-body-lg text-on-surface-variant">
        Un camarero se acercará a la <strong class="text-on-surface">Mesa {{ $mesa->numero }}</strong> para cobrar tu parte.
    </p>

    {{-- Importe, como en un ticket --}}
    <section class="mt-space-xl p-space-lg bg-surface-container-lowest shadow-nivel-1">
        <p class="text-label-lg">Tu parte (Persona {{ $parte->persona }})</p>
        <x-precio :centimos="$parte->importe_centimos" class="block mt-2 font-mono text-[52px] leading-none font-semibold tracking-tight" />
        <p class="mt-3 pt-3 border-t-[1.5px] border-dashed border-secondary-fixed-dim font-mono text-label-md font-normal text-secondary">
            Total de la mesa: <x-precio :centimos="$cuenta->total_centimos" />
        </p>
    </section>

    <p class="mt-space-lg text-body-md text-secondary">
        Si tienes el importe exacto, agilizarás el cobro. El camarero te dará el ticket en mano.
    </p>
</x-layouts.cliente>
