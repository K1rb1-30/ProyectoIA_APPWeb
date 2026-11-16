{{--
    Esqueleto HTML común a todas las páginas: <head>, fuentes, CSS y JS.
    Los demás layouts (admin, camarero, cocina, cliente, invitado...) lo
    envuelven, así el <head> está escrito en un solo sitio.

    Uso: <x-layouts.base titulo="Carta"> ... </x-layouts.base>
    Las clases que se le pasen se añaden al <body>.
--}}
@props(['titulo' => null])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        {{-- Token CSRF para las peticiones con JS (el polling de cocina, por ejemplo) --}}
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $titulo ? $titulo.' · ' : '' }}{{ config('app.name', 'MesaYa') }}</title>

        {{-- CSS (Tailwind + fuentes en local) y JS (Alpine) compilados por Vite --}}
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body {{ $attributes->merge(['class' => 'font-sans antialiased bg-background text-on-surface text-body-md']) }}>
        {{ $slot }}
    </body>
</html>
