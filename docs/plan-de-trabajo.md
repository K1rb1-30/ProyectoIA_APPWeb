# Plan de trabajo de MesaYa

Este documento es **el plan común de los dos**: qué ramas hay que hacer, en qué orden, quién se encarga de cada una y cómo va.

**Cómo usarlo**
- Antes de empezar una rama, **poned vuestro nombre en "Responsable"** y cambiad el estado a 🟡. Así el otro sabe que no tiene que tocarla.
- Al abrir la PR, poned el enlace en la columna "PR". Al mergearla, cambiad el estado a ✅.
- Estos cambios del plan se suben con la PR de la propia rama.
- Si el orden o el reparto cambian, se actualiza aquí y no en el chat.

Estados: ⬜ pendiente · 🟡 en curso · 🔍 en revisión (PR abierta) · ✅ mergeada en `main`

---

## 1. Seguimiento

### Tareas previas

| # | Tarea | Responsable | Estado |
|---|---|---|---|
| P1 | Corregir en `docs/01-proposta.md` "Laravel 12" → 13 y "SQLite en local" → MySQL siempre | Iván | 🔍 [#20](https://github.com/K1rb1-30/ProyectoIA_APPWeb/pull/20) |
| P2 | Escribir `docs/02-requisits.md` (requisitos numerados RF1, RF2…) | Iván | 🔍 [#20](https://github.com/K1rb1-30/ProyectoIA_APPWeb/pull/20) |
| P3 | Escribir `docs/03-disseny.md` (diagrama de tablas y lista de rutas) | Iván | 🔍 [#20](https://github.com/K1rb1-30/ProyectoIA_APPWeb/pull/20) |
| P4 | Despliegue mínimo en Railway con MySQL (aunque solo enseñe el login) | | ⬜ |
| P5 | Renombrar las rutas de Breeze `/dashboard` → `/panel` y `/profile` → `/perfil` | | ⬜ |
| ✔ | Login con Breeze en castellano (PR #4) | Gabriel | ✅ |

### 🟢 MVP (v0.1) — objetivo: 23/11

| # | Rama | Depende de | Responsable | Estado | PR |
|---|---|---|---|---|---|
| 1 | `feature/restaurantes-y-roles` | — | Gabriel | ✅ | [#8](https://github.com/K1rb1-30/ProyectoIA_APPWeb/pull/8) |
| 2 | `feature/diseno-base` | — | Iván | ✅ | [#7](https://github.com/K1rb1-30/ProyectoIA_APPWeb/pull/7) |
| 3 | `feature/gestion-carta` | 1, 2 | Iván | ✅ | [#10](https://github.com/K1rb1-30/ProyectoIA_APPWeb/pull/10) |
| 4 | `feature/mesas-qr` | 1, 2 | Gabriel | ✅ | [#12](https://github.com/K1rb1-30/ProyectoIA_APPWeb/pull/12) |
| 5 | `feature/comandas-camarero` | 3, 4 | Gabriel | ✅ | [#14](https://github.com/K1rb1-30/ProyectoIA_APPWeb/pull/14) |
| 6 | `feature/pantalla-cocina` | 5 | Gabriel | ✅ | [#15](https://github.com/K1rb1-30/ProyectoIA_APPWeb/pull/15) |
| 7 | `feature/avisos-camarero` | 6 | Gabriel | ✅ | [#17](https://github.com/K1rb1-30/ProyectoIA_APPWeb/pull/17) |

Al terminar la 7: entrada **v0.1** en `CHANGELOG.md` y etiqueta `v0.1`.

### 🔵 v1.0 — objetivo: 30/11

| # | Rama | Depende de | Responsable | Estado | PR |
|---|---|---|---|---|---|
| 8 | `feature/carta-publica-qr` | 3, 4 | Iván | ✅ | [#13](https://github.com/K1rb1-30/ProyectoIA_APPWeb/pull/13) |
| 9 | `feature/autopedido` | 5, 8 | Iván | ✅ | [#16](https://github.com/K1rb1-30/ProyectoIA_APPWeb/pull/16) |
| 10 | `feature/dividir-cuenta` | 5 | Iván | ✅ | [#19](https://github.com/K1rb1-30/ProyectoIA_APPWeb/pull/19) |
| 11 | `feature/pago-stripe` | 10 | Gabriel | ✅ | [#21](https://github.com/K1rb1-30/ProyectoIA_APPWeb/pull/21) |
| 12 | `feature/efectivo-y-cierre` | 10 | Gabriel | ✅ | [#22](https://github.com/K1rb1-30/ProyectoIA_APPWeb/pull/22) |
| 13 | `feature/estadisticas` | 11, 12 | Gabriel | ✅ | [#25](https://github.com/K1rb1-30/ProyectoIA_APPWeb/pull/25) |

### 🟠 Siguientes: mejoras propias (Iván, mientras Gabriel hace la 13)

No estaban en la propuesta: salen de probar la aplicación. No confundir con los extras 🟣 de la propuesta.

- Las dos **se pueden empezar ya**: sus dependencias (9, 10, 11 y 12) están en `main` y **no dependen de la 13**.
- **Orden: primero la 14 y luego la 15.** Las dos tocan la cuenta de la mesa, así que mejor una detrás de otra para no pisarse.
- No tocan las estadísticas, así que se pueden hacer a la vez que la 13 de Gabriel.

| # | Rama | Depende de | Responsable | Estado | PR |
|---|---|---|---|---|---|
| 14 | `feature/platos-compartidos` | 10 | Iván | ✅ | [#24](https://github.com/K1rb1-30/ProyectoIA_APPWeb/pull/24) |
| 15 | `feature/pagar-al-pedir` | 9, 11, 12 y la 14 (orden) | Iván | 🟡 | |
| 16 | `feature/pagina-inicio` | 2 | Gabriel | ✅ | [#26](https://github.com/K1rb1-30/ProyectoIA_APPWeb/pull/26) |

### Semana 4 — hasta el 07/12

| Tarea | Responsable | Estado |
|---|---|---|
| `docs/04-installacio.md` (falta el despliegue, P4) | Iván | 🔍 [#20](https://github.com/K1rb1-30/ProyectoIA_APPWeb/pull/20) |
| `docs/05-manual-usuari.md` con capturas | | ⬜ |
| `docs/06-proves.md` (los 6 golden tests y cómo ejecutarlos; falta el nº 4, rama 12) | Iván | 🔍 [#20](https://github.com/K1rb1-30/ProyectoIA_APPWeb/pull/20) |
| Revisión de seguridad (secretos, aislamiento entre restaurantes, roles) | | ⬜ |
| `CHANGELOG.md` v1.0 y etiqueta `v1.0` | | ⬜ |
| Ensayo de la demo (flujo de la mesa 5) | | ⬜ |

### Reparto propuesto

Es el de `docs/01-proposta.md`. Cambiadlo si queréis, pero apuntadlo aquí.

- **Persona A:** 2 → 3 → 4 → 8 → 9 → 10, manual de usuario y capturas.
- **Persona B:** 1 → 5 → 6 → 7 → 11 → 12, despliegue, pruebas y CHANGELOG.
- **La 13 (estadísticas)**: Gabriel.
- **Mejoras 14 → 15**: Iván, a la vez que la 13.

La rama 5 necesita la 3 y la 4. Mientras tanto, la persona B puede cerrar la 1 con sus tests y hacer el despliegue (P4).

---

## 2. Qué incluye cada rama

### 1. `feature/restaurantes-y-roles`
- Migración `restaurantes` (nombre, slug).
- Migración que añade `rol` y `restaurante_id` a `users`.
- Middleware `ComprobarRol` (por ejemplo, `->middleware('rol:admin')`).
- Trait con el *global scope* que filtra por el `restaurante_id` del usuario logueado y lo rellena solo al crear un registro. Lo usarán todos los modelos de negocio.
- Seeder con 2 restaurantes y un usuario de cada rol en cada uno (datos de Faker o inventados).
- Tras el login, cada rol va a su pantalla: admin → panel, camarero → mesas, cocina → pedidos.
- **Test:** base del golden test nº 6 (el restaurante A no ve datos del B).

### 2. `feature/diseno-base`
- Colores, tipografía y radios de `docs/design/DesignFrontend/DESIGN.md` en `tailwind.config.js`.
- Fuente Inter e iconos Material Symbols. Hay que decidir si se cargan desde Google Fonts o en local.
- 4 layouts Blade: admin (escritorio con menú lateral), camarero (móvil), cocina (tablet horizontal) y cliente (móvil).
- Componente o helper para mostrar céntimos como `12,50 €`.
- Login con el aspecto de `landing/login/`.

### 3. `feature/gestion-carta`
- Migraciones `categorias`, `platos`, `alergenos` y `alergeno_plato`.
- `AlergenosSeeder` con los 14 alérgenos legales.
- CRUD de categorías y platos: foto (`php artisan storage:link`), precio en céntimos e interruptor de disponible.
- Vista según `panelAdmin/carta/`. Alérgenos no tiene maqueta: se sigue ese mismo estilo.
- **Test:** el CRUD solo ve y modifica platos del propio restaurante.

### 4. `feature/mesas-qr`
- Migración `mesas` (numero y estado `libre` / `ocupada`).
- QR con `simple-qrcode` que apunta a `/r/{slug}/mesa/{numero}`.
- PDF descargable. ⚠️ Hay que decidir la librería (ver apartado 3).
- Vista según `panelAdmin/mesas-qr/`.

### 5. `feature/comandas-camarero`
- Migraciones `pedidos` y `lineas_pedido`.
- El camarero elige mesa, platos, cantidades y notas. Se guarda el precio del momento en `lineas_pedido.precio_centimos`.
- Pedido y líneas se crean dentro de una transacción.
- Poned la creación del pedido en un solo sitio (una clase o un método), porque la rama 9 la reutiliza.
- Vistas según `camarero/mesas/` y `camarero/nueva-comanda/`.
- **Test:** golden test nº 1.

### 6. `feature/pantalla-cocina`
- Tablero con columnas por estado, según `cocina/pedidos/`.
- Método `cambiarEstado()` en `Pedido` que solo permite `pendiente → preparando → listo → servido`.
- Endpoint JSON y polling con JS cada 5 s.
- **Test:** golden test nº 2.

### 7. `feature/avisos-camarero`
- El camarero consulta cada 5 s los pedidos en `listo` y recibe el aviso.
- Botón para marcar el pedido como `servido`.

### 8. `feature/carta-publica-qr`
- Ruta pública `/r/{slug}/mesa/{numero}`, sin login.
- No hay usuario logueado, así que el *global scope* no actúa: **hay que filtrar a mano por el restaurante del slug**.
- Solo se muestran los platos disponibles. Vista según `cliente/carta/`.
- **Test:** golden test nº 5.

### 9. `feature/autopedido`
- Carrito con Alpine.js y envío del pedido con el código de la rama 5.
- Pantallas `cliente/mi-pedido/` y `cliente/estado-pedido/`, esta última con polling.
- **Test:** ampliar el nº 1 con un pedido hecho por el cliente (`user_id` nulo).

### 10. `feature/dividir-cuenta`
- Migraciones `cuentas` y `partes_cuenta`.
- División a partes iguales (el céntimo que sobra va a la primera parte) y por platos.
- Vista según `cliente/dividir-cuenta/`.
- **Test:** golden test nº 3 (dividir entre 3).

### 11. `feature/pago-stripe`
- Migración `pagos`.
- Stripe Checkout en modo test (tarjeta `4242 4242 4242 4242`). Las claves van en `.env`, nunca en el código.
- Para confirmar el pago, lo más sencillo es comprobar la sesión de Stripe al volver a la URL de éxito.
- Vistas `cliente/pago-tarjeta/` y `cliente/pago-completado/`.

### 12. `feature/efectivo-y-cierre`
- El cliente elige efectivo y la parte pasa a `pendiente_efectivo`. El camarero la confirma y pasa a `pagado`.
- La mesa solo se cierra cuando todas las partes están pagadas, y entonces vuelve a `libre`.
- Vistas `cliente/pago-efectivo/` y `camarero/detalle-mesa/`.
- **Test:** golden test nº 4 (pagos mixtos).
- **Añadido al hacerla** (acordado el 23/11/2026): el camarero puede dividir la cuenta y cobrar en la mesa (efectivo con cálculo del cambio o TPV), y la pestaña «Cobros». Ver RF30 a RF32.

### 13. `feature/estadisticas`
- Ventas por día, platos más pedidos y tarjeta frente a efectivo, con Chart.js.
- Vista según `panelAdmin/estadisticas/`.

### 14. `feature/platos-compartidos` 🟠
- Al dividir la cuenta por platos, opción **«Todos»** en cada plato: se reparte a partes iguales entre todas las personas (el céntimo que sobra, a la primera).
- Ahora cada plato va entero a una sola persona (decisión de la rama 10); lo normal en un bar es pedir algo para compartir.
- Solo toca `DividirCuenta` y la pantalla de la cuenta.
- **Test:** con platos compartidos, la suma de las partes sigue siendo exactamente el total.

### 15. `feature/pagar-al-pedir` 🟠
- **Problema:** el QR es una URL fija. Con una foto se puede pedir desde fuera del bar, o hacer pedidos de broma que la cocina prepara para nadie.
- **Solución:** el pedido que hace el **cliente** desde el QR se **paga con tarjeta al momento**. El pedido **no existe** (ni llega a cocina) hasta que Stripe confirma el pago.

**Decisiones (23/11/2026)**
1. **Efectivo:** el cliente no puede pagar en efectivo desde el móvil. En «Mi pedido» se le dice que, si quiere pagar en efectivo, **llame al camarero**, que le toma nota con una comanda normal (y se cobra con la cuenta, como ahora).
2. **Cuenta:** lo pagado al pedir **sale en la cuenta como ya pagado** (para el camarero y para el cliente) y **no se vuelve a cobrar**.
3. **El autopedido sin pagar desaparece del todo:** «Enviar a cocina» pasa a ser «Pagar y enviar a cocina».

**Cómo se hace**
- Al pulsar «Pagar y enviar a cocina», el servidor comprueba los platos (de este restaurante y disponibles), calcula el total con **sus** precios y abre una sesión de Stripe Checkout por ese importe. Las líneas del carrito se guardan en la sesión de Laravel, junto al id de la sesión de Stripe.
- Al volver de Stripe, se **pregunta a Stripe** si está pagado (como en la rama 11). Si cuadra (pagado, importe, moneda y mesa), en una transacción:
  1. se crea el pedido con `CrearPedido` (sin usuario), que llega a cocina;
  2. se apunta en la **cuenta abierta** de la mesa (se crea si no hay) y el total de la cuenta sube;
  3. se añade a la cuenta una **parte ya pagada** (`pagado`, tarjeta) por el importe del pedido, con su `Pago` y la referencia de Stripe.
- Así **no hay que tocar** la división de la cuenta ni el cierre de la mesa (ramas 10 y 12): como la cuenta ya tiene una parte pagada, al dividir solo se reparte lo que falta, y la mesa se puede cerrar cuando todas las partes están pagadas.
- **Cambio de tabla (acordado):** `partes_cuenta.pedido_id`, opcional, para saber que esa parte es un pedido pagado al pedir y mostrarlo así al camarero («Pedido #12 · pagado al pedir»).
- Si al volver de Stripe un plato se ha agotado o ha cambiado de precio, no se crea el pedido y se avisa al cliente para que enseñe la referencia del pago al camarero (lo devuelve desde el panel de Stripe).
- Recargar la página de éxito no crea el pedido dos veces (la referencia de Stripe es única).

**Tests:** sin pagar no se crea ningún pedido; al pagar, el pedido sale en cocina, la cuenta incluye la parte pagada y al dividir no se cobra otra vez; la mesa se cierra sin cobrar de nuevo; una sesión de Stripe no pagada, de otra mesa o de otro importe no crea nada; recargar no duplica; plato agotado mientras pagaba.

### 16. `feature/pagina-inicio` 🟠
- No estaba en el plan: sustituye la página `welcome` de Laravel en `/` por la página de inicio pública de MesaYa (ruta `inicio`), según la maqueta `landing/inicio/`.
- Solo cuenta lo que la aplicación hace de verdad: se quitan las cifras y funciones inventadas de la maqueta (TicketBAI, Bizum, stock, soporte 24/7, precios...).
- Botón «Acceso del personal» (login) o «Ir a mi pantalla» si ya hay sesión (`User::rutaDeInicio()`). «Ver una carta de ejemplo» solo si existe la mesa 1 de `la-tasca`.
- Ilustraciones con HTML y Tailwind en lugar de las fotos externas de la maqueta. Páginas simples de privacidad y cookies.
- **Test:** `/` responde sin login con MesaYa y el enlace al login; con sesión, enlace a la pantalla del rol; nada externo.

---

## 3. Decisiones pendientes

`CLAUDE.md` dice que no se añaden librerías sin acordarlo. Apuntad aquí lo que decidáis.

| Tema | Opciones | Decisión |
|---|---|---|
| PDF de los QR | `barryvdh/laravel-dompdf` (sirve también para el ticket PDF de los extras) **o** una página imprimible con "Guardar como PDF" del navegador | **Página imprimible** (16/11/2026), en `/mesas/imprimir`. Motivos: no añade ninguna librería, reutiliza Blade y Tailwind (con `print:`) y es más fácil de explicar. Inconveniente: no se descarga un `.pdf` con un clic; se elige "Guardar como PDF" en el diálogo de imprimir. Si se hace el ticket PDF de los extras, se vuelve a decidir. |
| Stripe | `stripe/stripe-php` | **`stripe/stripe-php` ^23** (23/11/2026), con Stripe Checkout en modo test y sin webhooks: el pago se confirma consultando la sesión al volver a la URL de éxito. Motivos: es el SDK oficial, evita escribir a mano las llamadas HTTP y la página de la tarjeta la pone Stripe (los datos de la tarjeta no pasan por nuestro servidor). Todo el uso está en `App\Servicios\PasarelaStripe`, que en los tests se simula. |
| Gráficos | Chart.js por npm (ya está en el stack) | **`chart.js` ^4** por npm (23/11/2026), empaquetado con Vite y sin CDN, en una entrada aparte (`resources/js/estadisticas.js`) que solo carga la pantalla de estadísticas. Se registran solo las piezas que se usan (barras y circular). Además, la zona horaria de la app pasa a `Europe/Madrid` (`APP_TIMEZONE`), para que las ventas «de hoy» y por día cuenten los días como en el restaurante. |
| Fuentes e iconos | Google Fonts **o** archivos servidos en local | **En local** (16/11/2026), con los paquetes npm `@fontsource-variable/inter` y `@material-symbols/font-400`. Motivos: la demo no depende del wifi y la carta pública no envía la IP del cliente a Google. Vite las empaqueta en `npm run build`. |

---

## 4. Cómo pedir cada rama a Claude Code

1. Crear la rama desde `main` actualizado:
   ```bash
   git checkout main
   git pull
   git checkout -b feature/<nombre>
   ```
2. *"Lee CLAUDE.md, docs/plan-de-trabajo.md (rama N) y la maqueta de X. Propón el plan: migraciones, modelos, rutas, vistas y test."*
3. Revisar el plan y dar el OK.
4. Implementación y `php artisan test`. Pedid que os explique lo que no entendáis.
5. Entrada en `docs/registre-ia.md` y actualización de este plan → commit → PR → revisión del compañero.
