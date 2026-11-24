{{--
    Formulario para crear o editar una mesa (rol admin). No tiene maqueta:
    sigue el estilo del panel (tarjeta blanca y campos de DESIGN.md).
    Si la mesa ya existe en la base de datos, es "editar"; si no, "crear".
--}}
@php
    $editando = $mesa->exists;
@endphp

<x-layouts.admin :titulo="$editando ? 'Editar mesa '.$mesa->numero : 'Añadir mesa'">
    <div class="max-w-lg">
        <a href="{{ route('admin.mesas') }}" class="inline-flex items-center gap-1 mb-space-md text-label-lg text-secondary hover:text-on-surface">
            Volver a las mesas
        </a>

        <x-tarjeta>
            <form method="POST" action="{{ $editando ? route('admin.mesas.actualizar', $mesa) : route('admin.mesas.guardar') }}" class="space-y-space-lg">
                @csrf
                {{-- Los formularios HTML solo envían GET y POST: @method('PUT') le dice a Laravel que es una actualización --}}
                @if ($editando)
                    @method('PUT')
                @endif

                <x-campo-texto nombre="numero" tipo="number" etiqueta="Número de mesa"
                               :value="old('numero', $mesa->numero)" min="1" max="999" required autofocus />

                @if ($editando)
                    <p class="text-body-md text-secondary">
                        Si cambias el número, cambia también la URL del QR: tendrás que volver a imprimirlo.
                    </p>
                @endif

                <div class="flex flex-wrap gap-3">
                    <x-boton-primario>
                        {{ $editando ? 'Guardar cambios' : 'Crear mesa' }}
                    </x-boton-primario>
                    <a href="{{ route('admin.mesas') }}"
                       class="inline-flex items-center justify-center min-h-[48px] px-5 py-3 rounded border-[1.5px] border-on-surface text-label-lg hover:bg-surface-container-lowest">
                        Cancelar
                    </a>
                </div>
            </form>
        </x-tarjeta>
    </div>
</x-layouts.admin>
