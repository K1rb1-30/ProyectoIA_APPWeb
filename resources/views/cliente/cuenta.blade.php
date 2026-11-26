{{--
    "Pedir la cuenta y dividir" del cliente (rama 10, rediseño en la rama 23).
    Rediseño «Comanda impresa»: el total de la mesa es el dato principal (grande
    y en letra de ticket); las personas y los platos van en filas con línea, no
    en tarjetas; sin iconos decorativos.
    Variables:
      $restaurante, $mesa
      $cuenta          cuenta abierta con sus partes, o null si aún no se ha dividido
      $lineas          líneas de los pedidos que entran en la cuenta
      $totalCentimos   total de esas líneas
      $hayPedidosNuevos  true si hay pedidos hechos después de dividir
      $soloLoNuevo     true si ya hay pagos: el formulario solo divide lo nuevo (rama 12)
      $lineasFormulario, $totalFormulario  lo que divide el formulario

    El formulario usa Alpine solo para ENSEÑAR el reparto antes de enviarlo
    (mismo cálculo que DividirCuenta::repartirAPartesIguales). El reparto de
    verdad lo hace el servidor al pulsar "Dividir la cuenta".

    Fuera de la maqueta: IVA desglosado, factura con CIF y barra de progreso
    de pago. "Tarjeta" lleva al pago con Stripe (rama 11) y "Efectivo" deja la
    parte esperando al camarero (rama 12).

    Rama 26: si el restaurante no ha conectado su cuenta de Stripe en
    «Integraciones», no se ofrece «Tarjeta»: solo efectivo, y se explica.
--}}
@php
    $rutaMesa = ['slug' => $restaurante->slug, 'numero' => $mesa->numero];
    $aceptaTarjeta = $restaurante->tieneStripeConfigurado();

    // { idLinea: { cantidad, precio } }, para calcular el reparto por platos.
    // precio es el de UNA unidad: al separar una línea, cada unidad cuesta eso.
    $lineasDatos = $lineasFormulario->mapWithKeys(fn ($linea) => [$linea->id => [
        'cantidad' => $linea->cantidad,
        'precio' => $linea->precio_centimos,
    ]]);

    // Al abrir: tantas personas como partes tenga la cuenta (o 2 si no hay).
    // Al dividir solo lo nuevo, empieza en 1.
    $personasIniciales = $soloLoNuevo ? 1 : ($cuenta?->partes->count() ?: 2);

    // Al abrir en modo "por platos", todas las líneas empiezan en la persona 1,
    // sin separar. Si se separa una línea, cada unidad empieza en la persona 1.
    $asignacionesIniciales = $lineasFormulario->mapWithKeys(fn ($linea) => [$linea->id => 1]);
    $unidadesIniciales = $lineasFormulario->mapWithKeys(fn ($linea) => [$linea->id => array_fill(0, $linea->cantidad, 1)]);
@endphp

