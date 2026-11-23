{{--
    "Pago en efectivo" del cliente (rama 12). Maqueta: cliente/pago-efectivo.
    Se llega aquí cuando la parte está en "pendiente_efectivo".
    Variables: $restaurante, $mesa, $parte, $cuenta.

    Diferencias con la maqueta: sin selector de billete ni cambio, sin "aviso
    recibido en TPV", sin "reiterar llamada" y sin Bizum. El aviso al camarero
    es que la parte le sale como "Efectivo sin confirmar" en el detalle de la mesa.
--}}
@php
    $rutaMesa = ['slug' => $restaurante->slug, 'numero' => $mesa->numero];
@endphp

<x-layouts.cliente :restaurante="$restaurante->nombre" :mesa="$mesa->numero" seccion="cuenta"
                   :titulo="'Pago en efectivo · '.$restaurante->nombre">

    <a href="{{ route('cliente.cuenta', $rutaMesa) }}" class="inline-flex items-center gap-2 mb-space-md text-label-lg text-secondary hover:text-on-surface">
        <x-icono nombre="arrow_back" /> Volver a la cuenta
    </a>

    {{-- Aviso principal, con el borde ámbar de la maqueta --}}
    <x-tarjeta class="mb-space-md border-l-4 border-l-amber-400">
        <div class="flex gap-space-md">
            <span class="shrink-0 flex items-center justify-center w-12 h-12 rounded-lg bg-amber-50 text-amber-700">
                <x-icono nombre="room_service" class="text-3xl" />
            </span>
            <div>
                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full border border-amber-300 bg-amber-50 text-label-sm text-amber-800">
                    <span class="w-2 h-2 rounded-full bg-estado-pendiente"></span> Pendiente de cobro en efectivo
                </span>
                <h1 class="mt-2 text-headline-md">Avisa al camarero, vendrá a cobrarte</h1>
                <p class="mt-1 text-body-md text-secondary">
                    Un camarero se acercará a la <strong class="text-on-surface">Mesa {{ $mesa->numero }}</strong> para cobrar tu parte.
                </p>
            </div>
        </div>
    </x-tarjeta>

    {{-- Importe --}}
    <x-tarjeta class="mb-space-md">
        <div class="flex items-end justify-between gap-space-md">
            <div>
                <p class="text-label-sm uppercase text-secondary">Concepto</p>
                <p class="text-headline-sm">Tu parte a pagar (Persona {{ $parte->persona }})</p>
            </div>
            <div class="text-right">
                <p class="text-label-md text-secondary">Mesa total: <x-precio :centimos="$cuenta->total_centimos" /></p>
                <x-precio :centimos="$parte->importe_centimos" class="text-[34px] leading-none font-extrabold text-primary-container" />
            </div>
        </div>

        <p class="mt-space-md flex gap-2 px-4 py-3 rounded bg-surface-container-low text-body-md">
            <x-icono nombre="lightbulb" class="text-xl text-primary" />
            Si tienes el importe exacto, agilizarás el cobro.
        </p>
    </x-tarjeta>

    <p class="text-center text-body-md text-secondary">
        <x-icono nombre="verified_user" class="align-middle text-lg" /> El camarero te dará el ticket en mano.
    </p>
</x-layouts.cliente>
