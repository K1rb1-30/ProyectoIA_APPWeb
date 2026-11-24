{{--
    Etiqueta pequeña con el nombre de un alérgeno.
    Uso: <x-badge-alergeno>Gluten</x-badge-alergeno>
         <x-badge-alergeno critico>Frutos secos</x-badge-alergeno>
    "critico" la pinta en rojo, para los avisos importantes (DESIGN.md).

    Rediseño: en vez de una cápsula gris rellena, es una etiqueta con un borde
    fino y esquinas casi rectas, como las de una carta impresa.
--}}
@props(['critico' => false])

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1 px-1.5 py-0.5 rounded-sm border text-label-sm whitespace-nowrap '.($critico ? 'border-red-200 bg-red-50 text-red-700' : 'border-outline-variant text-on-surface-variant')]) }}>
    {{ $slot }}
</span>
