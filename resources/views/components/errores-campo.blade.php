{{--
    Errores de validación de un campo que no usa <x-campo-texto> (select, textarea...).
    Uso: <x-errores-campo nombre="categoria_id" />
--}}
@props(['nombre'])

@if ($errors->has($nombre))
    <ul {{ $attributes->merge(['class' => 'mt-1.5 space-y-1 text-body-md text-error']) }}>
        @foreach ($errors->get($nombre) as $mensajes)
            {{-- Con comodines (alergenos.*) get() devuelve listas dentro de la lista --}}
            @foreach ((array) $mensajes as $mensaje)
                <li>{{ $mensaje }}</li>
            @endforeach
        @endforeach
    </ul>
@endif
