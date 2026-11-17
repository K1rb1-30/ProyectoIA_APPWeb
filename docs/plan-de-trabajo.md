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
| P1 | Corregir en `docs/01-proposta.md` "Laravel 12" → 13 y "SQLite en local" → MySQL siempre | | ⬜ |
| P2 | Escribir `docs/02-requisits.md` (requisitos numerados RF1, RF2…) | | ⬜ |
| P3 | Escribir `docs/03-disseny.md` (diagrama de tablas y lista de rutas) | | ⬜ |
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
| 5 | `feature/comandas-camarero` | 3, 4 | | ⬜ | |
| 6 | `feature/pantalla-cocina` | 5 | | ⬜ | |
| 7 | `feature/avisos-camarero` | 6 | | ⬜ | |

Al terminar la 7: entrada **v0.1** en `CHANGELOG.md` y etiqueta `v0.1`.

### 🔵 v1.0 — objetivo: 30/11

| # | Rama | Depende de | Responsable | Estado | PR |
|---|---|---|---|---|---|
| 8 | `feature/carta-publica-qr` | 3, 4 | Iván | 🟡 | |
| 9 | `feature/autopedido` | 5, 8 | | ⬜ | |
| 10 | `feature/dividir-cuenta` | 5 | | ⬜ | |
| 11 | `feature/pago-stripe` | 10 | | ⬜ | |
| 12 | `feature/efectivo-y-cierre` | 10 | | ⬜ | |
| 13 | `feature/estadisticas` | 11, 12 | | ⬜ | |

### Semana 4 — hasta el 07/12

| Tarea | Responsable | Estado |
|---|---|---|
| `docs/04-installacio.md` | | ⬜ |
| `docs/05-manual-usuari.md` con capturas | | ⬜ |
| `docs/06-proves.md` (los 6 golden tests y cómo ejecutarlos) | | ⬜ |
| Revisión de seguridad (secretos, aislamiento entre restaurantes, roles) | | ⬜ |
| `CHANGELOG.md` v1.0 y etiqueta `v1.0` | | ⬜ |
| Ensayo de la demo (flujo de la mesa 5) | | ⬜ |

### Reparto propuesto

Es el de `docs/01-proposta.md`. Cambiadlo si queréis, pero apuntadlo aquí.

- **Persona A:** 2 → 3 → 4 → 8 → 9 → 10, manual de usuario y capturas.
- **Persona B:** 1 → 5 → 6 → 7 → 11 → 12, despliegue, pruebas y CHANGELOG.
- **La 13 (estadísticas)** la coge quien acabe antes.

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

### 13. `feature/estadisticas`
- Ventas por día, platos más pedidos y tarjeta frente a efectivo, con Chart.js.
- Vista según `panelAdmin/estadisticas/`.

---

## 3. Decisiones pendientes

`CLAUDE.md` dice que no se añaden librerías sin acordarlo. Apuntad aquí lo que decidáis.

| Tema | Opciones | Decisión |
|---|---|---|
| PDF de los QR | `barryvdh/laravel-dompdf` (sirve también para el ticket PDF de los extras) **o** una página imprimible con "Guardar como PDF" del navegador | **Página imprimible** (16/11/2026), en `/mesas/imprimir`. Motivos: no añade ninguna librería, reutiliza Blade y Tailwind (con `print:`) y es más fácil de explicar. Inconveniente: no se descarga un `.pdf` con un clic; se elige "Guardar como PDF" en el diálogo de imprimir. Si se hace el ticket PDF de los extras, se vuelve a decidir. |
| Stripe | `stripe/stripe-php` | |
| Gráficos | Chart.js por npm (ya está en el stack) | |
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
