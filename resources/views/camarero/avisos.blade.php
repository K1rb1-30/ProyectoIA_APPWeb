{{--
    Avisos de plato listo (rama 7). No tiene maqueta propia: sigue el estilo
    de docs/design/DesignFrontend/camarero/mesas (tarjetas con borde izquierdo
    de color y botones grandes).

    Los datos (pedidos, servir(), tiempo()...) vienen del componente Alpine
    "avisosCamarero" que está en el layout (resources/js/avisos-camarero.js).
--}}
<x-layouts.camarero titulo="Avisos" seccion="avisos">
    {{-- Sin conexión: el polling ha fallado --}}
    <div x-show="sinConexion" x-cloak role="alert"
         class="mb-space-sm flex items-center gap-2 rounded bg-error-container text-on-error-container px-3 py-2 text-label-md">
        <x-icono nombre="wifi_off" /> Sin conexión. Reintentando…
    </div>

    {{-- Respuesta del servidor al pulsar «Servido» --}}
    <div x-show="mensaje" x-cloak role="status" x-text="mensaje"
         class="mb-space-sm rounded bg-surface-container-low px-3 py-2 text-label-md"></div>

    <h2 class="text-headline-sm font-bold mb-space-sm">Listos para servir</h2>

    <p x-show="! cargadoUnaVez" class="text-body-lg text-secondary">Cargando…</p>

    <template x-if="cargadoUnaVez && pedidos.length === 0">
        <x-tarjeta>
            <p class="text-body-lg text-secondary">No hay platos esperando. Cuando la cocina marque un pedido como listo, aparecerá aquí.</p>
        </x-tarjeta>
    </template>

    <div class="flex flex-col gap-3">
        <template x-for="pedido in pedidos" :key="pedido.id">
            {{-- Borde verde (listo). Los que acaban de llegar se resaltan con un anillo naranja --}}
            <article class="flex flex-col gap-3 bg-surface-container-lowest rounded-lg border border-outline-variant/30 border-l-4 border-l-estado-listo shadow-nivel-1 p-3"
                     :class="nuevos.includes(pedido.id) && 'ring-2 ring-primary-container'">
                <div class="flex items-center justify-between">
                    <h3 class="text-headline-md font-extrabold uppercase" x-text="'Mesa ' + pedido.mesa"></h3>
                    <span class="text-label-md text-secondary" x-text="tiempo(pedido.minutos)"></span>
                </div>

                <ul class="text-body-md">
                    <template x-for="linea in pedido.lineas">
                        <li>
                            <span class="font-bold" x-text="linea.cantidad + '×'"></span>
                            <span x-text="linea.plato"></span>
                            <span x-show="linea.notas" class="block text-label-md text-secondary" x-text="linea.notas"></span>
                        </li>
                    </template>
                </ul>

                <x-boton-primario type="button" class="w-full"
                                  x-on:click="servir(pedido)"
                                  x-bind:disabled="enviandoId === pedido.id">
                    <x-icono nombre="check_circle" class="text-xl" />
                    Marcar como servido
                </x-boton-primario>
            </article>
        </template>
    </div>
</x-layouts.camarero>
