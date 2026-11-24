{{--
    Tablero de cocina (rama 6). Referencia: docs/design/DesignFrontend/cocina/pedidos.
    De la maqueta no están (no existen en la BD): pestañas por estación,
    comensales, marcar platos uno a uno, alerta sonora y prioridad urgente.

    Cómo funciona:
    - Las columnas se escriben aquí en Blade, pero las TARJETAS las pinta Alpine
      con <template x-for>. Así el HTML de una tarjeta está escrito una sola
      vez y sirve tanto para la primera carga como para cada refresco.
    - Polling: cada 5 s el navegador pide /cocina/pedidos/datos (JSON) y
      Alpine vuelve a pintar las tarjetas sin recargar la página.
    - Los botones envían un PATCH con fetch y refrescan el tablero al momento.
--}}
@php
    // Una entrada por columna. Las clases van escritas enteras (no montadas
    // con variables) porque Tailwind solo genera las que encuentra tal cual.
    // Rediseño «Comanda impresa»: el color del estado solo va en la raya gruesa
    // bajo el título de la columna; los tickets son todos blancos.
    $columnas = [
        [
            'estado' => 'pendiente', 'titulo' => 'Pendientes',
            'borde' => 'border-b-estado-pendiente',
            // Empezar: botón de tinta, la acción más frecuente.
            'boton' => 'bg-on-surface text-surface-container-lowest hover:bg-on-surface-variant',
            'textoBoton' => 'Empezar a preparar',
        ],
        [
            'estado' => 'preparando', 'titulo' => 'Preparando',
            'borde' => 'border-b-estado-preparando',
            'boton' => 'bg-tertiary-container text-on-tertiary hover:bg-tertiary',
            'textoBoton' => 'Marcar como listo',
        ],
        [
            'estado' => 'listo', 'titulo' => 'Listos para servir',
            'borde' => 'border-b-estado-listo',
            // Servido: con borde, porque el pedido ya casi no necesita atención.
            'boton' => 'border-[1.5px] border-on-surface text-on-surface hover:bg-surface-container-low',
            'textoBoton' => 'Servido',
        ],
    ];
@endphp

