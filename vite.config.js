import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            // estadisticas.js (rama 13) va aparte: solo lo carga la página de
            // estadísticas, así Chart.js no se descarga en las demás.
            input: ['resources/css/app.css', 'resources/js/app.js', 'resources/js/estadisticas.js'],
            refresh: true,
        }),
    ],
});
