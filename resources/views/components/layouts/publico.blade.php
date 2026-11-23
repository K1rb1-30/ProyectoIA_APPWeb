{{--
    Layout de las páginas públicas de MesaYa (rama 16): inicio, privacidad y
    cookies. Referencia: docs/design/DesignFrontend/landing/inicio.
    No se usa el de invitado porque ese es la pantalla partida del login.

    Uso: <x-layouts.publico titulo="Privacidad"> ...contenido... </x-layouts.publico>

    - Cabecera fija con el logo, las anclas de la página de inicio (se ocultan
      en el móvil para que no haya scroll horizontal) y el botón de acceso.
    - Botón de acceso: sin sesión lleva al login; con sesión, a la pantalla de
      cada rol (User::rutaDeInicio()).
    - Pie con las mismas anclas y las páginas legales.
    Los enlaces usan route('inicio').'#ancla' para que funcionen también desde
    privacidad y cookies.
--}}
@props(['titulo' => null])

@php
    $anclas = [
        ['texto' => 'Cómo funciona', 'ancla' => 'como-funciona'],
        ['texto' => 'Cocina y sala', 'ancla' => 'cocina-y-sala'],
        ['texto' => 'Panel del restaurante', 'ancla' => 'panel'],
    ];
    $usuario = Auth::user();
@endphp

<x-layouts.base :titulo="$titulo">
    {{-- overflow-x-hidden: los brillos decorativos no pueden crear scroll lateral en el móvil --}}
    <div class="min-h-screen flex flex-col overflow-x-hidden">
        <header class="sticky top-0 z-30 bg-surface-container-lowest/90 backdrop-blur border-b border-outline-variant/30">
            <div class="max-w-6xl mx-auto flex items-center justify-between gap-space-md px-4 sm:px-margin-md h-16">
                <a href="{{ route('inicio') }}" class="flex items-center gap-2.5 min-w-0">
                    <x-logo />
                    <span class="min-w-0">
                        <span class="block text-headline-sm font-extrabold leading-tight">MesaYa</span>
                        <span class="hidden sm:block text-label-sm text-secondary">Comandas y cartas QR</span>
                    </span>
                </a>

                <nav class="hidden md:flex items-center gap-space-lg" aria-label="Secciones">
                    @foreach ($anclas as $ancla)
                        <a href="{{ route('inicio') }}#{{ $ancla['ancla'] }}" class="text-label-lg text-on-surface-variant hover:text-primary">{{ $ancla['texto'] }}</a>
                    @endforeach
                </nav>

                <x-boton-acceso :usuario="$usuario" class="shrink-0" />
            </div>
        </header>

        <main class="flex-1">
            {{ $slot }}
        </main>

        <footer class="border-t border-outline-variant/30 bg-surface-container-lowest">
            <div class="max-w-6xl mx-auto px-4 sm:px-margin-md py-space-xl grid gap-space-lg sm:grid-cols-2 lg:grid-cols-4">
                <div class="lg:col-span-2 space-y-3">
                    <div class="flex items-center gap-2.5">
                        <x-logo />
                        <span class="text-headline-sm font-extrabold">MesaYa</span>
                    </div>
                    <p class="max-w-sm text-body-md text-secondary">
                        Carta por QR, pedidos a cocina y cuenta dividida para bares y restaurantes.
                    </p>
                </div>
                <div>
                    <p class="text-label-lg mb-2">La aplicación</p>
                    <ul class="space-y-1.5 text-body-md text-secondary">
                        @foreach ($anclas as $ancla)
                            <li><a href="{{ route('inicio') }}#{{ $ancla['ancla'] }}" class="hover:text-primary">{{ $ancla['texto'] }}</a></li>
                        @endforeach
                        <li><a href="{{ $usuario ? route($usuario->rutaDeInicio()) : route('login') }}" class="hover:text-primary">{{ $usuario ? 'Ir a mi pantalla' : 'Acceso del personal' }}</a></li>
                    </ul>
                </div>
                <div>
                    <p class="text-label-lg mb-2">Legal</p>
                    <ul class="space-y-1.5 text-body-md text-secondary">
                        <li><a href="{{ route('legal.privacidad') }}" class="hover:text-primary">Privacidad</a></li>
                        <li><a href="{{ route('legal.cookies') }}" class="hover:text-primary">Cookies</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-outline-variant/30">
                <p class="max-w-6xl mx-auto px-4 sm:px-margin-md py-space-md text-label-sm text-secondary">
                    © {{ date('Y') }} MesaYa · Práctica de DAW (M0617 – Vibe Coding I).
                </p>
            </div>
        </footer>
    </div>
</x-layouts.base>
