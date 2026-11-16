{{--
    Los 14 alérgenos legales. No tiene maqueta: sigue el estilo de panelAdmin/carta.
    Solo consulta: los alérgenos los fija la ley y se asignan desde cada plato.
    Variable: $alergenos (con platos_count = platos de este restaurante).
--}}
<x-layouts.admin titulo="Alérgenos">
    <x-tarjeta class="mb-space-lg">
        <p class="flex items-start gap-2 text-body-lg">
            <x-icono nombre="health_and_safety" class="text-primary text-2xl" />
            <span>Estos son los 14 alérgenos de declaración obligatoria del Reglamento (UE) 1169/2011. Para indicar cuáles lleva un plato, márcalos al <a href="{{ route('admin.carta') }}" class="text-primary font-semibold hover:underline">editar el plato en la carta</a>.</span>
        </p>
    </x-tarjeta>

    <div class="grid gap-space-md sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($alergenos as $alergeno)
            <x-tarjeta class="flex items-center gap-space-md">
                <span class="flex items-center justify-center w-12 h-12 rounded-full bg-primary-fixed/40 text-primary">
                    <x-icono :nombre="$alergeno->icono" class="text-2xl" />
                </span>
                <span class="flex-1">
                    <span class="block text-label-lg">{{ $alergeno->id }}. {{ $alergeno->nombre }}</span>
                    <span class="block text-body-md text-secondary">
                        {{-- Singular o plural según el número de platos --}}
                        {{ $alergeno->platos_count }} {{ $alergeno->platos_count === 1 ? 'plato lo contiene' : 'platos lo contienen' }}
                    </span>
                </span>
            </x-tarjeta>
        @endforeach
    </div>
</x-layouts.admin>
