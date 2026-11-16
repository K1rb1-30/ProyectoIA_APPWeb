{{--
    Lista de platos de la carta. Referencia: panelAdmin/carta (parte izquierda).
    Variables: $platos, $categorias, $categoriaElegida, $totalPlatos, $totalAgotados.
--}}
<x-layouts.admin titulo="Carta">
    <x-slot name="acciones">
        {{-- Son enlaces con el mismo aspecto que los botones (un <button> dentro de un <a> no es HTML válido) --}}
        <a href="{{ route('admin.carta.categorias') }}"
           class="pulsable inline-flex items-center justify-center gap-2 min-h-[48px] px-5 py-3 rounded bg-surface-container-lowest border-[1.5px] border-outline-variant/60 text-on-surface text-label-lg shadow-sm transition hover:border-primary-container">
            <x-icono nombre="category" class="text-xl" />
            Categorías
        </a>
        <a href="{{ route('admin.carta.crear') }}"
           class="pulsable inline-flex items-center justify-center gap-2 min-h-[48px] px-5 py-3 rounded bg-primary-container text-on-primary text-label-lg font-bold shadow-sm transition hover:bg-primary">
            <x-icono nombre="add" class="text-xl" />
            Nuevo plato
        </a>
    </x-slot>

    {{-- Resumen: disponibles y agotados --}}
    <x-tarjeta class="mb-space-lg">
        <p class="flex flex-wrap items-center gap-x-3 gap-y-1 text-body-lg">
            <x-icono nombre="verified" class="text-tertiary text-2xl" />
            <span class="font-semibold text-tertiary">{{ $totalPlatos - $totalAgotados }} platos disponibles</span>
            <span class="text-outline-variant">|</span>
            <span class="font-semibold text-error">{{ $totalAgotados }} agotados</span>
        </p>
    </x-tarjeta>

    {{-- Filtro por categoría: un enlace por categoría con ?categoria=id --}}
    <nav class="flex gap-2 overflow-x-auto pb-2 mb-space-md" aria-label="Filtrar por categoría">
        @php
            // Clases de las cápsulas: oscura la elegida, blanca el resto.
            $claseChip = 'shrink-0 inline-flex items-center min-h-[44px] px-5 rounded-full text-label-lg border transition-colors';
            $activa = 'bg-inverse-surface text-inverse-on-surface border-inverse-surface';
            $inactiva = 'bg-surface-container-lowest text-on-surface border-outline-variant/50 hover:border-primary-container';
        @endphp
        <a href="{{ route('admin.carta') }}" class="{{ $claseChip }} {{ $categoriaElegida ? $inactiva : $activa }}">
            Todos ({{ $totalPlatos }})
        </a>
        @foreach ($categorias as $categoria)
            <a href="{{ route('admin.carta', ['categoria' => $categoria->id]) }}"
               class="{{ $claseChip }} {{ $categoriaElegida === $categoria->id ? $activa : $inactiva }}">
                {{ $categoria->nombre }} ({{ $categoria->platos_count }})
            </a>
        @endforeach
    </nav>

    {{-- Tabla de platos --}}
    <div class="bg-surface-container-lowest rounded-lg border border-outline-variant/30 shadow-nivel-1 overflow-x-auto">
        @if ($platos->isEmpty())
            <div class="p-space-xl text-center text-secondary">
                <x-icono nombre="restaurant" class="text-5xl" />
                <p class="mt-2 text-body-lg">Todavía no hay platos aquí.</p>
                <a href="{{ route('admin.carta.crear') }}" class="mt-2 inline-block text-label-lg text-primary hover:underline">Añadir el primero</a>
            </div>
        @else
            <table class="w-full min-w-[860px] text-left">
                <thead class="bg-surface-container-low text-label-md text-secondary">
                    <tr>
                        <th scope="col" class="px-4 py-3">Plato</th>
                        <th scope="col" class="px-4 py-3">Nombre y descripción</th>
                        <th scope="col" class="px-4 py-3">Categoría</th>
                        <th scope="col" class="px-4 py-3">Precio</th>
                        <th scope="col" class="px-4 py-3">Alérgenos</th>
                        <th scope="col" class="px-4 py-3">Disponible</th>
                        <th scope="col" class="px-4 py-3"><span class="sr-only">Acciones</span></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/30">
                    @foreach ($platos as $plato)
                        <tr @class(['align-middle', 'opacity-60' => ! $plato->disponible])>
                            {{-- Miniatura o, si no hay foto, un icono --}}
                            <td class="px-4 py-3">
                                @if ($plato->urlFoto())
                                    <img src="{{ $plato->urlFoto() }}" alt="" class="w-16 h-16 rounded object-cover">
                                @else
                                    <div class="w-16 h-16 rounded bg-surface-container flex items-center justify-center text-secondary">
                                        <x-icono nombre="restaurant" class="text-2xl" />
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3 max-w-xs">
                                <p class="text-label-lg">{{ $plato->nombre }}</p>
                                {{-- Str::limit corta la descripción larga para que la tabla no crezca --}}
                                <p class="text-body-md text-secondary">{{ \Illuminate\Support\Str::limit($plato->descripcion, 70) }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-block px-3 py-1 rounded-full bg-secondary-container text-label-md text-on-secondary-fixed-variant">{{ $plato->categoria->nombre }}</span>
                            </td>
                            <td class="px-4 py-3 text-price-display">
                                <x-precio :centimos="$plato->precio_centimos" />
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-1">
                                    @forelse ($plato->alergenos as $alergeno)
                                        <x-badge-alergeno title="{{ $alergeno->nombre }}">
                                            <x-icono :nombre="$alergeno->icono" class="text-sm" />
                                            {{ $alergeno->nombre }}
                                        </x-badge-alergeno>
                                    @empty
                                        <span class="text-body-md text-secondary">—</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                {{-- Interruptor: es un formulario que cambia disponible/agotado --}}
                                <form method="POST" action="{{ route('admin.carta.disponibilidad', $plato) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" role="switch" aria-checked="{{ $plato->disponible ? 'true' : 'false' }}"
                                            aria-label="Disponible: {{ $plato->nombre }}"
                                            class="inline-flex items-center gap-2 min-h-[44px] text-label-md">
                                        <span @class([
                                            'relative inline-block w-11 h-6 rounded-full transition-colors',
                                            'bg-tertiary-container' => $plato->disponible,
                                            'bg-outline-variant' => ! $plato->disponible,
                                        ])>
                                            <span @class([
                                                'absolute top-0.5 w-5 h-5 rounded-full bg-white shadow transition-all',
                                                'left-[22px]' => $plato->disponible,
                                                'left-0.5' => ! $plato->disponible,
                                            ])></span>
                                        </span>
                                        {{ $plato->disponible ? 'Sí' : 'Agotado' }}
                                    </button>
                                </form>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('admin.carta.editar', $plato) }}" title="Editar"
                                       class="inline-flex items-center justify-center w-11 h-11 rounded text-secondary hover:bg-surface-container-low hover:text-primary">
                                        <x-icono nombre="edit" />
                                        <span class="sr-only">Editar {{ $plato->nombre }}</span>
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
