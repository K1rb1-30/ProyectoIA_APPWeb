{{--
    Marco común de las páginas legales simples (rama 16, rediseño en la rama 21):
    título, aviso de que es una práctica de clase y el texto (slot) con estilos
    de lectura.
    Uso: <x-pagina-legal titulo="Privacidad"> <h2>...</h2> <p>...</p> </x-pagina-legal>

    Rediseño «Comanda impresa»: título grande, aviso como nota discreta con una
    línea arriba y abajo (sin caja amarilla) y subtítulos con una raya encima,
    para que el texto largo se pueda recorrer de un vistazo.
--}}
@props(['titulo'])

<x-layouts.publico :titulo="$titulo">
    <article class="max-w-3xl mx-auto px-4 sm:px-8 py-16">
        <h1 class="text-[clamp(2.25rem,5vw,3rem)] leading-none tracking-[-0.04em] font-extrabold">{{ $titulo }}</h1>

        <p role="note" class="mt-space-lg py-3 border-y border-outline-variant text-body-md text-secondary">
            MesaYa es una práctica de clase (DAW, módulo M0617). Este texto explica lo que hace la aplicación con los datos; no es un texto legal revisado por un profesional.
        </p>

        {{-- Estilos de los títulos, párrafos y listas del texto --}}
        <div class="mt-space-lg space-y-space-md text-body-lg text-on-surface-variant
                    [&_h2]:mt-10 [&_h2]:pt-space-md [&_h2]:border-t-2 [&_h2]:border-on-surface [&_h2]:text-headline-md [&_h2]:text-on-surface
                    [&_strong]:text-on-surface
                    [&_ul]:list-disc [&_ul]:pl-6 [&_ul]:space-y-2 [&_code]:font-mono [&_code]:text-body-md [&_code]:text-on-surface">
            {{ $slot }}
        </div>
    </article>
</x-layouts.publico>
