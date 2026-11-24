{{--
    Mesas del camarero (rama 5, rediseño en la rama 20).
    Cada mesa ocupada enlaza a su detalle (cuenta, cobro en efectivo y cierre)
    y a añadir un pedido; cada mesa libre, a abrirla con una comanda.

    Rediseño «Comanda impresa»: los contadores van en una sola línea y el
    protagonista es el mapa. Las mesas ocupadas son de tinta (oscuras) y las
    libres, blancas con borde: se distinguen de un vistazo por la claridad,
    no solo por el color.
--}}
<x-layouts.camarero titulo="Mesas" seccion="mesas">
    <x-mensajes-sesion />

    {{-- Contadores de mesas ocupadas y libres, en una línea --}}
    <p class="mb-space-md text-body-md text-on-surface-variant">
        <span class="font-mono font-semibold text-on-surface">{{ $ocupadas }}</span> {{ $ocupadas === 1 ? 'ocupada' : 'ocupadas' }} ·
        <span class="font-mono font-semibold text-on-surface">{{ $libres }}</span> {{ $libres === 1 ? 'libre' : 'libres' }}
    </p>

    @if ($mesas->isEmpty())
        <x-tarjeta>
            <p class="text-body-lg text-secondary">Todavía no hay mesas. El administrador las crea en «Mesas y QR».</p>
        </x-tarjeta>
    @else
        <h2 class="sr-only">Mapa de mesas</h2>
        <div class="grid grid-cols-2 gap-2">
            @foreach ($mesas as $mesa)
                @php $libre = $mesa->estado === 'libre'; @endphp
                <article @class([
                    'flex flex-col gap-space-sm p-3 rounded',
                    'bg-surface-container-lowest border-[1.5px] border-secondary-fixed-dim' => $libre,
                    'bg-inverse-surface text-inverse-on-surface' => ! $libre,
                ])>
                    {{-- Número y estado uno encima del otro: así "Mesa 12" nunca se parte en dos líneas --}}
                    <div>
                        <h3 class="text-headline-md whitespace-nowrap">Mesa {{ $mesa->numero }}</h3>
                        <span @class([
                            'text-label-md',
                            'text-secondary' => $libre,
                            'text-secondary-fixed-dim' => ! $libre,
                        ])>{{ $libre ? 'Libre' : 'Ocupada' }}</span>
                    </div>

                    @if ($libre)
                        <a href="{{ route('camarero.comanda.crear', $mesa) }}"
                           class="pulsable mt-auto inline-flex items-center justify-center min-h-[48px] px-3 rounded border-[1.5px] border-on-surface text-label-lg font-bold hover:bg-surface-container-low">
                            Abrir mesa
                        </a>
                    @else
                        {{-- Rama 12: detalle con la cuenta, el cobro en efectivo y el cierre --}}
                        <a href="{{ route('camarero.mesas.detalle', $mesa) }}"
                           class="pulsable mt-auto inline-flex items-center justify-center min-h-[48px] px-3 rounded bg-surface-container-lowest text-on-surface text-label-lg font-bold hover:bg-surface-container-low">
                            Cuenta y cobro
                        </a>
                        <a href="{{ route('camarero.comanda.crear', $mesa) }}"
                           class="inline-flex items-center justify-center min-h-[44px] text-label-lg underline underline-offset-4 decoration-inverse-on-surface/40 hover:decoration-inverse-on-surface">
                            Añadir pedido
                        </a>
                    @endif
                </article>
            @endforeach
        </div>
    @endif
</x-layouts.camarero>
