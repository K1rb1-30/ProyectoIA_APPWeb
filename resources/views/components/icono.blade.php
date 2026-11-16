{{--
    Icono de Material Symbols.
    Uso: <x-icono nombre="mail" class="text-xl" />
    El nombre es el del icono en https://fonts.google.com/icons (por ejemplo "mail").
    El tamaño se cambia con las clases de texto de Tailwind (text-lg, text-xl...).
    aria-hidden evita que los lectores de pantalla lean el nombre del icono.
--}}
@props(['nombre'])

<span {{ $attributes->merge(['class' => 'material-symbols-outlined align-middle']) }} aria-hidden="true">{{ $nombre }}</span>
