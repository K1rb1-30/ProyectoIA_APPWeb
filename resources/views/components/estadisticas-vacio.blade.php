{{--
    Mensaje de "no hay datos" de la pantalla de estadísticas (rama 13), en lugar
    de un gráfico vacío.
    Uso: <x-estadisticas-vacio texto="Todavía no hay ventas..." />
--}}
@props(['texto'])

<div class="flex flex-col items-center justify-center gap-2 py-space-lg text-center text-secondary">
    <x-icono nombre="monitoring" class="text-5xl" />
    <p class="text-body-md">{{ $texto }}</p>
</div>
