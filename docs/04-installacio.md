# Instalación de MesaYa en local

Guía para poner en marcha el proyecto en un ordenador (probada en Windows 11). Si algo falla, mira el apartado 7, «Problemas frecuentes».

---

## 1. Requisitos

| Programa | Versión | Para qué |
|---|---|---|
| PHP | 8.3 o superior (probado con 8.5) | Laravel |
| Composer | 2.x | Librerías de PHP |
| Node.js y npm | Node 20 o superior | Vite, Tailwind y Alpine |
| MySQL | 8.0 | Base de datos (**nunca SQLite**) |
| Git | cualquiera reciente | Control de versiones |

### Extensiones de PHP

Abre el `php.ini` (en Windows suele ser `C:\php\php.ini`; compruébalo con `php --ini`) y deja así estas líneas, **sin el `;` delante**:

```ini
extension=gd          ; la necesita la librería de los códigos QR
extension=intl        ; formato de números
extension=mbstring
extension=openssl
extension=pdo_mysql   ; conexión con MySQL
extension=fileinfo    ; subir fotos de los platos
extension=zip
opcache.enable=0      ; desactivado en local (ver «Problemas frecuentes»)
```

Comprueba que están activadas (tiene que salir cada nombre):

```bash
php -m
```

Después de cambiar el `php.ini`, abre una terminal nueva.

---

## 2. Descargar el proyecto

```bash
git clone https://github.com/K1rb1-30/ProyectoIA_APPWeb.git
cd ProyectoIA_APPWeb
composer install
npm install
```

---

## 3. Bases de datos

Crea en MySQL las dos bases de datos: una para trabajar y otra para los tests.

```sql
CREATE DATABASE proyectoIA CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE DATABASE proyectoIA_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

`utf8mb4_unicode_ci` hace que funcionen bien las tildes, la ñ y los emojis.

---

## 4. Archivo `.env`

```bash
cp .env.example .env
php artisan key:generate
```

Abre `.env` y pon la contraseña de **tu** MySQL en `DB_PASSWORD`. El resto ya viene preparado:

```env
APP_TIMEZONE=Europe/Madrid
APP_LOCALE=es
APP_FAKER_LOCALE=es_ES
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=proyectoIA
DB_USERNAME=root
DB_PASSWORD=
```

> ⚠️ El `.env` **no se sube nunca a Git** (está en `.gitignore`): tiene tus contraseñas. Si hace falta una variable nueva, se añade a `.env.example` con un valor de ejemplo.
>
> Si ya tenías el `.env` de antes, `cp` no lo actualiza: **copia a mano las variables nuevas** de `.env.example`. Las últimas son `STRIPE_SUSCRIPCION_PRECIO_CENTIMOS` y `STRIPE_WEBHOOK_SECRET` (rama 24).

Los tests usan la base de datos `proyectoIA_test`, que ya está configurada en `phpunit.xml`.

### Claves de Stripe (modo test)

El pago con tarjeta usa Stripe Checkout **siempre en modo de prueba**: no se cobra nada de verdad.

1. Crea una cuenta gratuita en [stripe.com](https://stripe.com). No hace falta activar los pagos reales.
2. En el panel, activa **«Modo de prueba»** y ve a *Desarrolladores → Claves de API*.
3. Copia las dos claves en tu `.env`:

```env
STRIPE_KEY=pk_test_...
STRIPE_SECRET=sk_test_...
```

4. Ejecuta `php artisan config:clear`.

> ⚠️ **Nunca claves `pk_live_` ni `sk_live_`.** La aplicación se niega a usar una clave secreta que no empiece por `sk_test_`, y rechaza los avisos del webhook que no sean de modo de prueba.

Para pagar, usa la tarjeta de prueba `4242 4242 4242 4242`, cualquier fecha futura y cualquier CVC. Los tests no necesitan estas claves: simulan Stripe.

Con esas dos claves ya funcionan los pagos de las mesas y el **alta de restaurantes con suscripción** (`/registro`). El pago de las mesas y el alta se confirman preguntando a Stripe al volver de la página de pago, así que funcionan en `localhost` sin nada más.

### Suscripción de los restaurantes (rama 24)

**Precio.** El importe mensual va en céntimos en el `.env`. Si no lo pones, se usan 29,00 €:

```env
STRIPE_SUSCRIPCION_PRECIO_CENTIMOS=2900
```

**Portal de cliente.** El botón «Gestionar en Stripe» de la pantalla «Suscripción» abre el portal de cliente de Stripe. Ahí el admin cambia la tarjeta, descarga las facturas o cancela la suscripción. El portal **hay que activarlo una vez** en el panel de Stripe (en modo de prueba): *Configuración → Facturación → Portal de clientes* (en inglés, *Settings → Billing → Customer portal*) → **Activar**. Si no está activado, el botón muestra «Ahora mismo no se puede abrir el portal de Stripe».

### Webhook de Stripe (rama 24b)

Lo que pasa **después** del alta (un cobro mensual que falla, una cancelación desde el portal, una renovación) lo avisa Stripe con un POST a `/stripe/webhook`. Cada aviso va **firmado**: la aplicación comprueba la firma con `STRIPE_WEBHOOK_SECRET` y rechaza (400) todo lo que no cuadre.

Sin webhook, la aplicación funciona igual, pero no se entera de los impagos ni de las cancelaciones: el restaurante sigue «Activo».

Stripe no puede llegar a tu `localhost`. En local, **Stripe CLI** recibe los avisos y te los reenvía:

1. Instala Stripe CLI: [docs.stripe.com/stripe-cli](https://docs.stripe.com/stripe-cli). En Windows, descarga el `.zip`, descomprime `stripe.exe` y ponlo en una carpeta del `PATH`.
2. Inicia sesión con tu cuenta de Stripe (se abre el navegador):
   ```bash
   stripe login
   ```
3. Con `composer run dev` arrancado, en **otra terminal**:
   ```bash
   stripe listen --forward-to localhost:8000/stripe/webhook
   ```
   Saca un secreto `whsec_...`. Cópialo en tu `.env`:
   ```env
   STRIPE_WEBHOOK_SECRET=whsec_...
   ```
   y ejecuta `php artisan config:clear`. Deja esa terminal abierta mientras pruebas: si la cierras, los avisos dejan de llegar. El secreto se mantiene aunque la vuelvas a abrir.
4. Prueba que llegan los avisos:
   - Da de alta un restaurante en `/registro` y paga con `4242 4242 4242 4242`.
   - En **Suscripción → Gestionar en Stripe**, cancela. Stripe cancela al **final del mes pagado**: llega un `customer.subscription.updated` y el restaurante sigue «Activo» hasta esa fecha. Para verlo cancelado al momento, cancela la suscripción desde el panel de Stripe (*Facturación → Suscripciones*) y elige cancelar **inmediatamente**: llega `customer.subscription.deleted` y el restaurante pasa a «Cancelada».
   - En la terminal de `stripe listen` sale cada aviso con su respuesta: `200` si se ha aceptado y `400` si la firma no cuadra.
   - Si quieres comprobar solo la firma, usa `stripe trigger invoice.payment_failed`. Ese aviso es de un cliente inventado por la CLI, así que responde `200` pero no cambia ningún restaurante.

En un servidor publicado (Railway, Laravel Cloud) no se usa Stripe CLI. En el panel de Stripe, *Desarrolladores → Webhooks → Añadir destino*, pon la URL `https://<tu-dominio>/stripe/webhook` y elige los eventos `customer.subscription.updated`, `customer.subscription.deleted` e `invoice.payment_failed`. El secreto `whsec_...` de ese destino va en el `.env` del servidor (es distinto del de `stripe listen`).

