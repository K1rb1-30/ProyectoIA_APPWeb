{{--
    Cookies (rama 16). Página simple: lista las cookies que pone de verdad la
    aplicación (las de Laravel) y el almacenamiento del carrito. Si se añade
    alguna cookie, hay que apuntarla aquí.
--}}
<x-pagina-legal titulo="Cookies">
    <p>
        MesaYa solo usa las cookies necesarias para funcionar. <strong>No hay cookies de publicidad ni de
        analítica</strong>, y las fuentes e iconos se sirven desde la propia aplicación, sin llamar a servicios externos.
    </p>

    <h2>Cookies de la aplicación</h2>
    <ul>
        <li><code>{{ config('session.cookie') }}</code>: guarda la sesión (por ejemplo, que el personal ha iniciado sesión o lo que ha pedido el cliente desde su móvil). Caduca al poco tiempo sin usarse.</li>
        <li><code>XSRF-TOKEN</code>: protege los formularios para que nadie pueda enviarlos haciéndose pasar por ti.</li>
        <li><code>remember_web_…</code>: solo si el personal marca «Mantener la sesión iniciada en este dispositivo».</li>
    </ul>

    <h2>El carrito del cliente</h2>
    <p>
        Mientras el cliente elige los platos, el carrito se guarda en el <strong>almacenamiento local</strong> de su
        navegador (no es una cookie y no se envía a ningún sitio). Así no se pierde si cambia de pantalla. Se vacía al
        enviar el pedido.
    </p>

    <h2>Pago con tarjeta</h2>
    <p>
        Al pagar con tarjeta, el cliente pasa a la página de Stripe, que pone sus propias cookies para que el pago sea
        seguro. Esas cookies son de Stripe, no de MesaYa.
    </p>

    <p>Más información sobre los datos en la página de <a href="{{ route('legal.privacidad') }}" class="text-primary underline">privacidad</a>.</p>
</x-pagina-legal>
