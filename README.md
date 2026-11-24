# MesaYa

**SaaS de comandas para bares y restaurantes.** El cliente escanea el QR de su mesa, ve la carta y pide desde el móvil; la cocina recibe el pedido al momento y, al final, la cuenta se divide por persona y se paga con tarjeta o en efectivo.

Práctica de **DAW M0617 – Vibe Coding I**, hecha en pareja por **Iván Hernández García** y **Gabriel**. Presentación con demo en vivo la semana del 7/12/2026.

---

## Qué hace

| Quién | Cómo entra | Qué puede hacer |
|---|---|---|
| **Cliente** | Escaneando el QR de su mesa, sin registrarse | Ver la carta con alérgenos, pedir (pagando con tarjeta al momento), seguir el estado de su pedido en directo, dividir la cuenta (a partes iguales o por platos, con platos compartidos) y pagar su parte con tarjeta o en efectivo |
| **Camarero** | Login, desde el móvil | Ver las mesas, tomar comandas, recibir el aviso de plato listo, cobrar en efectivo o con TPV y cerrar la mesa cuando todo está pagado |
| **Cocina** | Login, en una tablet | Ver los pedidos por estado (pendiente → preparando → listo) con refresco automático |
| **Admin** | Login, en el ordenador | Panel con el resumen del día, carta (platos, fotos, precios, disponibilidad y los 14 alérgenos), mesas y sus QR para imprimir, personal y estadísticas de ventas. Puede entrar en todas las pantallas con «Ver como» |

Es **multi-restaurante**: varios locales usan la misma aplicación y **cada uno solo ve sus propios datos**.

### Lo que la hace segura y fiable

- **Aislamiento entre restaurantes** en todas las pantallas, comprobado con tests.
- **Dinero siempre en céntimos** (enteros): al dividir una cuenta, la suma de las partes es exactamente el total.
- **Pagos con Stripe en modo test**: los datos de la tarjeta nunca pasan por nuestro servidor y el pago se confirma preguntando a Stripe.
- **Pedidos del cliente pagados al hacerlos**: nadie puede llenar la cocina de pedidos de broma con una foto del QR.
- **Ninguna clave en el código**: todo va en el `.env`, que no se sube.

---

## Tecnologías

| Parte | Tecnología |
|---|---|
| Backend | Laravel 13 (PHP 8.3+) + Laravel Breeze |
| Frontend | Blade + Tailwind CSS 3 + Alpine.js |
| Base de datos | MySQL (también en local) |
| Pagos | Stripe Checkout en modo test |
| Tiempo real | Polling con JavaScript cada ~5 s |
| Códigos QR | `simplesoftwareio/simple-qrcode` |
| Gráficos | Chart.js |

---

## Puesta en marcha rápida

Necesitas PHP 8.3+ (con `gd`, `intl`, `pdo_mysql` y `fileinfo`), Composer, Node.js 20+ y MySQL 8. La guía completa, con la configuración de `php.ini` y los problemas frecuentes, está en [`docs/04-installacio.md`](docs/04-installacio.md).

```bash
git clone https://github.com/K1rb1-30/ProyectoIA_APPWeb.git
cd ProyectoIA_APPWeb
composer install
npm install
cp .env.example .env
php artisan key:generate
```

Crea las bases de datos `proyectoIA` y `proyectoIA_test` en MySQL, pon tu contraseña en `DB_PASSWORD` del `.env` (y las claves de prueba de Stripe en `STRIPE_KEY` y `STRIPE_SECRET` si quieres probar los pagos). Después:

```bash
php artisan migrate --seed
php artisan storage:link
composer run dev
```

La aplicación queda en http://localhost:8000.

### Cuentas de prueba

| Correo | Contraseña | Rol |
|---|---|---|
| `ivan@mesaya.com` · `gabriel@mesaya.com` | `1234` | admin de «La Tasca de Prueba» |
| `admin@la-tasca.test` | `password` | admin |
| `camarero@la-tasca.test` (y `camarero2@`, `camarero3@`) | `password` | camarero |
| `cocina@la-tasca.test` (y `cocina2@`) | `password` | cocina |
| Las mismas con `@bar-el-rincon.test` | `password` | del segundo restaurante |

El cliente no tiene cuenta: entra por el QR, por ejemplo en http://localhost:8000/r/la-tasca/mesa/1. Para pagar con tarjeta en modo test, usa `4242 4242 4242 4242` con cualquier fecha futura y cualquier CVC.

Todo son datos inventados y solo para local.

---

## Tests

```bash
php artisan test
```

Se ejecutan contra la base de datos `proyectoIA_test`, así que no tocan tus datos. Incluyen los seis *golden tests* del proyecto: comanda que llega a cocina, estados del pedido sin saltos, división de la cuenta exacta, pagos mixtos y cierre de mesa, platos agotados que no se pueden pedir y aislamiento entre restaurantes. Dónde está cada uno: [`docs/06-proves.md`](docs/06-proves.md).

---

## Documentación

| Documento | Contenido |
|---|---|
| [`docs/01-proposta.md`](docs/01-proposta.md) | Propuesta inicial: idea, roles, funcionalidades y planificación |
| [`docs/02-requisits.md`](docs/02-requisits.md) | Requisitos funcionales y no funcionales |
| [`docs/03-disseny.md`](docs/03-disseny.md) | Arquitectura, diagrama de la base de datos y rutas |
| [`docs/04-installacio.md`](docs/04-installacio.md) | Instalación en local y problemas frecuentes |
| [`docs/06-proves.md`](docs/06-proves.md) | Pruebas y golden tests |
| [`docs/plan-de-trabajo.md`](docs/plan-de-trabajo.md) | Ramas, reparto del trabajo y estado |
| [`docs/registre-ia.md`](docs/registre-ia.md) | Registro del uso de la IA en el proyecto |
| [`docs/design/`](docs/design/) | Diseño visual: maquetas y sistema de diseño |
| [`CHANGELOG.md`](CHANGELOG.md) | Cambios por versión |

---

## Cómo trabajamos

- Cada funcionalidad va en su propia rama `feature/<nombre>` y entra en `main` solo con una **pull request revisada por el compañero**.
- Todo está en **castellano**: base de datos, código, commits, documentación y la web.
- Usamos IA (Claude Code) para generar y explicar código y documentación. Cada uso queda apuntado en [`docs/registre-ia.md`](docs/registre-ia.md), y no entra en `main` ningún código que alguno de los dos no sepa explicar.
