{{--
    /dispositivo en un dispositivo de CAMARERO ya emparejado.
    Variables: $dispositivo (con su restaurante cargado)

    Provisional: el acceso de cada camarero con su nombre y su PIN llega en la
    rama siguiente (feature/acceso-camarero-pin). De momento solo confirma que
    el dispositivo está emparejado.
--}}
<x-guest-layout>
    <div class="mb-10">
        <h1 class="text-headline-lg">{{ $dispositivo->restaurante->nombre }}</h1>
        <p class="mt-2 text-body-lg text-secondary">Dispositivo de camarero: «{{ $dispositivo->nombre }}».</p>
    </div>

    @if (session('estado'))
        <div role="status" class="mb-space-md px-4 py-3 rounded bg-tertiary-fixed text-on-tertiary-fixed-variant text-body-md">
            {{ session('estado') }}
        </div>
    @endif

    <p class="text-body-lg">Este dispositivo ya está emparejado. Pronto cada camarero podrá entrar aquí con su nombre y su PIN.</p>
</x-guest-layout>
