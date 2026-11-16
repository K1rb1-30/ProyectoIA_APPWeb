{{--
    Cápsula pequeña con el nombre de un alérgeno.
    Uso: <x-badge-alergeno>Gluten</x-badge-alergeno>
         <x-badge-alergeno critico>Frutos secos</x-badge-alergeno>
    "critico" la pinta en rojo, para los avisos importantes (DESIGN.md).
--}}
@props(['critico' => false])

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-label-sm whitespace-nowrap '.($critico ? 'bg-red-50 text-red-700' : 'bg-gray-100 text-gray-700')]) }}>
    {{ $slot }}
</span>
