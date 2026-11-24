{{--
    Estado de un pedido o de una mesa: un punto de color y la palabra.
    Uso: <x-chip-estado estado="preparando" />
    Estados de los pedidos: pendiente, preparando, listo y servido.
    Estados de las mesas: libre y ocupada.

    Rediseño: ya no es una cápsula con fondo de color. Es "punto + palabra",
    así pesa menos visualmente y el estado no depende solo del color
    (la palabra siempre está, para quien no distinga bien los colores).

    Las clases están escritas enteras en la lista (no se montan con variables)
    porque Tailwind solo genera las clases que encuentra tal cual en el código.
--}}
@props(['estado'])

@php
    $estilos = [
        'pendiente' => ['texto' => 'Pendiente', 'color' => 'text-amber-800', 'punto' => 'bg-estado-pendiente'],
        'preparando' => ['texto' => 'Preparando', 'color' => 'text-blue-700', 'punto' => 'bg-estado-preparando'],
        'listo' => ['texto' => 'Listo', 'color' => 'text-tertiary', 'punto' => 'bg-estado-listo'],
        // Servido: punto hueco (solo borde), porque el pedido ya no necesita atención.
        'servido' => ['texto' => 'Servido', 'color' => 'text-secondary', 'punto' => 'border-2 border-estado-servido'],
        // Mesas: libre con punto hueco, ocupada con punto de tinta.
        'libre' => ['texto' => 'Libre', 'color' => 'text-secondary', 'punto' => 'border-2 border-outline'],
        'ocupada' => ['texto' => 'Ocupada', 'color' => 'text-on-surface', 'punto' => 'bg-on-surface'],
    ];

    // Si llega un estado que no conocemos, se muestra en gris con su nombre.
    $estilo = $estilos[$estado] ?? ['texto' => ucfirst($estado), 'color' => 'text-secondary', 'punto' => 'bg-estado-servido'];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-2 text-label-md whitespace-nowrap '.$estilo['color']]) }}>
    <span class="w-2 h-2 shrink-0 rounded-full {{ $estilo['punto'] }}" aria-hidden="true"></span>
    {{ $estilo['texto'] }}
</span>
