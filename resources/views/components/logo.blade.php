{{--
    Logo de MesaYa: cuadrado de tinta con una "M" y un punto naranja.
    Uso: <x-logo />  ·  <x-logo class="w-12 h-12" />
    Es decorativo (aria-hidden): el nombre "MesaYa" ya va escrito al lado.
--}}
<span {{ $attributes->merge(['class' => 'inline-flex shrink-0 items-center justify-center w-10 h-10 rounded bg-on-surface text-surface text-xl font-extrabold tracking-tight']) }} aria-hidden="true">M<span class="text-inverse-primary">.</span></span>
