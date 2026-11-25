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

### Entrada 31
- **Fecha y autor:** 23/11/2026 – Iván
- **Herramienta:** Claude Code
- **Objetivo:** Rama 15 (mejora propia): que el cliente pague su pedido con tarjeta al hacerlo, para evitar pedidos de broma o desde fuera del bar
- **Prompt:** "Si quiere pagar en efectivo, que llame al camarero; lo pagado al pedir que salga en la cuenta como pagado y no se cobre otra vez; el autopedido sin pagar desaparece del todo. Piensa cómo hacerlo, documenta y hazlo" + "OK, adelante"
- **Resultado:** Rama `feature/pagar-al-pedir`. Diseño documentado en el plan. «Enviar a cocina» pasa a «Pagar y enviar a cocina»: el servidor calcula el total con sus precios (`PagarPedido::calcularTotal`), abre Stripe Checkout (`PasarelaStripe::crearSesionPedido`, nuevo) y guarda el carrito en la sesión. Al volver (`/pedido/pagado`) se pregunta a Stripe; si cuadra (pagado, pedido, mesa, importe y euros), `PagarPedido::confirmar` crea el pedido con `CrearPedido` (llega a cocina), lo apunta en la cuenta abierta (o una nueva) y añade una parte ya pagada con `pagarConTarjeta()`. Así no se toca la división de la cuenta ni el cierre de la mesa. Cambio de tabla acordado: `partes_cuenta.pedido_id` para mostrar «Pedido #N · pagado al pedir». Si la carta cambia mientras paga, no se crea el pedido y se da la referencia de Stripe para devolverlo; recargar no duplica. En «Mi pedido», aviso de que para pagar en efectivo hay que llamar al camarero. Tests: `AutopedidoTest` adaptado al nuevo flujo y `PagarAlPedirTest` (13), con una pasarela de Stripe falsa (`tests/Fakes/PasarelaStripeFalsa.php`). Requisitos, diseño y pruebas actualizados. `php artisan test`: 216 OK; `npm run build` OK; pantalla «Mi pedido» revisada en el navegador (el pago real con la tarjeta 4242 queda por probar con las claves de Stripe) · ¿funcionó?: PENDIENTE DE REVISAR
- **Enlace:** 9216450

