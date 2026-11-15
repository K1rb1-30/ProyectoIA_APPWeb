# Diseño del frontend de MesaYa

Esta carpeta es **la referencia visual de la aplicación**. Las pantallas se generaron con Google Stitch a partir de los prompts de [`../prompts.md`](../prompts.md).

## Qué hay aquí

- [`DESIGN.md`](DESIGN.md): el sistema de diseño ("Warm Iberian Modernism"), común a todas las pantallas. Contiene colores, tipografía, radios, espaciado y normas de componentes.
- Una carpeta por pantalla, agrupadas por rol. Cada una tiene dos archivos:
  - `screen.png`: captura de cómo tiene que quedar.
  - `code.html`: maqueta en HTML + Tailwind, con datos de ejemplo de "Bar La Plaza".

## Índice de pantallas

| Rol | Pantalla | Carpeta | Dispositivo | Prioridad |
|---|---|---|---|---|
| Público | Página de inicio | [`landing/inicio/`](landing/inicio/) | Escritorio | 🟢 MVP |
| Público | Login del personal | [`landing/login/`](landing/login/) | Escritorio | 🟢 MVP |
| Público | Precios y planes | [`landing/precios/`](landing/precios/) | Escritorio | Sin requisito: preguntar antes |
| Admin | Panel principal | [`panelAdmin/panel/`](panelAdmin/panel/) | Escritorio | 🟢 MVP |
| Admin | Carta y edición de plato | [`panelAdmin/carta/`](panelAdmin/carta/) | Escritorio | 🟢 MVP |
| Admin | Mesas y códigos QR | [`panelAdmin/mesas-qr/`](panelAdmin/mesas-qr/) | Escritorio | 🟢 MVP |
| Admin | Estadísticas y ventas | [`panelAdmin/estadisticas/`](panelAdmin/estadisticas/) | Escritorio | 🔵 v1.0 |
| Camarero | Vista de mesas y avisos | [`camarero/mesas/`](camarero/mesas/) | Móvil | 🟢 MVP |
| Camarero | Nueva comanda | [`camarero/nueva-comanda/`](camarero/nueva-comanda/) | Móvil | 🟢 MVP |
| Camarero | Detalle de mesa (cobro y cierre) | [`camarero/detalle-mesa/`](camarero/detalle-mesa/) | Móvil | 🔵 v1.0 |
| Cocina | Tablero de pedidos | [`cocina/pedidos/`](cocina/pedidos/) | Tablet horizontal | 🟢 MVP |
| Cliente | Carta por QR | [`cliente/carta/`](cliente/carta/) | Móvil | 🔵 v1.0 |
| Cliente | Mi pedido | [`cliente/mi-pedido/`](cliente/mi-pedido/) | Móvil | 🔵 v1.0 |
| Cliente | Estado del pedido | [`cliente/estado-pedido/`](cliente/estado-pedido/) | Móvil | 🔵 v1.0 |
| Cliente | Pedir la cuenta y dividir | [`cliente/dividir-cuenta/`](cliente/dividir-cuenta/) | Móvil | 🔵 v1.0 |
| Cliente | Pago con tarjeta (Stripe) | [`cliente/pago-tarjeta/`](cliente/pago-tarjeta/) | Móvil | 🔵 v1.0 |
| Cliente | Pago en efectivo | [`cliente/pago-efectivo/`](cliente/pago-efectivo/) | Móvil | 🔵 v1.0 |
| Cliente | Pago completado | [`cliente/pago-completado/`](cliente/pago-completado/) | Móvil | 🔵 v1.0 |

No hay maqueta de las pantallas de **Alérgenos** ni de **Personal** del panel de administración: se hacen siguiendo el estilo de `panelAdmin/carta/`.

## Cómo usar el diseño al programar una vista

1. Busca la pantalla en el índice y mira su `screen.png`.
2. Abre su `code.html` y copia la estructura y las clases de Tailwind a la vista Blade.
3. Aplica las normas de `DESIGN.md` en lo que la maqueta no cubra.

## Diferencias entre las maquetas y el proyecto

Las maquetas son HTML suelto; al pasarlas a Blade hay que adaptar esto:

- **Tailwind.** Las maquetas cargan Tailwind 3 desde un CDN, con los colores en un `tailwind.config` dentro de cada HTML. El proyecto usa Tailwind 3 compilado con Vite (lo instala Breeze), así que no se copia ese `<script>`: los colores, tamaños de letra y espaciados se definen una sola vez en `tailwind.config.js`, en la raíz del proyecto. Los 18 HTML usan exactamente la misma paleta.
- **Radios.** Los `code.html` usan radios más pequeños (`lg` = 0,5 rem) que los que indica `DESIGN.md` (`lg` = 1 rem). Manda la captura `screen.png`.
- **Iconos.** Usan la fuente Material Symbols Outlined de Google Fonts, y la tipografía Inter. Hay que decidir si se cargan desde Google Fonts o se sirven en local.
- **Datos.** Los textos, precios y platos están escritos a mano. En Blade salen de la base de datos, con el dinero guardado en céntimos y mostrado como `12,50 €`.
- **Fotos.** Las imágenes son de ejemplo y apuntan a servidores externos; no se usan en la aplicación.
- **Interactividad.** Las maquetas no tienen lógica. Se añade con Alpine.js y con el polling cada 5 s.
- **Nombres.** Las clases e identificadores propios que se creen van en castellano y sin tildes, como en el resto del proyecto.
