{{--
    "Pago con tarjeta" del cliente (rama 11). Maqueta: cliente/pago-tarjeta.
    Variables: $restaurante, $mesa, $parte (con su cuenta), $totalPersonas.

    Diferencias con la maqueta:
      - Los campos de la tarjeta NO están aquí: los pone la página de Stripe
        Checkout, a la que se va al pulsar el botón. Así los datos de la tarjeta
        nunca pasan por nuestro servidor.
      - Sin propina, Apple Pay, "guardar tarjeta" ni la lista de platos de la
        parte (al dividir a partes iguales no hay platos por persona).
--}}
@php
    $rutaMesa = ['slug' => $restaurante->slug, 'numero' => $mesa->numero];
@endphp

<x-layouts.cliente :restaurante="$restaurante->nombre" :mesa="$mesa->numero" seccion="cuenta"
                   :titulo="'Pagar con tarjeta · '.$restaurante->nombre">

    <a href="{{ route('cliente.cuenta', $rutaMesa) }}" class="inline-flex items-center gap-2 mb-space-md text-label-lg text-secondary hover:text-on-surface">
        <x-icono nombre="arrow_back" /> Volver a la cuenta
    </a>

    {{-- Resumen de la parte --}}
    <x-tarjeta class="mb-space-md border-t-4 border-t-primary-container">
        <div class="flex items-start justify-between gap-space-md">
            <div>
                <span class="inline-block px-3 py-1 mb-2 rounded-full bg-secondary-container text-label-sm">División de cuenta</span>
                <h1 class="text-headline-sm">Tu parte a pagar (Persona {{ $parte->persona }} de {{ $totalPersonas }})</h1>
                <p class="text-body-md text-secondary">Mesa {{ $mesa->numero }} · {{ $restaurante->nombre }}</p>
            </div>
            <div class="text-right">
                <x-precio :centimos="$parte->importe_centimos" class="block text-[34px] leading-none font-extrabold text-primary" />
                <span class="text-label-sm text-secondary">IVA incluido</span>
            </div>
        </div>
    </x-tarjeta>

    <x-tarjeta>
        {{-- Aviso de modo test: en la demo nunca se cobra de verdad --}}
        <div role="note" class="mb-space-md flex gap-2 px-4 py-3 rounded border border-amber-300 bg-amber-50 text-amber-900 text-body-md">
            <x-icono nombre="warning" class="text-xl" />
            <div>
                <p class="font-semibold">Modo de prueba (Test mode)</p>
                <p>No se hará ningún cargo real. Usa la tarjeta de prueba de Stripe
                    <span class="font-semibold tabular-nums whitespace-nowrap">4242 4242 4242 4242</span>,
                    cualquier fecha futura y cualquier CVC.</p>
            </div>
        </div>

        <p class="mb-space-md text-body-md text-secondary">
            Al pulsar el botón irás a la página segura de Stripe para escribir los datos de la tarjeta.
            Cuando termines, volverás aquí.
        </p>

        {{-- POST: crea la sesión de Stripe y nos manda a su página de pago --}}
        <form method="POST" action="{{ route('cliente.pago.pagar', $rutaMesa + ['parte' => $parte->id]) }}"
              x-data="{ enviando: false }" x-on:submit="enviando = true">
            @csrf
            <button type="submit" x-bind:disabled="enviando"
                    class="pulsable w-full flex items-center justify-center gap-2 min-h-[56px] rounded-lg bg-primary-container text-on-primary text-label-lg font-bold shadow-nivel-2 disabled:opacity-60">
                <x-icono nombre="shield" />
                <span x-text="enviando ? 'Abriendo Stripe…' : @js('Pagar '.number_format($parte->importe_centimos / 100, 2, ',', '.').' € de forma segura')">
                    Pagar <x-precio :centimos="$parte->importe_centimos" /> de forma segura
                </span>
            </button>
        </form>

        <p class="mt-space-md text-center text-label-md text-secondary">
            Pago gestionado por <span class="font-bold text-[#635bff]">stripe</span>
        </p>
    </x-tarjeta>

    <p class="mt-space-md text-center text-body-md text-secondary">
        <x-icono nombre="support_agent" class="align-middle" /> ¿Tienes dudas con el importe? Avisa al camarero.
    </p>
</x-layouts.cliente>
