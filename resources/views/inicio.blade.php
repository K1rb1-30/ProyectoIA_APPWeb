{{--
    Página de inicio pública de MesaYa (rama 16). Maqueta: landing/inicio.
    Variables: $urlCartaDemo  URL de la carta de la mesa 1 de "la-tasca", o null
                              si no existe (entonces no sale el botón).

    Solo cuenta lo que la aplicación hace de verdad. De la maqueta se han
    quitado las cifras y funciones inventadas (ver docs/registre-ia.md, entrada 30).
    Las fotos de la maqueta (de un servidor externo) se han cambiado por
    ilustraciones hechas con HTML y Tailwind: un móvil con la carta y una
    tarjeta de la pantalla de cocina. Los platos y precios de las
    ilustraciones son de ejemplo y están escritos aquí a mano.

    Secciones (anclas del menú): #inicio, #como-funciona, #cocina-y-sala, #panel.
--}}
@php
    $usuario = Auth::user();

    // Platos de ejemplo del móvil de la portada (precio en céntimos).
    $platosEjemplo = [
        ['nombre' => 'Croquetas caseras', 'texto' => 'Bechamel cremosa y rebozado crujiente.', 'alergenos' => ['Gluten', 'Lácteos'], 'centimos' => 850],
        ['nombre' => 'Patatas bravas', 'texto' => 'Con salsa brava de la casa.', 'alergenos' => [], 'centimos' => 600],
        ['nombre' => 'Tortilla de patatas', 'texto' => 'Jugosa, hecha al momento.', 'alergenos' => ['Huevos'], 'centimos' => 750],
    ];

    // Tres pasos de "Cómo funciona".
    $pasos = [
        [
            'icono' => 'qr_code_scanner', 'color' => 'bg-primary-fixed/40 text-primary',
            'titulo' => '1. Carta por QR y autopedido',
            'texto' => 'Cada mesa tiene su QR. El cliente lo escanea con la cámara, ve la carta con fotos, precios y alérgenos, y envía su pedido a cocina. Sin instalar nada.',
            'puntos' => ['Los 14 alérgenos de declaración obligatoria en cada plato', 'Los platos agotados no aparecen y no se pueden pedir', 'El cliente sigue el estado de su pedido en directo'],
        ],
        [
            'icono' => 'soup_kitchen', 'color' => 'bg-secondary-container text-on-secondary-container',
            'titulo' => '2. Cocina y sala conectadas',
            'texto' => 'Los pedidos del QR y las comandas del camarero llegan a la pantalla de cocina al momento. Cocina los marca como «preparando» y «listo», y el camarero recibe el aviso.',
            'puntos' => ['Estados pendiente → preparando → listo → servido', 'Aviso con sonido al camarero cuando un plato está listo', 'Las pantallas se actualizan solas cada pocos segundos'],
        ],
        [
            'icono' => 'payments', 'color' => 'bg-tertiary-fixed/50 text-tertiary',
            'titulo' => '3. Cuenta dividida y pago',
            'texto' => 'La mesa pide la cuenta desde el móvil y la divide a partes iguales, por platos o con platos compartidos. Cada persona paga su parte.',
            'puntos' => ['La suma de las partes es siempre el total exacto', 'Pago con tarjeta (Stripe) o en efectivo con el camarero', 'La mesa se libera cuando todo está cobrado'],
        ],
    ];

    // Una pantalla para cada rol (tabla de roles de CLAUDE.md).
    $roles = [
        ['icono' => 'smartphone', 'rol' => 'Cliente', 'texto' => 'Sin cuenta ni contraseña: carta por QR, pedir, ver el estado, dividir la cuenta y pagar.'],
        ['icono' => 'room_service', 'rol' => 'Camarero', 'texto' => 'Mapa de mesas, comandas, avisos de plato listo, cobro en efectivo o con TPV y cierre de mesa.'],
        ['icono' => 'skillet', 'rol' => 'Cocina', 'texto' => 'Tablero con los pedidos por estado, notas de cada plato y cambio de estado con un toque.'],
        ['icono' => 'admin_panel_settings', 'rol' => 'Administrador', 'texto' => 'Carta, alérgenos, mesas y QR, y estadísticas de ventas del restaurante.'],
    ];

    // Lo que tiene el panel del restaurante.
    $panel = [
        ['icono' => 'menu_book', 'titulo' => 'Carta con fotos', 'texto' => 'Platos por categorías, con foto, precio, alérgenos y un interruptor de «agotado».'],
        ['icono' => 'qr_code_2', 'titulo' => 'Mesas y QR', 'texto' => 'Cada mesa con su código QR, listo para imprimir o guardar en PDF desde el navegador.'],
        ['icono' => 'bar_chart', 'titulo' => 'Estadísticas', 'texto' => 'Ventas por día, platos más pedidos y tarjeta frente a efectivo.'],
        ['icono' => 'domain', 'titulo' => 'Varios restaurantes', 'texto' => 'Cada restaurante tiene sus datos separados: nunca ve los de otro.'],
    ];
