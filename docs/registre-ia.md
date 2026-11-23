# Registro de uso de IA

Cada entrada: **Fecha y autor · Herramienta · Objetivo · Prompt · Resultado (¿funcionó?) · Enlace al commit/PR**.
Se rellena en cada sesión de trabajo, no al final. Mínimo 15 entradas.

---

### Entrada 1
- **Fecha y autor:** 10/11/2026 – Iván
- **Herramienta:** Claude (claude.ai)
- **Objetivo:** Redactar la propuesta inicial del proyecto
- **Prompt:** "Con el contexto del proyecto MesaYa, redacta docs/01-proposta.md"
- **Resultado:** Generó la propuesta completa; revisada y ajustada a mano · ¿funcionó?: Sí
- **Enlace:** commit `99efbaf` · PR [#17](https://github.com/K1rb1-30/ProyectoIA_APPWeb/pull/17)

### Entrada 2
- **Fecha y autor:** 10/11/2026 – Iván
- **Herramienta:** Claude (claude.ai)
- **Objetivo:** Preparar el contexto de Claude Code y bloquear el acceso a secretos
- **Prompt:** "Pásame el CLAUDE.md y los archivos necesarios para que Claude Code tenga todo el contexto, restringiéndole el acceso a .env, contraseñas y claves API"
- **Resultado:** Generó `CLAUDE.md`, `.claude/settings.json` (reglas de bloqueo) y este registro · ¿funcionó?: PENDIENTE DE REVISAR
- **Enlace:** commit `99efbaf` · PR [#17](https://github.com/K1rb1-30/ProyectoIA_APPWeb/pull/17)

### Entrada 3
- **Fecha y autor:** 10/11/2026 – Iván
- **Herramienta:** Claude Code
- **Objetivo:** Crear el proyecto Laravel y dejar la configuración inicial lista para una PR a `main`
- **Prompt:** "Créame un proyecto de Laravel en esta carpeta" + "Haz un commit y genera la pull request a main de configuración inicial, comprueba todo"
- **Resultado:** Proyecto Laravel 13 creado; `.env.example` con MySQL (`proyectoIA`), idioma `es` y Faker `es_ES`; `phpunit.xml` con MySQL (`proyectoIA_test`); CLAUDE.md actualizado a Laravel 13 y a esos nombres de BD. Comprobado: conexión a MySQL, migraciones, `php artisan test` (2 tests OK) y `npm run build`. Falta crear la BD `proyectoIA_test` y activar la extensión `intl` de PHP · ¿funcionó?: PENDIENTE DE REVISAR
- **Enlace:** [PullRequest](https://github.com/K1rb1-30/ProyectoIA_APPWeb.git)


### Entrada 4
- **Fecha y autor:** 15/11/2026 – Gabriel
- **Herramienta:** Claude Code
- **Objetivo:** Ordenar las maquetas exportadas de Google Stitch para que sirvan de referencia de diseño del proyecto
- **Prompt:** "Ya está en la carpeta DesignFrontend; júntalo, modifica o crea archivos para que Claude sepa que eso es el diseño de la web. Si los design.md son todos iguales, sácalo. Hazlo fácil para tener acceso al diseño y ponernos a trabajar"
- **Resultado:** Reorganizada `docs/design/DesignFrontend/` en carpetas `<rol>/<pantalla>/` con nombres cortos (18 pantallas, cada una con `code.html` y `screen.png`); los 5 `DESIGN.md` eran idénticos y se ha dejado uno solo en la raíz; creado `README.md` con el índice de pantallas y las diferencias entre las maquetas y el proyecto; añadida la sección "Diseño visual" a `CLAUDE.md`. No se ha tocado código de la aplicación · ¿funcionó?: PENDIENTE DE REVISAR
- **Enlace:** commit `99efbaf` · PR [#17](https://github.com/K1rb1-30/ProyectoIA_APPWeb/pull/17)

### Entrada 5
- **Fecha y autor:** 15/11/2026 – Gabriel
- **Herramienta:** Claude Code + graphify 0.9.109
- **Objetivo:** Instalar la skill graphify y generar un grafo del proyecto en una rama aparte
- **Prompt:** "Instala esta skill (https://github.com/Graphify-Labs/graphify.git) y haz un gráfico del proyecto; hazlo todo en una rama nueva para no interferir en el main"
- **Resultado:** Rama `feature/grafico-proyecto` creada desde `main`. Instalado el paquete `graphifyy` con pip (en el usuario, fuera del proyecto) y registrada la skill en `.claude/skills/graphify/`. Generado el grafo en `graphify-out/` (`graph.html`, `graph.json`, `GRAPH_REPORT.md`): 227 nodos, 238 relaciones, solo análisis local sin IA externa. Añadido `.graphifyignore` para que no lea `.env`, `vendor/`, `node_modules/` ni las maquetas. Activado después el modo siempre activo a petición nuestra: hooks en `.claude/settings.json` (llaman a `python -m graphify hook-guard`, solo dan avisos) y sección "Grafo del proyecto (graphify)" en `CLAUDE.md`, traducida al castellano · ¿funcionó?: PENDIENTE DE REVISAR
- **Enlace:** commit `99efbaf` · PR [#17](https://github.com/K1rb1-30/ProyectoIA_APPWeb/pull/17)

### Entrada 6
- **Fecha y autor:** 15/11/2026 – Gabriel
- **Herramienta:** Claude Code
- **Objetivo:** Empezar la aplicación por el login: instalar Breeze y dejarlo en castellano
- **Prompt:** "Vamos a empezar con la aplicación; dime por dónde empezarías y crea una rama desde main. Empezamos poco a poco" + "Sí, me parece bien" (al plan: Breeze con Blade, traducción y registro público desactivado)
- **Resultado:** Rama `feature/login-breeze`. Instalado `laravel/breeze` 2.4 con Blade y Alpine.js (cambia Tailwind 4 por Tailwind 3). Traducciones en `lang/es.json` y `lang/es/` (`validation`, `auth`, `passwords`, `pagination`); idioma por defecto `es` en `config/app.php`. Quitadas las rutas, el controlador, la vista y el test de registro; añadido `RegistroDesactivadoTest`. Actualizadas las notas de Tailwind en `CLAUDE.md` y en el README del diseño. `npm run build` funciona. `php artisan test`: los 25 tests pasan (al principio fallaban 22 por el entorno: faltaban la contraseña de MySQL y `APP_KEY` en el `.env`). Probado en el navegador: pantalla de login en castellano, mensaje de error con credenciales incorrectas, acceso correcto con el usuario del seeder y `/register` devuelve 404. Añadido `.claude/launch.json` para arrancar el servidor de pruebas · ¿funcionó?: PENDIENTE DE REVISAR
- **Enlace:** commit `99efbaf` · PR [#17](https://github.com/K1rb1-30/ProyectoIA_APPWeb/pull/17)


### Entrada 7
- **Fecha y autor:** 16/11/2026 – Gabriel
- **Herramienta:** Claude Code
- **Objetivo:** Revisar el estado del proyecto y obtener un plan de cómo construir las funcionalidades de la app
- **Prompt:** "Mira el proyecto y hazme un informe de cómo proceder en hacer las distintas cosas de la app"
- **Resultado:** Informe en el chat (sin cambios en el código): estado actual del repo; tareas previas (corregir Laravel 12/SQLite en la propuesta, escribir `02-requisits.md` y `03-disseny.md`, despliegue de prueba en Railway, renombrar `/dashboard` y `/profile`); orden de 13 ramas `feature/*` para el MVP (v0.1) y la v1.0 con sus dependencias y el golden test de cada una; reparto entre Persona A y B; decisiones pendientes sobre librerías (PDF de los QR, `stripe/stripe-php`, Chart.js) y cómo pedir cada rama a Claude Code. Después, a petición del usuario, el plan se ha guardado en el repo como `docs/plan-de-trabajo.md` (tablas de seguimiento con responsable, estado y PR de cada rama; detalle de cada rama; decisiones pendientes) y se ha enlazado desde `CLAUDE.md` · ¿funcionó?: PENDIENTE DE REVISAR
- **Enlace:** commit `99efbaf` · PR [#17](https://github.com/K1rb1-30/ProyectoIA_APPWeb/pull/17)

### Entrada 8
- **Fecha y autor:** 16/11/2026 – Iván
- **Herramienta:** Claude Code
- **Objetivo:** Rama 2 (`feature/diseno-base`): pasar el sistema de diseño a Tailwind, crear los componentes y layouts base y dar al login el aspecto de la maqueta
- **Prompt:** "Somos la Persona A del plan y empezamos la rama 2: feature/diseno-base. Lee CLAUDE.md, el plan, el README y DESIGN.md del diseño y la maqueta landing/login. Propón el plan y espera mi OK: tailwind.config.js con paleta, tipografías, radios, espaciados y sombras; Inter y Material Symbols (pros y contras de Google Fonts frente a local); componentes Blade en castellano; 4 layouts; login como landing/login y el resto de vistas de Breeze adaptadas" + "En local, adelante"
- **Resultado:** `tailwind.config.js` con la paleta, tamaños de letra, radios (`lg` = 1rem), espaciados y sombras `nivel-1` y `nivel-2` de `DESIGN.md`, más los colores de estado. Fuentes en local con los paquetes npm `@fontsource-variable/inter` y `@material-symbols/font-400` (este último en vez de `material-symbols`, que pesaba 4 MB frente a 0,5 MB). Componentes `boton-primario`, `boton-secundario`, `boton-peligro`, `tarjeta`, `chip-estado`, `badge-alergeno`, `campo-texto`, `precio`, `icono` y `logo`. Layouts `layouts.base`, `admin`, `camarero`, `cocina` y `cliente` en `resources/views/components/layouts/`. Login rehecho según `landing/login` (sin selector de perfil, PIN, foto ni cifras inventadas) y resto de vistas de Breeze adaptadas; borrados 11 componentes de Breeze que ya no se usan. Tests nuevos `ComponentesDisenoTest` y `LayoutsTest`. `npm run build` funciona y `php artisan test` pasa (34 tests). Login comprobado en el navegador en escritorio y móvil, con el error de credenciales y el botón de ver contraseña; el panel y el perfil no se han mirado en el navegador (no hay usuario de prueba en el seeder). Apuntada la decisión de las fuentes en el apartado 3 del plan · ¿funcionó?: PENDIENTE DE REVISAR
- **Enlace:** commit `99efbaf` · PR [#17](https://github.com/K1rb1-30/ProyectoIA_APPWeb/pull/17)

### Entrada 9
- **Fecha y autor:** 16/11/2026 – Gabriel
- **Herramienta:** Claude Code
- **Objetivo:** Rama 1 `feature/restaurantes-y-roles`: multi-restaurante, roles y aislamiento de datos
- **Prompt:** "Somos la persona B y empezamos la rama 1 [...] Propón un plan con migraciones, modelo Restaurante, middleware ComprobarRol, trait con global scope, seeder, redirección tras el login y test base del golden test nº 6" + "OK, implementa"
- **Resultado:** Migraciones `restaurantes` (nombre, slug único) y `rol` (enum) + `restaurante_id` (FK con borrado en cascada) en `users`. Modelo `Restaurante` y relaciones con `User`; método `User::rutaDeInicio()`. `RestauranteFactory` y estados `camarero()`/`cocina()` en `UserFactory`. Trait `App\Models\Concerns\PerteneceARestaurante` (global scope por el restaurante del usuario logueado y relleno de `restaurante_id` al crear). Middleware `ComprobarRol` con alias `rol`; `/dashboard` solo para admin; rutas provisionales `camarero.mesas` y `cocina.pedidos` con la vista `proximamente`. Tras el login cada rol va a su pantalla (sin `intended()`); el menú y la portada apuntan también a la pantalla de cada rol. `RestaurantesSeeder` con 2 restaurantes inventados y un usuario de cada rol. Tests nuevos: `AislamientoRestaurantesTest` (base del golden test nº 6, con tabla temporal de MySQL), `ComprobarRolTest` y `RedireccionTrasLoginTest`. `php artisan test`: 36 tests pasan. Incidencias: en este equipo faltaban `vendor/`, `public/build`, la contraseña de MySQL y `APP_KEY` en el `.env` (los rellenaron los alumnos); un `migrate:fresh --seed` lanzado por Claude borró y volvió a sembrar la base de desarrollo `proyectoIA`. No se ha actualizado el grafo porque graphify no está instalado en este equipo · ¿funcionó?: PENDIENTE DE REVISAR
- **Enlace:** commit `99efbaf` · PR [#17](https://github.com/K1rb1-30/ProyectoIA_APPWeb/pull/17)

### Entrada 10
- **Fecha y autor:** 16/11/2026 – Iván
- **Herramienta:** Claude Code
- **Objetivo:** Tener dos cuentas fijas (Gabriel e Iván) para poder entrar siempre en local
- **Prompt:** "Créame un seeder con dos usuarios admin para mi compañero y yo, con contraseña 1234 y correos gabriel@mesaya.com e ivan@mesaya.com, ficticio todo pero para poder entrar siempre"
- **Resultado:** Rama `feature/usuarios-admin`. Nuevo `UsuariosAdminSeeder` (usa `updateOrCreate`, no se ejecuta en producción) llamado desde `DatabaseSeeder`, y `UsuariosAdminSeederTest` (3 tests: crea las cuentas, no duplica, se puede hacer login). El rol `admin` aún no se asigna porque la columna `users.rol` llega con la rama 1; queda un TODO. Rama 2 marcada como ✅ en el plan. `php artisan test`: 37 OK · ¿funcionó?: PENDIENTE DE REVISAR
- **Enlace:** 816a37e · [PR #9](https://github.com/K1rb1-30/ProyectoIA_APPWeb/pull/9)

### Entrada 11
- **Fecha y autor:** 16/11/2026 – Gabriel
- **Herramienta:** Claude Code
- **Objetivo:** Resolver los conflictos de la PR #9 (`feature/usuarios-admin`) tras mergear la rama 1
- **Prompt:** "Arregla la pull request #9"
- **Resultado:** Fusionado `main` en `feature/usuarios-admin`. Conflictos resueltos: `DatabaseSeeder` llama primero a `RestaurantesSeeder` y después a `UsuariosAdminSeeder` (se quita el "Test User" de Breeze); plan con las ramas 1 y 2 en ✅; en el registro la entrada de la rama 1 queda como la 9 y la de las cuentas fijas pasa a la 10. Hecho el `TODO` del seeder: las cuentas de Gabriel e Iván son `admin` de "La Tasca de Prueba" (se crea con `firstOrCreate` si no existe), porque desde la rama 1 `restaurante_id` es obligatorio y el seeder fallaba. `UsuariosAdminSeederTest` comprueba ahora el rol, el restaurante y que el login lleva al panel. `php artisan test`: 49 tests pasan · ¿funcionó?: PENDIENTE DE REVISAR
- **Enlace:** commit `99efbaf` · PR [#17](https://github.com/K1rb1-30/ProyectoIA_APPWeb/pull/17)

### Entrada 12
- **Fecha y autor:** 16/11/2026 – Iván
- **Herramienta:** Claude Code
- **Objetivo:** Rama 3 del plan: gestión de la carta (categorías, platos y alérgenos) en el panel de admin
- **Prompt:** "Empieza la rama 3" + "OK, adelante" (al plan: edición del plato en página aparte en vez de panel lateral, y seeder con una carta de ejemplo)
- **Resultado:** Rama `feature/gestion-carta`. Migraciones `categorias`, `alergenos`, `platos` y `alergeno_plato`; modelos `Categoria`, `Plato` (con `PerteneceARestaurante`) y `Alergeno` (común a todos los restaurantes); `AlergenosSeeder` (14 alérgenos legales) y `CartaSeeder` (carta inventada para cada restaurante). Controladores `Admin\PlatoController`, `Admin\CategoriaController` y `Admin\AlergenoController` con rutas `admin.carta*` y `admin.alergenos` protegidas con `rol:admin`; validación en `PlatoRequest` (precio "8,50" → 850 céntimos, categoría solo del propio restaurante, foto de hasta 5 MB) y `CategoriaRequest`. Vistas de la carta según la maqueta `panelAdmin/carta`, categorías y alérgenos con el mismo estilo; componentes `mensajes-sesion` y `errores-campo`. 17 tests nuevos en `GestionCartaTest`, incluido el aislamiento entre restaurantes (golden test nº 6 aplicado a la carta). `php artisan test`: 66 OK; `npm run build` OK; probado en el navegador (lista y edición). Hace falta `php artisan storage:link` para ver las fotos. No se ha actualizado graphify (no está instalado en este equipo) · ¿funcionó?: PENDIENTE DE REVISAR
- **Enlace:** 26f1887

### Entrada 13
- **Fecha y autor:** 16/11/2026 – Iván
- **Herramienta:** Claude Code
- **Objetivo:** Que el rol admin pueda entrar en todas las pantallas del personal
- **Prompt:** "En el seeder de los dos usuarios dales el rol admin, y que si tienes el rol admin puedas acceder a todo"
- **Resultado:** Rama `feature/admin-acceso-total`. El seeder ya daba `rol = admin` a las dos cuentas (lo añadió Gabriel en la PR #9), así que no se toca. `ComprobarRol` deja pasar siempre al admin, también a las pantallas de camarero y cocina; sigue viendo solo los datos de su restaurante (eso lo hace `PerteneceARestaurante`). 2 tests nuevos en `ComprobarRolTest`. `php artisan test`: 51 OK · ¿funcionó?: PENDIENTE DE REVISAR
- **Enlace:** commit `99efbaf` · PR [#17](https://github.com/K1rb1-30/ProyectoIA_APPWeb/pull/17)

### Entrada 14
- **Fecha y autor:** 16/11/2026 – Iván
- **Herramienta:** Claude Code
- **Objetivo:** Desplegable para que el admin cambie de vista (administración, camarero, cocina) y pruebe las pantallas más fácil
- **Prompt:** "¿Puedes crearme en el dashboard de admin un desplegable que me deje cambiar el rol entre cliente, cocina y camarero, para que sea más fácil?"
- **Resultado:** En la rama `feature/admin-acceso-total` (PR #11). Componente `selector-vista` ("Ver como"): solo lo ve el admin y navega a la pantalla elegida; no cambia el rol en la base de datos. Puesto en la cabecera del panel (`navigation`), del layout admin, de cocina y en una franja encima de la cabecera del camarero. "Cliente" sale desactivado hasta la rama 8 (carta pública). 3 tests en `SelectorVistaTest`. `php artisan test`: 54 OK; probado en el navegador (panel → cocina) · ¿funcionó?: PENDIENTE DE REVISAR
- **Enlace:** commit `99efbaf` · PR [#17](https://github.com/K1rb1-30/ProyectoIA_APPWeb/pull/17)

### Entrada 15
- **Fecha y autor:** 16/11/2026 – Gabriel
- **Herramienta:** Claude Code
- **Objetivo:** Rama 4 `feature/mesas-qr`: mesas de cada restaurante con su código QR y una página para imprimirlos
- **Prompt:** "Empezamos la rama 4: feature/mesas-qr [...] Propón un plan con migración mesas, modelo Mesa, CRUD solo para admin, QR con simple-qrcode, PDF (explica dompdf frente a página imprimible), factory, seeder y tests" + "Imprimible, adelante"
- **Resultado:** Migración `mesas` (`numero`, `estado` libre/ocupada con `libre` por defecto, índice único `restaurante_id` + `numero`). Modelo `Mesa` con el trait de aislamiento y `urlQr()` (URL absoluta `/r/{slug}/mesa/{numero}`, sin ruta pública todavía). `MesaController` con CRUD solo para `rol:admin`: validación en castellano y `Rule::unique` filtrado a mano por restaurante; una mesa de otro restaurante da 404. Instalado `simplesoftwareio/simple-qrcode` 4.2 (QR en SVG, componente `qr-mesa`). Vista según `panelAdmin/mesas-qr` con `layouts.admin` (se quitan zonas, comensales y camareros, que no existen en la BD); estados `libre` y `ocupada` añadidos a `chip-estado`; el menú lateral marca la opción en todas sus subrutas. PDF: decidida la página imprimible `/mesas/imprimir` (apuntado en el plan). `MesaFactory` y `MesasSeeder` (8 + 6 mesas). Tests: `MesasTest` (13) y un caso con mesas en `AislamientoRestaurantesTest`. `php artisan test`: 62 tests pasan. Pantallas de listado y formulario revisadas en el navegador; la página para imprimir no se ha abierto en el navegador (solo con tests). Incidencias del equipo: hubo que activar la extensión GD en `php.ini` (la exige simple-qrcode); con GD y OPcache activos, el servidor web de PHP 8.5.7 falla al arrancar Laravel ("Target class [config] does not exist"), así que la revisión se hizo con OPcache desactivado; se borró un `public/hot` obsoleto de Vite. graphify sigue sin instalar · ¿funcionó?: PENDIENTE DE REVISAR
- **Enlace:** commit `99efbaf` · PR [#17](https://github.com/K1rb1-30/ProyectoIA_APPWeb/pull/17)

### Entrada 16
- **Fecha y autor:** 16/11/2026 – Gabriel
- **Herramienta:** Claude Code
- **Objetivo:** Revisar en el navegador la rama de mesas tras fusionar main y arreglar los errores encontrados
- **Prompt:** "Comprueba en el navegador y si hay algún error revísalo y arréglalo"
- **Resultado:** Probados como admin el listado de mesas, crear, editar, borrar (con la confirmación simulada), la página para imprimir, la carta y los alérgenos. Errores arreglados: (1) en el móvil (375 px) la barra superior del layout de admin se salía de la pantalla (se añade `flex-wrap`); (2) la página de la carta tenía scroll horizontal por un texto `sr-only` con `position: absolute` dentro de la tabla (se añade `relative` a su contenedor); (3) el formulario de platos se ensanchaba por el selector de foto (`grid-cols-1` y `min-w-0`); (4) la página para imprimir los QR también desbordaba (una columna en el móvil); (5) el panel `/dashboard` no tenía enlaces a Carta ni a Mesas (ahora usa `layouts.admin`). Los arreglos 2 y 3 tocan vistas de la rama 3. `php artisan test`: 84 tests pasan. No se ha podido leer el dibujo del QR en el navegador (no tiene lector de QR). El servidor de `composer run dev` sigue dando 500 en este equipo por OPcache + GD; la revisión se hizo con un servidor temporal sin OPcache · ¿funcionó?: PENDIENTE DE REVISAR
- **Enlace:** commit `99efbaf` · PR [#17](https://github.com/K1rb1-30/ProyectoIA_APPWeb/pull/17)

### Entrada 17
- **Fecha y autor:** 17/11/2026 – Gabriel
- **Herramienta:** Claude Code
- **Objetivo:** Rama 5 `feature/comandas-camarero`: que el camarero elija una mesa y envíe una comanda (platos, cantidades y notas)
- **Prompt:** "Vamos a hacer la rama 5: feature/comandas-camarero" con la lista de requisitos (migraciones `pedidos` y `lineas_pedido`, modelos con `$table` y trait, solo platos disponibles del propio restaurante, precio del momento, `DB::transaction`, creación del pedido en un solo sitio para la rama 9, mesa a `ocupada`, golden test nº 1 y test de otro restaurante). Primero plan y OK; después "firma como Gabriel y lo demás OK, adelante".
- **Resultado:** Migraciones `pedidos` (`mesa_id` y `plato_id` con `restrictOnDelete`, `user_id` nulo con `nullOnDelete`, `estado` enum con `pendiente` por defecto) y `lineas_pedido`. Modelos `Pedido` (trait de aislamiento, `ESTADOS`, `totalCentimos()`) y `LineaPedido` (sin el trait, porque su tabla no tiene `restaurante_id`; queda aislada a través de su pedido); relaciones `Mesa::pedidos()` y `Plato::lineasPedido()`. Clase `App\Acciones\CrearPedido::ejecutar($mesa, $lineas, $usuario = null)`: filtra a mano los platos por el restaurante de la mesa y por `disponible` (sin fiarse del global scope, que no actúa sin login), crea el pedido y las líneas con el precio del momento dentro de `DB::transaction` y pone la mesa en `ocupada`. `ComandaController` + `ComandaRequest` con las rutas `camarero.mesas`, `camarero.comanda.crear` y `camarero.comanda.guardar` (`rol:camarero`; la mesa de otro restaurante da 404). Vistas `camarero/mesas` (contadores y mapa de mesas) y `camarero/nueva-comanda` (selector de mesa, pestañas de categorías, botones −/+ con Alpine, nota por plato, agotados en gris y barra con el total y «Enviar a cocina»); se dejan fuera los avisos, cobros, comensales, notas rápidas y buscador de las maquetas. La pestaña «Mesas» del layout ya enlaza. El admin no puede borrar una mesa ni un plato que tengan pedidos (sale un aviso). Tests: `ComandasCamareroTest` (11, incluye el golden nº 1). `php artisan test`: 95 tests pasan. Probado en el navegador (375 px): crear una comanda en la mesa 3 con 2 platos y una nota, total 22,50 € y la mesa pasa a ocupada. Se instaló graphify (`pip install graphifyy`) y se borró un `public/hot` obsoleto · ¿funcionó?: PENDIENTE DE REVISAR
- **Enlace:** commit `a59d5a1` · PR [#14](https://github.com/K1rb1-30/ProyectoIA_APPWeb/pull/14)

### Entrada 18
- **Fecha y autor:** 17/11/2026 – Iván
- **Herramienta:** Claude Code
- **Objetivo:** Rama 8 del plan: carta pública que abre el cliente con el QR de su mesa
- **Prompt:** "Sigue con la 8, crea su rama y tal" + "OK, adelante" (al plan: ocultar los platos agotados y dejar los botones de añadir para la rama 9)
- **Resultado:** Rama `feature/carta-publica-qr`. Ruta pública `/r/{slug}/mesa/{numero}` (`cliente.carta`, sin login) con `Cliente\CartaController`, que filtra a mano por el restaurante del slug (`withoutGlobalScope('restaurante')`), para que la carta sea la misma con o sin sesión iniciada. Solo salen los platos disponibles y las categorías que tienen alguno. Vista `cliente/carta` según la maqueta (cápsulas de categorías, tarjetas con alérgenos, foto y precio). `Mesa::urlQr()` usa ya `route()`. En «Ver como» del admin se activa «Cliente», que abre la carta de su primera mesa. 12 tests en `CartaPublicaTest` (golden test nº 5, 404 de slug/mesa, aislamiento entre restaurantes). `php artisan test`: 96 OK; `npm run build` OK; probada en el navegador en tamaño móvil · ¿funcionó?: PENDIENTE DE REVISAR
- **Enlace:** e734b1c

### Entrada 19
- **Fecha y autor:** 17/11/2026 – Gabriel
- **Herramienta:** Claude Code
- **Objetivo:** Rama 6 `feature/pantalla-cocina`: tablero de cocina por estados, con cambio de estado controlado y refresco automático cada 5 s
- **Prompt:** "Vamos a hacer la rama 6: feature/pantalla-cocina" con la lista de requisitos (tablero por columnas según la maqueta, `cambiarEstado()` que solo permite pendiente → preparando → listo → servido, botones protegidos con `rol:cocina`, endpoint JSON y polling con Alpine cada 5 s sin WebSockets, aislamiento por restaurante, golden tests nº 1 y nº 2, tests de aislamiento y de acceso). Primero plan y después "OK, adelante".
- **Resultado:** `Pedido::SIGUIENTE_ESTADO`, `siguienteEstado()` y `cambiarEstado()`, que devuelve `false` sin cambiar nada si el salto no está permitido; guarda con `where estado = <el anterior>` para que dos cocineros a la vez no se salten un paso. `Cocina/PedidoController` con las rutas `cocina.pedidos` (tablero), `cocina.pedidos.datos` (JSON de los pedidos sin servir, del más antiguo al más nuevo) y `cocina.pedidos.estado` (PATCH; responde 422 si el salto no vale y 404 si el pedido es de otro restaurante); los datos se montan en un solo método para la primera carga y para el JSON. Vista `cocina/pedidos` según la maqueta: 3 columnas con contador y tarjetas pintadas con `x-for` de Alpine (mesa, número, hora, minutos, platos con cantidad y notas, botón del siguiente estado). Polling: `setInterval` cada 5 s, sin peticiones solapadas, aviso «Sin conexión» si falla la red y recarga si caduca la sesión; los botones usan `fetch` con `X-CSRF-TOKEN` y refrescan al momento. Se añade `[x-cloak]` a `app.css`. Fuera de la maqueta: estaciones, comensales, platos marcados uno a uno, alerta sonora y urgencias. Tests: `PantallaCocinaTest` (11, incluye el golden nº 2) y el golden nº 1 ampliado (el pedido sale en el JSON de cocina). `php artisan test`: 118 tests pasan. Probado en el navegador: una comanda de la mesa 5 hecha en otra pestaña aparece sola en «Pendiente»; los botones la llevan a «Preparando» y «Listo»; con el servidor parado sale el aviso de sin conexión (no se pudo ver cómo desaparece al volver, porque al reiniciar el servidor la pestaña cambió de página). En el plan, la fila 8 pasa a ✅ porque la PR #13 ya está fusionada · ¿funcionó?: PENDIENTE DE REVISAR
- **Enlace:** commit `e85bea2` · PR [#15](https://github.com/K1rb1-30/ProyectoIA_APPWeb/pull/15)

### Entrada 20
- **Fecha y autor:** 17/11/2026 – Iván
- **Herramienta:** Claude Code
- **Objetivo:** Rama 9 del plan: que el cliente pida desde el QR (carrito, «Mi pedido», envío a cocina y estado en directo)
- **Prompt:** "A por la 9" + "OK, adelante" (al plan: carrito en localStorage y sin comentarios para toda la mesa)
- **Resultado:** Rama `feature/autopedido`. `Mesa::delQr()` busca la mesa del QR filtrando a mano por el restaurante del slug (la usan la carta y el pedido). Carrito como componente Alpine `carrito` (`resources/js/carrito.js`) guardado en `localStorage` por restaurante y mesa; quita los platos que se han agotado. En la carta, botón «Añadir» / contador −/+ y barra «Ver pedido». Pantalla «Mi pedido» (notas por plato, total y «Enviar a cocina») y `POST /r/{slug}/mesa/{numero}/pedido` con `PedidoClienteRequest` y `CrearPedido` (rama 5) con usuario `null`, limitado a 10 envíos por minuto (`throttle`). Pantalla «Estado del pedido» con los 4 pasos y polling cada 5 s a `/estado/datos`; solo enseña los pedidos guardados en la sesión de ese navegador. 13 tests en `AutopedidoTest` (golden test nº 1 ampliado, nº 5 «no se puede pedir», validación, pedidos ajenos, JSON del estado y límite de envíos). `php artisan test`: 120 OK; `npm run build` OK; probado en el navegador (móvil): añadir platos, nota, enviar y ver cómo cambia el estado sin recargar. Al fusionar con la rama 6 (cocina) chocaban dos clases `PedidoController` en `routes/web.php`: el controlador del cliente se renombra a `Cliente\AutopedidoController`. Con todo junto, 131 tests OK y probado el flujo cliente → cocina (el pedido del cliente sale en el tablero y pasa a «preparando») · ¿funcionó?: PENDIENTE DE REVISAR
- **Enlace:** 318a21c

### Entrada 21
- **Fecha y autor:** 22/11/2026 – Gabriel
- **Herramienta:** Claude Code
- **Objetivo:** Rama 7 `feature/avisos-camarero`: que el camarero reciba el aviso de pedido listo y lo marque como servido
- **Prompt:** "Léete el plan de trabajo y ayúdame a hacer el prompt para la rama 7" + "Sí, empieza" + "OK, impleméntalo" (al plan: pantalla de avisos, contador en la pestaña «Avisos», pitido y tests)
- **Resultado:** `Camarero/AvisoController` con las rutas `camarero.avisos` (pantalla), `camarero.avisos.datos` (JSON de los pedidos en `listo` del restaurante, del que más lleva esperando al más nuevo) y `camarero.avisos.servido` (PATCH que usa `Pedido::cambiarEstado('servido')`; 422 si el pedido no está listo y 404 si es de otro restaurante). Componente Alpine `avisosCamarero` (`resources/js/avisos-camarero.js`) en el layout del camarero: hace el polling cada 5 s en todas sus pantallas, pinta un contador en la pestaña «Avisos» (que deja de apuntar a `#`) y, si llega un pedido nuevo, lo resalta y suena un pitido con la Web Audio API (sin archivos ni librerías; el navegador solo deja sonar tras tocar la página una vez). Vista `camarero/avisos` sin maqueta propia, con el estilo de `camarero/mesas`. 7 tests en `AvisosCamareroTest`. `php artisan test`: 138 OK; `npm run build` OK. La BD local tenía datos antiguos que impedían migrar y se rehízo con `migrate:fresh --seed` (con permiso). Probado en el navegador (móvil): el pedido listo de la mesa 4 sale con el globo «1» en «Avisos», «Marcar como servido» lo quita y muestra el mensaje, y un pedido que llega con la pantalla abierta aparece solo a los 5 s y resaltado. El pitido no se ha podido comprobar (el navegador de pruebas no tiene audio) · ¿funcionó?: Sí. PR revisada y fusionada en `main` el 22/11/2026; con ella se cierra el MVP y se crea la etiqueta `v0.1`
- **Enlace:** commit `99efbaf` · PR [#17](https://github.com/K1rb1-30/ProyectoIA_APPWeb/pull/17)

### Entrada 22
- **Fecha y autor:** 23/11/2026 – Iván
- **Herramienta:** Claude Code
- **Objetivo:** Rama 10 del plan: que el cliente pida la cuenta y la divida a partes iguales o por platos
- **Prompt:** "Empieza el 10" + "OK, adelante" (al plan: añadir `pedidos.cuenta_id` y que cada plato vaya entero a una persona)
- **Resultado:** Rama `feature/dividir-cuenta`. Migraciones `cuentas` (`abierta`/`cerrada`), `partes_cuenta` (`metodo` vacío hasta pagar, estados `pendiente`/`pendiente_efectivo`/`pagado`, una sola fila por persona) y `pedidos.cuenta_id` (cambio de tabla acordado). Modelos `Cuenta` (con el trait) y `ParteCuenta` (sin él, aislada por su cuenta). Clase `App\Acciones\DividirCuenta`: `repartirAPartesIguales()` (división entera y el céntimo que sobra a la primera parte), reparto por platos (renumera las personas sin huecos), transacción, comprobación de que la suma de las partes es el total y bloqueo si ya hay pagos. Pantalla `/r/{slug}/mesa/{numero}/cuenta` según la maqueta (total, modo iguales/por platos con vista previa en Alpine, tarjetas por persona; los botones de pagar quedan para las ramas 11 y 12); la pestaña «Cuenta» del layout ya enlaza. Tests: `RepartoCuentaTest` (golden test nº 3 y otros repartos) y `DividirCuentaTest` (12). `php artisan test`: 153 OK; `npm run build` OK; probado en el navegador: 59,50 € entre 3 (19,84 + 19,83 + 19,83) y por platos (25,50 + 34,00) · ¿funcionó?: PENDIENTE DE REVISAR
- **Enlace:** a4329c4

### Entrada 23
- **Fecha y autor:** 23/11/2026 – Iván
- **Herramienta:** Claude Code
- **Objetivo:** Adelantar la documentación que no depende de las ramas 11, 12 y 13
- **Prompt:** "Vamos con documentación, lo que veas que puedas hacer teniendo en cuenta que no están la 11, la 12 y la 13"
- **Resultado:** Rama `feature/documentacion`. P1: corregida la propuesta (Laravel 13, MySQL también en local, nombre del compañero) con una nota de cambios. P2: `02-requisits.md` con 29 requisitos funcionales (estado y PR de cada uno) y 11 no funcionales. P3: `03-disseny.md` con la arquitectura, las decisiones, el diagrama entidad-relación (Mermaid, sacado de las migraciones reales), las tablas, los estados y la lista de rutas (sacada de `route:list`). `04-installacio.md`: requisitos y `php.ini`, bases de datos, `.env`, seeders y cuentas de prueba, `storage:link`, pasos después de cada `git pull` y tabla de problemas frecuentes (los que nos han pasado de verdad); el despliegue queda pendiente (P4). `06-proves.md`: cómo ejecutar los tests y dónde está cada golden test (el nº 4 pendiente de la rama 12). Plan actualizado (rama 10 en ✅). Solo documentación, sin cambios en el código; `php artisan test`: 153 OK · ¿funcionó?: PENDIENTE DE REVISAR
- **Enlace:** 8382942

### Entrada 24
- **Fecha y autor:** 23/11/2026 – Gabriel
- **Herramienta:** Claude Code
- **Objetivo:** Rama 11 del plan: que el cliente pague su parte de la cuenta con tarjeta mediante Stripe Checkout en modo test
- **Prompt:** "Vamos a hacer la rama 11: feature/pago-stripe" (leer CLAUDE.md, el plan, las maquetas `cliente/pago-tarjeta` y `cliente/pago-completado` y el código de la rama 10; librería `stripe/stripe-php` con confirmación; claves en `.env.example`; migración y modelo `Pago`; sesión de Checkout con el importe en céntimos; comprobar la sesión al volver sin duplicar pagos; URL de cancelación; filtrar a mano por el restaurante del slug; sin webhooks; tests con Stripe simulado) + "OK, adelante" (al plan)
- **Resultado:** Rama `feature/pago-stripe`. `composer require stripe/stripe-php` (^23, decisión apuntada en el plan). `STRIPE_KEY` y `STRIPE_SECRET` vacías en `.env.example` y leídas en `config/services.php`. Migración `pagos` (`parte_cuenta_id` con `restrictOnDelete`, `importe_centimos`, `metodo`, `referencia_stripe` única) y modelo `Pago`; `ParteCuenta` gana `pagos()` y `estaPagada()`. Clase `App\Servicios\PasarelaStripe` (único sitio que habla con Stripe: `crearSesion()` y `consultarSesion()`; se niega a funcionar si la clave no empieza por `sk_test_`). `Cliente\PagoTarjetaController` con 4 rutas públicas en `/r/{slug}/mesa/{numero}/cuenta/partes/{parte}/…`: `tarjeta` (GET pantalla y POST que crea la sesión y redirige a Stripe), `pagado` (consulta la sesión y exige que esté pagada y que coincidan la parte, el importe y la moneda; transacción con `lockForUpdate` para no duplicar) y `cancelado`. La parte se busca a mano por mesa y restaurante (404 si es de otro). Vistas `cliente/pago-tarjeta` y `cliente/pago-completado` según las maquetas (sin propina, Apple Pay ni formulario de tarjeta, porque lo pone Stripe), y el botón «Tarjeta» activado en la cuenta. 11 tests en `PagoStripeTest` con `PasarelaStripe` simulada. `php artisan test`: 164 OK. Probado en el navegador: botones de la cuenta, pantalla de pago, cancelación y 404 de otro restaurante; el pago real en Stripe no se ha probado (faltan las claves test en el `.env`). Después se fusionó `main` en la rama (conflictos en el registro y el plan) y se pusieron al día los docs que llegaron con la PR #20: claves de Stripe y problema frecuente en `04-installacio.md`, tabla `pagos` y rutas de pago en `03-disseny.md`, RF21 en `02-requisits.md` y `PagoStripeTest` y la prueba manual en `06-proves.md` · ¿funcionó?: PENDIENTE DE REVISAR
- **Enlace:** 69e1eb0

### Entrada 25
- **Fecha y autor:** 23/11/2026 – Gabriel
- **Herramienta:** Claude Code
- **Objetivo:** Rama 12 del plan: pago en efectivo de una parte (lo confirma el camarero) y cierre de la mesa cuando todo está pagado
- **Prompt:** "Vamos a hacer la rama 12: feature/efectivo-y-cierre" (leer CLAUDE.md, el plan, los docs y las maquetas `cliente/pago-efectivo` y `camarero/detalle-mesa`; transiciones de la parte en un solo sitio; cierre solo con todo pagado; tests con el golden nº 4) + decisiones: cierre con botón del camarero y no cerrar si hay pedidos fuera de la cuenta + "OK, adelante" (al plan)
- **Resultado:** Rama `feature/efectivo-y-cierre`. `ParteCuenta`: constante `TRANSICIONES` y métodos `pedirEfectivo()`, `confirmarEfectivo()` y `pagarConTarjeta()` (update con `where estado = <el de antes>` para no cobrar dos veces; el `Pago` se crea en la misma transacción), `deLaMesa()` (filtro a mano por mesa y restaurante, sacado de la rama 11) y `textoEstado()`. `Cuenta`: `faltaPorCobrar()` (frases para el camarero), `hayPedidosFuera()` y `cerrar()` (cuenta `cerrada` y mesa `libre` solo si no falta nada). `DividirCuenta`: si ya hay pagos, divide solo lo pedido después y añade partes detrás (con comprobación de que todas las partes suman el total). `PagoTarjetaController` usa ahora `pagarConTarjeta()` y `deLaMesa()`. Cliente: `PagoEfectivoController` (POST elige efectivo, GET pantalla «Avisa al camarero») y botón «Efectivo» en la cuenta. Camarero: `DetalleMesaController` (detalle, cobrar y cerrar), vista `camarero/detalle-mesa` según la maqueta (sin TPV, comensales ni cambio) y enlace «Cuenta y cobro» en las mesas ocupadas. 12 tests en `EfectivoYCierreTest` (golden nº 4 incluido). `php artisan test`: 176 OK. Probado en el navegador con la BD local (mesa 1 de la-tasca): dividir lo pedido después de un pago con tarjeta, elegir efectivo, cobrar dos partes, botón de cerrar bloqueado con la lista de lo que falta y cierre con la mesa en «Libre». Docs puestos al día: `02-requisits`, `03-disseny`, `06-proves` y plan (rama 11 en ✅) · ¿funcionó?: PENDIENTE DE REVISAR
- **Enlace:** 057d063 (modelos) · 773880f (efectivo del cliente) · ce6ad7b (camarero)

### Entrada 26
- **Fecha y autor:** 23/11/2026 – Gabriel
- **Herramienta:** Claude Code
- **Objetivo:** Completar la rama 12 con lo que había quedado fuera: que el camarero divida la cuenta y cobre en la mesa (efectivo con el cambio o TPV) y la pestaña «Cobros»
- **Prompt:** "haz lo que queda afuera" + "OK, en la rama 12" (al plan)
- **Resultado:** En la misma rama `feature/efectivo-y-cierre`. `ParteCuenta`: `cobrarEfectivoEnMesa()` (hace pendiente → pendiente_efectivo → pagado en una transacción, sin saltarse el paso), `pagarConTpv()` (pendiente → pagado con tarjeta y `Pago` sin `referencia_stripe`) y `esperandoEfectivo()`. `DetalleMesaController`: `dividir()` (reutiliza `DividirCuenta`, a partes iguales; también divide lo pedido después) y `cobrar()` con el campo `metodo` (`confirmar`, `efectivo` o `tpv`). Vista parcial `camarero/partes/cobro-efectivo` con el cálculo del cambio en Alpine (botones «Exacto» y billetes, aviso «Falta dinero» y botón desactivado; no se guarda). Nueva pantalla `camarero/cobros` (`CobroController`, sin maqueta): partes esperando efectivo por mesa y lo cobrado hoy separado en efectivo, Stripe y TPV; la pestaña «Cobros» ya enlaza y muestra un globo con el número de personas esperando. 8 tests en `CobroCamareroTest`. `php artisan test`: 184 OK. Probado en el navegador con la mesa 5 de la BD local: dividir entre 2 desde el camarero, cambio de 5 € (2,00 €), aviso con 2,50 €, cobro en efectivo y con TPV, y «Cobros» con los totales. Docs: RF30 a RF32 en `02-requisits` (marcados como añadidos), rutas y estados en `03-disseny`, tests y prueba manual en `06-proves`, nota en el plan · ¿funcionó?: PENDIENTE DE REVISAR
- **Enlace:** ce6ad7b

### Entrada 27
- **Fecha y autor:** 23/11/2026 – Iván
- **Herramienta:** Claude Code
- **Objetivo:** Apuntar en el plan dos mejoras propias que salen de probar la aplicación y dejarlas como lo siguiente
- **Prompt:** "Ponlo en algún lado como extras en el plan de trabajo" + "Que si el cliente quiere pedir tenga que pagarlo al momento y que le salga al camarero" + "Súbelo y ponlo como siguiente, con de qué dependen"
- **Resultado:** Rama `feature/plan-mejoras`. Nueva sección «🟠 Siguientes: mejoras propias» en `docs/plan-de-trabajo.md` con la rama 14 `feature/platos-compartidos` (opción «Todos» al dividir por platos) y la 15 `feature/pagar-al-pedir` (el pedido del cliente se paga antes de ir a cocina y le sale al camarero; las comandas del camarero no cambian), su detalle y las decisiones pendientes. Las dos para Iván, se pueden empezar ya (no dependen de la 13) y en orden 14 → 15. Ramas 12 en ✅ y 13 en 🟡 para Gabriel. Primero se subió por error a la rama de la PR #20 (ya fusionada) sin preguntar; se rehízo en esta rama nueva · ¿funcionó?: PENDIENTE DE REVISAR
- **Enlace:** PENDIENTE

### Entrada 28
- **Fecha y autor:** 23/11/2026 – Iván
- **Herramienta:** Claude Code
- **Objetivo:** Rama 14 (mejora propia): poder marcar platos como compartidos («Todos») al dividir la cuenta por platos
- **Prompt:** "Empieza la 14" + "OK, adelante"
- **Resultado:** Rama `feature/platos-compartidos`. `DividirCuentaRequest`: en `asignaciones` el 0 significa «Todos», `personas` pasa a ser obligatorio también por platos y nadie puede asignarse a una persona que no existe (`lte:personas`). `DividirCuenta::importesPorPlatos()`: suma primero todos los platos compartidos y reparte esa suma una sola vez con `repartirAPartesIguales` (el céntimo que sobra a la primera persona), para no cargar un céntimo de más a la persona 1 por cada plato; sigue funcionando con el «dividir lo nuevo» de la rama 12. Pantalla de la cuenta: opción «Todos (compartido)» y la vista previa con el mismo cálculo. Al probarlo en el navegador salió un fallo: unas comillas dobles en un comentario de JavaScript dentro de `x-data` cortaban el atributo y rompían el formulario; arreglado y cubierto con un test que lee el HTML como el navegador. 8 tests nuevos en `PlatosCompartidosTest` y 2 ajustados en `DividirCuentaTest`. `php artisan test`: 192 OK; `npm run build` OK; probado en el navegador (59,50 € entre 3 con un plato compartido: 55,50 + 2,00 + 2,00) · ¿funcionó?: PENDIENTE DE REVISAR
- **Enlace:** 340eae6

### Entrada 29
- **Fecha y autor:** 23/11/2026 – Gabriel
- **Herramienta:** Claude Code
- **Objetivo:** Rama 13 del plan: pantalla de estadísticas del admin (ventas por día, platos más pedidos y tarjeta frente a efectivo) con Chart.js
- **Prompt:** "Vamos a hacer la rama 13: feature/estadisticas" (leer CLAUDE.md, el plan, los docs y la maqueta `panelAdmin/estadisticas`; Chart.js por npm con confirmación; cálculos en una clase con un método por gráfico y consultas agregadas; aislamiento de `pagos` a través de `partes_cuenta → cuentas`; mensaje sin datos; seeder de demo; tests) + decisiones: instalar Chart.js y poner la zona horaria `Europe/Madrid` + OK al plan
- **Resultado:** Rama `feature/estadisticas`. `npm install chart.js` (^4) como entrada aparte de Vite (`resources/js/estadisticas.js`, solo la carga esta pantalla; se registran solo barras, circular, ejes y tooltip). `config/app.php`: `timezone` = `env('APP_TIMEZONE', 'Europe/Madrid')`, y `APP_TIMEZONE` en `.env.example`. Clase `App\Estadisticas\EstadisticasRestaurante` con `resumenDeHoy()`, `ventasPorDia()`, `platosMasPedidos()` y `metodosDePago()`: una consulta `SUM`/`COUNT`/`GROUP BY` por método, dinero en céntimos, días sin ventas rellenados con 0 en PHP y aislamiento escrito a mano (`pagos JOIN partes_cuenta JOIN cuentas WHERE cuentas.restaurante_id = ?`; `pedidos.restaurante_id` para los platos), porque el global scope no actúa en `DB::table()` con joins. `Admin\EstadisticaController` (solo pasa los datos), ruta `admin.estadisticas` con `rol:admin` y enlace en el menú lateral. Vista `admin/estadisticas` según la maqueta (tarjetas de hoy, barras de 30 días, top 10 con barras en HTML, circular tarjeta/efectivo con Stripe y TPV) y componente `x-estadisticas-vacio` para cuando no hay datos. Euros solo al mostrar (`Intl.NumberFormat` en ejes y tooltips). `VentasDemoSeeder` (30 días de mesas cobradas con platos reales, cuentas cuadradas con `repartirAPartesIguales`, ~30 % Stripe, ~30 % TPV y ~40 % efectivo; no se ejecuta en producción ni duplica) añadido a `DatabaseSeeder`. 11 tests en `EstadisticasTest`. `php artisan test`: 195 OK; `npm run build` OK. Seeder ejecutado en la BD local y pantalla probada en el navegador con el admin de la-tasca (gráficos pintados, sin errores nuevos en consola). Docs: plan (decisión «Gráficos», fila 12 en ✅), `02-requisits`, `03-disseny`, `04-installacio` y `06-proves`. Después se fusionó `main` (ramas 14 y mejoras del plan): conflictos en el registro (esta entrada pasa de 27 a 29) y en el plan (sección de mejoras 14 y 15, con la 14 en ✅); `php artisan test`: 203 OK · ¿funcionó?: PENDIENTE DE REVISAR
- **Enlace:** fa84c8d (consultas) · 785c9f8 (pantalla)

### Entrada 30
- **Fecha y autor:** 23/11/2026 – Gabriel
- **Herramienta:** Claude Code
- **Objetivo:** Rama 16 (nueva, no estaba en el plan): página de inicio pública de MesaYa en `/`, en lugar de la `welcome` de Laravel
- **Prompt:** "Vamos a hacer una rama nueva que no estaba en el plan: la 16, feature/pagina-inicio" (maqueta `landing/inicio`; solo funciones reales; «Acceso del personal» al login o a la pantalla del rol; nada externo; responsive; tests) + decisiones: ilustraciones con HTML, páginas simples de privacidad y cookies, y botón de la carta de ejemplo solo si existe + OK al plan
- **Resultado:** Rama `feature/pagina-inicio`. Ruta `/` con nombre `inicio` (`InicioController`, que solo comprueba con `rescue()` si existe la mesa 1 de `la-tasca` para el botón «Ver una carta de ejemplo»); `welcome.blade.php` borrada. Layout nuevo `x-layouts.publico` (cabecera fija con anclas que se ocultan en el móvil, pie con anclas y legales) y componente `x-boton-acceso` («Acceso del personal» → login, o «Ir a mi pantalla» → `User::rutaDeInicio()`). Vista `inicio` por secciones: portada con un móvil y una pantalla de cocina hechos con HTML/Tailwind (sin las fotos externas de la maqueta), «Cómo funciona» (carta QR y autopedido, cocina y sala, cuenta y pago), «Cocina y sala» (una pantalla por rol), «Panel del restaurante» (carta, mesas y QR, estadísticas, multi-restaurante) y llamada final. Páginas `legal/privacidad` y `legal/cookies` con lo que la app hace de verdad (datos guardados, Stripe, cookies de Laravel y carrito en `localStorage`) y aviso de práctica de clase. **Textos de la maqueta quitados por no existir en la app:** «nº 1 para hostelería», «+30 % rotación», «+22 % ticket medio», «< 9 min», «+35 %», «0 errores», «plataforma líder»; ventas sugeridas, fotos HD y filtros veganos/celíacos, Apple Pay/Google Pay/Bizum, impresión en partidas e impresoras ESC/POS, notificaciones push, alertas por tiempo de espera y tiempos por plato, escandallos y márgenes, rotación por franja, ficha técnica «homologada», gestión de stock, «QR dinámico», API para TPVs; «Ley 1169/2011», «Cumplimiento UE», TicketBAI y VeriFactu; «demostración gratuita», «Empezar gratis», 14 días de prueba, sin permanencia, puesta en marcha en 24 h, soportes y QR grabados, Precios y planes, soporte telefónico y 24/7, blog, centro de ayuda, términos de servicio, «Sistemas operativos 100 %», ciudades; el caso de éxito «Bar La Plaza» (cambiado por la sección de roles). La sección y la maqueta de precios no se han hecho. 7 tests en `InicioTest`. `php artisan test`: 210 OK; `npm run build` OK. Probado en el navegador en escritorio (1280 px) y móvil (375 px, sin scroll horizontal), con todos los enlaces respondiendo 200 y las anclas apuntando a secciones que existen. Docs: fila 16 y sección 16 en el plan (y la 13 en ✅), RF33 en `02-requisits` (marcado como añadido), rutas en `03-disseny` y tests en `06-proves` · ¿funcionó?: PENDIENTE DE REVISAR
- **Enlace:** 7a11254 (página) · 373a3f3 (legales)
