# MesaYa — Instrucciones para Claude Code

## Quiénes somos y qué es esto
Práctica de clase **DAW M0617 – Vibe Coding I**, hecha por parejas. **Presentación con demo en vivo: semana del 7/12/2026.**

**MesaYa** es un SaaS de comandas para bares y restaurantes:
- El cliente escanea el QR de su mesa, ve la carta y pide.
- La cocina recibe el pedido al momento.
- La cuenta se divide por persona y se paga con tarjeta (Stripe test) o en efectivo.

La propuesta completa está en `docs/01-proposta.md`, los requisitos en `docs/02-requisits.md` y el diseño en `docs/03-disseny.md`. **Léelos antes de implementar una funcionalidad.**

## Cómo tienes que trabajar con nosotros
1. **Responde siempre en castellano.**
2. **Explica todo el código que generes**: qué hace cada parte y por qué. Es una práctica evaluada y la regla del profe es que **no entra en `main` ningún código que alguno de los dos no sepa explicar**. Si algo es complejo, prefiere la solución sencilla aunque sea menos elegante.
3. **Pasos pequeños.** Cada cambio tiene que caber en una rama y una PR. Antes de un cambio grande, propón el plan y espera el OK.
4. **Sigue el stack y las tablas de este documento.** No añadas librerías ni tablas nuevas sin preguntar.
5. Si no sabes algo del proyecto, **pregunta**; no te lo inventes.

## TODO en castellano (obligatorio)
Todo lo que escribamos nosotros va en castellano:
- **Base de datos:** nombres de tablas, columnas, valores de estados y roles.
- **Código:** modelos, controladores, variables, métodos, rutas, vistas, migraciones, seeders y tests.
- **Comentarios** en el código.
- **La web:** todos los textos que ve el usuario (botones, menús, formularios, mensajes de error y de validación, correos). Eso incluye las vistas que genera Breeze: hay que traducirlas.
- **Commits, ramas, PR y documentación.**

Normas para los nombres:
- **Sin tildes ni ñ** en identificadores: `categorias`, `alergenos`, `anio`, `contrasena`. En los textos de la web, sí con tildes y ñ.
- Tablas en plural y `snake_case` (`lineas_pedido`), modelos en singular y `PascalCase` (`LineaPedido`), variables y métodos en `camelCase` (`$precioTotal`, `calcularTotal()`).
- **En cada modelo declara siempre `protected $table = '...';`**, porque Laravel pluraliza en inglés y se equivocaría (por ejemplo, `LineaPedido` → `linea_pedidos`).
- Ramas: `feature/<nombre-en-castellano>`, por ejemplo `feature/gestion-carta`. El prefijo `feature/` se mantiene porque lo pide el profe.

**Única excepción:** lo que ya trae Laravel de serie se queda como está, porque renombrarlo rompe el framework y Breeze. Eso incluye las tablas `users`, `sessions`, `cache`, `jobs`, `migrations` y `password_reset_tokens`, el modelo `User`, los métodos propios de Laravel (`index`, `store`, `hasMany`…) y las columnas de serie de `users` (`name`, `email`, `password`). Las columnas que **añadamos** a `users` sí van en castellano (`rol`, `restaurante_id`).

Configura `APP_LOCALE=es` y `APP_FAKER_LOCALE=es_ES` en `.env.example`. Para traducir los mensajes de validación, crea los archivos en `lang/es/`. Si propones un paquete de traducciones, pregunta antes.

## Seguridad: normas que no se saltan nunca
- **No leas, muestres, edites ni copies** `.env` ni ningún archivo con contraseñas, claves API, tokens o certificados. Están bloqueados en `.claude/settings.json`; no intentes rodearlo con `cat`, `grep`, `tinker`, `config:show` ni nada parecido.
- Si necesitas una variable de entorno nueva, **añádela a `.env.example` con un valor vacío o de ejemplo** y dinos que la rellenemos nosotros en nuestro `.env`.
- **Nunca escribas claves ni contraseñas en el código**: siempre `env()` en `config/*.php` y `config()` en el resto.
- **Stripe siempre en modo test.** Tarjeta de prueba `4242 4242 4242 4242`. Nunca claves `sk_live_` ni `pk_live_`.
- **No uses datos personales reales** en seeders ni en ejemplos. Usa Faker (en `es_ES`) o nombres inventados.
- Antes de cada commit, comprueba que no se cuela ningún secreto en el diff.