<x-layouts.cliente :restaurante="$restaurante->nombre" :mesa="$mesa->numero" seccion="cuenta"
                   :titulo="'Cuenta · '.$restaurante->nombre">

    <h1 class="text-headline-lg mb-space-md">Pedir la cuenta</h1>

    @if (session('estado'))
        <div role="status" class="mb-space-md px-4 py-3 rounded bg-tertiary-fixed text-on-tertiary-fixed-variant text-body-md">
            {{ session('estado') }}
        </div>
    @endif

    @if ($errors->any())
        <div role="alert" class="mb-space-md px-4 py-3 rounded bg-error-container text-on-error-container text-body-md">
            <ul>
                @foreach ($errors->all() as $mensaje)
                    <li>{{ $mensaje }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if ($lineas->isEmpty())
        <div class="py-space-lg">
            <p class="text-headline-sm">Todavía no hay nada que pagar en esta mesa.</p>
            <a href="{{ route('cliente.carta', $rutaMesa) }}" class="mt-space-md inline-flex items-center min-h-[48px] px-5 rounded border-[1.5px] border-on-surface text-label-lg font-bold">Ver la carta</a>
        </div>
    @else
        {{-- Total de la mesa --}}
        <section class="mb-space-lg pb-space-md border-b-2 border-on-surface" aria-label="Total de la mesa">
            <x-precio :centimos="$totalCentimos" class="block font-mono text-[52px] leading-none font-semibold tracking-tight" />
            <p class="mt-2 text-body-md text-secondary">Total mesa {{ $mesa->numero }} · IVA incluido · {{ $lineas->sum('cantidad') }} artículos</p>
        </section>

        @if ($hayPedidosNuevos)
            <div role="status" class="mb-space-md px-4 py-3 rounded bg-amber-50 text-amber-900 text-body-md">
                <span>
                    @if ($soloLoNuevo)
                        Habéis pedido algo después de dividir la cuenta. Como ya hay pagos, dividid abajo solo lo nuevo.
                    @else
                        Habéis pedido algo después de dividir la cuenta. Volved a dividirla para incluirlo.
                    @endif
                </span>
            </div>
        @endif

        {{-- Partes ya creadas --}}
        @if ($cuenta)
            <h2 class="text-headline-sm">Lo que paga cada persona</h2>
            @unless ($aceptaTarjeta)
                <p class="mt-1 text-body-md text-on-surface-variant">Este restaurante solo acepta pagos en <strong class="text-on-surface">efectivo</strong>: elige «Efectivo» y el camarero vendrá a cobrar.</p>
            @endunless
            <ul class="mb-space-xl">
                @foreach ($cuenta->partes as $parte)
                    <li class="flex flex-wrap items-center gap-x-space-md gap-y-3 py-4 border-b border-outline-variant">
                        <span class="flex-1">
                            <span class="block text-label-lg">{{ $parte->nombre() }}</span>
                            @if ($parte->estado === 'pagado')
                                <span class="block text-label-md text-tertiary">{{ $parte->textoEstado() }}</span>
                            @elseif ($parte->estado === 'pendiente_efectivo')
                                <span class="block text-label-md text-amber-800">Efectivo: el camarero vendrá a cobrar</span>
                            @else
                                <span class="block text-label-md font-normal text-secondary">Pendiente de pago</span>
                            @endif
                        </span>
                        <x-precio :centimos="$parte->importe_centimos" @class(['font-mono text-headline-sm', 'line-through text-secondary font-normal' => $parte->estado === 'pagado']) />
                        {{-- Solo se puede elegir cómo pagar una parte pendiente. --}}
                        @if ($parte->estado === 'pendiente')
                            <div @class(['w-full grid gap-2', 'grid-cols-2' => $aceptaTarjeta])>
                                @if ($aceptaTarjeta)
                                    <a href="{{ route('cliente.pago.tarjeta', $rutaMesa + ['parte' => $parte->id]) }}"
                                       class="pulsable flex items-center justify-center min-h-[48px] rounded bg-on-surface text-surface-container-lowest text-label-lg font-bold">
                                        Tarjeta
                                    </a>
                                @endif
                                {{-- Formulario POST porque cambia el estado (pendiente → pendiente_efectivo) --}}
                                <form method="POST" action="{{ route('cliente.pago.efectivo.elegir', $rutaMesa + ['parte' => $parte->id]) }}">
                                    @csrf
                                    <button type="submit" class="pulsable w-full min-h-[48px] rounded border-[1.5px] border-on-surface text-label-lg font-bold">
                                        Efectivo
                                    </button>
                                </form>
                            </div>
                        @elseif ($parte->estado === 'pendiente_efectivo')
                            <a href="{{ route('cliente.pago.efectivo', $rutaMesa + ['parte' => $parte->id]) }}"
                               class="inline-flex items-center min-h-[44px] text-label-md underline underline-offset-2">Ver el aviso de pago en efectivo</a>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif

        {{-- Formulario para dividir (o volver a dividir, o dividir solo lo nuevo).
             Si ya hay pagos y no hay nada nuevo, no se enseña: no hay nada que dividir. --}}
        @if ($lineasFormulario->isNotEmpty())
        <form method="POST" action="{{ route('cliente.cuenta.dividir', $rutaMesa) }}"
              x-data="{
                  modo: @js(old('modo', 'iguales')),
                  personas: {{ (int) old('personas', $personasIniciales) }},
                  total: {{ $totalFormulario }},
                  lineas: @js((object) $lineasDatos->all()),
                  // Persona de cada línea entera: { idLinea: persona }.
                  asignaciones: @js((object) $asignacionesIniciales->all()),
                  // Persona de cada unidad, para las líneas separadas: { idLinea: [persona, persona...] }.
                  unidades: @js((object) $unidadesIniciales->all()),
                  // Líneas que se reparten por unidades: { idLinea: true }.
                  separadas: {},
                  enviando: false,
                  // Igual que DividirCuenta::repartirAPartesIguales en PHP.
                  partesIguales() {
                      const base = Math.floor(this.total / this.personas);
                      const partes = Array(this.personas).fill(base);
                      partes[0] += this.total % this.personas;
                      return partes;
                  },
                  // { persona: céntimos } según los platos asignados.
                  // Persona 0 = «Todos» (rama 14): se suma aparte y se reparte
                  // entre todas, igual que importesPorPlatos() en PHP.
                  // Una línea separada suma una unidad (su precio) a cada persona elegida.
                  partesPorPlatos() {
                      const suma = {};
                      let compartido = 0;
                      const anadir = (persona, centimos) => {
                          if (persona === 0) compartido += centimos;
                          else suma[persona] = (suma[persona] ?? 0) + centimos;
                      };
                      for (const [id, linea] of Object.entries(this.lineas)) {
                          if (this.separadas[id]) {
                              this.unidades[id].forEach((persona) => anadir(persona, linea.precio));
                          } else {
                              anadir(this.asignaciones[id], linea.cantidad * linea.precio);
                          }
                      }
                      if (compartido > 0) {
                          const base = Math.floor(compartido / this.personas);
                          for (let persona = 1; persona <= this.personas; persona++) {
                              const parte = base + (persona === 1 ? compartido % this.personas : 0);
                              suma[persona] = (suma[persona] ?? 0) + parte;
                          }
                      }
                      return suma;
                  },
                  sumar() { if (this.personas < 20) this.personas++ },
                  restar() {
                      if (this.personas > 1) this.personas--;
                      // Si una línea o una unidad estaba en una persona que ya no existe, pasa a la última.
                      for (const linea in this.asignaciones) {
                          if (this.asignaciones[linea] > this.personas) this.asignaciones[linea] = this.personas;
                          this.unidades[linea] = this.unidades[linea].map((p) => Math.min(p, this.personas));
                      }
                  },
                  euros(c) { return (c / 100).toLocaleString('es-ES', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' €' },
              }"
              x-on:submit="enviando = true">
            @csrf
            <input type="hidden" name="modo" :value="modo">

            <h2 class="text-headline-sm mb-space-sm">{{ $soloLoNuevo ? 'Dividir lo pedido después' : ($cuenta ? 'Volver a dividir' : '¿Cómo pagáis?') }}</h2>

            {{-- Selector de modo --}}
            {{-- Dos botones pegados con borde de tinta: el elegido va relleno --}}
            <div class="grid grid-cols-2 mb-space-md rounded border-[1.5px] border-on-surface overflow-hidden" role="tablist">
                <button type="button" role="tab" x-on:click="modo = 'iguales'" :aria-selected="modo === 'iguales'"
                        :class="modo === 'iguales' ? 'bg-on-surface text-surface-container-lowest' : 'text-on-surface'"
                        class="min-h-[48px] text-label-lg">
                    A partes iguales
                </button>
                <button type="button" role="tab" x-on:click="modo = 'platos'" :aria-selected="modo === 'platos'"
                        :class="modo === 'platos' ? 'bg-on-surface text-surface-container-lowest' : 'text-on-surface'"
                        class="min-h-[48px] text-label-lg">
                    Por platos
                </button>
            </div>

            {{-- Número de personas (sirve para los dos modos) --}}
            <div class="mb-space-md flex items-center justify-between gap-space-md">
                <div>
                    <p class="text-label-lg">Número de personas</p>
                    <p class="text-body-md text-secondary" x-show="modo === 'iguales'">Divide <span x-text="euros(total)"></span> a partes iguales</p>
                    <p class="text-body-md text-secondary" x-show="modo === 'platos'" x-cloak>Elige quién paga cada plato</p>
                </div>
                <div class="inline-flex items-center shrink-0 rounded border-[1.5px] border-on-surface">
                    <button type="button" x-on:click="restar()" aria-label="Una persona menos" class="w-12 h-12 text-headline-sm">−</button>
                    <span class="w-16 text-center">
                        <span class="block font-mono text-headline-sm tabular-nums" x-text="personas"></span>
                        <span class="block text-label-sm font-normal text-secondary">personas</span>
                    </span>
                    <button type="button" x-on:click="sumar()" aria-label="Una persona más" class="w-12 h-12 bg-on-surface text-surface-container-lowest text-headline-sm">+</button>
                </div>
                <input type="hidden" name="personas" :value="personas">
            </div>

            {{-- A partes iguales: vista previa del reparto --}}
            <div x-show="modo === 'iguales'">
                {{-- Vista previa del reparto, en letra de ticket --}}
                <div class="mb-space-md px-4 py-3 bg-surface-container-lowest text-body-md">
                    <span class="block text-label-md font-normal text-secondary">Reparto (el céntimo que sobra va a la persona 1)</span>
                    <span class="block font-mono" x-text="partesIguales().map(c => euros(c)).join(' + ') + ' = ' + euros(total)"></span>
                </div>
            </div>

            {{-- Por platos: cada línea con un selector de persona. Las líneas de
                 varias unidades (2× Café) se pueden «Separar» para elegir quién
                 paga cada unidad. --}}
            <div x-show="modo === 'platos'" x-cloak class="mb-space-md border-t border-outline-variant">
                @foreach ($lineasFormulario as $linea)
                    <div class="py-3 border-b border-outline-variant">
                        <div class="flex items-center justify-between gap-space-md">
                            <span class="min-w-0">
                                <span class="block text-label-lg">{{ $linea->cantidad }}× {{ $linea->plato?->nombre ?? 'Plato' }}</span>
                                <x-precio :centimos="$linea->subtotalCentimos()" class="font-mono text-body-md text-secondary" />
                                @if ($linea->cantidad > 1)
                                    <button type="button" x-on:click="separadas[{{ $linea->id }}] = ! separadas[{{ $linea->id }}]"
                                            class="block min-h-[44px] text-label-md underline underline-offset-2"
                                            x-text="separadas[{{ $linea->id }}] ? 'Juntar' : 'Separar las {{ $linea->cantidad }} unidades'"></button>
                                @endif
                            </span>
                            {{-- Una persona para toda la línea (cuando no está separada) --}}
                            <label class="shrink-0" x-show="! separadas[{{ $linea->id }}]">
                                <span class="sr-only">Quién paga {{ $linea->plato?->nombre }}</span>
                                {{-- name solo en modo platos y sin separar: si no, no se envía --}}
                                <select :name="modo === 'platos' && ! separadas[{{ $linea->id }}] ? 'asignaciones[{{ $linea->id }}][0]' : null"
                                        x-model.number="asignaciones[{{ $linea->id }}]"
                                        class="min-h-[44px] pl-3 pr-9 rounded border-[1.5px] border-secondary-fixed-dim text-label-md focus:border-primary-container focus:ring-1 focus:ring-primary-container">
                                    {{-- 0 = plato compartido entre todos (rama 14) --}}
                                    <option value="0" :selected="asignaciones[{{ $linea->id }}] === 0">Todos (compartido)</option>
                                    <template x-for="numero in personas" :key="numero">
                                        <option :value="numero" x-text="'Persona ' + numero" :selected="asignaciones[{{ $linea->id }}] == numero"></option>
                                    </template>
                                </select>
                            </label>
                        </div>

                        {{-- Separada: una fila por unidad, cada una con su persona --}}
                        @if ($linea->cantidad > 1)
                            <div x-show="separadas[{{ $linea->id }}]" x-cloak class="mt-2 pl-space-md border-l-2 border-outline-variant">
                                @for ($unidad = 0; $unidad < $linea->cantidad; $unidad++)
                                    <div class="flex items-center justify-between gap-space-md py-1">
                                        <span class="text-body-md">
                                            {{ $linea->plato?->nombre ?? 'Plato' }} {{ $unidad + 1 }} de {{ $linea->cantidad }}
                                            · <x-precio :centimos="$linea->precio_centimos" class="font-mono" />
                                        </span>
                                        <label class="shrink-0">
                                            <span class="sr-only">Quién paga la unidad {{ $unidad + 1 }} de {{ $linea->plato?->nombre }}</span>
                                            <select :name="modo === 'platos' && separadas[{{ $linea->id }}] ? 'asignaciones[{{ $linea->id }}][{{ $unidad }}]' : null"
                                                    x-model.number="unidades[{{ $linea->id }}][{{ $unidad }}]"
                                                    class="min-h-[44px] pl-3 pr-9 rounded border-[1.5px] border-secondary-fixed-dim text-label-md focus:border-primary-container focus:ring-1 focus:ring-primary-container">
                                                <option value="0" :selected="unidades[{{ $linea->id }}][{{ $unidad }}] === 0">Todos (compartido)</option>
                                                <template x-for="numero in personas" :key="numero">
                                                    <option :value="numero" x-text="'Persona ' + numero" :selected="unidades[{{ $linea->id }}][{{ $unidad }}] == numero"></option>
                                                </template>
                                            </select>
                                        </label>
                                    </div>
                                @endfor
                            </div>
                        @endif
                    </div>
                @endforeach

                {{-- Vista previa: cuánto paga cada persona --}}
                <div class="mt-space-md px-4 py-3 bg-surface-container-lowest text-body-md">
                    <template x-for="[persona, centimos] in Object.entries(partesPorPlatos())" :key="persona">
                        <p class="flex justify-between"><span x-text="'Persona ' + persona"></span><span class="font-mono" x-text="euros(centimos)"></span></p>
                    </template>
                </div>
            </div>

            <button type="submit" x-bind:disabled="enviando"
                    class="pulsable w-full flex items-center justify-center min-h-[56px] rounded bg-primary-container text-on-primary text-label-lg font-bold shadow-nivel-2 hover:bg-primary disabled:opacity-60">
                <span x-text="enviando ? 'Dividiendo…' : @js($soloLoNuevo ? 'Dividir lo nuevo' : ($cuenta ? 'Volver a dividir la cuenta' : 'Dividir la cuenta'))"></span>
            </button>
        </form>
        @endif
    @endif
</x-layouts.cliente>
