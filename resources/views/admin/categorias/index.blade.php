{{--
    Categorías de la carta. No tiene maqueta: sigue el estilo de panelAdmin/carta.
    Arriba se crea una categoría nueva y debajo se editan o borran las existentes.
    Variable: $categorias (con platos_count).
--}}
<x-layouts.admin titulo="Categorías de la carta">
    <x-slot name="acciones">
        <a href="{{ route('admin.carta') }}"
           class="pulsable inline-flex items-center justify-center min-h-[48px] px-5 rounded border-[1.5px] border-on-surface text-label-lg hover:bg-surface-container-lowest">
            Volver a la carta
        </a>
    </x-slot>

    {{-- Nueva categoría --}}
    <x-tarjeta class="mb-space-lg">
        <h2 class="text-headline-sm mb-space-md">Nueva categoría</h2>
        <form method="POST" action="{{ route('admin.carta.categorias.guardar') }}" class="grid gap-space-md sm:grid-cols-[1fr_140px_auto] sm:items-end">
            @csrf
            <x-campo-texto nombre="nombre" etiqueta="Nombre *" placeholder="Tapas" :value="old('nombre')" required maxlength="60" />
            <x-campo-texto nombre="orden" etiqueta="Orden *" tipo="number" min="0" max="999" :value="old('orden', $categorias->count() + 1)" required />
            <x-boton-primario>Añadir</x-boton-primario>
        </form>
    </x-tarjeta>

    {{-- Lista: sin tarjeta, con una raya gruesa bajo el título y una línea entre filas --}}
    <section>
        <h2 class="pb-3 border-b-2 border-on-surface text-headline-md">Categorías <span class="font-mono text-body-md font-normal text-secondary">{{ $categorias->count() }}</span></h2>
        <p class="py-space-md text-body-md text-secondary">El orden decide en qué posición sale cada categoría en la carta: primero la de número más bajo.</p>

        @forelse ($categorias as $categoria)
            <div class="flex flex-wrap items-end gap-space-md py-space-md border-t border-outline-variant">
                {{-- Cada fila es su propio formulario de edición. Los id llevan el
                     id de la categoría para que no se repitan en la página. --}}
                <form method="POST" action="{{ route('admin.carta.categorias.actualizar', $categoria) }}" class="flex flex-1 flex-wrap items-end gap-space-md">
                    @csrf
                    @method('PUT')
                    <div class="flex-1 min-w-[200px]">
                        <label for="nombre-{{ $categoria->id }}" class="block mb-1.5 text-label-md">Nombre</label>
                        <input id="nombre-{{ $categoria->id }}" name="nombre" value="{{ $categoria->nombre }}" required maxlength="60"
                               class="block w-full min-h-[48px] px-4 rounded border-[1.5px] border-secondary-fixed-dim text-body-lg focus:border-primary-container focus:ring-1 focus:ring-primary-container">
                    </div>
                    <div class="w-28">
                        <label for="orden-{{ $categoria->id }}" class="block mb-1.5 text-label-md">Orden</label>
                        <input id="orden-{{ $categoria->id }}" name="orden" type="number" min="0" max="999" value="{{ $categoria->orden }}" required
                               class="block w-full min-h-[48px] px-4 rounded border-[1.5px] border-secondary-fixed-dim text-body-lg focus:border-primary-container focus:ring-1 focus:ring-primary-container">
                    </div>
                    <span class="min-h-[48px] inline-flex items-center text-body-md text-secondary"><span class="font-mono">{{ $categoria->platos_count }}</span>&nbsp;platos</span>
                    <x-boton-secundario type="submit">Guardar</x-boton-secundario>
                </form>

                <form method="POST" action="{{ route('admin.carta.categorias.borrar', $categoria) }}"
                      onsubmit="return confirm('¿Seguro que quieres eliminar la categoría «{{ $categoria->nombre }}»?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" title="Eliminar"
                            class="inline-flex items-center justify-center w-12 h-12 rounded text-secondary hover:bg-error-container hover:text-error">
                        <x-icono nombre="delete" />
                        <span class="sr-only">Eliminar {{ $categoria->nombre }}</span>
                    </button>
                </form>
            </div>
        @empty
            <p class="py-space-md text-body-md text-secondary">Todavía no hay categorías. Crea la primera arriba.</p>
        @endforelse
    </section>
</x-layouts.admin>
