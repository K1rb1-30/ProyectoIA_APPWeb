{{--
    "Pago con tarjeta" del cliente (rama 11, rediseño en la rama 23).
    Variables: $restaurante, $mesa, $parte (con su cuenta), $totalPersonas.

    Los campos de la tarjeta NO están aquí: los pone la página de Stripe
    Checkout, a la que se va al pulsar el botón. Así los datos de la tarjeta
    nunca pasan por nuestro servidor.

    Rediseño «Comanda impresa»: el importe de tu parte es el dato principal,
    en grande; el aviso de modo de prueba es una nota con línea, sin caja amarilla.
--}}
@php
    $rutaMesa = ['slug' => $restaurante->slug, 'numero' => $mesa->numero];
@endphp

<x-layouts.cliente :restaurante="$restaurante->nombre" :mesa="$mesa->numero" seccion="cuenta"
                   :titulo="'Pagar con tarjeta · '.$restaurante->nombre">

    <a href="{{ route('cliente.cuenta', $rutaMesa) }}" class="inline-flex items-center min-h-[44px] text-label-lg text-secondary underline underline-offset-2 hover:text-on-surface">
        Volver a la cuenta
    </a>

    {{-- Dato principal: lo que pagas tú --}}
    <section class="mt-space-md pb-space-md border-b-2 border-on-surface">
        <h1 class="text-headline-sm">Tu parte (Persona {{ $parte->persona }} de {{ $totalPersonas }})</h1>
        <x-precio :centimos="$parte->importe_centimos" class="block mt-2 font-mono text-[52px] leading-none font-semibold tracking-tight" />
        <p class="mt-2 text-body-md text-secondary">Mesa {{ $mesa->numero }} · {{ $restaurante->nombre }} · IVA incluido</p>
    </section>

    {{-- Aviso de modo test: en la demo nunca se cobra de verdad --}}
    <div role="note" class="mt-space-lg pl-4 border-l-[3px] border-estado-pendiente text-body-md">
        <p class="text-label-lg">Modo de prueba: no se hará ningún cargo real</p>
        <p class="mt-1 text-on-surface-variant">Usa la tarjeta de prueba de Stripe
            <span class="font-mono font-semibold tabular-nums whitespace-nowrap">4242 4242 4242 4242</span>,
            cualquier fecha futura y cualquier CVC.</p>
    </div>

    <p class="mt-space-lg text-body-md text-on-surface-variant">
        Al pulsar el botón irás a la página segura de Stripe para escribir los datos de la tarjeta.
        Cuando termines, volverás aquí.
    </p>

    {{-- POST: crea la sesión de Stripe y nos manda a su página de pago --}}
    <form method="POST" action="{{ route('cliente.pago.pagar', $rutaMesa + ['parte' => $parte->id]) }}"
          x-data="{ enviando: false }" x-on:submit="enviando = true" class="mt-space-lg">
        @csrf
        <button type="submit" x-bind:disabled="enviando"
                class="pulsable w-full flex items-center justify-center min-h-[56px] rounded bg-primary-container text-on-primary text-label-lg font-bold shadow-nivel-2 hover:bg-primary disabled:opacity-60">
            <span x-text="enviando ? 'Abriendo Stripe…' : @js('Pagar '.number_format($parte->importe_centimos / 100, 2, ',', '.').' € de forma segura')">
                Pagar <x-precio :centimos="$parte->importe_centimos" /> de forma segura
            </span>
        </button>
    </form>

    <p class="mt-space-md text-center text-body-md text-secondary">
        Pago gestionado por Stripe. ¿Dudas con el importe? Avisa al camarero.
    </p>
</x-layouts.cliente>
