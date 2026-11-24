# Sistema de diseño de MesaYa: «Comanda impresa»

Rediseño de noviembre de 2026 (rama 19, `feature/rediseno-base`). Sustituye al sistema «Warm Iberian Modernism» de las maquetas de Google Stitch.

**Por qué se cambió:** las maquetas de Stitch tenían rasgos típicos de una web hecha con IA: portada centrada con un brillo de fondo, etiquetas en forma de pastilla encima de cada título, iconos dentro de cuadraditos de color, bloques de tres o cuatro tarjetas iguales y barras de color a la izquierda de las tarjetas. Además, la jerarquía era plana: todo pesaba lo mismo.

**La idea:** que la web se parezca a lo que se usa de verdad en un bar: la comanda y el ticket impresos. Tinta casi negra sobre papel, cifras en letra de máquina y un solo color de acento.

Los valores están definidos una sola vez en `tailwind.config.js`. **Los nombres de los colores son los de Stitch** (`primary-container`, `secondary`, `outline-variant`...) para no tener que cambiar todas las vistas; solo han cambiado sus valores.

> Las capturas `screen.png` y los `code.html` de cada carpeta son del diseño **anterior**. Sirven para ver qué contenido lleva cada pantalla, no su aspecto. El aspecto nuevo de las pantallas principales está en el lienzo del rediseño (pedid el enlace a Gabriel).

## Color

| Uso | Clase de Tailwind | Valor |
|---|---|---|
| Fondo de la página («papel») | `bg-background` / `bg-surface` | `#F5F4F0` |
| Superficies (tarjetas, campos) | `bg-surface-container-lowest` | `#FFFFFF` |
| Texto principal («tinta») | `text-on-surface` | `#1A1A1A` |
| Texto secundario | `text-secondary` | `#5F5E5A` (6:1 sobre el fondo) |
| Líneas finas (filetes) | `border-outline-variant` | `#DAD7CF` |
| Bordes de campos | `border-secondary-fixed-dim` | `#CFCCC4` |
| Acento «pimentón»: botón principal | `bg-primary-container` | `#C2410C` (blanco encima: 5,2:1) |
| Hover del botón y enlaces de acento | `bg-primary` / `text-primary` | `#9A3412` |
| Fondo naranja muy claro (notas de cocina) | `bg-primary-fixed` | `#FDECE2` |
| Pagado, disponible, listo | `text-tertiary` / `bg-tertiary-container` | `#166534` / `#15803D` |
| Error, agotado | `text-error` / `bg-error-container` | `#B42318` / `#FDE7E4` |

Estados de los pedidos (`bg-estado-*`): pendiente `#D97706`, preparando `#2563EB`, listo `#15803D`, servido `#8C8A84`. Se diferencian también en claridad, no solo en el tono.

**Normas:**
- **Un solo botón naranja por pantalla**, para la acción principal. Las demás acciones van en negro, con borde o como enlace.
- El color **nunca va solo**: un estado siempre lleva su palabra («Pendiente», «Listo»).
- Nada de degradados.

## Tipografía

- **Schibsted Grotesk** para todo el texto (`font-sans`, la de por defecto).
- **IBM Plex Mono** para las cifras que se leen como en un ticket: precios destacados, totales, números de mesa, horas y números de pedido (`font-mono`). El componente `x-precio` ya lleva `tabular-nums`, que alinea las cifras en columna.
- Las dos se sirven en local con los paquetes de `@fontsource` (sin llamadas a Google).

| Clase | Tamaño / interlineado | Grosor | Para |
|---|---|---|---|
| `text-display-hero` | 56 / 58 px | 800 | Título de la portada (escritorio) |
| `text-display-hero-mobile` | 38 / 40 px | 800 | Título de la portada (móvil) |
| `text-headline-lg` | 32 / 36 px | 800 | Título de la pantalla |
| `text-headline-md` | 24 / 30 px | 700 | Título de sección |
| `text-headline-sm` | 18 / 24 px | 700 | Nombre de un elemento (plato, mesa) |
| `text-body-lg` | 17 / 26 px | 400 | Texto destacado |
| `text-body-md` | 15 / 22 px | 400 | Texto normal |
| `text-label-lg` / `-md` / `-sm` | 15 / 14 / 12 px | 600 | Botones, etiquetas de campo, datos pequeños |
| `text-price-display` | 18 px | 600 | Precios |

Los títulos llevan el espaciado entre letras negativo (más apretados). Así se distinguen mejor del texto normal.

## Jerarquía

1. **Un dato principal por pantalla**, en grande. Ejemplos: «Ventas cobradas» en el panel, «Falta por cobrar» en el detalle de mesa, el total en la cuenta del cliente. Los datos secundarios van al lado, más pequeños.
2. **Títulos alineados a la izquierda.** Nada de bloques centrados con un texto encima del título.
3. **Listas en filas separadas por líneas finas**, no en tarjetas apiladas. Una línea gruesa (2 px, tinta) bajo el título de cada sección.
4. **Tarjetas solo para agrupar de verdad** (un formulario, un resumen). Borde fino, sin sombra marcada.
5. **Iconos solo cuando ayudan** (volver, añadir). Nunca dentro de un cuadradito de color decorativo.

## Forma y espacio

- Radios pequeños: `rounded-sm` 2 px, `rounded` 6 px, `rounded-md`/`rounded-lg` 8 px, `rounded-xl` 12 px. `rounded-full` solo para puntos de estado e interruptores.
- Sombras casi invisibles: `shadow-nivel-1` (tarjetas) y `shadow-nivel-2` (barras fijas de abajo y modales).
- Espaciados con nombre, iguales que antes: `space-xs` 4 px · `space-sm` 8 px · `space-md` 16 px · `space-lg` 24 px · `space-xl` 32 px.
- Zonas táctiles de **48 px de alto como mínimo** (44 px en botones pequeños de iconos).

## Componentes (`resources/views/components/`)

| Componente | Aspecto |
|---|---|
| `x-boton-primario` | Fondo pimentón, texto blanco en negrita. Al pulsar baja 1 px (`pulsable`). |
| `x-boton-secundario` | Sin relleno, borde de tinta de 1,5 px. |
| `x-boton-peligro` | Fondo rojo, para borrar. |
| `x-chip-estado` | Punto de color + palabra, sin cápsula. Servido y libre llevan el punto hueco. |
| `x-badge-alergeno` | Etiqueta con borde fino y esquinas casi rectas. `critico` la pinta en rojo. |
| `x-tarjeta` | Superficie blanca, borde fino, esquinas de 6 px. |
| `x-campo-texto` | 48 px de alto, borde de 1,5 px; al enfocar, borde y anillo naranjas. |
| `x-logo` | Cuadrado de tinta con «M» y un punto naranja. |
| `x-precio` | `12,50 €` con cifras tabulares. |

## Formato de los precios

Coma decimal, espacio que no se parte y símbolo del euro: `12,50 €`. Lo hace siempre `x-precio` a partir de los céntimos.
