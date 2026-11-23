{{--
    Detalle de una mesa para el camarero (rama 12). Maqueta: camarero/detalle-mesa.
    Variables:
      $mesa
      $cuenta               cuenta abierta con partes y pagos, o null si no se ha dividido
      $pedidos              pedidos de la cuenta y los hechos después de dividir
      $totalPedidosCentimos total de esos pedidos
      $falta                frases de lo que falta para cerrar (vacío = se puede cerrar)
      $puedeDividir         hay pedidos sin cuenta: el camarero puede dividirlos
      $soloLoNuevo          ya hay pagos: dividir solo lo pedido después

    El camarero puede dividir la cuenta (si nadie la ha pedido desde el QR),
    cobrar en efectivo con el cálculo del cambio o con el TPV del bar.
    Diferencias con la maqueta: sin comensales, "llamar comanda" ni "cambiar mesa".
--}}
@php
    $partes = $cuenta?->partes ?? collect();
    $pagadas = $partes->where('estado', 'pagado');
    $pagadoCentimos = $pagadas->sum('importe_centimos');
    $totalCuenta = $cuenta?->total_centimos ?? 0;
    $porcentaje = $totalCuenta > 0 ? (int) floor($pagadoCentimos * 100 / $totalCuenta) : 0;
@endphp