---

## 5. Tablas, datos de prueba y fotos

```bash
php artisan migrate --seed
php artisan storage:link
```

- `migrate --seed` crea las tablas y carga los datos de prueba: 4 restaurantes, sus admins y camareros (con PIN), los 14 alérgenos, una carta de ejemplo, mesas y **ventas de ejemplo de los últimos 30 días** para las estadísticas (`VentasDemoSeeder`, no se ejecuta en producción). Si ya tenías la base de datos, cárgalas con `php artisan db:seed --class=VentasDemoSeeder`; si el restaurante ya tiene pagos de días anteriores, no añade nada.
- `storage:link` enlaza `public/storage` con `storage/app/public`, para que se vean las **fotos de los platos**. Solo hay que hacerlo una vez.

### Cuentas de prueba

| Quién | Cómo entra | Rol |
|---|---|---|
| `ivan@mesaya.com` · `gabriel@mesaya.com` · `profesor@mesaya.com` | `/login` con contraseña `1234` | **desarrolladores**: admin de «La Tasca de Prueba», pueden **cambiar de restaurante** desde el menú lateral y entrar como camarero, cocina o cliente con «Ver como» |
| `admin@la-tasca.test` (y `admin@bar-el-rincon.test`, `admin@marisqueria-o-porto.test`, `admin@cafe-central.test`) | `/login` con contraseña `password` | admin de cada restaurante |
| Camareros (nombres inventados, sin correo) | En un **dispositivo de camarero** emparejado: tocan su nombre y escriben su PIN. El primero de cada restaurante tiene `1111`, el segundo `2222`, el tercero `3333`... | camarero |
| Cocina | No tiene cuenta: se empareja una **tablet de cocina** | — |

