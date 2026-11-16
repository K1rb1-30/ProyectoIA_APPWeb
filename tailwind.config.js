import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/*
 * Sistema de diseño de MesaYa ("Warm Iberian Modernism").
 * Todos los valores salen de docs/design/DesignFrontend/DESIGN.md.
 * Se definen aquí una sola vez y las vistas usan las clases que generan
 * (por ejemplo bg-primary-container, text-headline-lg, shadow-nivel-1).
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
            // Paleta: la misma que usan las 18 maquetas.
            colors: {
                'surface': '#f9f9ff',
                'surface-dim': '#d3daea',
                'surface-bright': '#f9f9ff',
                'surface-container-lowest': '#ffffff',
                'surface-container-low': '#f0f3ff',
                'surface-container': '#e7eefe',
                'surface-container-high': '#e2e8f8',
                'surface-container-highest': '#dce2f3',
                'surface-variant': '#dce2f3',
                'surface-tint': '#9d4300',
                'on-surface': '#151c27',
                'on-surface-variant': '#584237',
                'inverse-surface': '#2a313d',
                'inverse-on-surface': '#ebf1ff',
                'background': '#f9f9ff',
                'on-background': '#151c27',
                'outline': '#8c7164',
                'outline-variant': '#e0c0b1',

                'primary': '#9d4300',
                'on-primary': '#ffffff',
                'primary-container': '#f97316',
                'on-primary-container': '#582200',
                'inverse-primary': '#ffb690',
                'primary-fixed': '#ffdbca',
                'primary-fixed-dim': '#ffb690',
                'on-primary-fixed': '#341100',
                'on-primary-fixed-variant': '#783200',

                'secondary': '#555f6f',
                'on-secondary': '#ffffff',
                'secondary-container': '#d6e0f3',
                'on-secondary-container': '#596373',
                'secondary-fixed': '#d9e3f6',
                'secondary-fixed-dim': '#bdc7d9',
                'on-secondary-fixed': '#121c2a',
                'on-secondary-fixed-variant': '#3d4756',

                'tertiary': '#006c49',
                'on-tertiary': '#ffffff',
                'tertiary-container': '#00b07a',
                'on-tertiary-container': '#003b26',
                'tertiary-fixed': '#6ffbbe',
                'tertiary-fixed-dim': '#4edea3',
                'on-tertiary-fixed': '#002113',
                'on-tertiary-fixed-variant': '#005236',

                'error': '#ba1a1a',
                'on-error': '#ffffff',
                'error-container': '#ffdad6',
                'on-error-container': '#93000a',

                // Colores de estado (apartado "Status Indicators" de DESIGN.md).
                // "servido" no tiene color en el diseño: usamos un gris neutro.
                'estado': {
                    pendiente: '#f59e0b',
                    preparando: '#3b82f6',
                    listo: '#10b981',
                    servido: '#6b7280',
                },
            },

            // Inter para todo el texto. "Inter Variable" es el nombre con el
            // que el paquete @fontsource-variable/inter registra la fuente.
            fontFamily: {
                sans: ['"Inter Variable"', 'Inter', ...defaultTheme.fontFamily.sans],
            },

            // Tamaños de letra con nombre. Cada uno lleva su interlineado,
            // su espaciado entre letras y su grosor: text-headline-lg ya
            // sale en negrita sin añadir font-bold.
            fontSize: {
                'display-hero': ['40px', { lineHeight: '48px', letterSpacing: '-0.02em', fontWeight: '800' }],
                'display-hero-mobile': ['30px', { lineHeight: '36px', letterSpacing: '-0.015em', fontWeight: '800' }],
                'headline-lg': ['28px', { lineHeight: '34px', letterSpacing: '-0.01em', fontWeight: '700' }],
                'headline-md': ['22px', { lineHeight: '28px', letterSpacing: '-0.01em', fontWeight: '700' }],
                'headline-sm': ['18px', { lineHeight: '24px', fontWeight: '600' }],
                'body-lg': ['16px', { lineHeight: '24px', fontWeight: '400' }],
                'body-md': ['14px', { lineHeight: '20px', fontWeight: '400' }],
                'label-lg': ['15px', { lineHeight: '20px', letterSpacing: '0.01em', fontWeight: '600' }],
                'label-md': ['13px', { lineHeight: '18px', letterSpacing: '0.01em', fontWeight: '600' }],
                'label-sm': ['11px', { lineHeight: '14px', letterSpacing: '0.03em', fontWeight: '700' }],
                'price-display': ['18px', { lineHeight: '22px', letterSpacing: '-0.01em', fontWeight: '700' }],
            },

            // Radios de DESIGN.md (lg = 1rem). Las maquetas code.html usan
            // radios más pequeños, pero manda la captura screen.png.
            borderRadius: {
                sm: '0.25rem',
                DEFAULT: '0.5rem',
                md: '0.75rem',
                lg: '1rem',
                xl: '1.5rem',
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

            // Sombras de DESIGN.md, apartado "Elevation & Depth".
            boxShadow: {
                // Nivel 1: tarjetas de carta y de platos.
                'nivel-1': '0 2px 8px -2px rgba(31, 41, 55, 0.05), 0 1px 3px 0 rgba(31, 41, 55, 0.03)',
                // Nivel 2: barras fijas inferiores, hojas y modales.
                'nivel-2': '0 12px 24px -6px rgba(31, 41, 55, 0.12), 0 4px 8px -2px rgba(31, 41, 55, 0.04)',
            },
        },
    },

    plugins: [forms],
};
