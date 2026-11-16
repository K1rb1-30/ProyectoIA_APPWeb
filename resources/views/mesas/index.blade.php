{{--
    Mesas y códigos QR del restaurante (rol admin).
    Maqueta: docs/design/DesignFrontend/panelAdmin/mesas-qr/.
    Solo se pintan las mesas del restaurante del admin: el filtro lo hace el
    modelo Mesa (trait PerteneceARestaurante), no esta vista.
--}}
<x-layouts.admin titulo="Mesas y QR">
    <x-slot name="acciones">
        {{-- target="_blank": la página para imprimir se abre en otra pestaña --}}
        <a href="{{ route('mesas.imprimir') }}" target="_blank"
           class="pulsable inline-flex items-center justify-center gap-2 min-h-[48px] px-5 py-3 rounded bg-surface-container-lowest border-[1.5px] border-outline-variant/60 text-on-surface text-label-lg shadow-sm transition hover:border-primary-container">
            <x-icono nombre="picture_as_pdf" class="text-xl text-primary" />
            Descargar QR en PDF
        </a>
        <a href="{{ route('mesas.create') }}"
           class="pulsable inline-flex items-center justify-center gap-2 min-h-[48px] px-5 py-3 rounded bg-primary-container text-on-primary text-label-lg font-bold shadow-sm transition hover:bg-primary">
            <x-icono nombre="add" class="text-xl" />
            Añadir mesa
        </a>
    </x-slot>

    <div class="space-y-space-lg">
        {{-- Cabecera --}}
        <div>
            <div class="flex flex-wrap items-center gap-3">
                <h2 class="text-headline-lg font-extrabold">Mesas y códigos QR</h2>
                <span class="px-2.5 py-0.5 rounded-full text-label-md bg-primary-container/10 text-primary border border-primary-container/20">
                    {{ $mesas->count() }} {{ $mesas->count() === 1 ? 'mesa' : 'mesas' }}
                </span>
            </div>
            <p class="mt-1 text-body-md text-secondary max-w-3xl">
                Cada mesa tiene un código QR fijo. El cliente lo escanea para ver la carta y pedir desde su móvil.
            </p>
        </div>

        {{-- Mensaje tras crear, editar o borrar una mesa --}}
        @if (session('estado'))
            <div class="flex items-center gap-2 px-4 py-3 rounded bg-estado-listo/15 text-emerald-800 text-label-lg" role="status">
                <x-icono nombre="check_circle" class="text-xl" />
                {{ session('estado') }}
            </div>
        @endif

        {{-- Cifras rápidas --}}
        @php
            $libres = $mesas->where('estado', 'libre')->count();
            $ocupadas = $mesas->where('estado', 'ocupada')->count();
        @endphp
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="flex items-center gap-3 p-3.5 rounded-lg bg-surface-container-lowest border border-outline-variant/30 shadow-nivel-1">
                <div class="w-10 h-10 rounded bg-surface-container flex items-center justify-center">
                    <x-icono nombre="table_restaurant" class="text-[22px]" />
                </div>
                <div>
                    <div class="text-headline-sm font-bold">{{ $mesas->count() }} mesas</div>
                    <div class="text-label-sm text-secondary">En total</div>
                </div>
            </div>
            <div class="flex items-center gap-3 p-3.5 rounded-lg bg-surface-container-lowest border border-outline-variant/30 shadow-nivel-1">
                <div class="w-10 h-10 rounded bg-emerald-50 text-emerald-700 flex items-center justify-center">
                    <x-icono nombre="check_circle" class="text-[22px]" />
                </div>
                <div>
                    <div class="text-headline-sm font-bold text-emerald-800">{{ $libres }} libres</div>
                    <div class="text-label-sm text-secondary">Listas para sentar clientes</div>
                </div>
            </div>
            <div class="flex items-center gap-3 p-3.5 rounded-lg bg-surface-container-lowest border border-outline-variant/30 shadow-nivel-1">
                <div class="w-10 h-10 rounded bg-orange-50 text-primary-container flex items-center justify-center">
                    <x-icono nombre="groups" class="text-[22px]" />
                </div>
                <div>
                    <div class="text-headline-sm font-bold">{{ $ocupadas }} ocupadas</div>
                    <div class="text-label-sm text-secondary">Con clientes ahora mismo</div>
                </div>
            </div>
        </div>

        {{-- Cuadrícula de mesas --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
            @foreach ($mesas as $mesa)
                <div class="relative flex flex-col justify-between overflow-hidden rounded-lg bg-surface-container-lowest border border-outline-variant/30 shadow-nivel-1">
                    {{-- Barra de color a la izquierda según el estado (DESIGN.md, "Status Indicators") --}}
                    <div @class([
                        'absolute left-0 top-0 bottom-0 w-1.5',
                        'bg-estado-listo' => $mesa->estado === 'libre',
                        'bg-primary-container' => $mesa->estado === 'ocupada',
                    ])></div>

                    <div class="p-5 pl-6">
                        <div class="flex items-center justify-between gap-2">
                            <h3 class="text-headline-sm font-bold whitespace-nowrap">Mesa {{ $mesa->numero }}</h3>
                            <x-chip-estado :estado="$mesa->estado" />
                        </div>

                        <div class="my-4 flex justify-center p-4 rounded-lg bg-surface-container-low/70 border border-outline-variant/20">
                            <x-qr-mesa :mesa="$mesa" :tamano="128" />
                        </div>

                        {{-- URL fija con botón de copiar. Alpine guarda en "copiado" si ya se ha copiado --}}
                        <div class="space-y-1" x-data="{ copiado: false }">
                            <span class="text-label-sm text-secondary">URL fija del QR:</span>
                            <div class="flex items-center justify-between gap-1 px-2.5 py-1.5 rounded bg-surface-container text-xs font-mono">
                                {{-- Se muestra sin "http://" para que quepa más; se copia la URL completa. title enseña la entera al pasar el ratón --}}
                                <span class="truncate" title="{{ $mesa->urlQr() }}">{{ preg_replace('#^https?://#', '', $mesa->urlQr()) }}</span>
                                <button type="button" title="Copiar enlace" aria-label="Copiar el enlace de la mesa {{ $mesa->numero }}"
                                        class="p-0.5 text-primary hover:text-on-surface"
                                        x-on:click="navigator.clipboard.writeText(@js($mesa->urlQr())); copiado = true; setTimeout(() => copiado = false, 2000)">
                                    <span class="material-symbols-outlined text-[16px]" aria-hidden="true" x-text="copiado ? 'check' : 'content_copy'">content_copy</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Acciones: editar y borrar --}}
                    <div class="flex items-center gap-2 p-3 bg-surface-container-low/40 border-t border-outline-variant/20">
                        <a href="{{ route('mesas.edit', $mesa) }}"
                           class="flex-1 inline-flex items-center justify-center gap-1.5 min-h-[40px] px-2 rounded bg-surface-container-lowest border border-outline-variant/30 text-label-md hover:bg-surface-container">
                            <x-icono nombre="edit" class="text-[16px] text-primary" />
                            Editar
                        </a>
                        {{-- Borrar va por un formulario DELETE con @csrf. confirm() pide confirmación antes de enviarlo --}}
                        <form method="POST" action="{{ route('mesas.destroy', $mesa) }}"
                              onsubmit="return confirm('¿Seguro que quieres eliminar la mesa {{ $mesa->numero }}? Su QR dejará de funcionar.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" title="Eliminar mesa" aria-label="Eliminar la mesa {{ $mesa->numero }}"
                                    class="inline-flex items-center justify-center min-h-[40px] px-2.5 rounded bg-surface-container-lowest border border-outline-variant/30 text-secondary hover:text-error hover:bg-error-container/40">
                                <x-icono nombre="delete" class="text-[16px]" />
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach

            {{-- Tarjeta discontinua para añadir otra mesa --}}
            <a href="{{ route('mesas.create') }}"
               class="flex flex-col items-center justify-center gap-3 p-6 min-h-[320px] rounded-lg border-2 border-dashed border-primary-container/30 text-center hover:border-primary-container hover:bg-primary-container/5 transition-colors">
                <span class="w-14 h-14 rounded-full bg-primary-container/15 text-primary flex items-center justify-center">
                    <x-icono nombre="add_circle" class="text-3xl" />
                </span>
                <span class="text-headline-sm font-bold">Añadir nueva mesa</span>
                <span class="text-body-md text-secondary">Se le genera su código QR automáticamente.</span>
            </a>
        </div>
    </div>
</x-layouts.admin>
