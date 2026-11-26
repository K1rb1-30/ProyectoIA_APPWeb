{{--
    «Restaurante no disponible» (rama 24). Pantalla nueva, sin maqueta: sigue
    el estilo de las páginas públicas («Comanda impresa»).
    La ve quien no es el admin (camarero, tablet de cocina o cliente del QR)
    cuando el restaurante no tiene la suscripción activa. La enseña el
    middleware ComprobarSuscripcion con código 403.

    No lleva botón naranja: quien la ve no puede hacer nada para arreglarlo;
    solo el admin del restaurante, desde su panel.
--}}
<x-layouts.base titulo="Restaurante no disponible">
    <main class="min-h-screen flex flex-col px-6 py-10 sm:px-10 lg:p-12">
        <p class="text-headline-md font-extrabold tracking-tight">MesaYa<span class="text-primary-container">.</span></p>

        <div class="w-full max-w-xl my-auto">
            <p class="font-mono text-label-md text-secondary">{{ $restaurante->nombre }}</p>
            <h1 class="mt-2 text-headline-lg">Restaurante no disponible</h1>
            <p class="mt-4 pt-4 border-t-2 border-on-surface text-body-lg text-on-surface-variant">
                Ahora mismo este restaurante no puede recibir pedidos desde MesaYa.
            </p>
            <p class="mt-2 text-body-md text-secondary">
                Si eres cliente, pide al personal del local. Si trabajas aquí, avisa al administrador del restaurante: tiene que revisar la suscripción en su panel de MesaYa.
            </p>
        </div>
    </main>
</x-layouts.base>
