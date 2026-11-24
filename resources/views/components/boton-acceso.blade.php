{{--
    Botón de acceso de las páginas públicas (rama 16, rediseño en la rama 21).
    - Sin sesión: «Acceso del personal» → login (botón con borde de tinta).
    - Con sesión: «Ir a mi pantalla» → la pantalla de su rol (User::rutaDeInicio():
      admin → panel, camarero → mesas, cocina → pedidos). Botón de tinta relleno.
    Uso: <x-boton-acceso :usuario="Auth::user()" />
--}}
@props(['usuario' => null])

@if ($usuario)
    <a href="{{ route($usuario->rutaDeInicio()) }}"
       {{ $attributes->merge(['class' => 'pulsable inline-flex items-center justify-center min-h-[44px] px-4 rounded bg-on-surface text-surface-container-lowest text-label-lg font-bold hover:bg-on-surface-variant']) }}>
        Ir a mi pantalla
    </a>
@else
    <a href="{{ route('login') }}"
       {{ $attributes->merge(['class' => 'pulsable inline-flex items-center justify-center min-h-[44px] px-4 rounded border-[1.5px] border-on-surface text-label-lg font-bold hover:bg-surface-container-lowest']) }}>
        Acceso del personal
    </a>
@endif
