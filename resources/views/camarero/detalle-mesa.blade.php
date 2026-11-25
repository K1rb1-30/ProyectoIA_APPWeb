{{--
    Detalle de una mesa para el camarero (rama 12, rediseño en la rama 20).
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

    Rediseño «Comanda impresa»: el dato principal es lo que FALTA POR COBRAR,
    en grande y arriba. Debajo, las personas en filas; luego las comandas
    (lo menos urgente) y, al final, el botón de cerrar la mesa.
--}}
@php
    $partes = $cuenta?->partes ?? collect();
    $pagadas = $partes->where('estado', 'pagado');
    $pagadoCentimos = $pagadas->sum('importe_centimos');
    $totalCuenta = $cuenta?->total_centimos ?? 0;
    $porcentaje = $totalCuenta > 0 ? (int) floor($pagadoCentimos * 100 / $totalCuenta) : 0;
@endphp

<x-layouts.camarero :titulo="'Mesa '.$mesa->numero" seccion="mesas">
    <x-slot:acciones>
        <a href="{{ route('camarero.mesas') }}" aria-label="Volver a mesas"
           class="pulsable inline-flex items-center justify-center w-11 h-11 rounded border-[1.5px] border-secondary-fixed-dim hover:bg-surface-container-low">
            <x-icono nombre="arrow_back" />
        </a>
    </x-slot:acciones>

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

    @if ($cuenta)
        {{-- Dato principal: lo que falta por cobrar, con la barra de progreso --}}
        <section class="mb-space-lg p-space-md bg-surface-container-lowest border border-outline-variant rounded" aria-label="Resumen de la cuenta">
            <p class="text-label-md text-secondary">Falta por cobrar</p>
            <x-precio :centimos="$totalCuenta - $pagadoCentimos" class="block mt-1 font-mono text-[44px] leading-none font-semibold tracking-tight" />
            <div class="mt-space-md h-2 bg-surface-container-high overflow-hidden" role="progressbar"
                 aria-valuenow="{{ $porcentaje }}" aria-valuemin="0" aria-valuemax="100" aria-label="Porcentaje cobrado">
                <div class="h-full bg-tertiary-container" style="width: {{ $porcentaje }}%"></div>
            </div>
            <div class="mt-2 flex justify-between text-label-md font-normal text-secondary">
                <span>Pagado: <x-precio :centimos="$pagadoCentimos" class="font-mono" /> · {{ $pagadas->count() }} de {{ $partes->count() }}</span>
                <span>Total: <x-precio :centimos="$totalCuenta" class="font-mono text-on-surface" /></span>
            </div>
        </section>

        {{-- Partes: una fila por persona --}}
        {{-- Cuenta solo las personas: los pedidos pagados al pedir (rama 15) no son una persona --}}
        @php $personas = $cuenta->numeroDePersonas(); @endphp
        <h2 class="pb-2 border-b-2 border-on-surface text-headline-sm">{{ $personas > 0 ? 'Entre '.$personas.' '.($personas === 1 ? 'persona' : 'personas') : 'Pagado al pedir' }}</h2>
        <ul class="mb-space-lg">
            @foreach ($partes as $parte)
                @php $esperandoEfectivo = $parte->estado === 'pendiente_efectivo'; @endphp
                <li class="py-space-md border-b border-outline-variant">
                    <div class="flex items-start gap-space-md">
                        <div class="flex-1 min-w-0">
                            {{-- «Persona 2» o, si se pagó al pedir (rama 15), «Pedido #12 · pagado al pedir» --}}
                            <p class="text-label-lg">{{ $parte->nombre() }}</p>
                            <p @class([
                                'text-label-md',
                                'text-tertiary' => $parte->estaPagada(),
                                'text-amber-800' => $esperandoEfectivo,
                                'text-secondary' => $parte->estado === 'pendiente',
                            ])>
                                {{ $parte->textoEstado() }}
                                {{-- Tarjeta sin referencia de Stripe = datáfono del bar --}}
                                @if ($parte->estaPagada() && $parte->metodo === 'tarjeta')
                                    {{ $parte->pagos->last()?->referencia_stripe ? '(Stripe)' : '(TPV)' }}
                                @endif
                                @if ($parte->estaPagada() && $parte->pagos->isNotEmpty())
                                    <span class="font-mono font-normal text-secondary">· {{ $parte->pagos->last()->created_at->format('H:i') }}</span>
                                @endif
                            </p>
                        </div>
                        {{-- Pagada: importe tachado; pendiente: importe normal --}}
                        <x-precio :centimos="$parte->importe_centimos" @class([
                            'font-mono text-headline-sm',
                            'line-through text-secondary font-normal' => $parte->estaPagada(),
                        ]) />
                    </div>

                    @if ($esperandoEfectivo)
                        {{-- El cliente eligió efectivo: confirmar el cobro (pendiente_efectivo → pagado) --}}
                        @include('camarero.partes.cobro-efectivo', ['mesa' => $mesa, 'parte' => $parte, 'metodo' => 'confirmar'])
                    @elseif ($parte->estado === 'pendiente')
                        {{-- El cliente no ha elegido nada: el camarero puede cobrarle en la mesa --}}
                        <div x-data="{ efectivo: false }" class="mt-space-sm">
                            <p class="text-body-md text-secondary">Esperando a que la persona elija tarjeta o efectivo, o cóbrale aquí:</p>
                            <div class="mt-2 grid grid-cols-2 gap-2">
                                <form method="POST" action="{{ route('camarero.mesas.cobrar', [$mesa, $parte->id]) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="metodo" value="tpv">
                                    <button type="submit" class="pulsable w-full min-h-[48px] rounded border-[1.5px] border-on-surface text-label-lg font-bold hover:bg-surface-container-lowest">
                                        Cobrar con TPV
                                    </button>
                                </form>
                                <button type="button" x-on:click="efectivo = ! efectivo" :aria-expanded="efectivo"
                                        class="pulsable min-h-[48px] rounded border-[1.5px] border-on-surface text-label-lg font-bold hover:bg-surface-container-lowest"
                                        :class="efectivo && 'bg-on-surface text-surface-container-lowest hover:bg-on-surface'">
                                    Cobrar efectivo
                                </button>
                            </div>
                            <div x-show="efectivo" x-cloak>
                                @include('camarero.partes.cobro-efectivo', ['mesa' => $mesa, 'parte' => $parte, 'metodo' => 'efectivo'])
                            </div>
                        </div>
                    @endif
                </li>
            @endforeach
        </ul>
    @elseif ($pedidos->isNotEmpty())
        {{-- Todavía sin dividir: el total de los pedidos es el dato principal --}}
        <section class="mb-space-lg p-space-md bg-surface-container-lowest border border-outline-variant rounded">
            <p class="text-body-md">Total de los pedidos: <x-precio :centimos="$totalPedidosCentimos" class="font-mono font-semibold" /></p>
            <p class="mt-1 text-body-md text-secondary">El cliente todavía no ha pedido la cuenta desde su móvil. Puedes dividirla tú aquí abajo.</p>
        </section>
    @endif

    {{-- El camarero divide la cuenta (o lo pedido después) a partes iguales.
         Usa el mismo DividirCuenta que el cliente. --}}
    @if ($puedeDividir)
        <form method="POST" action="{{ route('camarero.mesas.dividir', $mesa) }}"
              x-data="{ personas: 1 }" class="mb-space-lg">
            @csrf
            <div class="flex items-center justify-between gap-space-md">
                <div>
                    <p class="text-label-lg">{{ $soloLoNuevo ? 'Dividir lo pedido después' : ($cuenta ? 'Volver a dividir la cuenta' : 'Dividir la cuenta') }}</p>
                    <p class="text-body-md text-secondary">A partes iguales</p>
                </div>
                <div class="inline-flex items-center rounded border-[1.5px] border-on-surface">
                    <button type="button" x-on:click="if (personas > 1) personas--" aria-label="Una persona menos" class="w-11 h-11 text-headline-sm">−</button>
                    <span class="w-14 text-center">
                        <span class="block font-mono text-label-lg tabular-nums" x-text="personas">1</span>
                        <span class="block text-label-sm font-normal text-secondary">personas</span>
                    </span>
                    <button type="button" x-on:click="if (personas < 20) personas++" aria-label="Una persona más" class="w-11 h-11 bg-on-surface text-surface-container-lowest text-headline-sm">+</button>
                </div>
            </div>
            <input type="hidden" name="personas" :value="personas" value="1">
            <button type="submit" class="pulsable mt-space-sm w-full min-h-[48px] rounded bg-on-surface text-surface-container-lowest text-label-lg font-bold hover:bg-on-surface-variant">
                Dividir
            </button>
        </form>
    @endif

    {{-- Comandas de la mesa --}}
    <div class="flex items-baseline justify-between gap-space-md pb-2 border-b-2 border-on-surface">
        <h2 class="text-headline-sm">Comandas</h2>
        <a href="{{ route('camarero.comanda.crear', $mesa) }}" class="inline-flex items-center min-h-[44px] text-label-lg underline underline-offset-4">Añadir platos</a>
    </div>
    <ul class="mb-space-lg">
        @forelse ($pedidos as $pedido)
            <li class="py-3 border-b border-outline-variant">
                <div class="flex items-center justify-between gap-2">
                    <p class="font-mono text-label-md">#{{ $pedido->id }} · {{ $pedido->created_at->format('H:i') }}</p>
                    <x-chip-estado :estado="$pedido->estado" />
                </div>
                <p class="mt-1 text-body-md text-on-surface-variant">
                    @foreach ($pedido->lineas as $linea)
                        <strong class="font-mono text-on-surface">{{ $linea->cantidad }}×</strong> {{ $linea->plato?->nombre ?? 'Plato' }}@if (! $loop->last), @endif
                    @endforeach
                </p>
                @if ($cuenta && $pedido->cuenta_id === null)
                    <p class="mt-1 text-label-md text-amber-800">Pedido después de dividir: todavía no está en la cuenta.</p>
                @endif
            </li>
        @empty
            <li class="py-3 text-body-md text-secondary">Esta mesa no tiene pedidos pendientes de cobro.</li>
        @endforelse
    </ul>

    {{-- Cerrar mesa: desactivado mientras falte algo, con la lista de lo que falta --}}
    @if ($cuenta)
        <form method="POST" action="{{ route('camarero.mesas.cerrar', $mesa) }}">
            @csrf
            <button type="submit" @disabled($falta !== [])
                    class="pulsable w-full min-h-[56px] rounded bg-tertiary-container text-on-tertiary text-label-lg font-bold hover:bg-tertiary disabled:bg-surface-container-high disabled:text-secondary disabled:cursor-not-allowed">
                Cerrar mesa y liberar
            </button>
        </form>
        @if ($falta !== [])
            <div class="mt-space-sm text-body-md text-secondary">
                <p>No se puede cerrar hasta que todo esté cobrado. Falta:</p>
                <ul class="list-disc pl-5">
                    @foreach ($falta as $frase)
                        <li>{{ $frase }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    @endif
</x-layouts.camarero>
