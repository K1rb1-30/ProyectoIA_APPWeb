{{--
    Tarjeta: superficie blanca con un borde fino y esquinas pequeñas.
    Uso: <x-tarjeta> ...contenido... </x-tarjeta>
    Se le pueden añadir clases: <x-tarjeta class="mt-4">

    Rediseño: sin sombra marcada; la separación la hace el borde. Úsala para
    agrupar de verdad (un formulario, un resumen). Para listas de cosas
    parecidas (platos, pedidos) mejor filas separadas por líneas.
--}}
<div {{ $attributes->merge(['class' => 'bg-surface-container-lowest rounded border border-outline-variant shadow-nivel-1 p-space-md sm:p-space-lg']) }}>
    {{ $slot }}
</div>
