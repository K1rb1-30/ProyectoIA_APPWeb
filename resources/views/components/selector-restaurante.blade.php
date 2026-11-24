{{--
    Desplegable para cambiar de restaurante. Solo lo ven las cuentas que
    pueden hacerlo (equipo y profesor): User::puedeCambiarDeRestaurante().
    Uso: <x-selector-restaurante />  (está en el menú lateral del admin)

    Al elegir otro restaurante, el formulario se envía solo (onchange) y el
    servidor cambia el restaurante de tu usuario. Después ves solo los datos
    de ese restaurante, como cualquier admin suyo.
--}}
@php($usuario = Auth::user())

@if ($usuario?->puedeCambiarDeRestaurante())
    <form method="POST" action="{{ route('panel.restaurante') }}" {{ $attributes }}>
        @csrf
        <label for="restaurante_id" class="sr-only">Restaurante</label>
        <select id="restaurante_id" name="restaurante_id" onchange="this.form.submit()"
                class="w-full min-h-[44px] py-2 pl-3 pr-9 rounded border-outline-variant bg-surface-container-lowest text-body-md text-on-surface focus:border-primary-container focus:ring-1 focus:ring-primary-container">
            @foreach (\App\Models\Restaurante::orderBy('nombre')->get() as $restaurante)
                <option value="{{ $restaurante->id }}" @selected($restaurante->id === $usuario->restaurante_id)>{{ $restaurante->nombre }}</option>
            @endforeach
        </select>
        {{-- Sin JavaScript también funciona: --}}
        <noscript><button type="submit" class="mt-1 text-label-md underline">Cambiar</button></noscript>
    </form>
@endif
