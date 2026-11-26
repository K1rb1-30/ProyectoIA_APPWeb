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
| P5 | Renombrar las rutas de Breeze `/dashboard` → `/panel` y `/profile` → `/perfil` | Gabriel | 🟡 `/panel` hecho en la rama 17 (`/dashboard` redirige); falta `/profile` → `/perfil` |
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
| 15 | `feature/pagar-al-pedir` | 9, 11, 12 y la 14 (orden) | Iván | 🔍 | [#28](https://github.com/K1rb1-30/ProyectoIA_APPWeb/pull/28) |
| 16 | `feature/pagina-inicio` | 2 | Gabriel | ✅ | [#26](https://github.com/K1rb1-30/ProyectoIA_APPWeb/pull/26) |
| 18 | `feature/gestion-personal` | 1, 2 | Gabriel | 🔍 | [#30](https://github.com/K1rb1-30/ProyectoIA_APPWeb/pull/30) |
| 17 | `feature/panel-admin` | 3, 4, 13 | Gabriel | 🔍 | [#29](https://github.com/K1rb1-30/ProyectoIA_APPWeb/pull/29) |
| 19 | `feature/rediseno-base` | 2 | Gabriel | ✅ | [#31](https://github.com/K1rb1-30/ProyectoIA_APPWeb/pull/31) |
| 20 | `feature/rediseno-cocina-camarero` | 19 | Gabriel | ✅ | [#33](https://github.com/K1rb1-30/ProyectoIA_APPWeb/pull/33) |
| 21 | `feature/rediseno-publico` | 19 | Gabriel | ✅ | [#34](https://github.com/K1rb1-30/ProyectoIA_APPWeb/pull/34) |
| 22 | `feature/rediseno-admin` | 19 | Gabriel | ✅ | [#35](https://github.com/K1rb1-30/ProyectoIA_APPWeb/pull/35) |
| 23 | `feature/rediseno-cliente` | 19 | Gabriel | 🔍 | [#36](https://github.com/K1rb1-30/ProyectoIA_APPWeb/pull/36) |
| 24 | `feature/suscripcion-restaurantes` | 11, 18 | Gabriel | 🔍 | [#46](https://github.com/K1rb1-30/ProyectoIA_APPWeb/pull/46) |
| 24b | `feature/webhook-suscripcion` | 24 | Gabriel | 🔍 | [#47](https://github.com/K1rb1-30/ProyectoIA_APPWeb/pull/47) |
| 24c | `feature/reactivar-suscripcion-impagada` | 24, 24b | Gabriel | 🔍 | [#48](https://github.com/K1rb1-30/ProyectoIA_APPWeb/pull/48) |

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

### 18. `feature/gestion-personal` 🟠
- No estaba en el plan: el admin gestiona las cuentas de sus empleados en `/admin/personal` (opción «Personal» del menú, que antes no llevaba a ningún sitio). Sin maqueta: sigue el estilo de `panelAdmin/carta/`.
- `Admin\PersonalController` y Form Request `Admin\EmpleadoRequest`: nombre, correo (único en toda la tabla `users`, porque es el login), rol (`admin`, `camarero` o `cocina`) y contraseña con confirmación. Al editar, la contraseña vacía no se cambia; si se cambia, se cierran las sesiones abiertas de esa persona.
- `restaurante_id` lo pone el controlador con el del admin, nunca el formulario. `email_verified_at` se rellena al crear.
- **Aislamiento:** `User` no usa el trait (el login lo necesita sin filtro). El listado filtra con `where('restaurante_id', ...)` y `{empleado}` se busca con `Route::bind` en `routes/web.php`, que también filtra: el empleado de otro restaurante da 404.
- **Seguridad:** el admin no puede borrarse ni quitarse su rol, y el restaurante nunca se queda sin admin.
- **Pedidos al borrar (decidido el 23/11/2026, opción A):** se deja la clave foránea como está (`pedidos.user_id` con `nullOnDelete`). Los pedidos y las ventas se conservan; solo se pierde quién tomó la comanda. El aviso de borrar lo dice.
- `RestaurantesSeeder`: 6 empleados por restaurante (un admin, tres camareros y dos de cocina).
- **Tests:** crear un camarero que luego inicia sesión, editar sin contraseña, cambiarla (hash y sesiones), aislamiento A/B (404), no borrarse ni quitarse el rol, restaurante sin admin, roles (403), correo repetido, `restaurante_id` del formulario ignorado, borrar con pedidos y seeder.

### 17. `feature/panel-admin` 🟠
- No estaba en el plan: sustituye la vista `dashboard` de Breeze («You're logged in!») por el panel principal del admin, según la maqueta `panelAdmin/panel/`, con su controlador `Admin\PanelController`.
- **Tarea previa P5 (parte):** la ruta pasa de `/dashboard` (nombre `dashboard`) a `/panel` (nombre `panel`). `/dashboard` redirige a `/panel`. `/profile` → `/perfil` queda para otra rama.
- Cuatro tarjetas: ventas de hoy (con «vs. ayer»), pedidos de hoy y ticket medio (de `EstadisticasRestaurante::resumenDeHoy()`), mesas ocupadas y pedidos en curso por estado.
- Carta: número de platos y de agotados y 5 platos (los agotados primero) con precio, alérgenos e interruptor de disponible (ruta `admin.carta.disponibilidad`, que vuelve al panel).
- Mesas: libres, ocupadas y cuadrícula (hasta 24), con enlaces a Mesas y QR y a imprimir los QR.
- Solo datos reales: se quita de la maqueta lo que la app no hace (PRO, buscador, notificaciones, configuración, soporte, personal en turno, reglamento de alérgenos, contadores del menú...). El «tiempo medio de cocina» no se puede calcular: no se guarda cuándo pasa un pedido a `listo`.
- Las cifras se piden ya contadas (`COUNT`, `SUM`, `GROUP BY`): unas 10 consultas, que no crecen con los datos.
- **Tests:** cifras de hoy con datos conocidos, aislamiento A/B, camarero y cocina reciben 403, restaurante vacío, el interruptor vuelve al panel, `/dashboard` redirige y el número de consultas no crece con los datos.

### 16. `feature/pagina-inicio` 🟠
- No estaba en el plan: sustituye la página `welcome` de Laravel en `/` por la página de inicio pública de MesaYa (ruta `inicio`), según la maqueta `landing/inicio/`.
- Solo cuenta lo que la aplicación hace de verdad: se quitan las cifras y funciones inventadas de la maqueta (TicketBAI, Bizum, stock, soporte 24/7, precios...).
- Botón «Acceso del personal» (login) o «Ir a mi pantalla» si ya hay sesión (`User::rutaDeInicio()`). «Ver una carta de ejemplo» solo si existe la mesa 1 de `la-tasca`.
- Ilustraciones con HTML y Tailwind en lugar de las fotos externas de la maqueta. Páginas simples de privacidad y cookies.
- **Test:** `/` responde sin login con MesaYa y el enlace al login; con sesión, enlace a la pantalla del rol; nada externo.

### 19. `feature/rediseno-base` 🟠
- No estaba en el plan: primera de las cinco ramas del rediseño visual «Comanda impresa» (que la web no parezca hecha con IA y tenga mejor jerarquía). Las siguientes: `rediseno-cocina-camarero`, `rediseno-publico`, `rediseno-admin` (después de #29 y #30) y `rediseno-cliente` (después de #28).
- Cambia los **valores** de los colores de `tailwind.config.js` sin cambiar sus nombres, así todas las vistas cogen el aspecto nuevo sin editarlas. Tamaños de letra más contrastados, radios más pequeños y sombras casi invisibles.
- Fuentes en local: Schibsted Grotesk (texto) e IBM Plex Mono (cifras). Sustituyen a Inter.
- Componentes: botones, `chip-estado` (punto + palabra), `badge-alergeno`, `tarjeta`, `logo` y `campo-texto`. `DESIGN.md` reescrito.
- **Test:** los que ya había siguen pasando (no cambia ningún texto ni la lógica).

### 20. `feature/rediseno-cocina-camarero` 🟠
- Segunda rama del rediseño. Sale de la 19 (necesita sus colores y componentes): hasta que se mergee #31, su PR también enseña esos cambios.
- Cocina: barra de tinta, columnas con el número de pedidos en grande, pedidos como tickets blancos en letra de ticket, tiempo en rojo a partir de 10 min y nota resaltada en naranja claro.
- Camarero: título grande, pestaña activa con raya naranja; mesas ocupadas en tinta y libres en blanco; nueva comanda con la mesa como título y platos en filas; detalle de mesa con «Falta por cobrar» como dato principal; avisos y cobros en filas.
- Solo cambia el HTML y las clases: la lógica de Alpine, los formularios y las rutas son los mismos.
- **Test:** los que ya había siguen pasando (se mantienen todos los textos que comprueban).


### 21. `feature/rediseno-publico` 🟠
- Tercera rama del rediseño: páginas sin sesión.
- Portada: título «La mesa pide. La cocina se entera. La cuenta cuadra.» alineado a la izquierda y, al lado, una comanda impresa de ejemplo con la cuenta dividida entre 3 (misma regla del céntimo que la app). «Cómo funciona» en lista numerada con líneas; roles en columnas sin iconos; panel en franja oscura. Se mantiene «Pide desde tu mesa» (lo comprueba `InicioTest`).
- Layout público con el nombre en texto («MesaYa.») y pie en una línea. `boton-acceso` sin iconos.
- Login y pantallas de Breeze: panel de tinta sin degradados ni tarjetas, título «Acceso del personal», campos sin iconos y botón «Entrar».
- Páginas legales: solo cambia su marco (`pagina-legal`); el texto es el mismo.
- **Test:** los que ya había siguen pasando.

### 22. `feature/rediseno-admin` 🟠
- Cuarta rama del rediseño: panel de administración y perfil.
- Layout de admin: menú lateral solo con texto (la opción activa en tinta), título de la página en grande encima del contenido y nuevo slot opcional `subtitulo`.
- Panel y estadísticas: una franja con las ventas de hoy en grande y el resto de cifras al lado (antes, cuatro tarjetas iguales); secciones con una raya gruesa bajo el título en vez de tarjetas. Colores y letra de los gráficos de Chart.js adaptados.
- Carta (pestañas de categoría y tabla sin caja), categorías, formulario de plato, mesas y QR (cuadrícula con líneas finas y el número en grande), alérgenos (lista en dos columnas) y personal (tabla sin cápsulas de rol). Formularios de mesa y empleado y perfil sin iconos en campos ni botones.
- Se mantienen todos los textos que comprueban los tests (por ejemplo «Ticket medio: 12,50 €», que en el panel queda en un texto solo para lectores de pantalla).
- **Test:** los que ya había siguen pasando.

### 23. `feature/rediseno-cliente` 🟠
- Quinta y última rama del rediseño: pantallas del cliente que entra por el QR.
- Layout del cliente: la mesa en pequeño y en letra de ticket, el restaurante en grande (solo en la carta; en el resto de pantallas manda su propio título) y la pestaña activa con raya naranja.
- Carta como una carta impresa: título grande por categoría y cada plato en una fila con línea de puntos hasta el precio; alérgenos como texto («Contiene: …»).
- Mi pedido, estado del pedido, cuenta y pagos: filas con líneas en vez de tarjetas; el total o el importe de tu parte es el dato principal, en grande; pedidos y justificante con aspecto de ticket; sin iconos decorativos.
- Solo cambian el HTML y las clases: la lógica de Alpine (carrito, polling, vista previa del reparto) y los formularios son los mismos.
- **Test:** los que ya había siguen pasando.

### 24. `feature/suscripcion-restaurantes` 🟠
- No estaba en el plan: que MesaYa funcione como un SaaS de verdad. Un restaurante nuevo se da de alta solo en `/registro` (enlazado desde la portada) y paga una **suscripción mensual** con Stripe Checkout en modo test. Solo puede usar la aplicación mientras la suscripción esté activa.

**Decisiones (25/11/2026)**
1. **Columnas en `restaurantes`**, no una tabla nueva: `stripe_cliente_id`, `stripe_suscripcion_id`, `suscripcion_estado` y `suscripcion_fin_en`. Un restaurante tiene una sola suscripción a la vez y el middleware la comprueba sin joins.
2. **Un solo plan mensual de 29,00 €**, sin días de prueba. El importe va en `STRIPE_SUSCRIPCION_PRECIO_CENTIMOS` (céntimos), no en el código.
3. **Estados:** `pendiente` (dado de alta, sin pagar) / `activa` / `impagada` / `cancelada`. Solo `activa` deja usar la app.
4. **QR:** se bloquea todo `/r/{slug}/…` menos las dos vueltas de Stripe (`cliente.pago.exito` y `cliente.pedido.pagado`), para que un pago ya hecho se registre siempre.
5. **Webhook en una segunda PR** (`/stripe/webhook`, para impagos y cancelaciones desde el portal): rama `feature/webhook-suscripcion` (24b), ver abajo.

**Cómo se hace**
- `RegistroRestauranteController`: en una transacción crea el `Restaurante` (slug único: `bar-pepe`, `bar-pepe-2`…) en `pendiente` y su admin; inicia sesión y manda a Stripe. Si Stripe falla, el restaurante queda `pendiente` y se reintenta desde «Tu suscripción».
- `PasarelaStripe` (sigue siendo el único sitio que habla con Stripe): `crearSesionSuscripcion()` (`mode: subscription`, `recurring` mensual, `metadata.restaurante_id`), `consultarSesionSuscripcion()` y `crearSesionPortal()`.
- Vuelta de Stripe (como en la rama 11): no nos fiamos de la URL. Se consulta la sesión y solo se activa si está pagada **y** su `restaurante_id` es el del admin logueado.
- Middleware `ComprobarSuscripcion` (alias `suscripcion`) en admin, camarero, cocina y QR. El admin va a «Tu suscripción»; los demás ven «Restaurante no disponible» (403). Los desarrolladores no se bloquean nunca.
- Pantalla «Suscripción» en el menú del admin: estado, próxima fecha de cobro, importe y botón al portal de cliente de Stripe (cambiar tarjeta, facturas, cancelar).
- Seeders y factory: los restaurantes de demo y los de los tests, en `activa`.
- **Tests:** `SuscripcionTest` (13): el registro crea restaurante y admin pendientes y va a Stripe, slug único, validación, Stripe caído, solo se activa si Stripe confirma, un restaurante no activa la de otro, bloqueo de admin, QR, cocina y camarero, impagada y cancelada, desarrolladores, portal y seeders.

### 24b. `feature/webhook-suscripcion` 🟠
- Segunda PR de la rama 24. Sale de `feature/suscripcion-restaurantes`: hasta que se mergee #46, su PR también enseña esos cambios.
- `POST /stripe/webhook` (`StripeWebhookController`), sin CSRF (excepción en `bootstrap/app.php`). Lo protege la **firma** del aviso: `PasarelaStripe::leerEventoWebhook()` la comprueba con `STRIPE_WEBHOOK_SECRET` (`Webhook::constructEvent`, en local, sin llamar a Stripe). Firma mala, falta el secreto o aviso de modo real → 400 y no se toca nada.
- Avisos: `customer.subscription.updated` (estado traducido con `Restaurante::estadoDesdeStripe()` y próxima fecha de cobro), `customer.subscription.deleted` → `cancelada`, `invoice.payment_failed` → `impagada`. Los demás responden 200 y no hacen nada (si no, Stripe los reintenta).
- El restaurante se busca por `stripe_suscripcion_id`: un aviso de una suscripción desconocida (los de `stripe trigger`, o la antigua de un restaurante que ha reactivado) no toca nada.
- **Tests:** `StripeWebhookTest` (10), con avisos firmados de verdad con un secreto de prueba: firma mala o sin firma, sin secreto, modo real, impago, cancelación, renovación con fecha nueva, traducción de estados, suscripción desconocida, avisos que no usamos y bloqueo tras un impago.


### 24c. `feature/reactivar-suscripcion-impagada` 🟠
- Arregla un cobro doble. En `impagada` la suscripción antigua sigue viva en Stripe (`past_due`) y Stripe reintenta el cobro. Hasta ahora, «Reactivar la suscripción» abría un Checkout nuevo que creaba una **segunda** suscripción, y `exito()` guardaba su id, así que los avisos de la antigua se ignoraban.
- Ahora, en `impagada`, el botón «Actualizar la tarjeta y pagar» lleva al **portal de cliente** (`admin.suscripcion.portal`), donde se cambia la tarjeta y se paga la factura pendiente. El webhook (`customer.subscription.updated` → `active`) la devuelve a `activa` con la misma suscripción.
- `SuscripcionController::pagar()` se niega a abrir Checkout con `impagada`. En `pendiente` y `cancelada` todo sigue igual (Checkout).
- **Tests:** en `SuscripcionTest` (4 nuevos): el botón de `impagada` va al portal, `pagar()` no llama a `crearSesionSuscripcion` ni cambia `stripe_suscripcion_id`, el portal funciona en `impagada`, y `cancelada` se sigue reactivando con Checkout.
---

## 3. Decisiones pendientes

`CLAUDE.md` dice que no se añaden librerías sin acordarlo. Apuntad aquí lo que decidáis.

| Tema | Opciones | Decisión |
|---|---|---|
| PDF de los QR | `barryvdh/laravel-dompdf` (sirve también para el ticket PDF de los extras) **o** una página imprimible con "Guardar como PDF" del navegador | **Página imprimible** (16/11/2026), en `/mesas/imprimir`. Motivos: no añade ninguna librería, reutiliza Blade y Tailwind (con `print:`) y es más fácil de explicar. Inconveniente: no se descarga un `.pdf` con un clic; se elige "Guardar como PDF" en el diálogo de imprimir. Si se hace el ticket PDF de los extras, se vuelve a decidir. |
| Stripe | `stripe/stripe-php` | **`stripe/stripe-php` ^23** (23/11/2026), con Stripe Checkout en modo test y sin webhooks: el pago se confirma consultando la sesión al volver a la URL de éxito. Motivos: es el SDK oficial, evita escribir a mano las llamadas HTTP y la página de la tarjeta la pone Stripe (los datos de la tarjeta no pasan por nuestro servidor). Todo el uso está en `App\Servicios\PasarelaStripe`, que en los tests se simula. **Rama 24:** también la suscripción mensual de los restaurantes (`mode: subscription`) y el portal de cliente, con el mismo paquete y sin Cashier. Para lo que pasa después del alta (impagos, cancelaciones), **un webhook** con firma (rama 24b). |
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
