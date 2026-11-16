{{--
    Botón de acción secundaria (blanco con borde).
    Uso: <x-boton-secundario>Cancelar</x-boton-secundario>
    Por defecto es type="button", para que no envíe formularios sin querer.
--}}
<button {{ $attributes->merge([
    'type' => 'button',
    'class' => 'pulsable inline-flex items-center justify-center gap-2 min-h-[48px] px-5 py-3 rounded bg-surface-container-lowest border-[1.5px] border-outline-variant/60 text-on-surface text-label-lg shadow-sm transition hover:border-primary-container focus:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed',
]) }}>
    {{ $slot }}
</button>
