{{--
    «Integraciones» del restaurante (rol admin, rama 26). Pantalla nueva, sin
    maqueta: copia la estructura de admin/suscripcion.blade.php (estado en
    grande arriba, datos en filas con línea y un solo botón naranja).
    Variables:
      $restaurante  el del usuario logueado (nunca otro)

    De momento solo tiene la sección «Stripe»: la cuenta del RESTAURANTE con
    la que cobra a sus clientes (no es la suscripción a MesaYa).

    Seguridad: la clave secreta NO llega a esta vista. Solo se enseñan sus 4
    últimas cifras (stripe_cobros_final_clave) y el campo para pegar una
    nueva va siempre vacío.
--}}
@php
    $conectada = $restaurante->tieneStripeConfigurado();
@endphp

<x-layouts.admin titulo="Integraciones">
    <x-slot name="subtitulo">{{ $restaurante->nombre }}</x-slot>

    <div class="max-w-2xl">
        <section aria-labelledby="titulo-stripe" class="pb-space-lg border-b-2 border-on-surface">
            <h2 id="titulo-stripe" class="text-label-lg text-secondary">Stripe · pagos con tarjeta de tus clientes</h2>
            <x-chip-estado :estado="$conectada ? 'conectada' : 'sin_configurar'" class="mt-2 !text-headline-lg !gap-3" />
            <p class="mt-space-md text-body-lg text-on-surface-variant">
                @if ($conectada)
                    Lo que pagan tus clientes con tarjeta (la cuenta dividida y los pedidos desde el QR) llega a tu cuenta de Stripe.
                @else
                    Tus clientes solo pueden pagar en efectivo. Conecta tu cuenta de Stripe para que puedan pagar con tarjeta desde el móvil.
                @endif
            </p>
        </section>

        @if ($conectada)
            <dl class="divide-y divide-outline-variant border-b border-outline-variant">
                <div class="flex flex-wrap items-baseline justify-between gap-x-space-md gap-y-1 py-space-md">
                    <dt class="text-body-md text-secondary">Clave publicable</dt>
                    <dd class="font-mono text-label-md break-all">{{ $restaurante->stripe_cobros_clave_publicable }}</dd>
                </div>
                <div class="flex flex-wrap items-baseline justify-between gap-x-space-md gap-y-1 py-space-md">
                    <dt class="text-body-md text-secondary">Clave secreta</dt>
                    <dd class="text-label-md">Configurada · termina en <span class="font-mono">••••{{ $restaurante->stripe_cobros_final_clave }}</span></dd>
                </div>
                <div class="flex flex-wrap items-baseline justify-between gap-x-space-md gap-y-1 py-space-md">
                    <dt class="text-body-md text-secondary">Comprobada con Stripe</dt>
                    <dd class="font-mono text-label-md">{{ $restaurante->stripe_cobros_comprobada_en?->format('d/m/Y H:i') ?? '—' }}</dd>
                </div>
            </dl>
        @endif

        <section aria-labelledby="titulo-claves" class="mt-space-xl">
            <h2 id="titulo-claves" class="text-headline-md pb-2 border-b-2 border-on-surface">
                {{ $conectada ? 'Cambiar las claves' : 'Conectar tu cuenta de Stripe' }}
            </h2>

            <p class="mt-space-md text-body-md text-secondary">
                En el panel de Stripe, activa el <strong class="text-on-surface">modo de prueba</strong> y ve a
                <strong class="text-on-surface">Desarrolladores → Claves de API</strong>. Copia la clave publicable
                (<span class="font-mono">pk_test_...</span>) y crea una <strong class="text-on-surface">clave restringida</strong>
                (<span class="font-mono">rk_test_...</span>) con permiso de escritura en «Checkout Sessions».
                También vale la clave secreta (<span class="font-mono">sk_test_...</span>). Las claves de modo real no se aceptan.
            </p>

            <form method="POST" action="{{ route('admin.integraciones.stripe.guardar') }}" class="mt-space-lg space-y-space-md">
                @csrf
                @method('PUT')

                <x-campo-texto nombre="clave_publicable" etiqueta="Clave publicable"
                               :value="old('clave_publicable', $restaurante->stripe_cobros_clave_publicable)"
                               placeholder="pk_test_..." autocomplete="off" spellcheck="false" required />

                {{-- Sin value: la clave guardada no se vuelve a enseñar nunca.
                     autocomplete="new-password" evita que el navegador meta aquí
                     una contraseña guardada. --}}
                <x-campo-texto nombre="clave_secreta" tipo="password"
                               :etiqueta="$conectada ? 'Clave secreta nueva' : 'Clave secreta'"
                               placeholder="rk_test_... o sk_test_..." autocomplete="new-password" spellcheck="false" required />

                <div class="pt-space-sm">
                    <x-boton-primario type="submit">Guardar</x-boton-primario>
                </div>
                <p class="text-body-md text-secondary">
                    Al guardar, comprobamos con Stripe que la clave funciona. La clave secreta se guarda cifrada y no se vuelve a mostrar.
                </p>
            </form>

            @if ($conectada)
                {{-- Acción secundaria: enlace, no botón naranja --}}
                <form method="POST" action="{{ route('admin.integraciones.stripe.quitar') }}" class="mt-space-lg pt-space-md border-t border-outline-variant"
                      onsubmit="return confirm('¿Quitar las claves de Stripe? Tus clientes solo podrán pagar en efectivo.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center min-h-[44px] text-label-md text-secondary underline underline-offset-2 hover:text-error">
                        Quitar las claves de Stripe
                    </button>
                </form>
            @endif
        </section>
    </div>
</x-layouts.admin>
