{{--
    Mensaje de "no hay datos" de la pantalla de estadísticas (rama 13), en lugar
    de un gráfico vacío. Rediseño (rama 22): solo el texto, sin icono grande.
    Uso: <x-estadisticas-vacio texto="Todavía no hay ventas..." />
--}}
@props(['texto'])

<p class="py-space-xl text-center text-body-md text-secondary">{{ $texto }}</p>
