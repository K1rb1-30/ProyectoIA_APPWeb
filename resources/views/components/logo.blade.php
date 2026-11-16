{{--
    Logo de MesaYa: cuadrado naranja con el icono de cubiertos.
    Uso: <x-logo />  ·  <x-logo class="w-12 h-12" />
--}}
<span {{ $attributes->merge(['class' => 'inline-flex shrink-0 items-center justify-center w-10 h-10 rounded bg-primary-container text-on-primary shadow-sm']) }}>
    <x-icono nombre="restaurant_menu" />
</span>
