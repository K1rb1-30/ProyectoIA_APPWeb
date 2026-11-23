{{--
    Mesas del camarero (rama 5). Referencia: docs/design/DesignFrontend/camarero/mesas.
    De la maqueta solo están los contadores y el mapa de mesas. Los avisos de
    plato listo llegan con la rama 7. Desde la rama 12, cada mesa ocupada
    enlaza a su detalle (cuenta, cobro en efectivo y cierre).
--}}
<x-layouts.camarero titulo="Mesas" seccion="mesas">
    <x-mensajes-sesion />

    {{-- Contadores de mesas libres y ocupadas --}}
    <div class="grid grid-cols-2 gap-3 mb-space-lg">
        <div class="bg-surface-container-lowest rounded-lg border border-outline-variant/30 shadow-nivel-1 p-3">
            <div class="flex items-center justify-between text-label-md text-secondary">
                Libres <span class="w-2.5 h-2.5 rounded-full bg-estado-listo"></span>
            </div>
            <p class="mt-1"><span class="text-headline-lg font-bold">{{ $libres }}</span> <span class="text-body-md text-secondary">mesas</span></p>
        </div>
        <div class="bg-surface-container-lowest rounded-lg border border-outline-variant/30 shadow-nivel-1 p-3">
            <div class="flex items-center justify-between text-label-md text-secondary">
                Ocupadas <span class="w-2.5 h-2.5 rounded-full bg-primary-container"></span>
            </div>
            <p class="mt-1"><span class="text-headline-lg font-bold">{{ $ocupadas }}</span> <span class="text-body-md text-secondary">mesas</span></p>
        </div>
    </div>

    <h2 class="text-headline-sm font-bold mb-space-sm">Mapa de mesas</h2>

    @if ($mesas->isEmpty())
        <x-tarjeta>
            <p class="text-body-lg text-secondary">Todavía no hay mesas. El administrador las crea en «Mesas y QR».</p>
        </x-tarjeta>
    @else
        <div class="grid grid-cols-2 gap-3">
            @foreach ($mesas as $mesa)
                @php $libre = $mesa->estado === 'libre'; @endphp
                {{-- Borde izquierdo verde (libre) o naranja (ocupada), como en la maqueta --}}
                <article @class([
                    'flex flex-col gap-3 bg-surface-container-lowest rounded-lg border border-outline-variant/30 border-l-4 shadow-nivel-1 p-3',
                    'border-l-estado-listo' => $libre,
                    'border-l-primary-container' => ! $libre,
                ])>
                    <h3 class="text-headline-md font-extrabold uppercase">Mesa {{ $mesa->numero }}</h3>
                    <x-chip-estado :estado="$mesa->estado" class="self-start" />

                    <a href="{{ route('camarero.comanda.crear', $mesa) }}"
                       @class([
                           'pulsable mt-auto inline-flex items-center justify-center gap-1.5 min-h-[48px] px-3 rounded text-label-lg font-bold transition',
                           'bg-tertiary text-on-tertiary hover:bg-on-tertiary-fixed-variant' => $libre,
                           'bg-surface-container-lowest border-[1.5px] border-outline-variant/60 hover:border-primary-container' => ! $libre,
                       ])>
                        <x-icono :nombre="$libre ? 'person_add' : 'add_circle'" class="text-xl" />
                        {{ $libre ? 'Abrir mesa' : 'Añadir pedido' }}
                    </a>
                    {{-- Rama 12: detalle con la cuenta, el cobro en efectivo y el cierre --}}
                    @unless ($libre)
                        <a href="{{ route('camarero.mesas.detalle', $mesa) }}"
                           class="pulsable inline-flex items-center justify-center gap-1.5 min-h-[48px] px-3 rounded bg-primary-container text-on-primary text-label-lg font-bold">
                            <x-icono nombre="receipt_long" class="text-xl" /> Cuenta y cobro
                        </a>
                    @endunless
                </article>
            @endforeach
        </div>
    @endif
</x-layouts.camarero>
