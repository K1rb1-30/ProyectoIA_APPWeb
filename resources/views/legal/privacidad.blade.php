{{--
    Privacidad (rama 16; alta y suscripción, rama 25). Página simple: cuenta lo que la aplicación hace de
    verdad con los datos. Si cambia lo que se guarda, hay que cambiar este texto.
--}}
<x-pagina-legal titulo="Privacidad">
    <h2>Qué datos guarda MesaYa</h2>
    <ul>
        <li><strong>Personal del restaurante</strong>: el administrador, con su nombre, correo electrónico y contraseña; los camareros, con su nombre y un PIN de 4 cifras (sin correo). La contraseña y el PIN se guardan cifrados: nadie puede leerlos. La cocina no tiene cuenta: sus tablets se emparejan con el restaurante.</li>
        <li><strong>Alta del restaurante y suscripción</strong>: al dar de alta un restaurante se guardan su nombre y los datos de su administrador (los de arriba). De la suscripción mensual se guardan su estado, la fecha del próximo cobro y los identificadores de cliente y de suscripción que da Stripe.</li>
        <li><strong>Clientes</strong>: no tienen cuenta y no se les piden datos personales. Sus pedidos se guardan con la mesa, los platos, las cantidades y las notas que escriban (por ejemplo, «sin cebolla»).</li>
        <li><strong>Cuentas y pagos</strong>: el importe de cada parte, cómo se ha pagado (tarjeta o efectivo) y, si es con tarjeta por Stripe, el identificador de la operación.</li>
        <li><strong>Sesiones</strong>: cada visita (también la del cliente que entra por el QR) tiene una sesión guardada en la base de datos con la dirección IP y el navegador, como hace Laravel por defecto. Caduca al rato sin usarse.</li>
    </ul>

    <h2>Pagos con tarjeta</h2>
    <p>
        Los datos de la tarjeta se escriben en la página de pago de Stripe, no en MesaYa: la aplicación nunca los ve
        ni los guarda. La aplicación funciona siempre en el <strong>modo de prueba</strong> de Stripe, sin cobros reales.
    </p>

    <h2>Para qué se usan</h2>
    <p>
        Solo para que la aplicación funcione: enseñar la carta, llevar los pedidos a cocina, dividir y cobrar la cuenta y
        calcular las estadísticas del restaurante. Cada restaurante solo ve sus propios datos. No se venden ni se ceden a
        nadie; el único servicio externo es Stripe, para los pagos con tarjeta y la suscripción del restaurante.
    </p>

    <h2>Tus datos</h2>
    <p>
        Si eres el administrador de un restaurante que usa MesaYa, puedes cambiar tu nombre, tu correo y tu contraseña
        desde tu perfil. Las cuentas de los camareros las crea, cambia y borra el administrador. Para cualquier otra cosa,
        habla con el administrador de tu restaurante.
    </p>

    <p>Más información sobre las cookies en la página de <a href="{{ route('legal.cookies') }}" class="text-primary underline">cookies</a>.</p>
</x-pagina-legal>
