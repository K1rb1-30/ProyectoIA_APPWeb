{{--
    Alta de un restaurante nuevo (rama 24, textos de la rama 25). Pantalla sin
    maqueta: sigue el estilo del login (mismo layout partido, campos sin
    iconos y un solo botón naranja).

    Al enviar se crea el restaurante y su admin, se inicia sesión y se va a
    Stripe a pagar la suscripción mensual (RegistroRestauranteController).

    Rama 25: encima, los 3 pasos del alta en pequeño (estás en el 1); debajo
    del botón, las condiciones: cobro mensual, cancelar, qué pasa si se cierra
    Stripe sin pagar, y que el pago es de prueba.
--}}
<x-guest-layout>
    <x-slot name="frase">
        Para el dueño del bar o restaurante: te das de alta, pagas la suscripción y preparas tu carta, tus mesas y tu equipo.
    </x-slot>

    <x-pasos-alta compacto :actual="1" class="mb-8" />

    <div class="mb-10">
        <h1 class="text-headline-lg">Da de alta tu restaurante</h1>
        <p class="mt-2 text-body-lg text-secondary">
            Suscripción mensual de <x-precio :centimos="(int) config('services.stripe.suscripcion_precio_centimos')" class="font-mono text-on-surface" />.
        </p>
    </div>

    <form method="POST" action="{{ route('registro.guardar') }}" class="space-y-5">
        @csrf

        <x-campo-texto nombre="nombre_restaurante" etiqueta="Nombre del restaurante"
                       :value="old('nombre_restaurante')" placeholder="Bar Ejemplo"
                       required autofocus maxlength="100" autocomplete="organization" />

        <x-campo-texto nombre="name" etiqueta="Tu nombre"
                       :value="old('name')" required autocomplete="name" />

        <x-campo-texto nombre="email" tipo="email" etiqueta="Correo electrónico"
                       :value="old('email')" placeholder="nombre@restaurante.es"
                       required autocomplete="username" />

        <x-campo-texto nombre="password" tipo="password" etiqueta="Contraseña"
                       required autocomplete="new-password" />

        <x-campo-texto nombre="password_confirmation" tipo="password" etiqueta="Repite la contraseña"
                       required autocomplete="new-password" />

        <x-boton-primario class="w-full min-h-[52px]">
            Continuar al pago
        </x-boton-primario>

        {{-- Condiciones: lo que hace la aplicación de verdad (rama 24) --}}
        <ul class="pt-space-md border-t border-outline-variant space-y-2 text-body-md text-secondary">
            <li>El cobro se repite cada mes. La cancelas cuando quieras desde tu panel.</li>
            <li>Si cierras Stripe sin pagar, tu cuenta se guarda: entra con tu correo y paga cuando quieras.</li>
            <li>
                Es un pago de prueba: no se cobra nada de verdad. Usa la tarjeta
                <span class="font-mono text-on-surface whitespace-nowrap">4242 4242 4242 4242</span>.
            </li>
        </ul>
    </form>

    <p class="mt-space-lg pt-space-md border-t border-outline-variant text-body-md text-secondary">
        ¿Tu restaurante ya usa MesaYa?
        <a href="{{ route('login') }}" class="text-on-surface underline underline-offset-2 hover:text-primary">Entra con tu cuenta</a>
    </p>
</x-guest-layout>
