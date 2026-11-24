/*
 * Gráficos de la pantalla de estadísticas del admin (rama 13), con Chart.js.
 *
 * Este archivo es una entrada APARTE de Vite (vite.config.js) y solo lo carga
 * admin/estadisticas.blade.php con @vite: así Chart.js no se descarga en las
 * demás páginas (carta del cliente, cocina...).
 *
 * Los datos llegan de Blade en el atributo data-datos de cada <canvas>, en
 * CÉNTIMOS. Solo se pasan a euros al enseñarlos (ejes y tooltips).
 */
import {
    Chart,
    BarController,
    BarElement,
    CategoryScale,
    LinearScale,
    DoughnutController,
    ArcElement,
    Tooltip,
} from 'chart.js';

// Chart.js 4 obliga a registrar las piezas que se usan. Solo registramos estas
// y Vite deja fuera el resto de la librería (el archivo pesa menos).
Chart.register(BarController, BarElement, CategoryScale, LinearScale, DoughnutController, ArcElement, Tooltip);

// Colores del diseño «Comanda impresa» (tailwind.config.js).
const NARANJA = '#c2410c'; // primary-container
const GRIS_CLARO = '#cfccc4'; // secondary-fixed-dim
const OSCURO = '#1a1a1a'; // on-surface / inverse-surface
const GRIS_TEXTO = '#5f5e5a'; // secondary

// Letras de los ejes: la misma de las cifras de la web (IBM Plex Mono), en gris.
Chart.defaults.font.family = '"IBM Plex Mono", ui-monospace, monospace';
Chart.defaults.font.size = 12;
Chart.defaults.color = GRIS_TEXTO;

// 1250 → "12,50 €". Igual que el componente <x-precio> de Blade.
const formatoEuros = new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR' });
const euros = (centimos) => formatoEuros.format(centimos / 100);

// "2026-11-23" → "23 nov". Se crea la fecha con sus partes para que el
// navegador no la mueva de día por la zona horaria.
function diaCorto(fechaIso) {
    const [anio, mes, dia] = fechaIso.split('-').map(Number);
    return new Date(anio, mes - 1, dia).toLocaleDateString('es-ES', { day: 'numeric', month: 'short' });
}

function graficoVentasPorDia(canvas, filas) {
    const totales = filas.map((fila) => fila.total_centimos);
    const media = totales.reduce((suma, total) => suma + total, 0) / totales.length;

    new Chart(canvas, {
        type: 'bar',
        data: {
            labels: filas.map((fila) => diaCorto(fila.dia)),
            datasets: [{
                label: 'Ventas',
                data: totales, // en céntimos
                // Naranja si el día supera la media; gris claro si no.
                backgroundColor: totales.map((total) => (total > media ? NARANJA : GRIS_CLARO)),
                borderRadius: 2,
            }],
        },
        options: {
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    // Las marcas del eje se enseñan en euros.
                    ticks: { callback: (valor) => euros(valor) },
                    grid: { color: '#e6e3dc' }, // líneas de fondo muy suaves
                    border: { display: false },
                },
                x: { grid: { display: false } },
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        // "Ventas: 1.234,50 € (12 pagos)"
                        label: (contexto) => `Ventas: ${euros(contexto.raw)} (${filas[contexto.dataIndex].pagos} pagos)`,
                    },
                },
            },
        },
    });
}

function graficoMetodosDePago(canvas, datos) {
    new Chart(canvas, {
        type: 'doughnut',
        data: {
            labels: ['Tarjeta', 'Efectivo'],
            datasets: [{
                data: [datos.tarjeta, datos.efectivo], // en céntimos
                backgroundColor: [NARANJA, OSCURO],
                borderWidth: 0,
            }],
        },
        options: {
            cutout: '72%', // el hueco del centro, donde va el % en HTML
            plugins: {
                tooltip: {
                    callbacks: { label: (contexto) => `${contexto.label}: ${euros(contexto.raw)}` },
                },
            },
        },
    });
}

// Cada <canvas data-grafico="..."> se pinta con su función.
const graficos = {
    'ventas-por-dia': graficoVentasPorDia,
    'metodos-de-pago': graficoMetodosDePago,
};

document.querySelectorAll('canvas[data-grafico]').forEach((canvas) => {
    const pintar = graficos[canvas.dataset.grafico];
    if (pintar) {
        pintar(canvas, JSON.parse(canvas.dataset.datos));
    }
});
