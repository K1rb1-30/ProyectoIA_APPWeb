{{--
    Tarjeta: superficie blanca de "nivel 1" (DESIGN.md), con borde suave,
    esquinas de 1rem y relleno interior.
    Uso: <x-tarjeta> ...contenido... </x-tarjeta>
    Se le pueden añadir clases: <x-tarjeta class="mt-4">
--}}
<div {{ $attributes->merge(['class' => 'bg-surface-container-lowest rounded-lg border border-outline-variant/30 shadow-nivel-1 p-space-md sm:p-space-lg']) }}>
    {{ $slot }}
</div>