## Stack
- **Backend:** Laravel 13 + Breeze (autenticación)
- **Frontend:** Blade + Tailwind CSS + Alpine.js
- **BD:** **MySQL siempre**, tanto en local como en producción. Nada de SQLite.
- **Pagos:** Stripe Checkout en modo test
- **Tiempo real:** polling con JS cada ~5 s (sin WebSockets salvo que lo pidamos)
- **QR:** `simplesoftwareio/simple-qrcode`
- **Despliegue:** Railway o Laravel Cloud

### MySQL
- Base de datos de desarrollo: `proyectoIA`. Base de datos de tests: `proyectoIA_test`. (El nombre de la app aún no es definitivo; las bases de datos no se renombran con él.)
- En `.env.example`: `DB_CONNECTION=mysql`, `DB_HOST=127.0.0.1`, `DB_PORT=3306`, `DB_DATABASE=proyectoIA`, `DB_USERNAME=root` y `DB_PASSWORD=` vacío.
- En `phpunit.xml`, los tests usan MySQL con `DB_DATABASE=proyectoIA_test`. Quita las líneas que ponen SQLite en memoria. No pongas contraseñas en `phpunit.xml`.
- Cotejamiento `utf8mb4_unicode_ci` para que funcionen tildes, ñ y emojis.
- Si no puedes conectar con MySQL, **no cambies a SQLite**: avísanos.

## Roles
| Rol (`users.rol`) | Login | Puede |
|---|---|---|
| `admin` | Sí | Carta, alérgenos, mesas y QR, personal, estadísticas |
| `camarero` | Sí | Comandas, avisos de plato listo, cobrar en efectivo, cerrar mesa |
| `cocina` | Sí | Ver pedidos y cambiar su estado |
| Cliente | No | Carta por QR, pedir, dividir la cuenta, pagar |

Protege las rutas con un middleware por rol (por ejemplo, `ComprobarRol`).

## Multi-restaurante (muy importante)
- Todas las tablas de negocio llevan `restaurante_id`.
- Usa **global scopes** o filtra siempre por el restaurante del usuario logueado.
- **El restaurante A nunca puede ver ni tocar datos del restaurante B.** Hay un test que lo comprueba.

## Base de datos
| Tabla | Modelo | Columnas clave |
|---|---|---|
| `restaurantes` | `Restaurante` | nombre, slug |
| `users` *(de Laravel)* | `User` | + `rol`, `restaurante_id` |
| `mesas` | `Mesa` | restaurante_id, numero, estado (`libre` / `ocupada`) |
| `categorias` | `Categoria` | restaurante_id, nombre, orden |
| `platos` | `Plato` | restaurante_id, categoria_id, nombre, descripcion, precio_centimos, foto, disponible |
| `alergenos` | `Alergeno` | nombre, icono |
| `alergeno_plato` | *(pivote)* | alergeno_id, plato_id |
| `pedidos` | `Pedido` | restaurante_id, mesa_id, user_id (nullable si pide el cliente), estado |
| `lineas_pedido` | `LineaPedido` | pedido_id, plato_id, cantidad, precio_centimos, notas |
| `cuentas` | `Cuenta` | restaurante_id, mesa_id, total_centimos, estado |
| `partes_cuenta` | `ParteCuenta` | cuenta_id, persona, importe_centimos, metodo (`tarjeta` / `efectivo`), estado |
| `pagos` | `Pago` | parte_cuenta_id, importe_centimos, metodo, referencia_stripe |
| *(extra)* `reservas` | `Reserva` | restaurante_id, mesa_id, fecha, hora, personas |