<x-layouts.camarero :titulo="'Mesa '.$mesa->numero" seccion="mesas">
    <a href="{{ route('camarero.mesas') }}" class="inline-flex items-center gap-2 mb-space-md text-label-lg text-primary">
        <x-icono nombre="arrow_back" /> Mesas
    </a>

    <x-mensajes-sesion />

    @if ($errors->any())
        <div role="alert" class="mb-space-md flex gap-2 px-4 py-3 rounded bg-error-container text-on-error-container text-body-md">
            <x-icono nombre="error" class="text-xl" />
            <ul>
                @foreach ($errors->all() as $mensaje)
                    <li>{{ $mensaje }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Cabecera de la mesa --}}
    <x-tarjeta class="mb-space-md border-l-4 border-l-primary-container">
        <div class="flex items-center justify-between gap-space-md">
            <h2 class="text-headline-md font-extrabold">Mesa {{ $mesa->numero }}</h2>
            <x-chip-estado :estado="$mesa->estado" />
        </div>
        <a href="{{ route('camarero.comanda.crear', $mesa) }}"
           class="pulsable mt-space-sm inline-flex items-center justify-center gap-1.5 w-full min-h-[48px] rounded border-[1.5px] border-primary-container/60 bg-primary-container/10 text-label-lg font-bold text-on-primary-fixed-variant">
            <x-icono nombre="add_circle" class="text-xl" /> Añadir platos
        </a>
    </x-tarjeta>

    {{-- Comandas --}}
    <x-tarjeta class="mb-space-md">
        <h2 class="flex items-center gap-2 text-headline-sm font-bold mb-space-sm">
            <x-icono nombre="room_service" class="text-primary" /> Comandas
        </h2>
        @forelse ($pedidos as $pedido)
            <div class="py-2 border-b border-outline-variant/30 last:border-0">
                <div class="flex items-center justify-between gap-2">
                    <p class="text-label-lg">Pedido #{{ $pedido->id }} <span class="text-label-sm text-secondary">· {{ $pedido->created_at->format('H:i') }}</span></p>
                    <x-chip-estado :estado="$pedido->estado" />
                </div>
                <p class="text-body-md text-secondary">
                    @foreach ($pedido->lineas as $linea)
                        <strong class="text-on-surface">{{ $linea->cantidad }}×</strong> {{ $linea->plato?->nombre ?? 'Plato' }}@if (! $loop->last), @endif
                    @endforeach
                </p>
                @if ($cuenta && $pedido->cuenta_id === null)
                    <p class="text-label-sm text-amber-700">Pedido después de dividir: todavía no está en la cuenta.</p>
                @endif
            </div>
        @empty
            <p class="text-body-md text-secondary">Esta mesa no tiene pedidos pendientes de cobro.</p>
        @endforelse
    </x-tarjeta>

    @if ($cuenta)
        {{-- Total y progreso del cobro --}}
        <x-tarjeta class="mb-space-md">
            <div class="flex items-start justify-between gap-space-md">
                <div>
                    <p class="text-label-sm uppercase text-secondary">Total mesa {{ $mesa->numero }}</p>
                    <x-precio :centimos="$totalCuenta" class="text-[34px] leading-none font-extrabold" />
                </div>
                <span class="px-3 py-1 rounded-full bg-secondary-container text-label-md">{{ $partes->count() }} partes</span>
            </div>
            <p class="mt-space-sm text-label-md text-tertiary">
                {{ $pagadas->count() }} de {{ $partes->count() }} personas pagadas · {{ $porcentaje }}%
            </p>
            <div class="mt-1 h-2 rounded-full bg-surface-container-high overflow-hidden" role="progressbar"
                 aria-valuenow="{{ $porcentaje }}" aria-valuemin="0" aria-valuemax="100">
                <div class="h-full bg-tertiary" style="width: {{ $porcentaje }}%"></div>
            </div>
            <div class="mt-1 flex justify-between text-label-sm">
                <span class="text-secondary">Pagado: <x-precio :centimos="$pagadoCentimos" /></span>
                <span class="text-primary">Pendiente: <x-precio :centimos="$totalCuenta - $pagadoCentimos" /></span>
            </div>
        </x-tarjeta>

        {{-- Partes --}}
        <h2 class="text-headline-sm font-bold mb-space-sm">División por personas</h2>
        <div class="space-y-space-sm mb-space-md">
            @foreach ($partes as $parte)
                @php $esperandoEfectivo = $parte->estado === 'pendiente_efectivo'; @endphp
                <x-tarjeta @class([
                    'border-2 border-primary-container' => $esperandoEfectivo,
                    'border-tertiary/40' => $parte->estaPagada(),
                ])>
                    <div class="flex items-center gap-space-md">
                        <span @class([
                            'flex items-center justify-center w-11 h-11 rounded-full text-label-lg',
                            'bg-tertiary-fixed/50 text-on-tertiary-container' => $parte->estaPagada(),
                            'bg-primary-container/20 text-on-primary-fixed-variant' => $esperandoEfectivo,
                            'bg-surface-container-high' => $parte->estado === 'pendiente',
                        ])>P{{ $parte->persona }}</span>
                        <div class="flex-1 min-w-0">
                            {{-- Rama 15: las partes de pedidos pagados al hacerlos se ven así --}}
                            @if ($parte->pedido_id)
                                <p class="text-label-md text-tertiary">{{ $parte->nombre() }}</p>
                            @endif
                            <x-precio :centimos="$parte->importe_centimos" class="text-headline-sm font-bold" />
                            <p @class([
                                'text-label-md',
                                'text-tertiary' => $parte->estaPagada(),
                                'text-amber-700' => ! $parte->estaPagada(),
                            ])>
                                {{ $parte->textoEstado() }}
                                {{-- Tarjeta sin referencia de Stripe = datáfono del bar --}}
                                @if ($parte->estaPagada() && $parte->metodo === 'tarjeta')
                                    {{ $parte->pagos->last()?->referencia_stripe ? '(Stripe)' : '(TPV)' }}
                                @endif
                            </p>
                        </div>
                        @if ($parte->estaPagada() && $parte->pagos->isNotEmpty())
                            <span class="text-label-sm text-secondary">{{ $parte->pagos->last()->created_at->format('H:i') }} h</span>
                        @endif
                    </div>

                    @if ($esperandoEfectivo)
                        {{-- El cliente eligió efectivo: confirmar el cobro (pendiente_efectivo → pagado) --}}
                        @include('camarero.partes.cobro-efectivo', ['mesa' => $mesa, 'parte' => $parte, 'metodo' => 'confirmar'])
                    @elseif ($parte->estado === 'pendiente')
                        {{-- El cliente no ha elegido nada: el camarero puede cobrarle en la mesa --}}
                        <div x-data="{ efectivo: false }" class="mt-space-sm">
                            <p class="text-label-sm text-secondary">Esperando a que la persona elija tarjeta o efectivo, o cóbrale aquí:</p>
                            <div class="mt-2 grid grid-cols-2 gap-2">
                                <form method="POST" action="{{ route('camarero.mesas.cobrar', [$mesa, $parte->id]) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="metodo" value="tpv">
                                    <button type="submit" class="pulsable w-full flex items-center justify-center gap-1.5 min-h-[48px] rounded border-[1.5px] border-on-surface/70 text-label-md font-bold">
                                        <x-icono nombre="contactless" class="text-xl" /> Cobrar con TPV
                                    </button>
                                </form>
                                <button type="button" x-on:click="efectivo = ! efectivo" :aria-expanded="efectivo"
                                        class="pulsable flex items-center justify-center gap-1.5 min-h-[48px] rounded border-[1.5px] border-primary-container/60 text-label-md font-bold text-on-primary-fixed-variant">
                                    <x-icono nombre="payments" class="text-xl" /> Cobrar efectivo
                                </button>
                            </div>
                            <div x-show="efectivo" x-cloak>
                                @include('camarero.partes.cobro-efectivo', ['mesa' => $mesa, 'parte' => $parte, 'metodo' => 'efectivo'])
                            </div>
                        </div>
                    @endif
                </x-tarjeta>
            @endforeach
        </div>
    @elseif ($pedidos->isNotEmpty())
        <x-tarjeta class="mb-space-md">
            <p class="text-body-md">Total de los pedidos: <x-precio :centimos="$totalPedidosCentimos" class="font-bold" /></p>
            <p class="text-body-md text-secondary">El cliente todavía no ha pedido la cuenta desde su móvil. Puedes dividirla tú aquí abajo.</p>
        </x-tarjeta>
    @endif

    {{-- El camarero divide la cuenta (o lo pedido después) a partes iguales.
         Usa el mismo DividirCuenta que el cliente. --}}
    @if ($puedeDividir)
        <x-tarjeta class="mb-space-md">
            <form method="POST" action="{{ route('camarero.mesas.dividir', $mesa) }}"
                  x-data="{ personas: 1 }" class="flex flex-wrap items-center justify-between gap-space-md">
                @csrf
                <div>
                    <p class="text-label-lg">{{ $soloLoNuevo ? 'Dividir lo pedido después' : ($cuenta ? 'Volver a dividir la cuenta' : 'Dividir la cuenta') }}</p>
                    <p class="text-body-md text-secondary">A partes iguales</p>
                </div>
                <div class="inline-flex items-center gap-1 p-1 rounded-lg border border-outline-variant/50">
                    <button type="button" x-on:click="if (personas > 1) personas--" aria-label="Una persona menos" class="w-11 h-11 rounded bg-surface-container-low text-headline-sm">−</button>
                    <span class="w-12 text-center">
                        <span class="block text-headline-sm tabular-nums" x-text="personas">1</span>
                        <span class="block text-label-sm text-secondary">personas</span>
                    </span>
                    <button type="button" x-on:click="if (personas < 20) personas++" aria-label="Una persona más" class="w-11 h-11 rounded bg-surface-container-low text-headline-sm">+</button>
                </div>
                <input type="hidden" name="personas" :value="personas" value="1">
                <button type="submit" class="pulsable w-full flex items-center justify-center gap-2 min-h-[48px] rounded-lg bg-inverse-surface text-inverse-on-surface text-label-lg font-bold">
                    <x-icono nombre="call_split" /> Dividir
                </button>
            </form>
        </x-tarjeta>
    @endif

    {{-- Cerrar mesa: desactivado mientras falte algo, con la lista de lo que falta --}}
    @if ($cuenta)
        <div class="p-space-md rounded-lg bg-surface-container-low">
            <form method="POST" action="{{ route('camarero.mesas.cerrar', $mesa) }}">
                @csrf
                <button type="submit" @disabled($falta !== [])
                        class="pulsable w-full flex items-center justify-center gap-2 min-h-[56px] rounded-lg bg-tertiary text-on-tertiary text-label-lg font-bold disabled:bg-surface-container-high disabled:text-secondary disabled:cursor-not-allowed">
                    <x-icono :nombre="$falta === [] ? 'lock_open' : 'lock'" /> Cerrar mesa y liberar
                </button>
            </form>
            @if ($falta !== [])
                <div class="mt-space-sm flex gap-2 text-body-md text-secondary">
                    <x-icono nombre="warning" class="text-lg text-amber-700" />
                    <div>
                        <p>No se puede cerrar hasta que todo esté cobrado. Falta:</p>
                        <ul class="list-disc pl-5">
                            @foreach ($falta as $frase)
                                <li>{{ $frase }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
        </div>
    @endif
</x-layouts.camarero>