**Emparejar un dispositivo en local:** entra como admin → **Dispositivos** → elige Cocina o Camarero → sale un código. Abre `http://localhost:8000/dispositivo` en **otro navegador o en una ventana de incógnito** (que hará de tablet) y escribe el código. Para desvincularlo, «Desvincular» en la misma pantalla.

El cliente no tiene cuenta: entra por el QR, por ejemplo en `http://localhost:8000/r/la-tasca/mesa/2`.

Los restaurantes de prueba tienen la **suscripción activa**, así que no hace falta pagar nada para usarlos. Para probar el alta de un restaurante nuevo, abre `http://localhost:8000/registro` sin sesión iniciada (necesita las claves de Stripe). Hasta que pague, ese restaurante no puede usar la aplicación: su admin solo ve «Suscripción» y su QR dice «Restaurante no disponible».

> Son datos inventados y **solo para local**: el seeder de las cuentas del equipo no hace nada en producción.

---

## 6. Arrancar

```bash
composer run dev
```

Arranca a la vez el servidor de Laravel (http://localhost:8000) y Vite (estilos y JavaScript con recarga automática). Para parar, `Ctrl + C`.

### Después de cada `git pull`

Cuando entra el trabajo de otra rama, ejecuta siempre estos tres comandos (si no hay nada nuevo, no hacen nada):

```bash
composer install
npm install
php artisan migrate
```

Ejemplo real: tras la rama de mesas y QR, la página de mesas daba error 500 (`Class "SimpleSoftwareIO\QrCode\Facades\QrCode" not found`) solo porque faltaba `composer install`.

### Ejecutar los tests

```bash
php artisan test
```

Más detalles en `06-proves.md`.

---

## 7. Problemas frecuentes

| Síntoma | Causa | Solución |
|---|---|---|
| Error 500 en todas las páginas con *«Target class [config] does not exist»*, aunque los tests funcionan | GD y OPcache a la vez en PHP 8.5 con el servidor web | Pon `opcache.enable=0` en el `php.ini` y reinicia `composer run dev` |
| `composer install` falla con *«requires ext-gd»* | GD desactivada | Quita el `;` de `extension=gd` |
| Error 500 *«Class ... not found»* tras un `git pull` | Faltan librerías nuevas | `composer install` |
| *«Table ... doesn't exist»* | Faltan migraciones | `php artisan migrate` |
| No se ven las fotos de los platos | Falta el enlace de `storage` | `php artisan storage:link` |
| La página sale sin estilos | Existe `public/hot` de un `npm run dev` anterior y Vite no está arrancado | Arranca `composer run dev` o borra `public/hot` (Git lo ignora) |
| `php artisan db:seed` falla por un dato repetido | Seeders antiguos | Los seeders actuales se pueden repetir. Para empezar de cero: `php artisan migrate:fresh --seed` (⚠️ **borra todos los datos**) |
| *«could not find driver»* | Falta `pdo_mysql` | Quita el `;` de `extension=pdo_mysql` |
| Al pagar con tarjeta: *«Ahora mismo no se puede pagar con tarjeta»* | Faltan las claves de Stripe en el `.env` o no son de modo test | Pon `STRIPE_KEY` y `STRIPE_SECRET` (`pk_test_`/`sk_test_`) y ejecuta `php artisan config:clear` |
| Tras `/registro` o al pulsar «Pagar la suscripción»: *«Ahora mismo no se puede abrir el pago con Stripe»* | Las mismas claves | Igual que la fila anterior. El restaurante ya está creado: se reintenta con «Pagar la suscripción» |
| «Gestionar en Stripe»: *«Ahora mismo no se puede abrir el portal de Stripe»* | El portal de clientes no está activado en Stripe | Actívalo en el panel de Stripe (modo de prueba): *Configuración → Facturación → Portal de clientes* |
| Un restaurante de prueba sale como *«Restaurante no disponible»* | Su suscripción no está activa (por ejemplo, la has cancelado probando el webhook) | `php artisan migrate:fresh --seed` (⚠️ borra los datos) o reactívala pagando desde «Suscripción» |
| `stripe listen` muestra `400` en cada aviso | `STRIPE_WEBHOOK_SECRET` no está o no es el que da `stripe listen` | Copia el `whsec_...` que saca `stripe listen` en el `.env` y ejecuta `php artisan config:clear` |
| `npm run build` falla con *«Unable to resolve @fontsource...»* | `node_modules` es anterior a las fuentes locales (rama 19) | `npm install` |

---

## 8. Despliegue

⏳ Pendiente (tarea P4 del plan): despliegue en Railway con MySQL. Se documentará aquí cuando esté hecho.
