# Requisitos de MesaYa

Requisitos funcionales (RF) y no funcionales (RNF) de la aplicación. Salen de la propuesta (`01-proposta.md`) y de las reglas de `CLAUDE.md`.

**Estado** (23/11/2026): ✅ hecho y en `main` · ⏳ pendiente (se indica la rama del plan que lo hará).

**Prioridad:** 🟢 MVP (v0.1) · 🔵 entrega final (v1.0) · 🟣 extra (solo si sobra tiempo).

---

## 1. Requisitos funcionales

### Acceso, roles y restaurantes

| ID | Requisito | Rol | Prioridad | Estado |
|---|---|---|---|---|
| RF1 | El personal entra con correo y contraseña. No hay registro público: las cuentas las crea el restaurante. | Todos | 🟢 | ✅ PR #4 |
| RF2 | Hay tres roles con login: `admin`, `camarero` y `cocina`. Cada uno solo entra en sus pantallas; el admin puede entrar en todas. | Todos | 🟢 | ✅ PR #8, #11 |
| RF3 | Después del login, cada rol va a su pantalla: admin → panel, camarero → mesas, cocina → pedidos. | Todos | 🟢 | ✅ PR #8 |
| RF4 | Multi-restaurante: cada usuario pertenece a un restaurante y **solo ve y modifica los datos de su restaurante**. | Todos | 🟢 | ✅ PR #8 |
| RF5 | El admin puede cambiar de vista («Ver como») a camarero, cocina o cliente sin cerrar sesión. | Admin | — | ✅ PR #11, #13 |

### Carta y alérgenos (admin)

| ID | Requisito | Rol | Prioridad | Estado |
|---|---|---|---|---|
| RF6 | Crear, editar y borrar categorías de la carta, con un orden. No se puede borrar una categoría con platos. | Admin | 🟢 | ✅ PR #10 |
| RF7 | Crear, editar y borrar platos con nombre, descripción, categoría, precio y foto. | Admin | 🟢 | ✅ PR #10 |
| RF8 | Marcar un plato como disponible o agotado con un interruptor. | Admin | 🟢 | ✅ PR #10 |
| RF9 | Indicar en cada plato cuáles de los **14 alérgenos legales** (Reglamento UE 1169/2011) contiene, y consultar la lista. | Admin | 🟢 | ✅ PR #10 |

### Mesas y QR (admin)

| ID | Requisito | Rol | Prioridad | Estado |
|---|---|---|---|---|
| RF10 | Crear, editar y borrar mesas. El número no se repite dentro de un restaurante. | Admin | 🟢 | ✅ PR #12 |
| RF11 | Cada mesa tiene un QR fijo que apunta a `/r/{slug}/mesa/{numero}`. Se pueden imprimir o guardar en PDF desde una página imprimible. | Admin | 🟢 | ✅ PR #12 |

### Comandas, cocina y avisos (personal)

| ID | Requisito | Rol | Prioridad | Estado |
|---|---|---|---|---|
| RF12 | El camarero ve las mesas con su estado (libre / ocupada). | Camarero | 🟢 | ✅ PR #14 |
| RF13 | El camarero toma una comanda: elige mesa, platos disponibles, cantidades y notas. La mesa pasa a ocupada. | Camarero | 🟢 | ✅ PR #14 |
| RF14 | La cocina ve los pedidos en columnas por estado y se refrescan solos cada ~5 s. | Cocina | 🟢 | ✅ PR #15 |
| RF15 | La cocina cambia el estado del pedido: `pendiente` → `preparando` → `listo`. **No se puede saltar de `pendiente` a `listo`.** | Cocina | 🟢 | ✅ PR #15 |
| RF16 | El camarero recibe un aviso cuando un pedido está listo y lo marca como `servido`. | Camarero | 🟢 | ✅ PR #17 |

### Cliente por QR (sin login)

