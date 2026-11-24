{{--
    Mesas y códigos QR del restaurante (rol admin). Rediseño en la rama 22.
    Solo se pintan las mesas del restaurante del admin: el filtro lo hace el
    modelo Mesa (trait PerteneceARestaurante), no esta vista.

    Rediseño «Comanda impresa»: las cifras van como subtítulo (antes eran tres
    tarjetas con icono); las mesas son celdas de una cuadrícula separadas por
    líneas finas, con el número en grande y en letra de ticket.
--}}
@php
    $libres = $mesas->where('estado', 'libre')->count();
    $ocupadas = $mesas->where('estado', 'ocupada')->count();
@endphp

<x-layouts.admin titulo="Mesas y QR">
    <x-slot name="subtitulo">
        <span class="font-mono">{{ $mesas->count() }}</span> {{ $mesas->count() === 1 ? 'mesa' : 'mesas' }} ·
        <span class="font-mono">{{ $libres }}</span> {{ $libres === 1 ? 'libre' : 'libres' }} ·
        <span class="font-mono">{{ $ocupadas }}</span> con clientes
    </x-slot>
    <x-slot name="acciones">
        {{-- target="_blank": la página para imprimir se abre en otra pestaña --}}
        <a href="{{ route('admin.mesas.imprimir') }}" target="_blank"
           class="pulsable inline-flex items-center justify-center min-h-[48px] px-5 rounded border-[1.5px] border-on-surface text-label-lg hover:bg-surface-container-lowest">
            Descargar QR en PDF
        </a>
        <a href="{{ route('admin.mesas.crear') }}"
           class="pulsable inline-flex items-center justify-center min-h-[48px] px-6 rounded bg-primary-container text-on-primary text-label-lg font-bold hover:bg-primary">
            Añadir mesa
        </a>
    </x-slot>

    <p class="mb-space-lg max-w-3xl text-body-md text-secondary">
        Cada mesa tiene un código QR fijo. El cliente lo escanea para ver la carta y pedir desde su móvil.
    </p>

    {{-- Cuadrícula de mesas: gap-px sobre fondo gris = líneas finas entre celdas --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-px bg-outline-variant border border-outline-variant">
        @foreach ($mesas as $mesa)
            <article class="flex flex-col gap-space-md p-space-lg bg-surface-container-lowest">
                <div class="flex items-start justify-between gap-2">
                    {{-- Se ve solo el número en grande; el lector de pantalla lee «Mesa 11» --}}
                    <h2 class="font-mono text-[40px] leading-none font-semibold">
                        <span class="sr-only">Mesa {{ $mesa->numero }}</span>
                        <span aria-hidden="true">{{ $mesa->numero }}</span>
                    </h2>
                    <x-chip-estado :estado="$mesa->estado" />
                </div>

                <x-qr-mesa :mesa="$mesa" :tamano="112" class="self-start border border-outline-variant" />

                {{-- URL fija con botón de copiar. Alpine guarda en "copiado" si ya se ha copiado --}}
                <div class="flex items-center gap-1 min-w-0" x-data="{ copiado: false }">
                    {{-- Se muestra sin "http://" para que quepa más; se copia la URL completa. title enseña la entera al pasar el ratón --}}
                    <span class="flex-1 truncate font-mono text-label-md font-normal text-secondary" title="{{ $mesa->urlQr() }}">{{ preg_replace('#^https?://#', '', $mesa->urlQr()) }}</span>
                    <button type="button" aria-label="Copiar el enlace de la mesa {{ $mesa->numero }}"
                            class="shrink-0 inline-flex items-center min-h-[44px] px-1 text-label-md underline underline-offset-2 hover:text-primary"
                            x-on:click="navigator.clipboard.writeText(@js($mesa->urlQr())); copiado = true; setTimeout(() => copiado = false, 2000)"
                            x-text="copiado ? 'Copiado' : 'Copiar'">Copiar</button>
                </div>

                {{-- Acciones: editar y borrar --}}
                <div class="mt-auto flex items-center gap-space-md pt-space-sm border-t border-outline-variant">
                    <a href="{{ route('admin.mesas.editar', $mesa) }}" class="inline-flex items-center min-h-[44px] text-label-lg underline underline-offset-4 hover:text-primary">
                        Editar<span class="sr-only"> la mesa {{ $mesa->numero }}</span>
                    </a>
                    {{-- Borrar va por un formulario DELETE con @csrf. confirm() pide confirmación antes de enviarlo --}}
                    <form method="POST" action="{{ route('admin.mesas.borrar', $mesa) }}" class="ml-auto"
                          onsubmit="return confirm('¿Seguro que quieres eliminar la mesa {{ $mesa->numero }}? Su QR dejará de funcionar.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" aria-label="Eliminar la mesa {{ $mesa->numero }}"
                                class="inline-flex items-center min-h-[44px] text-label-md text-secondary hover:text-error">
                            Eliminar
                        </button>
                    </form>
                </div>
            </article>
        @endforeach

        {{-- Celda para añadir otra mesa --}}
        <a href="{{ route('admin.mesas.crear') }}"
           class="flex flex-col items-start justify-end gap-1 min-h-[280px] p-space-lg bg-background hover:bg-surface-container-low transition-colors">
            <span class="font-mono text-[40px] leading-none text-secondary" aria-hidden="true">+</span>
            <span class="text-headline-sm">Añadir nueva mesa</span>
            <span class="text-body-md text-secondary">Se le genera su código QR automáticamente.</span>
        </a>
    </div>
</x-layouts.admin>
