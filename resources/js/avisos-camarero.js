/*
 * Avisos de plato listo para el camarero (rama 7).
 *
 * Este componente de Alpine va en el layout del camarero, así que funciona en
 * TODAS sus pantallas: cada 5 s pide los pedidos listos y con ellos
 *   - pinta el contador de la pestaña «Avisos» de la barra de abajo, y
 *   - en la pantalla «Avisos» pinta las tarjetas con el botón «Servido».
 * Así solo hay una consulta cada 5 s aunque las dos cosas estén a la vista.
 *
 * Se usa en Blade: x-data="avisosCamarero({ urlDatos: '...', urlServido: '...' })"
 * urlServido lleva "__ID__" en el sitio del id del pedido.
 */
export default function avisosCamarero({ urlDatos, urlServido }) {
    return {
        pedidos: [],          // pedidos en estado "listo"
        cargadoUnaVez: false, // para enseñar «Cargando…» solo al principio
        cargando: false,      // hay una petición del polling en marcha
        sinConexion: false,   // la última petición ha fallado
        enviandoId: null,     // pedido cuyo botón se acaba de pulsar
        mensaje: '',          // respuesta del servidor al pulsar «Servido»
        idsVistos: new Set(), // ids ya avisados, para pitar solo con los nuevos
        nuevos: [],           // ids que acaban de llegar (se resaltan)

        init() {
            this.cargar();
            setInterval(() => this.cargar(), 5000);
        },

        async cargar() {
            // Si la anterior aún no ha vuelto, no lanzamos otra.
            if (this.cargando) return;
            this.cargando = true;

            try {
                const respuesta = await fetch(urlDatos, { headers: { 'Accept': 'application/json' } });

                // Sesión caducada: al recargar, Laravel manda al login.
                if (respuesta.status === 401 || respuesta.status === 419) {
                    window.location.reload();
                    return;
                }
                if (! respuesta.ok) throw new Error('Respuesta ' + respuesta.status);

                const datos = await respuesta.json();

                // Pedidos que no habíamos visto antes. En la primera carga no
                // pitamos: esos avisos ya estaban ahí al abrir la pantalla.
                const llegados = datos.pedidos.filter((p) => ! this.idsVistos.has(p.id)).map((p) => p.id);
                if (this.cargadoUnaVez && llegados.length > 0) {
                    this.pitar();
                    this.nuevos = llegados;
                }
                llegados.forEach((id) => this.idsVistos.add(id));

                this.pedidos = datos.pedidos;
                this.sinConexion = false;
                this.cargadoUnaVez = true;
            } catch (e) {
                // Sin red: dejamos los últimos datos y lo indicamos.
                this.sinConexion = true;
            } finally {
                this.cargando = false;
            }
        },

        // Botón «Marcar como servido».
        async servir(pedido) {
            this.enviandoId = pedido.id;
            this.mensaje = '';

            try {
                const respuesta = await fetch(urlServido.replace('__ID__', pedido.id), {
                    method: 'PATCH',
                    headers: {
                        'Accept': 'application/json',
                        // Laravel pide este token en todo lo que no sea GET.
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                });
                const datos = await respuesta.json().catch(() => ({}));
                this.mensaje = datos.mensaje ?? 'No se ha podido marcar como servido.';
            } catch (e) {
                this.mensaje = 'Sin conexión con el servidor. Inténtalo otra vez.';
            }

            this.enviandoId = null;
            // Refrescamos ya, sin esperar a los 5 s.
            while (this.cargando) await new Promise((listo) => setTimeout(listo, 100));
            await this.cargar();
        },

        // "ahora" o "hace 3 min".
        tiempo(minutos) {
            return minutos < 1 ? 'ahora' : 'hace ' + minutos + ' min';
        },

        /*
         * Pitido corto con la Web Audio API del navegador (sin archivos de
         * sonido). Los navegadores no dejan sonar nada hasta que el usuario
         * ha tocado la página una vez; si aún no lo ha hecho, no suena y no
         * pasa nada.
         */
        pitar() {
            try {
                const audio = new (window.AudioContext || window.webkitAudioContext)();
                const oscilador = audio.createOscillator();
                oscilador.frequency.value = 880; // nota La, aguda
                oscilador.connect(audio.destination);
                oscilador.start();
                oscilador.stop(audio.currentTime + 0.2); // 0,2 s
            } catch (e) {
                // Navegador sin audio: el aviso visual es suficiente.
            }
        },
    };
}
