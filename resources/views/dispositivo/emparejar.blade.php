{{--
    /dispositivo en una tablet o un móvil sin emparejar: se escribe el código
    que el admin ha generado en «Dispositivos». Si vale, el controlador guarda
    la cookie y el dispositivo ya no vuelve a ver esta pantalla.
    Usa el layout del login (pantalla partida), como el resto de accesos.
--}}
<x-guest-layout>
    <div class="mb-10">
        <h1 class="text-headline-lg">Emparejar dispositivo</h1>
        <p class="mt-2 text-body-lg text-secondary">
            Pide el código al administrador del restaurante: lo genera en «Dispositivos».
        </p>
    </div>

    <form method="POST" action="{{ route('dispositivo.emparejar') }}" class="space-y-5">
        @csrf

        {{-- autocapitalize y spellcheck: en el móvil sale el teclado en mayúsculas y sin correcciones --}}
        <x-campo-texto nombre="codigo" etiqueta="Código" :value="old('codigo')" placeholder="K7Q-4ZP"
                       class="font-mono uppercase tracking-[0.15em]" autocomplete="off" autocapitalize="characters"
                       spellcheck="false" maxlength="20" required autofocus />

        <x-boton-primario class="w-full min-h-[52px]">Emparejar</x-boton-primario>
    </form>

    <p class="mt-space-lg pt-space-md border-t border-outline-variant text-body-md text-secondary">
        ¿Eres administrador?
        <a href="{{ route('login') }}" class="text-on-surface underline underline-offset-2 hover:text-primary">Inicia sesión</a>
    </p>
</x-guest-layout>
