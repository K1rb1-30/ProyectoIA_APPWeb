{{--
    Avisos de plato listo (rama 7, rediseño en la rama 20).
    Los datos (pedidos, servir(), tiempo()...) vienen del componente Alpine
    "avisosCamarero" que está en el layout (resources/js/avisos-camarero.js).

    Rediseño «Comanda impresa»: cada aviso es un ticket blanco con el número
    de mesa en grande y los platos en letra de ticket. Los que acaban de
    llegar se resaltan con un borde naranja.
--}}
<x-layouts.camarero titulo="Avisos" seccion="avisos">
    {{-- Sin conexión: el polling ha fallado --}}
    <div x-show="sinConexion" x-cloak role="alert"
         class="mb-space-sm flex items-center gap-2 rounded bg-error-container text-on-error-container px-3 py-2 text-label-md">
        <x-icono nombre="wifi_off" /> Sin conexión. Reintentando…
    </div>

    {{-- Respuesta del servidor al pulsar «Servido» --}}
    <div x-show="mensaje" x-cloak role="status" x-text="mensaje"
         class="mb-space-sm rounded bg-surface-container px-3 py-2 text-label-md"></div>

    <h2 class="pb-2 mb-space-md border-b-2 border-on-surface text-headline-sm">Listos para servir</h2>

    <p x-show="! cargadoUnaVez" class="text-body-lg text-secondary">Cargando…</p>

    <template x-if="cargadoUnaVez && pedidos.length === 0">
        <p class="text-body-lg text-secondary">No hay platos esperando. Cuando la cocina marque un pedido como listo, aparecerá aquí.</p>
    </template>

    <div class="flex flex-col gap-space-md">
        <template x-for="pedido in pedidos" :key="pedido.id">
            <article class="flex flex-col gap-3 bg-surface-container-lowest p-space-md border-[1.5px]"
                     :class="nuevos.includes(pedido.id) ? 'border-primary-container' : 'border-transparent shadow-nivel-1'">
                <div class="flex items-baseline justify-between">
                    <h3 class="text-headline-lg leading-none" x-text="'Mesa ' + pedido.mesa"></h3>
                    <span class="font-mono text-label-md text-secondary" x-text="tiempo(pedido.minutos)"></span>
                </div>

                <ul class="pt-3 border-t-[1.5px] border-dashed border-secondary-fixed-dim font-mono text-body-md space-y-1">
                    <template x-for="linea in pedido.lineas">
                        <li>
                            <span class="font-semibold" x-text="linea.cantidad + '×'"></span>
                            <span x-text="linea.plato"></span>
                            <span x-show="linea.notas" class="block ml-6 text-label-md text-on-primary-fixed-variant" x-text="linea.notas"></span>
                        </li>
                    </template>
                </ul>

                <x-boton-primario type="button" class="w-full"
                                  x-on:click="servir(pedido)"
                                  x-bind:disabled="enviandoId === pedido.id">
                    Marcar como servido
                </x-boton-primario>
            </article>
        </template>
    </div>
</x-layouts.camarero>
