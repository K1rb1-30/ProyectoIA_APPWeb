{{--
    Marco común de las páginas legales simples (rama 16): título, aviso de que
    es una práctica de clase y el texto (slot) con estilos de lectura.
    Uso: <x-pagina-legal titulo="Privacidad"> <h2>...</h2> <p>...</p> </x-pagina-legal>
--}}
@props(['titulo'])

<x-layouts.publico :titulo="$titulo">
    <article class="max-w-3xl mx-auto px-4 sm:px-margin-md py-space-xl">
        <h1 class="text-headline-lg">{{ $titulo }}</h1>

        <p role="note" class="mt-space-md flex gap-2 px-4 py-3 rounded border border-amber-300 bg-amber-50 text-amber-900 text-body-md">
            <x-icono nombre="info" class="text-xl" />
            <span>MesaYa es una práctica de clase (DAW, módulo M0617). Este texto explica lo que hace la aplicación con los datos; no es un texto legal revisado por un profesional.</span>
        </p>

        {{-- Estilos de los títulos, párrafos y listas del texto --}}
        <div class="mt-space-lg space-y-space-md text-body-md text-on-surface-variant
                    [&_h2]:mt-space-lg [&_h2]:text-headline-sm [&_h2]:text-on-surface
                    [&_ul]:list-disc [&_ul]:pl-6 [&_ul]:space-y-1 [&_code]:text-label-md [&_code]:text-on-surface">
            {{ $slot }}
        </div>
    </article>
</x-layouts.publico>
