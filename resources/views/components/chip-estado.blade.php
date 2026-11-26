{{--
    Estado de un pedido o de una mesa: un punto de color y la palabra.
    Uso: <x-chip-estado estado="preparando" />
    Estados de los pedidos: pendiente, preparando, listo y servido.
    Estados de las mesas: libre y ocupada.
    Estados de la suscripción (rama 24): pendiente, activa, impagada y cancelada.
    Estados de una integración (rama 26): conectada y sin_configurar.

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
        // Suscripción del restaurante (rama 24). "pendiente" es la de arriba.
        'activa' => ['texto' => 'Activa', 'color' => 'text-tertiary', 'punto' => 'bg-estado-listo'],
        'impagada' => ['texto' => 'Impagada', 'color' => 'text-error', 'punto' => 'bg-error'],
        'cancelada' => ['texto' => 'Cancelada', 'color' => 'text-secondary', 'punto' => 'border-2 border-outline'],
        // Integraciones (rama 26): Stripe del restaurante.
        'conectada' => ['texto' => 'Conectada', 'color' => 'text-tertiary', 'punto' => 'bg-estado-listo'],
        'sin_configurar' => ['texto' => 'Sin configurar', 'color' => 'text-secondary', 'punto' => 'border-2 border-outline'],
    ];

    // Si llega un estado que no conocemos, se muestra en gris con su nombre.
    $estilo = $estilos[$estado] ?? ['texto' => ucfirst($estado), 'color' => 'text-secondary', 'punto' => 'bg-estado-servido'];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-2 text-label-md whitespace-nowrap '.$estilo['color']]) }}>
    <span class="w-2 h-2 shrink-0 rounded-full {{ $estilo['punto'] }}" aria-hidden="true"></span>
    {{ $estilo['texto'] }}
</span>