| ID | Requisito | Rol | Prioridad | Estado |
|---|---|---|---|---|
| RF17 | Al escanear el QR, el cliente ve la carta de ese restaurante con alérgenos y precios. **Los platos agotados no aparecen.** | Cliente | 🔵 | ✅ PR #13 |
| RF18 | El cliente añade platos a un carrito, escribe notas y envía el pedido a cocina. No se puede pedir un plato agotado. | Cliente | 🔵 | ✅ PR #16 |
| RF19 | El cliente sigue el estado de sus pedidos en directo (polling cada 5 s). Solo ve los pedidos que ha hecho desde su móvil. | Cliente | 🔵 | ✅ PR #16 |
| RF20 | El cliente pide la cuenta y la divide **a partes iguales** (el céntimo que sobra va a la primera parte) o **por platos**. La suma de las partes es siempre exactamente el total. | Cliente | 🔵 | ✅ PR #19 |
| RF21 | Cada persona paga su parte con tarjeta (Stripe en **modo test**). | Cliente | 🔵 | 🔍 rama 11 |
| RF22 | Cada persona puede elegir pagar en efectivo; la parte queda `pendiente_efectivo` hasta que el camarero confirma el cobro. | Cliente, camarero | 🔵 | ⏳ rama 12 |
| RF23 | La mesa solo se cierra (vuelve a `libre`) cuando todas las partes de la cuenta están pagadas. | Camarero | 🔵 | ⏳ rama 12 |

### Estadísticas (admin)

| ID | Requisito | Rol | Prioridad | Estado |
|---|---|---|---|---|
| RF24 | El admin ve las ventas por día, los platos más pedidos y la proporción tarjeta / efectivo (Chart.js). | Admin | 🔵 | ⏳ rama 13 |

### Extras (🟣, solo si sobra tiempo)

| ID | Requisito | Estado |
|---|---|---|
| RF25 | Reserva de mesa (día, hora y número de personas). | No empezado |
| RF26 | Reserva con el plato ya elegido. | No empezado |
| RF27 | Plano visual de las mesas. | No empezado |
| RF28 | Ticket descargable en PDF. | No empezado |
| RF29 | Modo oscuro en la pantalla de cocina. | No empezado |

---

## 2. Requisitos no funcionales

| ID | Requisito | Cómo se cumple |
|---|---|---|
| RNF1 | **Aislamiento entre restaurantes:** el restaurante A nunca ve ni toca datos del B. | Trait `PerteneceARestaurante` (global scope) en los modelos de negocio; en las pantallas públicas se filtra a mano por el restaurante del slug. Golden test nº 6. |
| RNF2 | **Dinero en céntimos** (enteros), nunca en decimales. | Columnas `_centimos`; en la web se muestra como `12,50 €` con el componente `<x-precio>`. |
| RNF3 | **Precio congelado:** un cambio de carta no altera los pedidos ya hechos. | `lineas_pedido.precio_centimos` copia el precio en el momento del pedido. |
| RNF4 | **Seguridad de secretos:** ninguna clave ni contraseña en el código. | Todo en `.env` (no se sube a Git); `.env.example` solo con valores de ejemplo. Stripe siempre en modo test. |
| RNF5 | **Pantallas públicas protegidas contra abusos.** | Límite de envíos por minuto (`throttle`) en el pedido y en la cuenta del cliente. |
| RNF6 | **Todo en castellano:** textos de la web, base de datos, código, commits y documentación. | Traducciones en `lang/es/`; identificadores sin tildes ni ñ. |
| RNF7 | **Móvil primero** para cliente y camarero; tablet horizontal para cocina; escritorio para admin. Botones de al menos 48 px de alto. | Layouts y componentes según `docs/design/DesignFrontend/DESIGN.md`. |
| RNF8 | **Tiempo real sencillo:** sin WebSockets. | Polling con JavaScript cada ~5 s. |
| RNF9 | **La demo no depende de internet** para las fuentes e iconos. | Inter y Material Symbols se sirven en local (paquetes npm). |
| RNF10 | **MySQL siempre**, en local y en producción, con cotejamiento `utf8mb4_unicode_ci`. | Ver `04-installacio.md`. |
| RNF11 | **Código explicable:** no entra en `main` nada que alguno de los dos no sepa explicar. | Pull requests revisadas por el compañero y comentarios en castellano. |
