{{--
    Botón de acceso de las páginas públicas (rama 16).
    - Sin sesión: «Acceso del personal» → login.
    - Con sesión: «Ir a mi pantalla» → la pantalla de su rol (User::rutaDeInicio():
      admin → panel, camarero → mesas, cocina → pedidos).
    Uso: <x-boton-acceso :usuario="Auth::user()" />
--}}
@props(['usuario' => null])

@if ($usuario)
    <a href="{{ route($usuario->rutaDeInicio()) }}"
       {{ $attributes->merge(['class' => 'pulsable inline-flex items-center justify-center gap-2 min-h-[44px] px-4 rounded bg-primary-container text-on-primary text-label-lg font-bold shadow-sm hover:bg-primary']) }}>
        <x-icono nombre="arrow_forward" class="text-xl" /> Ir a mi pantalla
    </a>
@else
    <a href="{{ route('login') }}"
       {{ $attributes->merge(['class' => 'pulsable inline-flex items-center justify-center gap-2 min-h-[44px] px-4 rounded border-[1.5px] border-outline-variant/60 bg-surface-container-lowest text-label-lg font-bold hover:border-primary-container']) }}>
        <x-icono nombre="badge" class="text-xl text-primary" /> Acceso del personal
    </a>
@endif
