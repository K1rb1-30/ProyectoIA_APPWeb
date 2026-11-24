{{--
    Botón de acción secundaria: sin relleno, con borde de tinta.
    Uso: <x-boton-secundario>Cancelar</x-boton-secundario>
    Por defecto es type="button", para que no envíe formularios sin querer.
--}}
<button {{ $attributes->merge([
    'type' => 'button',
    'class' => 'pulsable inline-flex items-center justify-center gap-2 min-h-[48px] px-6 py-3 rounded bg-transparent border-[1.5px] border-on-surface text-on-surface text-label-lg transition-colors hover:bg-surface-container-lowest focus:outline-none focus-visible:ring-2 focus-visible:ring-on-surface focus-visible:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed',
]) }}>
    {{ $slot }}
</button>
