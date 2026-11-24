{{--
    Lista de platos de la carta (rediseño en la rama 22).
    Variables: $platos, $categorias, $categoriaElegida, $totalPlatos, $totalAgotados.

    Rediseño «Comanda impresa»: el resumen va como subtítulo; el filtro de
    categorías son pestañas con una raya naranja (no cápsulas); la tabla no
    tiene caja ni fondo de cabecera, solo líneas entre filas.
--}}
<x-layouts.admin titulo="Carta">
    <x-slot name="subtitulo">
        <span class="font-mono">{{ $totalPlatos - $totalAgotados }}</span> platos disponibles ·
        <span @class(['font-mono', 'text-error font-semibold' => $totalAgotados > 0])>{{ $totalAgotados }}</span> agotados
    </x-slot>
    <x-slot name="acciones">
        {{-- Son enlaces con el mismo aspecto que los botones (un <button> dentro de un <a> no es HTML válido) --}}
        <a href="{{ route('admin.carta.categorias') }}"
           class="pulsable inline-flex items-center justify-center min-h-[48px] px-5 rounded border-[1.5px] border-on-surface text-label-lg hover:bg-surface-container-lowest">
            Categorías
        </a>
        <a href="{{ route('admin.carta.crear') }}"
           class="pulsable inline-flex items-center justify-center min-h-[48px] px-6 rounded bg-primary-container text-on-primary text-label-lg font-bold hover:bg-primary">
            Nuevo plato
        </a>
    </x-slot>

    {{-- Filtro por categoría: un enlace por categoría con ?categoria=id --}}
    <nav class="flex gap-x-space-lg overflow-x-auto border-b border-outline-variant" aria-label="Filtrar por categoría">
        @php
            // La pestaña elegida lleva una raya naranja debajo.
            $clase = 'shrink-0 inline-flex items-center min-h-[44px] -mb-px border-b-[3px] text-label-lg';
            $activa = 'border-primary-container text-on-surface';
            $inactiva = 'border-transparent text-secondary hover:text-on-surface';
        @endphp
        <a href="{{ route('admin.carta') }}" class="{{ $clase }} {{ $categoriaElegida ? $inactiva : $activa }}"
           @unless ($categoriaElegida) aria-current="true" @endunless>
            Todos <span class="ml-1.5 font-mono font-normal">{{ $totalPlatos }}</span>
        </a>
        @foreach ($categorias as $categoria)
            <a href="{{ route('admin.carta', ['categoria' => $categoria->id]) }}"
               class="{{ $clase }} {{ $categoriaElegida === $categoria->id ? $activa : $inactiva }}"
               @if ($categoriaElegida === $categoria->id) aria-current="true" @endif>
                {{ $categoria->nombre }} <span class="ml-1.5 font-mono font-normal">{{ $categoria->platos_count }}</span>
            </a>
        @endforeach
    </nav>

    {{-- Tabla de platos. "relative" hace que los textos sr-only (position: absolute) de la
         tabla se queden dentro de esta caja con scroll; sin él ensanchaban toda la página en el móvil --}}
    <div class="relative overflow-x-auto">
        @if ($platos->isEmpty())
            <div class="py-space-xl text-center">
                <p class="text-body-lg text-secondary">Todavía no hay platos aquí.</p>
                <a href="{{ route('admin.carta.crear') }}" class="mt-space-md inline-flex items-center min-h-[48px] px-6 rounded border-[1.5px] border-on-surface text-label-lg font-bold hover:bg-surface-container-lowest">Añadir el primero</a>
            </div>
        @else
            <table class="w-full min-w-[820px] text-left">
                <thead class="text-label-md font-normal text-secondary">
                    <tr class="border-b border-outline-variant">
                        <th scope="col" class="py-3 pr-4 font-medium">Plato</th>
                        <th scope="col" class="py-3 px-4 font-medium">Categoría</th>
                        <th scope="col" class="py-3 px-4 font-medium">Alérgenos</th>
                        <th scope="col" class="py-3 px-4 font-medium text-right">Precio</th>
                        <th scope="col" class="py-3 px-4 font-medium">Disponible</th>
                        <th scope="col" class="py-3 pl-4"><span class="sr-only">Acciones</span></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($platos as $plato)
                        <tr @class(['align-middle border-b border-outline-variant', 'bg-surface-container-low' => ! $plato->disponible])>
                            {{-- Miniatura (o un hueco gris si no hay foto), nombre y descripción --}}
                            <td class="py-3.5 pr-4">
                                <div class="flex items-center gap-space-md">
                                    @if ($plato->urlFoto())
                                        <img src="{{ $plato->urlFoto() }}" alt="" class="w-12 h-12 shrink-0 rounded-sm object-cover">
                                    @else
                                        <div class="w-12 h-12 shrink-0 rounded-sm bg-surface-container-high" aria-hidden="true"></div>
                                    @endif
                                    <div class="min-w-0 max-w-xs">
                                        <p @class(['text-label-lg', 'text-secondary' => ! $plato->disponible])>{{ $plato->nombre }}</p>
                                        {{-- Str::limit corta la descripción larga para que la tabla no crezca --}}
                                        <p class="text-body-md text-secondary">{{ \Illuminate\Support\Str::limit($plato->descripcion, 70) }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-body-md">{{ $plato->categoria->nombre }}</td>
                            <td class="py-3.5 px-4">
                                <div class="flex flex-wrap gap-1">
                                    @forelse ($plato->alergenos as $alergeno)
                                        <x-badge-alergeno title="{{ $alergeno->nombre }}">{{ $alergeno->nombre }}</x-badge-alergeno>
                                    @empty
                                        <span class="text-body-md text-secondary">—</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <x-precio :centimos="$plato->precio_centimos" class="font-mono text-label-lg" />
                            </td>
                            <td class="py-3.5 px-4">
                                {{-- Interruptor: es un formulario que cambia disponible/agotado --}}
                                <form method="POST" action="{{ route('admin.carta.disponibilidad', $plato) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" role="switch" aria-checked="{{ $plato->disponible ? 'true' : 'false' }}"
                                            aria-label="Disponible: {{ $plato->nombre }}"
                                            class="inline-flex items-center gap-2 min-h-[44px] text-label-md">
                                        <span @class([
                                            'relative inline-block w-11 h-6 rounded-full transition-colors',
                                            'bg-on-surface' => $plato->disponible,
                                            'bg-secondary-fixed-dim' => ! $plato->disponible,
                                        ])>
                                            <span @class([
                                                'absolute top-0.5 w-5 h-5 rounded-full bg-white transition-all',
                                                'left-[22px]' => $plato->disponible,
                                                'left-0.5' => ! $plato->disponible,
                                            ])></span>
                                        </span>
                                        <span @class(['text-error' => ! $plato->disponible])>{{ $plato->disponible ? 'Sí' : 'Agotado' }}</span>
                                    </button>
                                </form>
                            </td>
                            <td class="py-3.5 pl-4">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('admin.carta.editar', $plato) }}"
                                       class="inline-flex items-center min-h-[44px] px-2 text-label-lg underline underline-offset-4 hover:text-primary">
                                        Editar<span class="sr-only"> {{ $plato->nombre }}</span>
                                    </a>
                                    {{-- confirm() pide confirmación en el navegador antes de borrar --}}
                                    <form method="POST" action="{{ route('admin.carta.borrar', $plato) }}"
                                          onsubmit="return confirm('¿Seguro que quieres eliminar «{{ $plato->nombre }}»?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Eliminar"
                                                class="inline-flex items-center justify-center w-11 h-11 rounded text-secondary hover:bg-error-container hover:text-error">
                                            <x-icono nombre="delete" />
                                            <span class="sr-only">Eliminar {{ $plato->nombre }}</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</x-layouts.admin>
