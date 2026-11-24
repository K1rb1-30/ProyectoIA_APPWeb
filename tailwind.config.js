import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/*
 * Sistema de diseño de MesaYa ("Comanda impresa", rediseño de noviembre 2026).
 * Todos los valores salen de docs/design/DesignFrontend/DESIGN.md.
 * Se definen aquí una sola vez y las vistas usan las clases que generan
 * (por ejemplo bg-primary-container, text-headline-lg, shadow-nivel-1).
 *
 * IMPORTANTE: los NOMBRES de los colores son los de antes (vienen de las
 * maquetas de Stitch). Solo han cambiado sus VALORES. Así todas las vistas
 * cogen el aspecto nuevo sin tener que editarlas una a una.
 *   - "primary-container" es el naranja de los botones principales.
 *   - "secondary" es el gris de los textos secundarios.
 *   - "outline-variant" es el color de las líneas finas (filetes).
 */

/** @type {import('tailwindcss').Config} */
export default {
    // Archivos donde Tailwind busca las clases que se usan de verdad.
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            colors: {
                // Fondos y superficies: gris "papel" y blanco.
                'surface': '#f5f4f0',
                'surface-dim': '#e6e3dc',
                'surface-bright': '#f5f4f0',
                'surface-container-lowest': '#ffffff',
                'surface-container-low': '#efede8',
                'surface-container': '#e9e7e1',
                'surface-container-high': '#e6e3dc',
                'surface-container-highest': '#dad7cf',
                'surface-variant': '#e6e3dc',
                'surface-tint': '#c2410c',
                'background': '#f5f4f0',

                // Textos: tinta casi negra y su versión suave.
                'on-surface': '#1a1a1a',
                'on-surface-variant': '#3d3c39',
                'on-background': '#1a1a1a',

                // Superficie invertida (barras oscuras, aviso de plato listo).
                'inverse-surface': '#1a1a1a',
                'inverse-on-surface': '#f5f4f0',

                // Líneas: "outline" para bordes marcados, "outline-variant" para filetes.
                'outline': '#8c8a84',
                'outline-variant': '#dad7cf',

                // Acento "pimentón". Blanco sobre #c2410c tiene un contraste de 5,2:1
                // (el naranja de antes, #f97316, se quedaba en 2,8:1 y no se leía bien).
                'primary': '#9a3412',              // hover de los botones y enlaces
                'on-primary': '#ffffff',
                'primary-container': '#c2410c',    // fondo del botón principal
                'on-primary-container': '#431407',
                'inverse-primary': '#f08a4b',
                'primary-fixed': '#fdece2',        // fondo naranja muy claro (notas de cocina)
                'primary-fixed-dim': '#f7c6a8',
                'on-primary-fixed': '#431407',
                'on-primary-fixed-variant': '#9a3412',

                // Grises neutros (antes eran grises azulados).
                'secondary': '#5f5e5a',            // texto secundario: 6:1 sobre el fondo
                'on-secondary': '#ffffff',
                'secondary-container': '#e6e3dc',
                'on-secondary-container': '#3d3c39',
                'secondary-fixed': '#e6e3dc',
                'secondary-fixed-dim': '#cfccc4',
                'on-secondary-fixed': '#1a1a1a',
                'on-secondary-fixed-variant': '#3d3c39',

                // Verde: pagado, disponible, listo.
                'tertiary': '#166534',
                'on-tertiary': '#ffffff',
                'tertiary-container': '#15803d',
                'on-tertiary-container': '#14532d',
                'tertiary-fixed': '#dcfce7',
                'tertiary-fixed-dim': '#bbf7d0',
                'on-tertiary-fixed': '#052e16',
                'on-tertiary-fixed-variant': '#166534',

                // Rojo: errores y "agotado".
                'error': '#b42318',
                'on-error': '#ffffff',
                'error-container': '#fde7e4',
                'on-error-container': '#7a1a10',

                // Colores de estado de los pedidos. Son algo más oscuros que antes
                // para que se distingan también por claridad, no solo por el tono.
                'estado': {
                    pendiente: '#d97706',
                    preparando: '#2563eb',
                    listo: '#15803d',
                    servido: '#8c8a84',
                },
            },

            // Schibsted Grotesk para el texto e IBM Plex Mono para las cifras
            // (precios, números de mesa, horas), como en un ticket de cocina.
            // Los nombres son los que registran los paquetes de @fontsource.
            fontFamily: {
                sans: ['"Schibsted Grotesk Variable"', 'Schibsted Grotesk', ...defaultTheme.fontFamily.sans],
                mono: ['"IBM Plex Mono"', ...defaultTheme.fontFamily.mono],
            },

            // Tamaños de letra con nombre. Cada uno lleva su interlineado,
            // su espaciado entre letras y su grosor: text-headline-lg ya
            // sale en negrita sin añadir font-bold.
            // Los títulos son más grandes y apretados que antes: así hay más
            // diferencia entre un título y el texto normal (mejor jerarquía).
            fontSize: {
                'display-hero': ['56px', { lineHeight: '58px', letterSpacing: '-0.04em', fontWeight: '800' }],
                'display-hero-mobile': ['38px', { lineHeight: '40px', letterSpacing: '-0.035em', fontWeight: '800' }],
                'headline-lg': ['32px', { lineHeight: '36px', letterSpacing: '-0.03em', fontWeight: '800' }],
                'headline-md': ['24px', { lineHeight: '30px', letterSpacing: '-0.02em', fontWeight: '700' }],
                'headline-sm': ['18px', { lineHeight: '24px', letterSpacing: '-0.01em', fontWeight: '700' }],
                'body-lg': ['17px', { lineHeight: '26px', fontWeight: '400' }],
                'body-md': ['15px', { lineHeight: '22px', fontWeight: '400' }],
                'label-lg': ['15px', { lineHeight: '20px', fontWeight: '600' }],
                'label-md': ['14px', { lineHeight: '20px', fontWeight: '600' }],
                'label-sm': ['12px', { lineHeight: '16px', letterSpacing: '0.01em', fontWeight: '600' }],
                'price-display': ['18px', { lineHeight: '22px', fontWeight: '600' }],
            },

            // Radios más pequeños que antes: esquinas sobrias en vez de "burbujas".
            borderRadius: {
                sm: '0.125rem',
                DEFAULT: '0.375rem',
                md: '0.5rem',
                lg: '0.5rem',
                xl: '0.75rem',
                full: '9999px',
            },

            // Espaciados con nombre: p-space-md, gap-gutter, px-margin...
            spacing: {
                'gutter': '1rem',
                'gutter-md': '1.5rem',
                'margin': '1rem',
                'margin-md': '1.5rem',
                'margin-lg': '2.5rem',
                'space-xs': '0.25rem',
                'space-sm': '0.5rem',
                'space-md': '1rem',
                'space-lg': '1.5rem',
                'space-xl': '2rem',
            },

            // Sombras casi invisibles: la separación la hacen las líneas, no las sombras.
            boxShadow: {
                // Nivel 1: tarjetas.
                'nivel-1': '0 1px 0 rgba(26, 26, 26, 0.04)',
                // Nivel 2: barras fijas inferiores, hojas y modales.
                'nivel-2': '0 12px 28px -14px rgba(26, 26, 26, 0.35)',
            },
        },
    },

    plugins: [forms],
};
