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
    $columnas = [
        [
            'estado' => 'pendiente', 'titulo' => 'Pendiente',
            'punto' => 'bg-estado-pendiente', 'borde' => 'border-b-estado-pendiente',
            'contador' => 'bg-estado-pendiente/15 text-amber-800', 'tarjeta' => 'border-l-estado-pendiente',
            'boton' => 'bg-primary-container text-on-primary hover:bg-primary',
            'textoBoton' => 'Empezar a preparar', 'iconoBoton' => 'play_arrow',
        ],
        [
            'estado' => 'preparando', 'titulo' => 'Preparando',
            'punto' => 'bg-estado-preparando', 'borde' => 'border-b-estado-preparando',
            'contador' => 'bg-estado-preparando/15 text-blue-800', 'tarjeta' => 'border-l-estado-preparando',
            'boton' => 'bg-tertiary text-on-tertiary hover:bg-on-tertiary-fixed-variant',
            'textoBoton' => 'Marcar como listo', 'iconoBoton' => 'check_circle',
        ],
        [
            'estado' => 'listo', 'titulo' => 'Listo',
            'punto' => 'bg-estado-listo', 'borde' => 'border-b-estado-listo',
            'contador' => 'bg-estado-listo/15 text-emerald-800', 'tarjeta' => 'border-l-estado-listo',
            'boton' => 'bg-inverse-surface text-inverse-on-surface hover:bg-on-surface',
            'textoBoton' => 'Servido', 'iconoBoton' => 'done_all',
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
             class="flex items-center gap-2 mb-space-md px-4 py-3 rounded bg-amber-50 text-amber-900 text-body-md">
            <x-icono nombre="wifi_off" class="text-xl" />
            Sin conexión con el servidor. Reintentando cada 5 segundos…
        </div>
        <div x-cloak x-show="error" role="alert"
             class="flex items-center justify-between gap-2 mb-space-md px-4 py-3 rounded bg-error-container text-on-error-container text-body-md">
            <span class="flex items-center gap-2"><x-icono nombre="error" class="text-xl" /> <span x-text="error"></span></span>
            <button type="button" x-on:click="error = ''" aria-label="Cerrar aviso"><x-icono nombre="close" /></button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-space-md items-start">
            @foreach ($columnas as $columna)
                <section class="flex flex-col bg-surface-container-lowest rounded-lg border border-outline-variant/30 shadow-nivel-1 overflow-hidden"
                         aria-labelledby="columna-{{ $columna['estado'] }}">
                    {{-- Cabecera de la columna con el número de pedidos --}}
                    <header class="flex items-center justify-between gap-2 px-space-md py-3 border-b-4 {{ $columna['borde'] }}">
                        <h2 id="columna-{{ $columna['estado'] }}" class="flex items-center gap-2 text-headline-sm font-bold uppercase tracking-wide">
                            <span class="w-3.5 h-3.5 rounded-full {{ $columna['punto'] }}"></span>
                            {{ $columna['titulo'] }}
                        </h2>
                        <span class="px-3 py-1 rounded-full text-label-md {{ $columna['contador'] }}">
                            {{-- "1 pedido" en singular, "0 pedidos" / "3 pedidos" en plural --}}
                            <span x-text="deEstado('{{ $columna['estado'] }}').length + (deEstado('{{ $columna['estado'] }}').length === 1 ? ' pedido' : ' pedidos')">0 pedidos</span>
                        </span>
                    </header>

                    <div class="flex flex-col gap-space-md p-space-md bg-surface-container-low min-h-[160px]">
                        {{-- Una tarjeta por pedido. :key ayuda a Alpine a saber qué tarjeta es cuál al repintar --}}
                        <template x-for="pedido in deEstado('{{ $columna['estado'] }}')" :key="pedido.id">
                            <article class="bg-surface-container-lowest rounded-lg border border-outline-variant/30 border-l-4 {{ $columna['tarjeta'] }} shadow-sm p-space-md">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <h3 class="text-headline-lg font-extrabold uppercase leading-none">
                                            Mesa <span x-text="pedido.mesa"></span>
                                            <span class="text-headline-sm font-semibold text-secondary normal-case">#<span x-text="pedido.id"></span></span>
                                        </h3>
                                    </div>
                                    <div class="text-right shrink-0">
                                        <p class="text-headline-sm font-bold tabular-nums" x-text="pedido.hora"></p>
                                        <p class="text-label-sm text-secondary" x-text="tiempo(pedido.minutos)"></p>
                                    </div>
                                </div>

                                {{-- Platos con su cantidad y, si hay, la nota en rojo --}}
                                <ul class="mt-3 pt-3 border-t border-outline-variant/30 space-y-2">
                                    <template x-for="(linea, indice) in pedido.lineas" :key="indice">
                                        <li>
                                            <div class="flex items-start gap-3">
                                                <span class="flex items-center justify-center min-w-[2rem] h-8 px-1 rounded bg-surface-container-highest text-label-lg font-extrabold shrink-0"
                                                      x-text="linea.cantidad + 'x'"></span>
                                                <span class="text-body-lg font-semibold pt-1" x-text="linea.plato"></span>
                                            </div>
                                            <p x-show="linea.notas"
                                               class="flex items-center gap-2 mt-1.5 ml-11 px-3 py-1.5 rounded border border-error/40 bg-error-container/50 text-on-error-container text-label-md uppercase">
                                                <x-icono nombre="error" class="text-base" />
                                                <span x-text="linea.notas"></span>
                                            </p>
                                        </li>
                                    </template>
                                </ul>

                                <button type="button" x-on:click="avanzar(pedido)" :disabled="enviandoId === pedido.id"
                                        class="pulsable mt-space-md w-full inline-flex items-center justify-center gap-2 min-h-[48px] px-4 rounded text-label-lg font-bold shadow-sm transition disabled:opacity-50 {{ $columna['boton'] }}">
                                    <x-icono :nombre="$columna['iconoBoton']" class="text-xl" />
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
