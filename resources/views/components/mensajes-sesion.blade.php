{{--
    Mensajes de una sola vez que deja el controlador con ->with(...):
    - "estado": algo ha salido bien (verde).
    - "aviso":  algo no se ha podido hacer (ámbar).
    Uso: <x-mensajes-sesion />  (ya está puesto en el layout de admin)
--}}
@if (session('estado'))
    <div role="status" class="flex items-center gap-2 mb-space-md px-4 py-3 rounded bg-tertiary-fixed/40 text-on-tertiary-fixed-variant text-body-md">
        <x-icono nombre="check_circle" class="text-xl" />
        {{ session('estado') }}
    </div>
@endif

@if (session('aviso'))
    <div role="alert" class="flex items-center gap-2 mb-space-md px-4 py-3 rounded bg-amber-50 text-amber-900 text-body-md">
        <x-icono nombre="warning" class="text-xl" />
        {{ session('aviso') }}
    </div>
@endif
