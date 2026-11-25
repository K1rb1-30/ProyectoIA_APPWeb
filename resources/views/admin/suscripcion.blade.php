{{--
    «Suscripción» del restaurante (rol admin, rama 24). Pantalla nueva, sin
    maqueta: sigue el estilo de las vistas de admin rediseñadas (Dispositivos).
    Variables:
      $restaurante     el del admin logueado (nunca otro)
      $precioCentimos  importe mensual, de config (STRIPE_SUSCRIPCION_PRECIO_CENTIMOS)

    Es también «Tu suscripción»: el middleware ComprobarSuscripcion manda aquí
    al admin cuando la suscripción no está activa.

    «Comanda impresa»: el estado es el dato principal (punto + palabra, en
    grande); el resto en filas con línea; un solo botón naranja, que cambia
    según el estado: pagar, reactivar o gestionar en Stripe.
--}}
@php
    $activa = $restaurante->tieneSuscripcionActiva();
    $estado = $restaurante->suscripcion_estado;

    $explicaciones = [
        'pendiente' => 'Tu restaurante está dado de alta, pero falta pagar la suscripción. Hasta entonces, ni tú ni tu personal ni tus clientes podéis usar MesaYa.',
        'activa' => 'Todo en marcha. Stripe cobra la suscripción cada mes con la tarjeta que diste.',
        'impagada' => 'Stripe no ha podido cobrar el último mes. Mientras no se pague, el restaurante no está disponible.',
        'cancelada' => 'La suscripción está cancelada y el restaurante no está disponible. Puedes reactivarla cuando quieras.',
    ];
@endphp

<x-layouts.admin titulo="Suscripción">
    <x-slot name="subtitulo">{{ $restaurante->nombre }}</x-slot>

    <div class="max-w-2xl">
        <section aria-label="Estado de la suscripción" class="pb-space-lg border-b-2 border-on-surface">
            <p class="text-label-lg text-secondary">Estado</p>
            <x-chip-estado :estado="$estado" class="mt-2 !text-headline-lg !gap-3" />
            <p class="mt-space-md text-body-lg text-on-surface-variant">{{ $explicaciones[$estado] ?? '' }}</p>
        </section>

        <dl class="divide-y divide-outline-variant border-b border-outline-variant">
            <div class="flex items-baseline justify-between gap-space-md py-space-md">
                <dt class="text-body-md text-secondary">Importe</dt>
                <dd class="text-price-display"><x-precio :centimos="$precioCentimos" class="font-mono" /> <span class="text-body-md text-secondary">al mes</span></dd>
            </div>
            <div class="flex items-baseline justify-between gap-space-md py-space-md">
                <dt class="text-body-md text-secondary">{{ $activa ? 'Próximo cobro' : 'Pagada hasta' }}</dt>
                <dd class="font-mono text-label-lg">
                    {{ $restaurante->suscripcion_fin_en ? $restaurante->suscripcion_fin_en->format('d/m/Y') : '—' }}
                </dd>
            </div>
        </dl>

        <div class="mt-space-lg">
            @if ($activa)
                {{-- Cambiar la tarjeta, ver facturas o cancelar: lo hace Stripe en su portal --}}
                <form method="POST" action="{{ route('admin.suscripcion.portal') }}">
                    @csrf
                    <x-boton-primario>Gestionar en Stripe</x-boton-primario>
                </form>
                <p class="mt-space-sm text-body-md text-secondary">
                    En el portal de Stripe puedes cambiar la tarjeta, descargar las facturas o cancelar la suscripción.
                </p>
            @else
                <form method="POST" action="{{ route('admin.suscripcion.pagar') }}">
                    @csrf
                    <x-boton-primario>{{ $estado === 'pendiente' ? 'Pagar la suscripción' : 'Reactivar la suscripción' }}</x-boton-primario>
                </form>
                <p class="mt-space-sm text-body-md text-secondary">
                    El pago se hace en Stripe (modo de prueba: tarjeta <span class="font-mono">4242 4242 4242 4242</span>).
                </p>
            @endif
        </div>
    </div>
</x-layouts.admin>
