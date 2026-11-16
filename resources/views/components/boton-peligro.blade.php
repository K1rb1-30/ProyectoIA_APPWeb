{{--
    Botón de acción destructiva (rojo), por ejemplo "Eliminar cuenta".
    Uso: <x-boton-peligro>Eliminar</x-boton-peligro>
--}}
<button {{ $attributes->merge([
    'type' => 'submit',
    'class' => 'pulsable inline-flex items-center justify-center gap-2 min-h-[48px] px-5 py-3 rounded bg-error text-on-error text-label-lg font-bold shadow-sm transition hover:bg-on-error-container focus:outline-none focus-visible:ring-2 focus-visible:ring-error focus-visible:ring-offset-2',
]) }}>
    {{ $slot }}
</button>
