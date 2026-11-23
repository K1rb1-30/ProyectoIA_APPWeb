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

Los tests usan la base de datos `proyectoIA_test`, que ya está configurada en `phpunit.xml`.

---

## 5. Tablas, datos de prueba y fotos

```bash
php artisan migrate --seed
php artisan storage:link
```

- `migrate --seed` crea las tablas y carga los datos de prueba: 2 restaurantes, sus usuarios, los 14 alérgenos, una carta de ejemplo y mesas.
- `storage:link` enlaza `public/storage` con `storage/app/public`, para que se vean las **fotos de los platos**. Solo hay que hacerlo una vez.

### Cuentas de prueba

| Correo | Contraseña | Rol |
|---|---|---|
| `ivan@mesaya.com` · `gabriel@mesaya.com` | `1234` | admin de «La Tasca de Prueba» |
| `admin@la-tasca.test` | `password` | admin |
| `camarero@la-tasca.test` | `password` | camarero |
| `cocina@la-tasca.test` | `password` | cocina |
| `admin@bar-el-rincon.test` (y `camarero@`, `cocina@`) | `password` | del segundo restaurante, para probar el aislamiento |

El cliente no tiene cuenta: entra por el QR, por ejemplo en `http://localhost:8000/r/la-tasca/mesa/2`.

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

---

## 8. Despliegue

⏳ Pendiente (tarea P4 del plan): despliegue en Railway con MySQL. Se documentará aquí cuando esté hecho.
