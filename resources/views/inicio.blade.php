{{--
    Página de inicio pública de MesaYa (rama 16, rediseño en la rama 21).
    Variables: $urlCartaDemo  URL de la carta de la mesa 1 de "la-tasca", o null
                              si no existe (entonces no sale el botón).

    Solo cuenta lo que la aplicación hace de verdad (ver docs/registre-ia.md,
    entrada 30): nada de cifras ni funciones inventadas.

    Rediseño «Comanda impresa»:
    - Portada alineada a la izquierda con un título grande de tres frases y, al
      lado, una comanda impresa de ejemplo con la cuenta dividida entre 3
      (los platos y precios del ticket son de ejemplo, escritos aquí a mano).
    - «Cómo funciona» es una lista numerada con líneas, no tres tarjetas.
    - Los roles van en columnas con una raya gruesa encima, sin iconos.
    - El panel del restaurante va en una franja oscura.

    Secciones (anclas del menú): #inicio, #como-funciona, #cocina-y-sala, #panel
    y #suscripcion (rama 25: precio, qué incluye, alta en 3 pasos y condiciones).
--}}
@php
    $usuario = Auth::user();

    // Líneas del ticket de ejemplo (precio de la línea en céntimos).
    $lineasTicket = [
        ['cantidad' => 2, 'plato' => 'Croquetas caseras', 'centimos' => 1700, 'nota' => null],
        ['cantidad' => 1, 'plato' => 'Patatas bravas', 'centimos' => 600, 'nota' => null],
        ['cantidad' => 1, 'plato' => 'Tortilla de patatas', 'centimos' => 750, 'nota' => 'sin cebolla'],
        ['cantidad' => 1, 'plato' => 'Pan', 'centimos' => 50, 'nota' => null],
    ];
    $totalTicket = array_sum(array_column($lineasTicket, 'centimos')); // 3100 = 31,00 €

    // Reparto entre 3 con la misma regla que la aplicación: el céntimo que
    // sobra va a la primera parte (31,00 € → 10,34 + 10,33 + 10,33).
    $base = intdiv($totalTicket, 3);
    $partesTicket = [$base + ($totalTicket - $base * 3), $base, $base];

    // Tres pasos de "Cómo funciona".
    $pasos = [
        [
            'titulo' => 'Carta por QR y autopedido',
            'texto' => 'Cada mesa tiene su QR. El cliente lo escanea con la cámara, ve la carta con fotos, precios y los alérgenos de cada plato, y envía su pedido a cocina sin instalar nada. Los platos agotados no aparecen y no se pueden pedir.',
        ],
        [
            'titulo' => 'Cocina y sala conectadas',
            'texto' => 'Los pedidos del QR y las comandas del camarero llegan a la pantalla de cocina al momento. Cocina los pasa de pendiente a preparando y a listo, y el camarero recibe el aviso. Las pantallas se actualizan solas cada pocos segundos.',
        ],
        [
            'titulo' => 'Cuenta dividida y pago',
            'texto' => 'La mesa divide la cuenta a partes iguales, por platos o con platos compartidos, y la suma de las partes es siempre el total exacto. Cada persona paga con tarjeta (Stripe) o en efectivo, y la mesa se libera cuando todo está cobrado.',
        ],
    ];

    // Una pantalla para cada rol (tabla de roles de CLAUDE.md).
    $roles = [
        ['rol' => 'Cliente', 'texto' => 'Sin cuenta ni contraseña: carta por QR, pedir, ver el estado, dividir la cuenta y pagar.'],
        ['rol' => 'Camarero', 'texto' => 'Mapa de mesas, comandas, avisos de plato listo, cobro en efectivo o con TPV y cierre de mesa.'],
        ['rol' => 'Cocina', 'texto' => 'Tablero con los pedidos por estado y las notas de cada plato. Un toque para avanzar.'],
        ['rol' => 'Administración', 'texto' => 'Carta, alérgenos, mesas y QR, personal, dispositivos, estadísticas de ventas y la suscripción.'],
    ];

    // Rama 25: sección «Suscripción». El precio sale de config, nunca escrito a mano.
    $precioSuscripcion = (int) config('services.stripe.suscripcion_precio_centimos');

    // Qué incluye: solo lo que ya cuenta esta página (y hace la aplicación).
    $incluye = [
        ['titulo' => 'Carta y QR', 'texto' => 'Carta con fotos, precios y alérgenos, platos agotados que no se pueden pedir y un QR por mesa listo para imprimir.'],
        ['titulo' => 'Cocina', 'texto' => 'Tablero con los pedidos por estado en las tablets de cocina, que se actualiza solo.'],
        ['titulo' => 'Camareros', 'texto' => 'Mapa de mesas, comandas, avisos de plato listo y cobro en la mesa, cada uno con su nombre y su PIN.'],
        ['titulo' => 'Cuenta y pagos', 'texto' => 'Cuenta dividida a partes iguales, por platos o con platos compartidos, y pago con tarjeta o en efectivo.'],
        ['titulo' => 'Estadísticas', 'texto' => 'Ventas por día, platos más pedidos y tarjeta frente a efectivo.'],
    ];

    // Condiciones: lo que hace la aplicación con la suscripción (rama 24).
    $condiciones = [
        'Se cobra cada mes, con la tarjeta que des al darte de alta.',
        'La cancelas cuando quieras desde el portal de Stripe, en la pantalla «Suscripción» de tu panel.',
        'Si un cobro falla o la cancelas, tu restaurante deja de estar disponible para tu personal y tus clientes hasta que vuelvas a pagar. La carta, las mesas y las cuentas se conservan.',
    ];

    // Lo que tiene el panel del restaurante.
    $panel = [
        ['titulo' => 'Carta con fotos', 'texto' => 'Platos por categorías, con foto, precio, alérgenos y un interruptor de «agotado».'],
        ['titulo' => 'Mesas y QR', 'texto' => 'Cada mesa con su código QR, listo para imprimir o guardar en PDF desde el navegador.'],
        ['titulo' => 'Estadísticas', 'texto' => 'Ventas por día, platos más pedidos y tarjeta frente a efectivo.'],
        ['titulo' => 'Varios restaurantes', 'texto' => 'Cada restaurante tiene sus datos separados: nunca ve los de otro.'],
    ];