@endphp

<x-layouts.publico titulo="Comandas y cartas QR para bares y restaurantes">

    {{-- ===================== Portada ===================== --}}
    <section id="inicio" class="relative overflow-hidden">
        {{-- Brillos de fondo (decoración) --}}
        <div class="absolute -top-40 left-1/2 -translate-x-1/2 w-[720px] h-[420px] rounded-full bg-primary-fixed/30 blur-3xl pointer-events-none" aria-hidden="true"></div>

        <div class="relative max-w-6xl mx-auto px-4 sm:px-margin-md pt-space-xl pb-space-xl text-center">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-surface-container-lowest border border-outline-variant/40 text-label-sm text-on-surface-variant">
                <span class="w-2 h-2 rounded-full bg-estado-listo"></span>
                Comandas y cartas QR para bares y restaurantes
            </span>

            <h1 class="mt-space-md text-display-hero-mobile sm:text-display-hero">
                Pide desde tu mesa, <span class="text-primary-container underline decoration-4 underline-offset-8">sin esperas</span>
            </h1>
            <p class="mt-space-md max-w-2xl mx-auto text-body-lg text-secondary">
                El cliente escanea el QR de su mesa, ve la carta con los alérgenos y pide. La cocina recibe el pedido al
                momento y, al acabar, la cuenta se divide por persona y se paga con tarjeta o en efectivo.
            </p>

            <div class="mt-space-lg flex flex-col sm:flex-row items-stretch sm:items-center justify-center gap-space-sm">
                <a href="#como-funciona"
                   class="pulsable inline-flex items-center justify-center gap-2 min-h-[48px] px-6 rounded-lg bg-primary-container text-on-primary text-label-lg font-bold shadow-nivel-2 hover:bg-primary">
                    Ver cómo funciona <x-icono nombre="arrow_downward" class="text-xl" />
                </a>
                @if ($urlCartaDemo)
                    <a href="{{ $urlCartaDemo }}"
                       class="pulsable inline-flex items-center justify-center gap-2 min-h-[48px] px-6 rounded-lg border-[1.5px] border-outline-variant/60 bg-surface-container-lowest text-label-lg font-bold hover:border-primary-container">
                        <x-icono nombre="qr_code_scanner" class="text-xl text-primary" /> Ver una carta de ejemplo
                    </a>
                @endif
            </div>

            {{-- Ilustración: móvil con la carta, entre dos tarjetas (las tarjetas se apilan debajo en el móvil) --}}
            <div class="mt-space-xl grid gap-space-md lg:grid-cols-[1fr_auto_1fr] items-center text-left">
                <div class="order-2 lg:order-1 space-y-space-md">
                    <x-tarjeta class="max-w-sm lg:ml-auto">
                        <p class="text-label-sm uppercase text-secondary">Alérgenos</p>
                        <p class="mt-1 text-headline-sm font-bold">Los 14 en cada plato</p>
                        <p class="mt-1 text-body-md text-secondary">El cliente los ve en la carta antes de pedir. Los de declaración obligatoria vienen ya cargados.</p>
                    </x-tarjeta>
                    <x-tarjeta class="max-w-sm lg:ml-auto">
                        <p class="text-label-sm uppercase text-secondary">Cuenta</p>
                        <p class="mt-1 text-headline-sm font-bold">Dividida al céntimo</p>
                        <p class="mt-1 text-body-md text-secondary">31,00 € entre 3 = 10,34 + 10,33 + 10,33. El céntimo que sobra va a la primera parte.</p>
                    </x-tarjeta>
                </div>

                {{-- El móvil --}}
                <div class="order-1 lg:order-2 mx-auto w-full max-w-[300px] rounded-[2.5rem] border-[10px] border-inverse-surface bg-surface-container-lowest shadow-nivel-2 overflow-hidden" aria-hidden="true">
                    <div class="flex items-center gap-2 px-4 py-3 border-b border-outline-variant/30">
                        <x-logo class="w-8 h-8" />
                        <div class="min-w-0">
                            <p class="text-label-lg leading-tight truncate">La Tasca</p>
                            <p class="text-label-sm text-tertiary">Mesa 4</p>
                        </div>
                    </div>
                    <div class="flex gap-1.5 px-4 py-2">
                        <span class="px-2.5 py-1 rounded-full bg-inverse-surface text-inverse-on-surface text-label-sm">Tapas</span>
                        <span class="px-2.5 py-1 rounded-full bg-surface-container-low text-label-sm">Raciones</span>
                        <span class="px-2.5 py-1 rounded-full bg-surface-container-low text-label-sm">Bebidas</span>
                    </div>
                    <div class="px-4 pb-3 space-y-2">
                        @foreach ($platosEjemplo as $plato)
                            <div class="flex gap-2 p-2.5 rounded-lg border border-outline-variant/30">
                                <div class="flex-1 min-w-0">
                                    <p class="text-label-lg leading-tight">{{ $plato['nombre'] }}</p>
                                    <p class="text-label-sm text-secondary leading-snug">{{ $plato['texto'] }}</p>
                                    <div class="mt-1 flex flex-wrap items-center gap-1">
                                        @foreach ($plato['alergenos'] as $alergeno)
                                            <x-badge-alergeno>{{ $alergeno }}</x-badge-alergeno>
                                        @endforeach
                                        <x-precio :centimos="$plato['centimos']" class="ml-auto text-label-lg font-bold" />
                                    </div>
                                </div>
                                <span class="self-end flex items-center justify-center w-7 h-7 rounded-full bg-primary-container text-on-primary">
                                    <x-icono nombre="add" class="text-lg" />
                                </span>
                            </div>
                        @endforeach
                    </div>
                    <div class="mx-3 mb-3 flex items-center justify-between px-4 py-2.5 rounded-lg bg-primary-container text-on-primary text-label-lg font-bold">
                        <span class="flex items-center gap-1.5"><x-icono nombre="shopping_bag" class="text-lg" /> Ver pedido</span>
                        <x-precio :centimos="2200" />
                    </div>
                </div>

                <div class="order-3 space-y-space-md">
                    {{-- Mini pantalla de cocina --}}
                    <x-tarjeta class="max-w-sm">
                        <p class="flex items-center gap-1.5 text-label-sm uppercase text-secondary"><span class="w-2 h-2 rounded-full bg-estado-listo"></span> Pantalla de cocina</p>
                        <div class="mt-2 space-y-2">
                            <div class="p-2.5 rounded border-l-4 border-l-estado-preparando bg-surface-container-low">
                                <div class="flex items-center justify-between gap-2">
                                    <p class="text-label-lg">Mesa 4</p>
                                    <x-chip-estado estado="preparando" />
                                </div>
                                <p class="text-label-sm text-secondary">2× Croquetas caseras · 1× Patatas bravas</p>
                            </div>
                            <div class="p-2.5 rounded border-l-4 border-l-estado-listo bg-surface-container-low">
                                <div class="flex items-center justify-between gap-2">
                                    <p class="text-label-lg">Mesa 7</p>
                                    <x-chip-estado estado="listo" />
                                </div>
                                <p class="text-label-sm text-secondary">1× Tortilla de patatas <span class="italic">(sin cebolla)</span></p>
                            </div>
                        </div>
                    </x-tarjeta>
                    <x-tarjeta class="max-w-sm">
                        <p class="text-label-sm uppercase text-secondary">Pago</p>
                        <p class="mt-1 text-headline-sm font-bold">Tarjeta o efectivo</p>
                        <p class="mt-1 text-body-md text-secondary">Tarjeta con Stripe desde el móvil, o efectivo que confirma el camarero.</p>
                    </x-tarjeta>
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== Cómo funciona ===================== --}}
    <section id="como-funciona" class="scroll-mt-16 bg-surface-container-low">
        <div class="max-w-6xl mx-auto px-4 sm:px-margin-md py-space-xl">
            <div class="text-center max-w-2xl mx-auto">
                <span class="inline-block px-3 py-1 rounded-full bg-primary-fixed/40 text-label-sm text-on-primary-fixed-variant">Cómo funciona</span>
                <h2 class="mt-space-sm text-headline-lg">De la mesa a la cocina, y de vuelta a la cuenta</h2>
                <p class="mt-space-sm text-body-lg text-secondary">Tres pasos, cada uno con su pantalla.</p>
            </div>

            <div class="mt-space-lg grid gap-space-md md:grid-cols-3">
                @foreach ($pasos as $paso)
                    <x-tarjeta class="flex flex-col">
                        <span class="flex items-center justify-center w-12 h-12 rounded-lg {{ $paso['color'] }}">
                            <x-icono :nombre="$paso['icono']" class="text-2xl" />
                        </span>
                        <h3 class="mt-space-md text-headline-sm font-bold">{{ $paso['titulo'] }}</h3>
                        <p class="mt-space-sm text-body-md text-secondary">{{ $paso['texto'] }}</p>
                        <ul class="mt-space-md space-y-2 text-body-md">
                            @foreach ($paso['puntos'] as $punto)
                                <li class="flex gap-2"><x-icono nombre="check_circle" class="text-lg text-tertiary" /> {{ $punto }}</li>
                            @endforeach
                        </ul>
                    </x-tarjeta>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ===================== Cocina y sala (roles) ===================== --}}
    <section id="cocina-y-sala" class="scroll-mt-16">
        <div class="max-w-6xl mx-auto px-4 sm:px-margin-md py-space-xl">
            <div class="max-w-2xl">
                <span class="inline-block px-3 py-1 rounded-full bg-secondary-container text-label-sm">Cocina y sala</span>
                <h2 class="mt-space-sm text-headline-lg">Una pantalla para cada persona del equipo</h2>
                <p class="mt-space-sm text-body-lg text-secondary">
                    Cada uno entra con su cuenta y va directo a lo suyo. El cliente no necesita cuenta: le basta con el QR.
                </p>
            </div>

            <div class="mt-space-lg grid gap-space-md sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($roles as $rol)
                    <x-tarjeta>
                        <x-icono :nombre="$rol['icono']" class="text-3xl text-primary" />
                        <h3 class="mt-space-sm text-headline-sm font-bold">{{ $rol['rol'] }}</h3>
                        <p class="mt-1 text-body-md text-secondary">{{ $rol['texto'] }}</p>
                    </x-tarjeta>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ===================== Panel del restaurante ===================== --}}
    <section id="panel" class="scroll-mt-16 bg-surface-container-low">
        <div class="max-w-6xl mx-auto px-4 sm:px-margin-md py-space-xl grid gap-space-lg lg:grid-cols-2 items-center">
            <div>
                <span class="inline-block px-3 py-1 rounded-full bg-tertiary-fixed/50 text-label-sm text-on-tertiary-container">Panel del restaurante</span>
                <h2 class="mt-space-sm text-headline-lg">Todo el restaurante desde un panel</h2>
                <p class="mt-space-sm text-body-lg text-secondary">El administrador prepara la carta y las mesas, y ve cómo van las ventas.</p>

                <div class="mt-space-lg grid gap-space-md sm:grid-cols-2">
                    @foreach ($panel as $elemento)
                        <div class="flex gap-3">
                            <span class="shrink-0 flex items-center justify-center w-10 h-10 rounded-lg bg-surface-container-lowest border border-outline-variant/30 text-primary">
                                <x-icono :nombre="$elemento['icono']" />
                            </span>
                            <div>
                                <h3 class="text-label-lg">{{ $elemento['titulo'] }}</h3>
                                <p class="text-body-md text-secondary">{{ $elemento['texto'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Ilustración: tarjeta de estadísticas con barras hechas con divs --}}
            <x-tarjeta aria-hidden="true">
                <div class="flex items-center justify-between">
                    <p class="text-headline-sm font-bold">Ventas por día</p>
                    <span class="px-2.5 py-0.5 rounded-full bg-secondary-container text-label-sm">Últimos 7 días</span>
                </div>
                <div class="mt-space-md flex items-end gap-2 h-40">
                    @foreach ([45, 60, 38, 72, 55, 90, 68] as $indice => $altura)
                        <div class="flex-1 rounded-t {{ $altura > 60 ? 'bg-primary-container' : 'bg-secondary-container' }}" style="height: {{ $altura }}%"></div>
                    @endforeach
                </div>
                <div class="mt-space-md grid grid-cols-2 gap-space-sm">
                    <div class="p-3 rounded bg-surface-container-low">
                        <p class="flex items-center gap-1.5 text-label-md"><span class="w-2.5 h-2.5 rounded-full bg-primary-container"></span> Tarjeta</p>
                        <p class="text-label-sm text-secondary">Stripe y TPV</p>
                    </div>
                    <div class="p-3 rounded bg-surface-container-low">
                        <p class="flex items-center gap-1.5 text-label-md"><span class="w-2.5 h-2.5 rounded-full bg-inverse-surface"></span> Efectivo</p>
                        <p class="text-label-sm text-secondary">Cobrado en la mesa</p>
                    </div>
                </div>
            </x-tarjeta>
        </div>
    </section>

    {{-- ===================== Llamada final ===================== --}}
    <section class="max-w-6xl mx-auto px-4 sm:px-margin-md py-space-xl text-center">
        <h2 class="text-headline-lg">¿Tu equipo ya trabaja con MesaYa?</h2>
        <p class="mt-space-sm max-w-xl mx-auto text-body-lg text-secondary">
            Entra con la cuenta que te ha dado tu restaurante e irás directo a tu pantalla: mesas, cocina o panel.
        </p>
        <div class="mt-space-lg flex justify-center">
            <x-boton-acceso :usuario="$usuario" class="min-h-[48px] px-6" />
        </div>
    </section>
</x-layouts.publico>