Reglas:
- **Dinero siempre en céntimos (enteros)**, nunca `float`. Columnas acabadas en `_centimos`. Ejemplo: `precio_centimos = 1250` equivale a 12,50 €. En la web se muestra como `12,50 €`.
- `lineas_pedido.precio_centimos` guarda el precio en el momento del pedido, para que un cambio de carta no altere pedidos ya hechos.
- Al dividir una cuenta, **la suma de las partes tiene que ser exactamente el total.** El céntimo que sobra va a la primera parte.
- Estados de pedido: `pendiente` → `preparando` → `listo` (→ `servido`). **No se puede saltar de `pendiente` a `listo`.**
- Estados de una parte de la cuenta: `pendiente` → `pendiente_efectivo` → `pagado` (en efectivo) o `pendiente` → `pagado` (con tarjeta).
- Una mesa solo se cierra cuando todas sus partes están pagadas.
- Los 14 alérgenos legales van en un seeder (`AlergenosSeeder`).
- URL fija del QR: `/r/{slug}/mesa/{numero}`.

## Funcionalidades y prioridad
🟢 **MVP (v0.1):** login y roles, multi-restaurante, carta (CRUD + foto + disponibilidad), alérgenos, mesas y QR en PDF, comanda del camarero, pantalla de cocina, aviso de plato listo.
🔵 **v1.0:** carta pública por QR, autopedido, dividir la cuenta, pago con Stripe, pago en efectivo, cerrar mesa, estadísticas (Chart.js).
🟣 **Extras (solo si lo pedimos):** reservas, reserva con plato, plano de mesas, ticket PDF, modo oscuro de cocina.

No empieces nada 🟣 por tu cuenta.

## Tests (golden tests)
Ejecutar: `php artisan test` (contra la base de datos MySQL `proyectoIA_test`)
1. Enviar una comanda crea el `pedido` con sus `lineas_pedido` y aparece en cocina.
2. No se puede pasar de `pendiente` a `listo` sin pasar por `preparando`.
3. Dividir la cuenta: la suma de las partes es exactamente el total (probar a dividir entre 3).
4. Pagos mixtos: la mesa solo se cierra cuando todo está pagado.
5. Un plato no disponible no aparece en la carta del QR y no se puede pedir.
6. Aislamiento SaaS: el restaurante A no ve datos del B.

Los tests también van en castellano: `test_no_se_puede_pasar_de_pendiente_a_listo()`. Cuando implementes una de estas partes, escribe o actualiza su test.

## Git y GitHub
- **Nunca hagas commits ni push a `main`.** Una rama por funcionalidad: `feature/<nombre-corto>`.
- Commits pequeños, con mensajes en castellano y en imperativo, por ejemplo: `Añade CRUD de platos`.
- **No hagas `git push` sin preguntar**, y nunca `--force`.
- Todo entra en `main` por pull request revisada por el compañero.

## Registro de IA (obligatorio, lo evalúa el profe)
Al terminar cada tarea, **añade una entrada al final de `docs/registre-ia.md`**, numerada siguiendo la última:

```
### Entrada N
- **Fecha y autor:** DD/MM/AAAA – (pregunta quién está trabajando si no lo sabes)
- **Herramienta:** Claude Code
- **Objetivo:** qué se pedía, en una frase
- **Prompt:** el prompt del usuario (resumido si es largo)
- **Resultado:** qué has generado o cambiado · ¿funcionó?: PENDIENTE DE REVISAR
- **Enlace:** hash del commit si lo has hecho tú; si no, PENDIENTE
```

Nosotros revisamos la entrada, decidimos si funcionó y ponemos el enlace a la PR.

## Documentación del repo
`README.md` · `docs/01-proposta.md` · `docs/02-requisits.md` · `docs/03-disseny.md` · `docs/04-installacio.md` · `docs/05-manual-usuari.md` · `docs/06-proves.md` · `docs/registre-ia.md` · `CHANGELOG.md` (v0.1 y v1.0)

Si un cambio afecta a la instalación, a la API o al uso, avisa de qué documento hay que actualizar.

## Comandos útiles
```bash
composer install && npm install
php artisan migrate --seed
composer run dev          # servidor + vite
php artisan test
```
