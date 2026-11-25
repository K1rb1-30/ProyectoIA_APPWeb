{{--
    Alta de un restaurante nuevo (rama 24). Pantalla nueva, sin maqueta: sigue
    el estilo del login (mismo layout partido, campos sin iconos y un solo
    botón naranja).

    Al enviar se crea el restaurante y su admin, se inicia sesión y se va a
    Stripe a pagar la suscripción mensual (RegistroRestauranteController).
--}}
<x-guest-layout>
    <div class="mb-10">
        <h1 class="text-headline-lg">Da de alta tu restaurante</h1>
        <p class="mt-2 text-body-lg text-secondary">
            Suscripción mensual de <x-precio :centimos="(int) config('services.stripe.suscripcion_precio_centimos')" class="font-mono" />.
            Después podrás crear tu carta, tus mesas y las cuentas de tu personal.
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

        <p class="text-body-md text-secondary">
            El pago se hace en Stripe, en modo de prueba: usa la tarjeta <span class="font-mono">4242 4242 4242 4242</span>.
        </p>
    </form>

    <p class="mt-space-lg pt-space-md border-t border-outline-variant text-body-md text-secondary">
        ¿Tu restaurante ya usa MesaYa?
        <a href="{{ route('login') }}" class="text-on-surface underline underline-offset-2 hover:text-primary">Entra con tu cuenta</a>
    </p>
</x-guest-layout>
