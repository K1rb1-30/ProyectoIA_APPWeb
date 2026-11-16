{{--
    Layout de las páginas sin sesión iniciada: login, recuperar contraseña,
    restablecerla, confirmarla y verificar el correo.
    Pantalla partida como en la maqueta landing/login: a la izquierda un
    panel oscuro de presentación (solo en pantallas grandes) y a la derecha
    el formulario, que es el contenido de cada vista ($slot).

    Se usa con <x-guest-layout> (lo trae Breeze: app/View/Components/GuestLayout.php).
--}}
<x-layouts.base>
    <div class="min-h-screen flex flex-col lg:flex-row">
        {{-- Panel izquierdo: presentación. "hidden lg:flex" lo oculta en móvil y tablet. --}}
        <div class="hidden lg:flex lg:w-5/12 xl:w-1/2 relative flex-col justify-between p-10 xl:p-14 overflow-hidden bg-inverse-surface">
            {{-- Fondo: degradado oscuro con un brillo naranja. Sustituye a la foto de la maqueta. --}}
            <div class="absolute inset-0 bg-gradient-to-br from-on-secondary-fixed via-inverse-surface to-on-surface"></div>
            <div class="absolute -top-32 -right-32 w-96 h-96 rounded-full bg-primary-container/20 blur-3xl"></div>
            <div class="absolute -bottom-40 -left-24 w-96 h-96 rounded-full bg-tertiary-container/10 blur-3xl"></div>

            <div class="relative flex items-center gap-3">
                <x-logo />
                <div>
                    <span class="block text-headline-sm font-bold text-inverse-on-surface">{{ config('app.name', 'MesaYa') }}</span>
                    <span class="block text-label-sm uppercase text-secondary-fixed-dim">Comandas para bares y restaurantes</span>
                </div>
            </div>

            <div class="relative max-w-lg my-auto py-12 space-y-8">
                <div class="space-y-3">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-primary-fixed/20 border border-primary-fixed/30 text-label-sm uppercase text-inverse-primary">
                        <x-icono nombre="table_restaurant" class="text-sm" />
                        Servicio en tiempo real
                    </span>
                    <h1 class="text-display-hero text-inverse-on-surface">
                        Sincronía total entre barra, sala y fogones.
                    </h1>
                    <p class="text-body-lg text-secondary-fixed-dim">
                        El cliente pide desde el QR de su mesa, la cocina recibe la comanda al momento y la cuenta se divide por persona.
                    </p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="p-4 rounded-md bg-white/5 border border-white/10 space-y-1">
                        <div class="flex items-center gap-2 text-inverse-primary">
                            <x-icono nombre="qr_code_2" class="text-lg" />
                            <span class="text-label-sm uppercase">Pedido por QR</span>
                        </div>
                        <p class="text-price-display text-inverse-on-surface">Carta en el móvil</p>
                        <p class="text-label-sm text-secondary-fixed-dim">Sin instalar ninguna aplicación</p>
                    </div>
                    <div class="p-4 rounded-md bg-white/5 border border-white/10 space-y-1">
                        <div class="flex items-center gap-2 text-tertiary-fixed">
                            <x-icono nombre="verified_user" class="text-lg" />
                            <span class="text-label-sm uppercase">14 alérgenos</span>
                        </div>
                        <p class="text-price-display text-inverse-on-surface">Visibles en cada plato</p>
                        <p class="text-label-sm text-secondary-fixed-dim">Los que exige la normativa europea</p>
                    </div>
                </div>
            </div>

            <div class="relative pt-6 border-t border-white/10 text-label-sm text-secondary-fixed-dim">
                © {{ date('Y') }} {{ config('app.name', 'MesaYa') }} · Práctica de DAW M0617
            </div>
        </div>

        {{-- Panel derecho: el formulario de cada vista --}}
        <div class="w-full lg:w-7/12 xl:w-1/2 flex flex-col p-6 sm:p-10 lg:p-14 xl:p-16">
            <a href="/" class="flex items-center gap-3">
                {{-- En móvil no se ve el panel izquierdo, así que el logo se enseña aquí --}}
                <x-logo class="lg:hidden" />
                <span>
                    <span class="block text-headline-sm font-extrabold text-primary">{{ config('app.name', 'MesaYa') }}</span>
                    <span class="block text-label-md text-secondary">Gestión de sala, cocina y administración</span>
                </span>
            </a>

            <div class="w-full max-w-md mx-auto my-auto py-8">
                {{ $slot }}
            </div>

            <div class="pt-6 border-t border-outline-variant/30 text-center">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-surface-container text-label-sm text-secondary">
                    <x-icono nombre="lock" class="text-sm text-tertiary" />
                    Acceso reservado al personal del restaurante
                </span>
            </div>
        </div>
    </div>
</x-layouts.base>
