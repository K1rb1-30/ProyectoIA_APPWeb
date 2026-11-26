{{--
    Los 3 pasos del alta de un restaurante (rama 25). Los textos están solo
    aquí, así la landing y el registro dicen siempre lo mismo.

    Uso:
        <x-pasos-alta />                        grande, en la landing (como «Cómo funciona»)
        <x-pasos-alta compacto :actual="1" />    pequeño, encima del formulario de registro

    - compacto: una fila de tres pasos cortos, en letra pequeña.
    - actual:   el paso en el que está la persona (1, 2 o 3). Va en tinta y
                con aria-current; los demás, en gris.

    «Comanda impresa»: lista numerada 01, 02, 03 en letra de ticket, con líneas
    entre pasos (la misma estructura que «Cómo funciona» de inicio.blade.php).
--}}
@props(['compacto' => false, 'actual' => null])

@php
    $pasos = [
        [
            'titulo' => 'Datos del restaurante',
            'corto' => 'Tus datos',
            'texto' => 'Nombre del restaurante, tu nombre, tu correo y una contraseña. Con ese correo entrarás después como administrador.',
        ],
        [
            'titulo' => 'Pago en Stripe',
            'corto' => 'Pago',
            'texto' => 'Pagas la suscripción en la página de pago de Stripe. Los datos de la tarjeta no pasan por MesaYa.',
        ],
        [
            'titulo' => 'Carta, mesas y equipo',
            'corto' => 'Preparar',
            'texto' => 'Desde tu panel creas la carta, las mesas con su QR, las cuentas de tus camareros y emparejas las tablets de cocina.',
        ],
    ];
@endphp

@if ($compacto)
    <ol {{ $attributes->merge(['class' => 'grid grid-cols-3 border-t border-outline-variant']) }} aria-label="Pasos del alta">
        @foreach ($pasos as $indice => $paso)
            @php $esActual = $actual === $indice + 1; @endphp
            <li @class([
                'pt-2 border-t-2 -mt-px',
                'border-on-surface text-on-surface' => $esActual,
                'border-transparent text-secondary' => ! $esActual,
            ]) @if ($esActual) aria-current="step" @endif>
                <span class="font-mono text-label-sm">{{ sprintf('%02d', $indice + 1) }}</span>
                <span class="block text-label-md">{{ $paso['corto'] }}</span>
            </li>
        @endforeach
    </ol>
@else
    <ol {{ $attributes }}>
        @foreach ($pasos as $indice => $paso)
            <li @class([
                'flex flex-wrap gap-x-8 gap-y-2 py-6 border-t',
                'border-on-surface' => $loop->first,
                'border-outline-variant' => ! $loop->first,
                'border-b border-b-outline-variant' => $loop->last,
            ])>
                {{-- Número del paso en letra de ticket: 01, 02, 03 --}}
                <span class="w-12 font-mono text-label-lg text-primary-container">{{ sprintf('%02d', $indice + 1) }}</span>
                <div class="flex-[1_1_320px]">
                    <h3 class="text-headline-sm">{{ $paso['titulo'] }}</h3>
                    <p class="mt-1.5 text-body-md text-on-surface-variant">{{ $paso['texto'] }}</p>
                </div>
            </li>
        @endforeach
    </ol>
@endif
