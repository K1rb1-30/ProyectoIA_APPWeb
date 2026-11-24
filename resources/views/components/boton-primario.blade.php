{{--
    Botón de la acción principal (naranja pimentón).
    Uso: <x-boton-primario>Guardar</x-boton-primario>
    Por defecto es type="submit"; se puede cambiar con type="button".
    Cualquier otro atributo (class, x-on:click, disabled...) se pasa al <button>.
    min-h-[48px] es la altura mínima táctil que pide DESIGN.md.
    Norma del diseño: un solo botón primario por pantalla, para la acción
    principal. El resto de acciones usan x-boton-secundario o un enlace.
--}}
<button {{ $attributes->merge([
    'type' => 'submit',
    'class' => 'pulsable inline-flex items-center justify-center gap-2 min-h-[48px] px-6 py-3 rounded bg-primary-container text-on-primary text-label-lg font-bold transition-colors hover:bg-primary focus:outline-none focus-visible:ring-2 focus-visible:ring-on-surface focus-visible:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed',
]) }}>
    {{ $slot }}
</button>
