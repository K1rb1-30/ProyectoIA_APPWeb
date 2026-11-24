{{--
    Los 14 alérgenos legales (rediseño en la rama 22).
    Solo consulta: los alérgenos los fija la ley y se asignan desde cada plato.
    Variable: $alergenos (con platos_count = platos de este restaurante).

    Rediseño «Comanda impresa»: una lista en dos columnas con líneas, con el
    número de platos a la derecha en letra de ticket (antes, 14 tarjetas con
    icono). Los alérgenos que no lleva ningún plato salen en gris.
--}}
<x-layouts.admin titulo="Alérgenos">
    <x-slot name="subtitulo">Los 14 de declaración obligatoria (Reglamento UE 1169/2011)</x-slot>

    <p class="mb-space-lg max-w-3xl text-body-lg text-on-surface-variant">
        Para indicar cuáles lleva un plato, márcalos al <a href="{{ route('admin.carta') }}" class="font-semibold underline underline-offset-2 hover:text-primary">editar el plato en la carta</a>.
    </p>

    <ol class="grid gap-x-14 sm:grid-cols-2 border-t-2 border-on-surface">
        @foreach ($alergenos as $alergeno)
            <li class="flex items-baseline gap-space-md py-3.5 border-b border-outline-variant">
                <span class="w-7 font-mono text-label-md font-normal text-secondary">{{ $alergeno->id }}</span>
                <span @class(['flex-1 text-label-lg', 'text-secondary font-normal' => $alergeno->platos_count === 0])>{{ $alergeno->nombre }}</span>
                <span class="text-body-md text-secondary text-right">
                    {{-- Singular o plural según el número de platos --}}
                    <span class="font-mono text-on-surface">{{ $alergeno->platos_count }}</span>
                    {{ $alergeno->platos_count === 1 ? 'plato lo contiene' : 'platos lo contienen' }}
                </span>
            </li>
        @endforeach
    </ol>
</x-layouts.admin>
