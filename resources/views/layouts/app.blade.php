{{--
    Layout de las páginas con sesión iniciada que valen para cualquier rol
    (panel de bienvenida y perfil): barra de navegación arriba y contenido.
    Se usa con <x-app-layout> (lo trae Breeze: app/View/Components/AppLayout.php).
    El slot "header" (opcional) es el título de la página.
--}}
<x-layouts.base>
    <div class="min-h-screen">
        @include('layouts.navigation')

        @isset($header)
            <header class="bg-surface-container-lowest border-b border-outline-variant/30">
                <div class="max-w-[1280px] mx-auto px-margin-md py-space-lg">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <main class="max-w-[1280px] mx-auto p-margin-md">
            {{ $slot }}
        </main>
    </div>
</x-layouts.base>
