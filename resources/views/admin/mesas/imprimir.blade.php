{{--
    Página para imprimir los QR de todas las mesas o guardarlos como PDF
    (decisión del apartado 3 del plan: sin librería de PDF).

    Al abrirse, Alpine llama a window.print() y el navegador muestra su
    diálogo de imprimir, donde se puede elegir "Guardar como PDF".
    Las clases print:... solo se aplican al imprimir: print:hidden esconde la
    barra de botones y break-inside-avoid evita que un QR quede partido entre
    dos hojas.
--}}
<x-layouts.base titulo="QR de las mesas" class="bg-white">
    <div x-data x-init="window.print()" class="max-w-[1280px] mx-auto p-margin-md">
        <div class="print:hidden flex flex-wrap items-center justify-between gap-3 mb-space-lg p-4 rounded-lg bg-surface-container-low">
            <p class="text-body-md text-secondary">
                En el diálogo de imprimir, elige <strong>«Guardar como PDF»</strong> como impresora para descargar el archivo.
            </p>
            <div class="flex gap-2">
                <x-boton-secundario x-on:click="window.close()">Cerrar</x-boton-secundario>
                <x-boton-primario type="button" x-on:click="window.print()">
                    <x-icono nombre="print" class="text-xl" />
                    Imprimir o guardar como PDF
                </x-boton-primario>
            </div>
        </div>

        @if ($mesas->isEmpty())
            <p class="text-body-lg">Todavía no hay mesas. Créalas desde el panel de Mesas y QR.</p>
        @endif

        {{-- Una columna en el móvil, dos o tres en pantallas grandes y siempre dos en el papel --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 print:grid-cols-2 gap-6">
            @foreach ($mesas as $mesa)
                <div class="break-inside-avoid flex flex-col items-center gap-3 p-6 rounded-lg border-2 border-outline-variant/50 text-center">
                    <p class="text-label-lg text-secondary">{{ $mesa->restaurante->nombre }}</p>
                    <p class="text-display-hero">Mesa {{ $mesa->numero }}</p>
                    <x-qr-mesa :mesa="$mesa" :tamano="200" class="shadow-none" />
                    <p class="text-body-md">Escanea el código para ver la carta y pedir</p>
                    <p class="text-xs font-mono text-secondary break-all">{{ $mesa->urlQr() }}</p>
                </div>
            @endforeach
        </div>
    </div>
</x-layouts.base>
