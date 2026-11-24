{{--
    Layout de las páginas públicas de MesaYa (rama 16, rediseño en la rama 21):
    inicio, privacidad y cookies.
    No se usa el de invitado porque ese es la pantalla partida del login.

    Uso: <x-layouts.publico titulo="Privacidad"> ...contenido... </x-layouts.publico>

    - Cabecera con el nombre, las anclas de la página de inicio (se ocultan
      en el móvil para que no haya scroll horizontal) y el botón de acceso.
    - Botón de acceso: sin sesión lleva al login; con sesión, a la pantalla de
      cada rol (User::rutaDeInicio()).
    - Pie con las páginas legales.
    Los enlaces usan route('inicio').'#ancla' para que funcionen también desde
    privacidad y cookies.

    Rediseño «Comanda impresa»: el logo es solo texto («MesaYa.» con el punto
    naranja) y la cabecera no lleva fondo ni sombra: se integra en la página.
--}}
@props(['titulo' => null])

@php
    $anclas = [
        ['texto' => 'Cómo funciona', 'ancla' => 'como-funciona'],
        ['texto' => 'Cocina y sala', 'ancla' => 'cocina-y-sala'],
        ['texto' => 'Panel', 'ancla' => 'panel'],
    ];
    $usuario = Auth::user();
@endphp

<x-layouts.base :titulo="$titulo">
    <div class="min-h-screen flex flex-col overflow-x-hidden">
        <header class="sticky top-0 z-30 bg-background/95 backdrop-blur border-b border-outline-variant">
            <div class="max-w-6xl mx-auto flex items-center gap-space-lg px-4 sm:px-8 h-16">
                <a href="{{ route('inicio') }}" class="text-headline-md font-extrabold tracking-tight">MesaYa<span class="text-primary-container">.</span></a>

                <nav class="hidden md:flex items-center gap-space-lg flex-1" aria-label="Secciones">
                    @foreach ($anclas as $ancla)
                        <a href="{{ route('inicio') }}#{{ $ancla['ancla'] }}" class="text-label-lg text-on-surface-variant hover:text-on-surface">{{ $ancla['texto'] }}</a>
                    @endforeach
                </nav>

                <x-boton-acceso :usuario="$usuario" class="ml-auto shrink-0" />
            </div>
        </header>

        <main class="flex-1">
            {{ $slot }}
        </main>

        <footer class="border-t border-outline-variant">
            <div class="max-w-6xl mx-auto flex flex-wrap items-center gap-x-space-lg gap-y-2 px-4 sm:px-8 py-space-lg text-body-md text-secondary">
                <span class="flex-1 min-w-[220px]">© {{ date('Y') }} MesaYa · Práctica de DAW (M0617 – Vibe Coding I)</span>
                <a href="{{ route('legal.privacidad') }}" class="inline-flex items-center min-h-[44px] hover:text-on-surface">Privacidad</a>
                <a href="{{ route('legal.cookies') }}" class="inline-flex items-center min-h-[44px] hover:text-on-surface">Cookies</a>
            </div>
        </footer>
    </div>
</x-layouts.base>