@endphp

<x-layouts.publico titulo="Comandas y cartas QR para bares y restaurantes">

    {{-- ===================== Portada ===================== --}}
    <section id="inicio" class="max-w-6xl mx-auto px-4 sm:px-8 pt-space-xl pb-16 lg:pt-16 lg:pb-24 flex flex-wrap items-center gap-12 lg:gap-16">
        <div class="flex-[999_1_520px] min-w-0">
            <p class="text-body-lg font-semibold text-secondary">Pide desde tu mesa, sin esperas.</p>
            <h1 class="mt-space-lg text-[clamp(2.5rem,5vw,4.25rem)] leading-[0.98] tracking-[-0.045em] font-extrabold">
                La mesa pide.<br>La cocina se entera.<br><span class="text-primary-container">La cuenta cuadra.</span>
            </h1>
            <p class="mt-space-xl max-w-xl text-body-lg text-on-surface-variant">
                El cliente escanea el QR de su mesa, ve la carta con los alérgenos y pide. La cocina recibe el pedido al
                momento y, al acabar, la cuenta se divide por persona y se paga con tarjeta o en efectivo.
            </p>

            {{-- Rama 25: el único botón naranja de la página es el alta, porque
                 sale siempre (la carta de ejemplo depende de los datos de prueba).
                 Con sesión no sale: /registro es solo para quien no ha entrado. --}}
            <div class="mt-10 flex flex-wrap items-center gap-x-space-xl gap-y-space-md">
                @guest
                    <a href="{{ route('registro') }}"
                       class="pulsable inline-flex items-center justify-center min-h-[52px] px-7 rounded bg-primary-container text-on-primary text-label-lg font-bold hover:bg-primary">
                        Da de alta tu restaurante
                    </a>
                @endguest
                @if ($urlCartaDemo)
                    <a href="{{ $urlCartaDemo }}" class="inline-flex items-center min-h-[44px] text-label-lg underline underline-offset-[6px] hover:text-primary">Ver una carta de ejemplo</a>
                @endif
                <a href="#como-funciona" class="inline-flex items-center min-h-[44px] text-label-lg underline underline-offset-[6px] hover:text-primary">Cómo funciona</a>
            </div>
            <p class="mt-space-md text-body-md text-secondary">
                Suscripción mensual de <x-precio :centimos="$precioSuscripcion" class="font-mono text-on-surface" /> ·
                <a href="#suscripcion" class="text-on-surface underline underline-offset-2 hover:text-primary">Mira qué incluye</a>.
            </p>
        </div>

        {{-- Ilustración: una comanda impresa. aria-hidden porque es decorativa
             (los datos son de ejemplo, no de la base de datos). --}}
        <figure class="flex-[1_1_320px] max-w-[400px] mx-auto bg-surface-container-lowest p-7 shadow-nivel-2 rotate-[1.2deg]" aria-hidden="true">
            <div class="flex justify-between font-mono text-label-md font-normal text-secondary"><span>LA TASCA</span><span>21:14</span></div>
            <p class="mt-3 text-[40px] leading-none font-extrabold tracking-tight">Mesa 4</p>

            <div class="mt-5 pt-3.5 border-t-[1.5px] border-dashed border-secondary-fixed-dim space-y-2.5 font-mono text-body-md">
                @foreach ($lineasTicket as $linea)
                    <div class="flex justify-between gap-3">
                        <span>{{ $linea['cantidad'] }} × {{ $linea['plato'] }}</span>
                        <span>{{ number_format($linea['centimos'] / 100, 2, ',', '.') }}</span>
                    </div>
                    @if ($linea['nota'])
                        <div class="pl-7 text-secondary">{{ $linea['nota'] }}</div>
                    @endif
                @endforeach
            </div>

            <div class="mt-3.5 pt-3.5 border-t-[1.5px] border-dashed border-secondary-fixed-dim flex justify-between items-baseline">
                <span class="text-label-lg">Total</span>
                <x-precio :centimos="$totalTicket" class="font-mono text-[26px] font-semibold" />
            </div>

            <div class="mt-4 px-3.5 py-3 bg-background font-mono text-label-md font-normal leading-relaxed">
                <div class="text-secondary">Entre 3 personas</div>
                <div>{{ collect($partesTicket)->map(fn ($c) => number_format($c / 100, 2, ',', '.'))->implode(' + ') }}</div>
            </div>
        </figure>
    </section>

    {{-- ===================== Cómo funciona ===================== --}}
    <section id="como-funciona" class="scroll-mt-16 border-t border-outline-variant">
        <div class="max-w-6xl mx-auto px-4 sm:px-8 py-20 flex flex-wrap gap-x-16 gap-y-12">
            <h2 class="flex-[1_1_300px] text-[40px] leading-[1.05] tracking-[-0.03em] font-extrabold">De la mesa a la cocina, y de vuelta a la cuenta.</h2>

            <ol class="flex-[999_1_600px] min-w-0">
                @foreach ($pasos as $indice => $paso)
                    <li @class([
                        'flex flex-wrap gap-x-8 gap-y-2 py-7 border-t',
                        'border-on-surface' => $loop->first,
                        'border-outline-variant' => ! $loop->first,
                        'border-b border-b-outline-variant' => $loop->last,
                    ])>
                        {{-- Número del paso en letra de ticket: 01, 02, 03 --}}
                        <span class="w-12 font-mono text-label-lg text-primary-container">{{ sprintf('%02d', $indice + 1) }}</span>
                        <div class="flex-[1_1_420px]">
                            <h3 class="text-headline-md">{{ $paso['titulo'] }}</h3>
                            <p class="mt-2.5 text-body-lg text-on-surface-variant">{{ $paso['texto'] }}</p>
                        </div>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    {{-- ===================== Cocina y sala (roles) ===================== --}}
    <section id="cocina-y-sala" class="scroll-mt-16 bg-surface-container-lowest border-y border-outline-variant">
        <div class="max-w-6xl mx-auto px-4 sm:px-8 py-20">
            <h2 class="max-w-2xl text-[40px] leading-[1.05] tracking-[-0.03em] font-extrabold">Cada persona del equipo entra directa a lo suyo.</h2>
            <p class="mt-4 max-w-xl text-body-lg text-secondary">El cliente no necesita cuenta: le basta con el QR de la mesa.</p>

            <div class="mt-14 grid gap-x-8 gap-y-10 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($roles as $rol)
                    <div class="pt-5 border-t-[3px] border-on-surface">
                        <h3 class="text-headline-md">{{ $rol['rol'] }}</h3>
                        <p class="mt-2.5 text-body-md text-on-surface-variant">{{ $rol['texto'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ===================== Panel del restaurante ===================== --}}
    <section id="panel" class="scroll-mt-16 bg-inverse-surface text-inverse-on-surface">
        <div class="max-w-6xl mx-auto px-4 sm:px-8 py-24 flex flex-wrap items-start gap-x-20 gap-y-14">
            <div class="flex-[1_1_360px]">
                <h2 class="text-[40px] leading-[1.05] tracking-[-0.03em] font-extrabold">Todo el restaurante, desde un panel.</h2>
                <p class="mt-4 text-body-lg text-secondary-fixed-dim">El administrador prepara la carta y las mesas, y ve cómo van las ventas.</p>
            </div>
            <dl class="flex-[999_1_540px] min-w-0 grid gap-x-10 sm:grid-cols-2">
                @foreach ($panel as $elemento)
                    <div class="py-5 border-t border-on-surface-variant">
                        <dt class="text-headline-sm">{{ $elemento['titulo'] }}</dt>
                        <dd class="mt-1.5 text-body-md text-secondary-fixed-dim">{{ $elemento['texto'] }}</dd>
                    </div>
                @endforeach
            </dl>
        </div>
    </section>

    {{-- ===================== Suscripción (rama 25) =====================
         Misma estructura que «Cómo funciona»: título a la izquierda y, a la
         derecha, listas en filas con líneas. El dato principal es el precio,
         que sale de config (STRIPE_SUSCRIPCION_PRECIO_CENTIMOS): nunca a mano.
         No hay tarjetas de precios: hay un solo plan. --}}
    <section id="suscripcion" class="scroll-mt-16 border-b border-outline-variant">
        <div class="max-w-6xl mx-auto px-4 sm:px-8 py-20 flex flex-wrap gap-x-16 gap-y-12">
            <div class="flex-[1_1_300px]">
                <h2 class="text-[40px] leading-[1.05] tracking-[-0.03em] font-extrabold">Una suscripción al mes. Todo incluido.</h2>
                <p class="mt-space-lg font-mono text-[clamp(3rem,7vw,4.5rem)] leading-none font-semibold tracking-tight">
                    <x-precio :centimos="$precioSuscripcion" />
                </p>
                <p class="mt-2 text-body-lg text-secondary">al mes, por restaurante</p>

                {{-- Condiciones: solo lo que hace la aplicación de verdad --}}
                <ul class="mt-space-xl pt-space-md border-t-2 border-on-surface space-y-3 text-body-md text-on-surface-variant">
                    @foreach ($condiciones as $condicion)
                        <li>{{ $condicion }}</li>
                    @endforeach
                </ul>

                <a href="{{ route('registro') }}" class="mt-space-lg inline-flex items-center min-h-[44px] text-label-lg underline underline-offset-[6px] hover:text-primary">Da de alta tu restaurante</a>
            </div>

            <div class="flex-[999_1_600px] min-w-0">
                <h3 class="text-headline-md">Qué incluye</h3>
                <dl class="mt-space-md border-t border-on-surface">
                    @foreach ($incluye as $elemento)
                        <div class="flex flex-wrap gap-x-8 gap-y-1 py-4 border-b border-outline-variant">
                            <dt class="w-48 text-headline-sm">{{ $elemento['titulo'] }}</dt>
                            <dd class="flex-[1_1_300px] text-body-md text-on-surface-variant">{{ $elemento['texto'] }}</dd>
                        </div>
                    @endforeach
                </dl>

                <h3 class="mt-14 text-headline-md">Cómo darte de alta</h3>
                <x-pasos-alta class="mt-space-md" />
            </div>
        </div>
    </section>

    {{-- ===================== Llamada final =====================
         Rama 25: habla al dueño (precio y alta). El personal ya no tiene
         cuenta propia (los camareros entran con PIN y la cocina con su tablet):
         por eso «Acceso del personal» solo lo usa el administrador. --}}
    <section class="max-w-6xl mx-auto px-4 sm:px-8 py-24 flex flex-wrap items-end justify-between gap-x-12 gap-y-6">
        <div class="flex-[1_1_500px]">
            <h2 class="text-[40px] leading-[1.05] tracking-[-0.03em] font-extrabold">¿Quieres MesaYa en tu restaurante?</h2>
            <p class="mt-3.5 text-body-lg text-secondary">
                Suscripción mensual de <x-precio :centimos="$precioSuscripcion" class="font-mono text-on-surface" /> ·
                <a href="#suscripcion" class="text-on-surface underline underline-offset-2 hover:text-primary">Mira qué incluye</a>
                o <a href="{{ route('registro') }}" class="text-on-surface underline underline-offset-2 hover:text-primary">da de alta tu restaurante</a>.
            </p>
            <p class="mt-2 text-body-md text-secondary">
                ¿Tu restaurante ya lo usa? El administrador entra con su correo; los camareros y la cocina, desde los dispositivos del bar.
            </p>
        </div>
        <x-boton-acceso :usuario="$usuario" class="min-h-[52px] px-7" />
    </section>
</x-layouts.publico>
