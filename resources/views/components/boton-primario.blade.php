{{--
    Botón de la acción principal (naranja).
    Uso: <x-boton-primario>Guardar</x-boton-primario>
    Por defecto es type="submit"; se puede cambiar con type="button".
    Cualquier otro atributo (class, x-on:click, disabled...) se pasa al <button>.
    min-h-[48px] es la altura mínima táctil que pide DESIGN.md.
--}}
<button {{ $attributes->merge([
    'type' => 'submit',
    'class' => 'pulsable inline-flex items-center justify-center gap-2 min-h-[48px] px-5 py-3 rounded bg-primary-container text-on-primary text-label-lg font-bold shadow-sm transition hover:bg-primary focus:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed',
]) }}>
    {{ $slot }}
</button>