<x-layouts.cocina titulo="Pedidos">
    <script>
        /*
         * Componente de Alpine del tablero. Se define aquí, antes de que
         * arranque Alpine (app.js se carga después, como módulo).
         *
         *   pedidosIniciales: los pedidos que ya trae la página desde PHP.
         *   urlDatos:         endpoint JSON que se consulta cada 5 s.
         *   urlEstado:        URL para cambiar el estado, con "__ID__" en el
         *                     sitio del id del pedido.
         */
        function tableroCocina(pedidosIniciales, urlDatos, urlEstado) {
            return {
                pedidos: pedidosIniciales,
                cargando: false,      // hay una petición del polling en marcha
                sinConexion: false,   // la última petición ha fallado
                enviandoId: null,     // pedido cuyo botón se acaba de pulsar
                error: '',            // mensaje cuando el servidor rechaza un cambio

                // Alpine llama a init() solo al arrancar el componente.
                init() {
                    // Cada 5000 ms (5 s) pedimos los datos otra vez.
                    setInterval(() => this.cargar(), 5000);
                },

                // Pedidos de una columna.
                deEstado(estado) {
                    return this.pedidos.filter((pedido) => pedido.estado === estado);
                },

                // Pide el JSON y sustituye los pedidos. Alpine repinta solo.
                async cargar() {
                    // Si la anterior aún no ha vuelto (red lenta), no lanzamos
                    // otra: así no se acumulan peticiones.
                    if (this.cargando) return;
                    this.cargando = true;

                    try {
                        const respuesta = await fetch(urlDatos, {
                            headers: { 'Accept': 'application/json' },
                        });

                        // 401/419: la sesión ha caducado. Recargando, Laravel
                        // manda al login.
                        if (respuesta.status === 401 || respuesta.status === 419) {
                            window.location.reload();
                            return;
                        }
                        if (! respuesta.ok) throw new Error('Respuesta ' + respuesta.status);

                        const datos = await respuesta.json();
                        this.pedidos = datos.pedidos;
                        this.sinConexion = false;
                    } catch (e) {
                        // Sin red o servidor caído: dejamos los últimos datos
                        // en pantalla y avisamos. El siguiente intento es en 5 s.
                        this.sinConexion = true;
                    } finally {
                        this.cargando = false;
                    }
                },

                // Botón de la tarjeta: pasa el pedido a su siguiente estado.
                async avanzar(pedido) {
                    this.enviandoId = pedido.id;
                    this.error = '';

                    try {
                        const respuesta = await fetch(urlEstado.replace('__ID__', pedido.id), {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                // Laravel pide este token en todo lo que no
                                // sea GET. Está en una <meta> del layout base.
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            },
                            body: JSON.stringify({ estado: pedido.siguiente_estado }),
                        });

                        // 422: el servidor no deja el cambio (por ejemplo, otro
                        // cocinero ya había movido ese pedido).
                        if (respuesta.status === 422) {
                            this.error = (await respuesta.json()).mensaje;
                        } else if (! respuesta.ok) {
                            this.error = 'No se ha podido cambiar el pedido. Inténtalo otra vez.';
                        }
                    } catch (e) {
                        this.error = 'Sin conexión con el servidor. Inténtalo otra vez.';
                    }

                    this.enviandoId = null;
                    // Refrescamos ya, sin esperar al siguiente ciclo de 5 s.
                    // Si el polling estaba a medias, esperamos a que acabe.
                    while (this.cargando) await new Promise((listo) => setTimeout(listo, 100));
                    await this.cargar();
                },

                // "ahora" o "hace 3 min".
                tiempo(minutos) {
                    return minutos < 1 ? 'ahora' : 'hace ' + minutos + ' min';
                },
            };
        }
    </script>

    {{-- @js pasa los datos de PHP a JavaScript de forma segura --}}
    <div x-data="tableroCocina(@js($pedidos), @js(route('cocina.pedidos.datos')), @js(route('cocina.pedidos.estado', '__ID__')))">
        {{-- Avisos: sin conexión (polling) y cambio rechazado --}}
        <div x-cloak x-show="sinConexion" role="status"
             class="flex items-center gap-2 mx-margin mt-space-md px-4 py-3 rounded bg-amber-50 text-amber-900 text-body-md">
            <x-icono nombre="wifi_off" class="text-xl" />
            Sin conexión con el servidor. Reintentando cada 5 segundos…
        </div>
        <div x-cloak x-show="error" role="alert"
             class="flex items-center justify-between gap-2 mx-margin mt-space-md px-4 py-3 rounded bg-error-container text-on-error-container text-body-md">
            <span class="flex items-center gap-2"><x-icono nombre="error" class="text-xl" /> <span x-text="error"></span></span>
            <button type="button" x-on:click="error = ''" aria-label="Cerrar aviso" class="w-11 h-11 inline-flex items-center justify-center"><x-icono nombre="close" /></button>
        </div>

        {{-- Tres columnas separadas por una línea; en pantallas estrechas se apilan --}}
        <div class="grid grid-cols-1 md:grid-cols-3 md:divide-x divide-secondary-fixed-dim items-start">
            @foreach ($columnas as $columna)
                <section class="flex flex-col" aria-labelledby="columna-{{ $columna['estado'] }}">
                    {{-- Cabecera: título y número de pedidos en grande, raya gruesa del color del estado --}}
                    <header class="flex items-baseline justify-between gap-2 px-space-md pt-space-md pb-3 border-b-4 {{ $columna['borde'] }}">
                        <h2 id="columna-{{ $columna['estado'] }}" class="text-headline-sm">{{ $columna['titulo'] }}</h2>
                        <span class="font-mono text-headline-lg tabular-nums" x-text="deEstado('{{ $columna['estado'] }}').length">0</span>
                    </header>

                    <div class="flex flex-col gap-space-md p-space-md min-h-[160px]">
                        {{-- Una tarjeta-ticket por pedido. :key ayuda a Alpine a saber qué tarjeta es cuál al repintar --}}
                        <template x-for="pedido in deEstado('{{ $columna['estado'] }}')" :key="pedido.id">
                            <article class="bg-surface-container-lowest shadow-nivel-1 p-space-md">
                                <div class="flex items-baseline justify-between gap-3">
                                    <h3 class="text-headline-lg leading-none">Mesa <span x-text="pedido.mesa"></span></h3>
                                    {{-- El tiempo sale en rojo cuando el pedido lleva 10 minutos o más esperando --}}
                                    <p class="font-mono text-label-lg tabular-nums shrink-0"
                                       :class="pedido.minutos >= 10 ? 'text-error' : 'text-secondary'"
                                       x-text="tiempo(pedido.minutos)"></p>
                                </div>
                                <p class="mt-1 font-mono text-label-md font-normal text-secondary tabular-nums">
                                    #<span x-text="pedido.id"></span> · <span x-text="pedido.hora"></span>
                                </p>

                                {{-- Platos en letra de ticket; la nota, resaltada en naranja claro --}}
                                <ul class="mt-3 pt-3 border-t-[1.5px] border-dashed border-secondary-fixed-dim space-y-2 font-mono">
                                    <template x-for="(linea, indice) in pedido.lineas" :key="indice">
                                        <li class="text-body-lg">
                                            <span class="font-semibold" x-text="linea.cantidad"></span>
                                            <span class="ml-2" x-text="linea.plato"></span>
                                            <p x-show="linea.notas"
                                               class="mt-1 ml-6 px-2 py-1 bg-primary-fixed text-on-primary-fixed-variant text-label-lg"
                                               x-text="linea.notas"></p>
                                        </li>
                                    </template>
                                </ul>

                                <button type="button" x-on:click="avanzar(pedido)" :disabled="enviandoId === pedido.id"
                                        class="pulsable mt-space-md w-full inline-flex items-center justify-center min-h-[52px] px-4 rounded text-label-lg font-bold transition-colors disabled:opacity-50 {{ $columna['boton'] }}">
                                    {{ $columna['textoBoton'] }}
                                </button>
                            </article>
                        </template>

                        <p x-show="deEstado('{{ $columna['estado'] }}').length === 0"
                           class="py-space-lg text-center text-body-md text-secondary">Sin pedidos</p>
                    </div>
                </section>
            @endforeach
        </div>
    </div>
</x-layouts.cocina>