### Entrada 32
- **Fecha y autor:** 23/11/2026 – Gabriel
- **Herramienta:** Claude Code
- **Objetivo:** Rama 17 (nueva): sustituir el `dashboard` de Breeze por el panel principal del admin según la maqueta `panelAdmin/panel/`, con datos reales, y renombrar `/dashboard` → `/panel` (tarea previa P5)
- **Prompt:** "Rama nueva 17, feature/panel-admin: panel del admin con resumen de hoy (reutilizando EstadisticasRestaurante), mesas, carta con interruptor de disponible y pedidos en curso; solo datos reales; quitar lo que la app no hace; ¿se puede calcular el tiempo medio de cocina?; propuesta sobre P5; pocas consultas; tests de cifras, aislamiento y roles. Antes de escribir código, propón el plan" + "OK, con /panel"
- **Resultado:** Rama `feature/panel-admin`. `Admin\PanelController` (un solo método `__invoke`) y vista `admin/panel.blade.php`; borrada `dashboard.blade.php`. Ruta `/panel` (`panel`) con `rol:admin`; `/dashboard` redirige. Cambiados `route('dashboard')` en `User::rutaDeInicio()`, layout de admin, selector «Ver como», 4 controladores de verificación de Breeze, 8 tests y `docs/03-disseny.md`. «Panel» añadido al menú lateral. Unas 10 consultas con agregados (resumen de hoy, mesas, `COUNT`/`SUM` de platos, 5 platos con alérgenos y pedidos sin servir con `GROUP BY estado`). Quitado de la maqueta lo que la app no hace; el tiempo medio de cocina no se puede calcular (no se guarda cuándo pasa un pedido a `listo`) y no se ha añadido ninguna columna. `PanelAdminTest` (8 tests). `php artisan test`: 231 OK; `npm run build` OK; grafo actualizado. Revisado en el navegador con La Tasca de Prueba (cifras, cuadrícula de mesas e interruptor de disponible, que vuelve al panel); para eso se ejecutaron en la base de datos de desarrollo las 5 migraciones pendientes de las ramas 10 a 15 · ¿funcionó?: PENDIENTE DE REVISAR
- **Enlace:** 0db0960 · [PR #29](https://github.com/K1rb1-30/ProyectoIA_APPWeb/pull/29)

### Entrada 33
- **Fecha y autor:** 23/11/2026 – Gabriel
- **Herramienta:** Claude Code
- **Objetivo:** Rama 18 (nueva): que el admin gestione las cuentas de sus empleados (CRUD de personal en /admin/personal)
- **Prompt:** "Rama 18, feature/gestion-personal: CRUD de empleados con nombre, correo, rol y contraseña; restaurante_id solo del admin; filtrar a mano porque User no usa el trait; correo único; entrar sin verificar el correo; no borrarse ni quitarse el rol; nunca sin admin; ¿qué pasa con los pedidos al borrar?; seeder; tests. Propón el plan antes" + "OK, opción A"
- **Resultado:** Rama `feature/gestion-personal`. `Admin\PersonalController` y `Admin\EmpleadoRequest` (correo en minúsculas y único en `users`, contraseña con `confirmed` y `Password::defaults()`, opcional al editar; reglas en `after()`: no quitarse el rol de admin y no dejar el restaurante sin admin). `restaurante_id` lo pone el controlador y `email_verified_at` se rellena al crear (el middleware `verified` hoy no actúa porque `User` no implementa `MustVerifyEmail`). Aislamiento a mano: `where('restaurante_id')` en el listado y `Route::bind('empleado')` en `routes/web.php` (404 si es de otro restaurante). Al cambiar la contraseña o borrar, se borran las sesiones de esa persona en la tabla `sessions`. Pedidos al borrar: opción A, se mantiene `nullOnDelete` (los pedidos quedan sin quién los tomó) y el aviso de borrar lo dice. Relación `User::pedidos()`. Vistas `admin/personal/index` y `formulario` (sin maqueta, estilo de la carta; `@js()` en el texto del `confirm()`). «Personal» del menú lateral enlazado. `RestaurantesSeeder` con 6 empleados por restaurante. `GestionPersonalTest` (13). `php artisan test`: 244 OK; `npm run build` OK; grafo actualizado. Revisado en el navegador: listado, errores de correo repetido y contraseñas distintas, crear y borrar un empleado de prueba y la ficha propia con el rol fijo · ¿funcionó?: PENDIENTE DE REVISAR
- **Enlace:** b9af3e8 · [PR #30](https://github.com/K1rb1-30/ProyectoIA_APPWeb/pull/30)

### Entrada 34
- **Fecha y autor:** 24/11/2026 – Gabriel
- **Herramienta:** Claude Code
- **Objetivo:** Rediseñar las vistas para que no parezcan hechas con IA y tengan mejor jerarquía; primera rama (19): colores, fuentes y componentes base
- **Prompt:** "/design Por favor mira todas las vistas del proyecto y rediséñalas para que no parezcan hechas con IA, haz buena jerarquía" + "Sí, propón el plan" + "Sí, empieza la rama 1"
- **Resultado:** Primero, un lienzo de diseño (fuera del repo) con 14 pantallas del sistema «Comanda impresa» y un plan de 5 ramas. Después, rama `feature/rediseno-base`: `tailwind.config.js` con los mismos nombres de color y valores nuevos (papel `#F5F4F0`, tinta `#1A1A1A`, acento `#C2410C`, grises neutros), tamaños de letra más contrastados, radios pequeños y sombras casi invisibles. Fuentes Schibsted Grotesk e IBM Plex Mono con `@fontsource` en local (devDependencies) en lugar de Inter. Componentes rehechos: botones primario/secundario/peligro, `chip-estado` (punto + palabra), `badge-alergeno`, `tarjeta`, `logo` y `campo-texto`; `precio` sin cambios (un test comprueba su HTML exacto). `DESIGN.md` reescrito y aviso en el `README.md` de las maquetas de que las capturas son del diseño anterior. `npm run build` OK; `php artisan test`: 244 OK. Revisado en el navegador: portada, login y carta del QR en móvil · ¿funcionó?: PENDIENTE DE REVISAR
- **Enlace:** 06325ef · [PR #31](https://github.com/K1rb1-30/ProyectoIA_APPWeb/pull/31)

### Entrada 35
- **Fecha y autor:** 24/11/2026 – Gabriel
- **Herramienta:** Claude Code
- **Objetivo:** Rama 20 (rediseño 2 de 5): aplicar el diseño «Comanda impresa» a las pantallas de cocina y camarero
- **Prompt:** "Sí, empieza" (la rama 2 del plan de rediseño: cocina y camarero)
- **Resultado:** Rama `feature/rediseno-cocina-camarero`, sacada de `feature/rediseno-base`. Rehechos los layouts de cocina (barra de tinta) y camarero (título grande, pestaña activa con raya naranja, globos en naranja) y las vistas `cocina/pedidos`, `camarero/mesas`, `nueva-comanda`, `detalle-mesa`, `avisos`, `cobros` y el parcial `cobro-efectivo`. Solo cambian el HTML y las clases; la lógica de Alpine, los formularios, las rutas y los textos que comprueban los tests son los mismos. En la nueva comanda el título pasa a ser la mesa («Mesa 1») y «Nueva comanda» queda de subtítulo. `php artisan test`: 244 OK; `npm run build` OK; grafo actualizado. Revisado en el navegador con las cuentas de prueba del seeder: mesas, detalle, avisos y comanda (móvil) y tablero de cocina (1280×800, polling con 200). Para eso se ejecutaron en la base de datos de desarrollo 5 migraciones pendientes (cuentas, partes_cuenta, pagos...) · ¿funcionó?: PENDIENTE DE REVISAR
- **Enlace:** 77cadff · [PR #33](https://github.com/K1rb1-30/ProyectoIA_APPWeb/pull/33)

### Entrada 36
- **Fecha y autor:** 24/11/2026 – Gabriel
- **Herramienta:** Claude Code
- **Objetivo:** Rama 21 (rediseño 3 de 5): aplicar el diseño «Comanda impresa» a la portada, el login, las pantallas de autenticación y las páginas legales
- **Prompt:** "Sí, empieza, ya he mergeado la PR de la rama 2"
- **Resultado:** Rama `feature/rediseno-publico` desde `main` actualizado. Rehechos `inicio.blade.php` (título de tres frases a la izquierda y ticket de ejemplo con 31,00 € entre 3 calculado con la regla del céntimo; lista numerada; roles sin iconos; panel en franja oscura), `layouts/publico` (nombre en texto, pie en una línea), `layouts/guest` (panel de tinta sin degradados, brillos ni tarjetas), `auth/login` (título «Acceso del personal», campos sin iconos, botón «Entrar»), las otras 4 vistas de Breeze (h1, sin iconos), `boton-acceso` y `pagina-legal`. Se mantienen los textos y anclas que comprueba `InicioTest` («Pide desde tu mesa», `#como-funciona`, `#cocina-y-sala`, `#panel`). Marcada la rama 20 como mergeada en el plan. `php artisan test`: 244 OK; `npm run build` OK; grafo actualizado. Revisado en el navegador: portada en escritorio (1440) y móvil (375, sin scroll lateral), login en los dos tamaños y privacidad; se redujo el tamaño del título de la portada porque partía «La cocina se entera.» · ¿funcionó?: PENDIENTE DE REVISAR
- **Enlace:** 4447456 · [PR #34](https://github.com/K1rb1-30/ProyectoIA_APPWeb/pull/34)

### Entrada 37
- **Fecha y autor:** 24/11/2026 – Gabriel
- **Herramienta:** Claude Code
- **Objetivo:** Rama 22 (rediseño 4 de 5): aplicar el diseño «Comanda impresa» al panel de administración y al perfil
- **Prompt:** "Sí, empieza" (la rama 4 del plan de rediseño: administración)
- **Resultado:** Rama `feature/rediseno-admin` desde `main` actualizado. Rehechos `layouts/admin` (menú solo texto, título grande, slot opcional `subtitulo`), `admin/panel` y `admin/estadisticas` (ventas de hoy como dato principal y el resto de cifras al lado; secciones con raya en vez de tarjetas), `admin/carta/index` (pestañas y tabla sin caja), `admin/categorias`, `admin/carta/_formulario`, `admin/mesas/index` (cuadrícula con líneas finas y número grande), `admin/alergenos` (lista) y `admin/personal/index` (sin cápsulas de rol). Formularios de mesa y empleado, `layouts/app`, `layouts/navigation` y el perfil sin iconos en campos ni botones. `selector-vista` y `estadisticas-vacio` sin iconos. Colores y letra de Chart.js en `resources/js/estadisticas.js` adaptados al nuevo sistema. Se mantienen los textos que comprueban los tests; «Ticket medio: 12,50 €» queda en el panel como texto solo para lectores de pantalla. `php artisan test`: 244 OK; `npm run build` OK; grafo actualizado. Revisado en el navegador con la cuenta de prueba del admin: panel, carta y mesas a 1440 px y sin scroll lateral a 375 px; alérgenos, estadísticas y perfil comprobados por texto. No se han podido ver los gráficos pintados porque la base de datos de desarrollo no tiene pagos · ¿funcionó?: PENDIENTE DE REVISAR
- **Enlace:** 032725a · [PR #35](https://github.com/K1rb1-30/ProyectoIA_APPWeb/pull/35)

### Entrada 38
- **Fecha y autor:** 24/11/2026 – Gabriel
- **Herramienta:** Claude Code
- **Objetivo:** Rama 23 (rediseño 5 de 5): aplicar el diseño «Comanda impresa» a las pantallas del cliente (QR)
- **Prompt:** "Sí, empieza, ya he mergeado la PR anterior para que puedas continuar"
- **Resultado:** Rama `feature/rediseno-cliente` desde `main` actualizado. Rehechos `layouts/cliente` (mesa en letra de ticket, nombre del restaurante grande solo en la carta, pestaña activa con raya naranja), `cliente/carta` (filas con línea de puntos hasta el precio, alérgenos como texto), `cliente/mi-pedido` (filas, total grande, un solo botón naranja), `cliente/pago-tarjeta`, `cliente/pago-efectivo` y `cliente/pago-completado` (importe como dato principal, justificante con aspecto de ticket). En `cliente/estado-pedido` y `cliente/cuenta` solo se han cambiado clases y quitado iconos y tarjetas; la lógica de Alpine (polling, reparto) es la misma. Se mantienen los textos que comprueban los tests. `php artisan test`: 244 OK; `npm run build` OK; grafo actualizado. Revisado en el navegador a 375 px: carta (añadir platos y barra «Ver pedido»), mi pedido, cuenta de la mesa 3 en los dos modos de reparto y estado sin pedidos. Al revisarlo se arregló que hubiera dos títulos igual de grandes en «Mi pedido» y que «personas» no cupiera en el contador. Las pantallas de pago no se han visto en el navegador (harían falta una cuenta dividida y un pago de Stripe); las cubren los tests · ¿funcionó?: PENDIENTE DE REVISAR
- **Enlace:** a761305 · [PR #36](https://github.com/K1rb1-30/ProyectoIA_APPWeb/pull/36)

### Entrada 39
- **Fecha y autor:** 24/11/2026 – Gabriel
- **Herramienta:** Claude Code
- **Objetivo:** Actualizar la sección «Diseño visual» de `CLAUDE.md` para que mande el nuevo sistema «Comanda impresa» y no las capturas de Stitch
- **Prompt:** "Sí, actualízala"
- **Resultado:** Rama `feature/actualizar-claude-md`, sacada de `feature/rediseno-cliente` (PR #36) para que el registro no choque. En `CLAUDE.md`: manda `DESIGN.md`; las carpetas de maquetas de Stitch quedan como referencia de contenido, no de aspecto; se resumen las normas del rediseño (un botón naranja por pantalla, un dato principal, filas en vez de tarjetas, títulos a la izquierda, punto + palabra, sin iconos decorativos ni degradados, `font-mono` para cifras, sin colores sueltos en las vistas). Sin cambios de código: no hace falta pasar los tests · ¿funcionó?: PENDIENTE DE REVISAR
- **Enlace:** 7c7797a · [PR #37](https://github.com/K1rb1-30/ProyectoIA_APPWeb/pull/37)

### Entrada 40
- **Fecha y autor:** 25/11/2026 – Iván
- **Herramienta:** Claude Code
- **Objetivo:** Revisar lo ya hecho en busca de cosas raras y arreglar las de funcionamiento: la numeración de personas tras pagar al pedir, `/profile` → `/perfil` (tarea P5), el borrado de la propia cuenta y las cuentas del equipo en Personal
- **Prompt:** "Quiero que revisemos todo lo que hay, hay cosas raras [...]" y después "vamos a mirar las cosas raras de funcionamiento, cambiar la ruta y que sea perfil, que solo pueda borrar cuentas el admin del restaurante y nosotros, que no salgamos los 3 admin supremos en la parte de personal y [...] la 2 mira de arreglarla"
- **Resultado:** Rama `feature/arreglos-funcionamiento`. (1) Las partes de los pedidos pagados al pedir ya no tienen número de persona (`partes_cuenta.persona` pasa a ser opcional, migración nueva). Al dividir el resto entre 2 salen «Persona 1» y «Persona 2», no «Persona 2» y «Persona 3». Las vistas y mensajes usan `ParteCuenta::nombre()` y el contador de personas no cuenta esas partes (`Cuenta::numeroDePersonas()`). (2) El perfil está en `/perfil` (rutas `perfil.editar` y `perfil.actualizar`), y `/profile` redirige. (3) Se han quitado la ruta, el método y el formulario para borrar la propia cuenta: las cuentas solo se borran desde Personal. (4) Nuevo scope `User::delPersonal()`: las cuentas que pueden cambiar de restaurante no salen en Personal, dan 404 al editarlas o borrarlas desde otro admin y no cuentan como el admin del restaurante. Hay 5 tests nuevos y 2 se han cambiado. `php artisan test`: 264 OK. La migración se ha probado hacia atrás y hacia delante. Actualizados `docs/03-disseny.md`, `docs/06-proves.md` y `docs/02-requisits.md`. No se ha probado en el navegador. Grafo sin actualizar (graphify no está instalado en este equipo) · ¿funcionó?: PENDIENTE DE REVISAR
- **Enlace:** 96848e6 · b3fe2e3 · df9f3b5 · PENDIENTE (PR)

### Entrada 41
- **Fecha y autor:** 25/11/2026 – Iván
- **Herramienta:** Claude Code
- **Objetivo:** Arreglar el pie del menú lateral del admin, donde el desplegable «Ver como» se salía por la derecha
- **Prompt:** "mira esto podrías arreglarlo?" (con una captura del pie del menú)
- **Resultado:** En la rama `feature/arreglos-funcionamiento`. El menú mide 240 px y no cabían «Ver como» y el desplegable en una fila. El componente `selector-vista` tiene ahora la opción `apilado`: pone la etiqueta encima y el desplegable a todo el ancho, y solo la usa el menú del admin (en los demás layouts sigue igual). «Cerrar sesión» ya no va subrayado, igual que «Perfil». Comprobado en el navegador a 1280 px: el desplegable queda dentro del menú (de 28 a 211 px), igual de ancho que el selector de restaurante. `php artisan test`: 264 OK · ¿funcionó?: PENDIENTE DE REVISAR
- **Enlace:** 4b23783 · PENDIENTE (PR)

### Entrada 42
- **Fecha y autor:** 25/11/2026 – Iván
- **Herramienta:** Claude Code
- **Objetivo:** Arreglar los fallos menores que salieron en la revisión: docs desactualizadas, precio de 0 €, número de una mesa ocupada y rutas de admin desordenadas
- **Prompt:** "Haz ahora estos 3 cambios igual que la anterior" (los «menores» de la revisión) y "¿qué es el verified de las rutas?"
- **Resultado:** Rama `feature/arreglos-menores`, sacada de `main` con la PR #40 ya fusionada. (1) En `docs/02-requisits.md`, RF33, RF34 y RF35 pasan a ✅. En `docs/06-proves.md` se añaden los archivos de tests de las ramas 14 a 18 y del seeder (`PlatosCompartidosTest`, `PanelAdminTest`, `GestionPersonalTest`, `CambiarRestauranteTest` y `PedidosEnCursoSeederTest`) y se actualiza el recuento. El plan de trabajo no se ha tocado. (2) `PlatoRequest` no acepta un precio de 0 € («El precio tiene que ser mayor que 0.»). `MesaController::update` no deja cambiar el número de una mesa ocupada, porque su QR dejaría de funcionar mientras la usan. (3) Las rutas de admin quedan en un solo grupo `['auth', 'rol:admin']`, sin `verified`: no hacía nada, porque `User` no implementa `MustVerifyEmail`. Se ha comprobado con `route:list` que las 80 rutas son las mismas antes y después. Hay 3 tests nuevos. `php artisan test`: 267 OK. Grafo sin actualizar (graphify no está instalado en este equipo) · ¿funcionó?: PENDIENTE DE REVISAR
- **Enlace:** 1358660 · fc97f3b · PENDIENTE (PR)

### Entrada 43
- **Fecha y autor:** 25/11/2026 – Iván
- **Herramienta:** Claude Code
- **Objetivo:** Separar desarrollador y administrador (solo los desarrolladores usan «Ver como») y dejar repartir las unidades de un plato al dividir la cuenta por platos
- **Prompt:** "hay que diferenciar entre desarrollador y administrador [...] que el admin de un restaurante no pueda cambiar la visión y poner ver como cocina [...] a la hora de dividir cuenta cuando pides por ejemplo 2 cafés, deberías poder poner 1 persona paga 1 café y la otra el otro [...] en otra rama diferente haces esos 2 arreglos"
- **Resultado:** Rama `feature/desarrolladores-y-dividir-unidades`, sacada de `feature/arreglos-menores` (PR #41) para que el registro no choque. (1) Nuevo `User::esDesarrollador()`: un admin con la marca `puede_cambiar_restaurante` (Iván, Gabriel y el profesor). Solo los desarrolladores ven «Ver como» y pasan `ComprobarRol` en las pantallas de camarero y cocina. El admin de un restaurante recibe 403 ahí y solo ve administración. Nuevo estado `desarrollador()` en `UserFactory`. Se han adaptado 5 tests que daban por hecho lo contrario y hay 2 nuevos. (2) En «Por platos», las líneas de varias unidades tienen un botón «Separar las N unidades», con un selector de persona por unidad (también «Todos»). `DividirCuentaRequest` convierte cada línea en una lista y `DividirCuenta` acepta una persona para toda la línea o una por unidad, cada unidad a su precio unitario. La suma sigue cuadrando. Hay 3 tests nuevos. Probado en el navegador en la mesa 6 de La Tasca, sin enviar el formulario: separar, elegir la persona 2 para un café, ver la vista previa y juntar. `php artisan test`: 272 OK. Actualizados `02-requisits`, `06-proves`, `README` y `04-installacio`. No se ha cambiado `CLAUDE.md`: su tabla de roles aún dice que el admin ve todo. Grafo sin actualizar (graphify no está instalado en este equipo) · ¿funcionó?: PENDIENTE DE REVISAR
- **Enlace:** 1326c72 · 6e7119b · PENDIENTE (PR)
