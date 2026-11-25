{{--
    /dispositivo en un dispositivo de CAMARERO ya emparejado: cada camarero
    toca su nombre y escribe su PIN de 4 cifras con el teclado de la pantalla.
    Variables:
      $dispositivo  el dispositivo (con su restaurante cargado)
      $camareros    camareros del restaurante (id y nombre), por orden alfabético

    Alpine guarda qué camarero se ha tocado (elegido) y las cifras escritas
    (pin). El formulario envía camarero + pin a DispositivoController::entrar,
    que comprueba el PIN y el límite de fallos.

    Rediseño «Comanda impresa»: los nombres van en filas con línea; el PIN es
    el dato principal mientras se escribe (puntos grandes); un solo botón
    naranja («Entrar»).
--}}
<x-guest-layout>
    <div class="mb-space-lg">
        <h1 class="text-headline-lg">{{ $dispositivo->restaurante->nombre }}</h1>
        <p class="mt-2 text-body-lg text-secondary">Toca tu nombre y escribe tu PIN.</p>
    </div>

    @if (session('estado'))
        <div role="status" class="mb-space-md px-4 py-3 rounded bg-tertiary-fixed text-on-tertiary-fixed-variant text-body-md">
            {{ session('estado') }}
        </div>
    @endif

    @if ($camareros->isEmpty())
        <p class="text-body-lg">Todavía no hay camareros. El administrador los crea en «Personal» con su nombre y un PIN.</p>
    @else
        <form method="POST" action="{{ route('dispositivo.entrar') }}"
              x-data="{
                  elegido: @js(old('camarero') ? (int) old('camarero') : null),
                  pin: '',
                  enviando: false,
                  pulsar(cifra) { if (this.pin.length < 4) this.pin += cifra },
                  borrar() { this.pin = this.pin.slice(0, -1) },
              }"
              x-on:submit="enviando = true">
            @csrf
            <input type="hidden" name="camarero" :value="elegido">
            <input type="hidden" name="pin" :value="pin">

            {{-- 1. Nombres --}}
            <ul class="border-t border-outline-variant" x-show="! elegido">
                @foreach ($camareros as $camarero)
                    <li class="border-b border-outline-variant">
                        <button type="button" x-on:click="elegido = {{ $camarero->id }}; pin = ''"
                                class="w-full flex items-center min-h-[56px] text-left text-headline-sm hover:text-primary">
                            {{ $camarero->name }}
                        </button>
                    </li>
                @endforeach
            </ul>

            {{-- 2. PIN del camarero elegido --}}
            <div x-show="elegido" x-cloak>
                <div class="flex items-baseline justify-between gap-space-md pb-3 border-b-2 border-on-surface">
                    <p class="text-headline-sm">
                        @foreach ($camareros as $camarero)
                            <span x-show="elegido === {{ $camarero->id }}">{{ $camarero->name }}</span>
                        @endforeach
                    </p>
                    <button type="button" x-on:click="elegido = null; pin = ''"
                            class="inline-flex items-center min-h-[44px] text-label-md underline underline-offset-2">No soy yo</button>
                </div>

                {{-- Cuatro puntos: se rellenan al escribir. aria-live lee cuántas cifras van --}}
                <div class="flex justify-center gap-4 my-space-lg" aria-live="polite">
                    <template x-for="posicion in 4" :key="posicion">
                        <span class="w-4 h-4 rounded-full border-2 border-on-surface"
                              :class="pin.length >= posicion ? 'bg-on-surface' : ''"></span>
                    </template>
                    <span class="sr-only" x-text="pin.length + ' de 4 cifras'"></span>
                </div>

                <x-errores-campo nombre="pin" class="mb-space-md text-center" />
                <x-errores-campo nombre="camarero" class="mb-space-md text-center" />

                {{-- Teclado numérico grande (las tablets del bar suelen usarse con prisa) --}}
                <div class="grid grid-cols-3 gap-2 font-mono text-headline-md">
                    @foreach ([1, 2, 3, 4, 5, 6, 7, 8, 9] as $cifra)
                        <button type="button" x-on:click="pulsar('{{ $cifra }}')"
                                class="min-h-[64px] rounded border-[1.5px] border-secondary-fixed-dim hover:bg-surface-container-low">{{ $cifra }}</button>
                    @endforeach
                    <button type="button" x-on:click="borrar()" aria-label="Borrar la última cifra"
                            class="min-h-[64px] rounded text-label-lg font-sans hover:bg-surface-container-low">Borrar</button>
                    <button type="button" x-on:click="pulsar('0')"
                            class="min-h-[64px] rounded border-[1.5px] border-secondary-fixed-dim hover:bg-surface-container-low">0</button>
                    <span></span>
                </div>

                <x-boton-primario class="w-full min-h-[56px] mt-space-lg" x-bind:disabled="pin.length !== 4 || enviando">
                    Entrar
                </x-boton-primario>
            </div>
        </form>
    @endif

    <p class="mt-space-xl pt-space-md border-t border-outline-variant text-body-md text-secondary">
        Dispositivo «{{ $dispositivo->nombre }}». Si se pierde, el administrador puede desvincularlo en «Dispositivos».
    </p>
</x-guest-layout>
