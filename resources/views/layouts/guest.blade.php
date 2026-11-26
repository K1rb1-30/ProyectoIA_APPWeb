{{--
    Layout de las páginas sin sesión iniciada: login, recuperar contraseña,
    restablecerla, confirmarla y verificar el correo.
    Pantalla partida: a la izquierda un panel de tinta con una frase corta
    (en el móvil pasa a ser una franja arriba) y a la derecha el formulario,
    que es el contenido de cada vista ($slot).

    Se usa con <x-guest-layout> (lo trae Breeze: app/View/Components/GuestLayout.php).

    Rediseño «Comanda impresa» (rama 21): sin degradados, brillos, pastillas
    ni tarjetas de cifras. Solo el nombre, una frase y para quién es el acceso.
--}}
<x-layouts.base>
    <div class="min-h-screen flex flex-col lg:flex-row">
        {{-- Panel de tinta. En el móvil se queda arriba, más bajo y sin la frase grande. --}}
        <div class="lg:w-5/12 flex flex-col justify-between gap-space-lg px-6 py-5 sm:px-10 lg:p-12 bg-inverse-surface text-inverse-on-surface">
            <a href="{{ route('inicio') }}" class="text-headline-md font-extrabold tracking-tight">{{ config('app.name', 'MesaYa') }}<span class="text-inverse-primary">.</span></a>

            <div class="hidden lg:block">
                <p class="text-[52px] leading-[1.02] tracking-[-0.04em] font-extrabold">Buen servicio.</p>
                <p class="mt-5 max-w-sm text-body-lg text-secondary-fixed-dim">
                    {{-- Slot opcional "frase" (rama 24): el alta del restaurante pone la suya. --}}
                    @isset($frase)
                        {{ $frase }}
                    @else
                        {{-- Rama 25: aquí solo entra la administración. Los camareros
                             (PIN) y la cocina (tablet) entran desde los dispositivos. --}}
                        Entra con el correo y la contraseña de administración de tu restaurante. Los camareros y la cocina entran desde los dispositivos del bar.
                    @endisset
                </p>
            </div>

            <p class="hidden lg:block font-mono text-label-md font-normal text-outline">
                ¿Eres cliente? No necesitas cuenta: escanea el QR de tu mesa.
            </p>
        </div>

        {{-- Formulario de cada vista --}}
        <div class="flex-1 flex flex-col px-6 py-10 sm:px-10 lg:p-12">
            <div class="w-full max-w-md mx-auto my-auto">
                {{ $slot }}
            </div>

            <p class="mt-10 text-center text-body-md text-secondary">
                © {{ date('Y') }} {{ config('app.name', 'MesaYa') }} · Práctica de DAW M0617
            </p>
        </div>
    </div>
</x-layouts.base>
