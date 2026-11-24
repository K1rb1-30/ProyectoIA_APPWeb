{{--
    Barra de navegación superior del layout "app": logo, enlace al panel,
    nombre del usuario, perfil y cerrar sesión.
    flex-wrap hace que en móvil los enlaces bajen a una segunda línea.
--}}
<nav class="border-b border-outline-variant">
    <div class="max-w-[1280px] mx-auto px-margin-md py-space-sm flex flex-wrap items-center justify-between gap-space-sm">
        <div class="flex items-center gap-space-md">
            {{-- Rediseño (rama 22): el logo es el nombre en texto con el punto naranja --}}
            <a href="{{ route(Auth::user()->rutaDeInicio()) }}" class="text-headline-md font-extrabold tracking-tight">{{ config('app.name', 'MesaYa') }}<span class="text-primary-container">.</span></a>

            <a href="{{ route(Auth::user()->rutaDeInicio()) }}"
               @class([
                   'inline-flex items-center min-h-[48px] px-4 rounded text-label-lg transition-colors',
                   'bg-on-surface text-surface-container-lowest' => request()->routeIs(Auth::user()->rutaDeInicio()),
                   'text-secondary hover:bg-surface-container-low' => ! request()->routeIs(Auth::user()->rutaDeInicio()),
               ])>
                {{ __('Dashboard') }}
            </a>
        </div>

        <div class="flex flex-wrap items-center gap-space-sm">
            {{-- Solo lo ve el admin --}}
            <x-selector-vista />

            <span class="text-label-lg">{{ Auth::user()->name }}</span>

            <a href="{{ route('profile.edit') }}"
               @class([
                   'inline-flex items-center gap-2 min-h-[48px] px-4 rounded text-label-lg transition-colors',
                   'bg-on-surface text-surface-container-lowest' => request()->routeIs('profile.edit'),
                   'text-secondary hover:bg-surface-container-low' => ! request()->routeIs('profile.edit'),
               ])>
                {{ __('Profile') }}
            </a>

            {{-- Cerrar sesión va por POST con @csrf, no con un enlace --}}
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <x-boton-secundario type="submit">
                    {{ __('Log Out') }}
                </x-boton-secundario>
            </form>
        </div>
    </div>
</nav>
