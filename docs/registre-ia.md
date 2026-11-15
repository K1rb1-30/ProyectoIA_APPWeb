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
- **Enlace:** PENDIENTE

### Entrada 2
- **Fecha y autor:** 10/11/2026 – Iván
- **Herramienta:** Claude (claude.ai)
- **Objetivo:** Preparar el contexto de Claude Code y bloquear el acceso a secretos
- **Prompt:** "Pásame el CLAUDE.md y los archivos necesarios para que Claude Code tenga todo el contexto, restringiéndole el acceso a .env, contraseñas y claves API"
- **Resultado:** Generó `CLAUDE.md`, `.claude/settings.json` (reglas de bloqueo) y este registro · ¿funcionó?: PENDIENTE DE REVISAR
- **Enlace:** PENDIENTE

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
- **Enlace:** PENDIENTE

### Entrada 5
- **Fecha y autor:** 15/11/2026 – Gabriel
- **Herramienta:** Claude Code + graphify 0.9.109
- **Objetivo:** Instalar la skill graphify y generar un grafo del proyecto en una rama aparte
- **Prompt:** "Instala esta skill (https://github.com/Graphify-Labs/graphify.git) y haz un gráfico del proyecto; hazlo todo en una rama nueva para no interferir en el main"
- **Resultado:** Rama `feature/grafico-proyecto` creada desde `main`. Instalado el paquete `graphifyy` con pip (en el usuario, fuera del proyecto) y registrada la skill en `.claude/skills/graphify/`. Generado el grafo en `graphify-out/` (`graph.html`, `graph.json`, `GRAPH_REPORT.md`): 227 nodos, 238 relaciones, solo análisis local sin IA externa. Añadido `.graphifyignore` para que no lea `.env`, `vendor/`, `node_modules/` ni las maquetas. Activado después el modo siempre activo a petición nuestra: hooks en `.claude/settings.json` (llaman a `python -m graphify hook-guard`, solo dan avisos) y sección "Grafo del proyecto (graphify)" en `CLAUDE.md`, traducida al castellano · ¿funcionó?: PENDIENTE DE REVISAR
- **Enlace:** PENDIENTE

### Entrada 6
- **Fecha y autor:** 15/11/2026 – Gabriel
- **Herramienta:** Claude Code
- **Objetivo:** Empezar la aplicación por el login: instalar Breeze y dejarlo en castellano
- **Prompt:** "Vamos a empezar con la aplicación; dime por dónde empezarías y crea una rama desde main. Empezamos poco a poco" + "Sí, me parece bien" (al plan: Breeze con Blade, traducción y registro público desactivado)
- **Resultado:** Rama `feature/login-breeze`. Instalado `laravel/breeze` 2.4 con Blade y Alpine.js (cambia Tailwind 4 por Tailwind 3). Traducciones en `lang/es.json` y `lang/es/` (`validation`, `auth`, `passwords`, `pagination`); idioma por defecto `es` en `config/app.php`. Quitadas las rutas, el controlador, la vista y el test de registro; añadido `RegistroDesactivadoTest`. Actualizadas las notas de Tailwind en `CLAUDE.md` y en el README del diseño. `npm run build` funciona. `php artisan test`: los 25 tests pasan (al principio fallaban 22 por el entorno: faltaban la contraseña de MySQL y `APP_KEY` en el `.env`). Probado en el navegador: pantalla de login en castellano, mensaje de error con credenciales incorrectas, acceso correcto con el usuario del seeder y `/register` devuelve 404. Añadido `.claude/launch.json` para arrancar el servidor de pruebas · ¿funcionó?: PENDIENTE DE REVISAR
- **Enlace:** PENDIENTE

