{{--
    Dispositivos del restaurante (rol admin): tablets y móviles del bar.
    Variables:
      $emparejados  dispositivos que ya tienen su cookie y pueden entrar
      $pendientes   dispositivos creados que esperan a que se escriba el código

    Flujo: «Emparejar dispositivo» → elegir cocina o camarero → sale el código
    (session('codigoNuevo')) → se escribe en el dispositivo en /dispositivo.
    El código solo se enseña esa vez: en la base de datos se guarda cifrado.

    Rediseño «Comanda impresa»: el código es el dato principal (grande y en
    letra de ticket); las listas van en filas con línea; un solo botón naranja
    («Emparejar dispositivo»).
--}}
@php
    $codigoNuevo = session('codigoNuevo');
    $tipos = [
        'cocina' => ['texto' => 'Cocina', 'ayuda' => 'Entra directo al tablero de pedidos. Puedes tener varias.'],
        'camarero' => ['texto' => 'Camarero', 'ayuda' => 'Cada camarero entra con su nombre y su PIN.'],
    ];
@endphp

<x-layouts.admin titulo="Dispositivos">
    <x-slot name="subtitulo">
        <span class="font-mono">{{ $emparejados->count() }}</span> {{ $emparejados->count() === 1 ? 'emparejado' : 'emparejados' }}
        @if ($pendientes->isNotEmpty())
            · <span class="font-mono">{{ $pendientes->count() }}</span> esperando el código
        @endif
    </x-slot>

    <p class="mb-space-lg max-w-3xl text-body-md text-secondary">
        Las tablets y móviles del bar se emparejan una vez con un código. Después, la cocina entra
        directamente y los camareros solo necesitan su nombre y su PIN. Si un dispositivo se pierde,
        desvincúlalo y dejará de entrar.
    </p>

    {{-- Código recién creado: el dato principal de la pantalla --}}
    @if ($codigoNuevo)
        <section aria-label="Código para emparejar" class="mb-space-xl p-space-lg sm:p-8 bg-surface-container-lowest border border-outline-variant">
            <p class="text-label-lg text-secondary">Código para «{{ $codigoNuevo['nombre'] }}»</p>
            <p class="mt-2 font-mono text-[clamp(2.5rem,7vw,4rem)] leading-none font-semibold tracking-[0.08em]">{{ $codigoNuevo['codigo'] }}</p>
            <p class="mt-space-md text-body-md text-on-surface-variant">
                En el dispositivo, abre <span class="font-mono">{{ route('dispositivo') }}</span> y escribe este código.
                Vale hasta las <span class="font-mono">{{ $codigoNuevo['caduca'] }}</span> y solo sirve una vez.
            </p>
        </section>
    @endif

    <div class="flex flex-wrap items-start gap-x-14 gap-y-12">
        {{-- Emparejar uno nuevo --}}
        <section class="flex-[2_1_300px] min-w-0">
            <h2 class="pb-3 border-b-2 border-on-surface text-headline-md">Emparejar dispositivo</h2>

            <form method="POST" action="{{ route('admin.dispositivos.guardar') }}" class="mt-space-md space-y-space-md">
                @csrf

                <fieldset>
                    <legend class="text-label-lg">¿Para quién es?</legend>
                    <div class="mt-2 divide-y divide-outline-variant border-y border-outline-variant">
                        @foreach ($tipos as $valor => $tipo)
                            <label class="flex items-start gap-3 py-3 cursor-pointer">
                                <input type="radio" name="tipo" value="{{ $valor }}" @checked(old('tipo') === $valor)
                                       class="mt-1 h-5 w-5 border-secondary-fixed-dim text-primary-container focus:ring-primary-container">
                                <span>
                                    <span class="block text-label-lg">{{ $tipo['texto'] }}</span>
                                    <span class="block text-body-md text-secondary">{{ $tipo['ayuda'] }}</span>
                                </span>
                            </label>
                        @endforeach
                    </div>
                    <x-errores-campo nombre="tipo" />
                </fieldset>

                <x-campo-texto nombre="nombre" etiqueta="Nombre (opcional)" :value="old('nombre')"
                               placeholder="Por ejemplo: Tablet de la barra" maxlength="60" />

                <x-boton-primario class="w-full">Emparejar dispositivo</x-boton-primario>
            </form>
        </section>

        {{-- Listas --}}
        <section class="flex-[3_1_440px] min-w-0 space-y-space-xl">
            <div>
                <h2 class="pb-3 border-b-2 border-on-surface text-headline-md">Emparejados</h2>
                @if ($emparejados->isEmpty())
                    <p class="py-space-lg text-body-lg text-secondary">Todavía no hay ningún dispositivo emparejado.</p>
                @else
                    <ul class="divide-y divide-outline-variant">
                        @foreach ($emparejados as $dispositivo)
                            <li class="flex flex-wrap items-center gap-x-space-md gap-y-1 py-3.5">
                                <div class="flex-1 min-w-0">
                                    <p class="text-label-lg">{{ $dispositivo->nombre }}</p>
                                    <p class="text-body-md text-secondary">
                                        {{ $tipos[$dispositivo->tipo]['texto'] }} ·
                                        emparejado el <span class="font-mono">{{ $dispositivo->emparejado_en->format('d/m H:i') }}</span>
                                        @if ($dispositivo->ultimo_uso_en)
                                            · último uso <span class="font-mono">{{ $dispositivo->ultimo_uso_en->format('d/m H:i') }}</span>
                                        @endif
                                    </p>
                                </div>
                                <form method="POST" action="{{ route('admin.dispositivos.borrar', $dispositivo) }}"
                                      onsubmit="return confirm('¿Desvincular «{{ $dispositivo->nombre }}»? Dejará de entrar y habrá que emparejarlo otra vez.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center min-h-[44px] text-label-md text-secondary hover:text-error">
                                        Desvincular<span class="sr-only"> «{{ $dispositivo->nombre }}»</span>
                                    </button>
                                </form>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            @if ($pendientes->isNotEmpty())
                <div>
                    <h2 class="pb-3 border-b-2 border-on-surface text-headline-md">Esperando el código</h2>
                    <ul class="divide-y divide-outline-variant">
                        @foreach ($pendientes as $dispositivo)
                            <li class="flex flex-wrap items-center gap-x-space-md gap-y-1 py-3.5">
                                <div class="flex-1 min-w-0">
                                    <p class="text-label-lg">{{ $dispositivo->nombre }}</p>
                                    {{-- Estado con punto + palabra --}}
                                    <p class="inline-flex items-center gap-2 text-body-md {{ $dispositivo->codigoVigente() ? 'text-amber-800' : 'text-secondary' }}">
                                        <span class="w-2 h-2 rounded-full {{ $dispositivo->codigoVigente() ? 'bg-estado-pendiente' : 'border-2 border-outline' }}" aria-hidden="true"></span>
                                        {{ $tipos[$dispositivo->tipo]['texto'] }} ·
                                        @if ($dispositivo->codigoVigente())
                                            el código vale hasta las <span class="font-mono">{{ $dispositivo->codigo_caduca_en->format('H:i') }}</span>
                                        @else
                                            el código ha caducado
                                        @endif
                                    </p>
                                </div>
                                <form method="POST" action="{{ route('admin.dispositivos.codigo', $dispositivo) }}">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center min-h-[44px] text-label-md underline underline-offset-2 hover:text-primary">
                                        Código nuevo<span class="sr-only"> para «{{ $dispositivo->nombre }}»</span>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.dispositivos.borrar', $dispositivo) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center min-h-[44px] text-label-md text-secondary hover:text-error">
                                        Cancelar<span class="sr-only"> «{{ $dispositivo->nombre }}»</span>
                                    </button>
                                </form>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </section>
    </div>
</x-layouts.admin>
