{{--
    Cápsula con el estado de un pedido o de una mesa.
    Uso: <x-chip-estado estado="preparando" />
    Estados de los pedidos: pendiente, preparando, listo y servido.
    Estados de las mesas: libre y ocupada.

    Las clases están escritas enteras en la lista (no se montan con variables)
    porque Tailwind solo genera las clases que encuentra tal cual en el código.
--}}
@props(['estado'])

@php
    $estilos = [
        'pendiente' => ['texto' => 'Pendiente', 'fondo' => 'bg-estado-pendiente/15 text-amber-800', 'punto' => 'bg-estado-pendiente'],
        'preparando' => ['texto' => 'Preparando', 'fondo' => 'bg-estado-preparando/15 text-blue-800', 'punto' => 'bg-estado-preparando'],
        'listo' => ['texto' => 'Listo', 'fondo' => 'bg-estado-listo/15 text-emerald-800', 'punto' => 'bg-estado-listo'],
        'servido' => ['texto' => 'Servido', 'fondo' => 'bg-estado-servido/15 text-gray-700', 'punto' => 'bg-estado-servido'],
        // Mesas: libre en verde y ocupada en naranja, como en panelAdmin/mesas-qr.
        'libre' => ['texto' => 'Libre', 'fondo' => 'bg-estado-listo/15 text-emerald-800', 'punto' => 'bg-estado-listo'],
        'ocupada' => ['texto' => 'Ocupada', 'fondo' => 'bg-primary-container/15 text-on-primary-fixed-variant', 'punto' => 'bg-primary-container'],
    ];

    // Si llega un estado que no conocemos, se muestra en gris con su nombre.
    $estilo = $estilos[$estado] ?? ['texto' => ucfirst($estado), 'fondo' => 'bg-estado-servido/15 text-gray-700', 'punto' => 'bg-estado-servido'];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-label-md whitespace-nowrap '.$estilo['fondo']]) }}>
    <span class="w-2 h-2 rounded-full {{ $estilo['punto'] }}"></span>
    {{ $estilo['texto'] }}
</span>
