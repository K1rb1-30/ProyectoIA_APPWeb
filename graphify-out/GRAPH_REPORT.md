# Graph Report - ProyectoIA_APPWeb  (2026-11-25)

## Corpus Check
- 262 files · ~110,903 words
- Verdict: corpus is large enough that graph structure adds value.
- Unclassified: 11 file(s) not represented in the graph (top: (none) 7, .example 1, .xml 1)

## Summary
- 1628 nodes · 3639 edges · 190 communities (51 shown, 139 thin omitted)
- Extraction: 90% EXTRACTED · 10% INFERRED · 0% AMBIGUOUS · INFERRED: 381 edges (avg confidence: 0.93)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `2db508b7`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- AutopedidoTest
- composer.json
- package.json
- Illuminate\Database\Schema\Blueprint
- CartaPublicaTest
- Propuesta inicial: MesaYa
- bootstrap/app.php
- Illuminate\Http\RedirectResponse
- EfectivoYCierreTest
- Prompts por pantalla
- Dispositivo
- User
- AppServiceProvider
- Illuminate\Http\RedirectResponse
- ComponentesDisenoTest
- Laravel Application
- Cuenta
- PagoStripeTest
- TestCase
- .claude/CLAUDE.md
- DividirCuenta
- edit.blade.php
- layouts.navigation
- PantallaCocinaTest
- Illuminate\Database\Eloquent\Relations\BelongsTo
- PedidosEnCursoSeeder
- EstadisticasTest
- PedidosEnCursoSeederTest.php
- web.php
- GestionCartaTest
- AvisosCamareroTest
- ParteCuenta
- Illuminate\Validation\ValidationException
- Illuminate\Database\Eloquent\Model
- PasswordResetTest.php
- require-dev
- scripts
- [v0.1] – MVP – 22/11/2026
- VentasDemoSeeder.php
- DispositivosTest
- PlatoController
- config
- GestionPersonalTest
- 2. Qué incluye cada rama
- Controller
- RegistroRestauranteRequest
- DividirCuentaTest
- psr-4
- require
- autoload-dev
- extra
- crear.blade.php
- editar.blade.php
- InicioTest
- Illuminate\Http\Request
- Instalación de MesaYa en local
- Illuminate\View\View
- EmailVerificationTest.php
- RegistroDesactivadoTest
- 2026_11_16_000002_add_rol_y_restaurante_id_to_users_table.php
- 2026_11_17_000001_create_categorias_table.php
- Illuminate\Database\Migrations\Migration
- 2026_11_23_000003_add_cuenta_id_to_pedidos_table.php
- Illuminate\Support\Facades\Schema
- PagarAlPedirTest
- Illuminate\Database\Migrations\Migration
- 2026_11_17_000003_create_platos_table.php
- 2026_11_17_000005_create_pedidos_table.php
- EmpleadoRequest
- Registro de uso de IA
- 2026_11_23_000002_create_partes_cuenta_table.php
- 2026_11_23_000004_create_pagos_table.php
- PlatosCompartidosTest
- MesasTest
- camarero.partes.cobro-efectivo
- 1. Requisitos funcionales
- App\Http\Requests\ProfileUpdateRequest
- App\Models\Pedido
- App\Http\Controllers\Camarero\AvisoController
- InicioController.php
- DividirCuentaRequest
- Restaurante
- 1. Seguimiento
- cobro-efectivo.blade.php
- SelectorVistaTest
- PanelAdminTest
- 0001_01_01_000001_create_cache_table.php
- 2026_11_23_000005_add_pedido_id_to_partes_cuenta_table.php
- Illuminate\Foundation\Http\FormRequest
- Mesa
- CobroCamareroTest
- PedidosEnCursoSeederTest
- Admin/DispositivoController.php
- CategoriaController
- ComandasCamareroTest
- MesaYa
- Entrada 9
- Pago
- 3. Rutas
- 2026_11_17_000006_create_lineas_pedido_table.php
- 2026_11_23_000001_create_cuentas_table.php
- PlatoRequest
- CambiarRestauranteTest
- 3. Resto de tests por funcionalidad
- DetalleMesaController
- 0001_01_01_000002_create_jobs_table.php
- 2026_11_24_000001_add_puede_cambiar_restaurante_to_users_table.php
- 2026_11_25_000001_persona_opcional_en_partes_cuenta.php
- 2026_11_25_000005_add_suscripcion_to_restaurantes_table.php
- 2026_11_25_000002_create_dispositivos_table.php

## God Nodes (most connected - your core abstractions)
1. `User` - 98 edges
2. `Restaurante` - 85 edges
3. `TestCase` - 77 edges
4. `Mesa` - 50 edges
5. `Registro de uso de IA` - 48 edges
6. `ParteCuenta` - 46 edges
7. `Pedido` - 43 edges
8. `Controller` - 43 edges
9. `Plato` - 40 edges
10. `CrearPedido` - 38 edges

## Surprising Connections (you probably didn't know these)
- `14. `feature/platos-compartidos` 🟠` --references--> `DividirCuenta`  [INFERRED]
  docs/plan-de-trabajo.md → app/Acciones/DividirCuenta.php
- `3. Decisiones pendientes` --references--> `PasarelaStripe`  [INFERRED]
  docs/plan-de-trabajo.md → app/Servicios/PasarelaStripe.php
- `2. Requisitos no funcionales` --references--> `PerteneceARestaurante`  [INFERRED]
  docs/02-requisits.md → app/Models/Concerns/PerteneceARestaurante.php
- `3. `feature/gestion-carta`` --references--> `AlergenosSeeder`  [INFERRED]
  docs/plan-de-trabajo.md → database/seeders/AlergenosSeeder.php
- `6. `feature/pantalla-cocina`` --references--> `Pedido`  [INFERRED]
  docs/plan-de-trabajo.md → app/Models/Pedido.php

## Import Cycles
- None detected.

## Communities (190 total, 139 thin omitted)

### Community 1 - "composer.json"
Cohesion: 0.22
Nodes (8): description, keywords, license, minimum-stability, name, prefer-stable, $schema, type

### Community 2 - "package.json"
Cohesion: 0.05
Nodes (44): Entrada 21, dependencies, chart.js@^4.6.1, devDependencies, alpinejs@^3.4.2, autoprefixer@^10.4.2, concurrently@^10.0.3, @fontsource/ibm-plex-mono@^5.3.0 (+36 more)

### Community 3 - "Illuminate\Database\Schema\Blueprint"
Cohesion: 0.19
Nodes (9): {closure#1}(), {closure#2}(), {closure#3}(), {closure#1}(), {closure#2}(), {closure#3}(), {closure#4}(), {closure#5}() (+1 more)

### Community 5 - "Propuesta inicial: MesaYa"
Cohesion: 0.14
Nodes (14): 10. Riesgos y cómo los abordamos, 1. La idea en una frase, 2. Problema que resuelve, 3. Usuarios y roles, 4. Funcionalidades, 5. Flujo de la demo, 6. Tecnologías, 7. Reparto del trabajo (+6 more)

### Community 6 - "bootstrap/app.php"
Cohesion: 0.32
Nodes (3): {closure#1}(), {closure#2}(), {closure#3}()

### Community 7 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.14
Nodes (5): AutopedidoController, CartaController, PagoEfectivoController, PagoTarjetaController, Entrada 20

### Community 9 - "Prompts por pantalla"
Cohesion: 0.25
Nodes (7): Admin (escritorio), Camarero (móvil), Cliente (móvil), Cocina (Tablet horizontal), Prompt Maestro, PROMPTS para la IA de diseño Web STITCH, Prompts por pantalla

### Community 10 - "Dispositivo"
Cohesion: 0.10
Nodes (3): DispositivoController, Dispositivo, AccesoCamareroPinTest

### Community 11 - "User"
Cohesion: 0.14
Nodes (3): PersonalController, User, Entrada 33

### Community 13 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.12
Nodes (6): SuscripcionController, {closure#1}(), RegistroRestauranteController, PasarelaStripe, 24. `feature/suscripcion-restaurantes` 🟠, Entrada 47

### Community 14 - "ComponentesDisenoTest"
Cohesion: 0.16
Nodes (3): Entrada 8, ComponentesDisenoTest, LayoutsTest

### Community 17 - "Laravel Application"
Cohesion: 0.50
Nodes (3): Agent Setup, Laravel Application, Prerequisites

### Community 21 - "PagoStripeTest"
Cohesion: 0.07
Nodes (19): {closure#1}(), {closure#1}(), {closure#2}(), {closure#3}(), {closure#4}(), {closure#5}(), {closure#6}(), {closure#7}() (+11 more)

### Community 22 - "TestCase"
Cohesion: 0.08
Nodes (7): AuthenticationTest, PasswordConfirmationTest, PasswordUpdateTest, ExampleTest, ProfileTest, RedireccionTrasLoginTest, TestCase

### Community 30 - "DividirCuenta"
Cohesion: 0.12
Nodes (7): {closure#3}(), DividirCuenta, {closure#3}(), Entrada 22, Entrada 28, ExampleTest, RepartoCuentaTest

### Community 58 - "PantallaCocinaTest"
Cohesion: 0.08
Nodes (17): Comandos útiles, Cómo tienes que trabajar con nosotros, Diseño visual (obligatorio para cualquier vista), Documentación del repo, Funcionalidades y prioridad, Git y GitHub, Grafo del proyecto (graphify), MesaYa — Instrucciones para Claude Code (+9 more)

### Community 59 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.10
Nodes (6): {closure#1}(), {closure#2}(), LineaPedido, {closure#1}(), TODO en castellano (obligatorio), Entrada 19

### Community 62 - "PedidosEnCursoSeeder"
Cohesion: 0.26
Nodes (3): PagarPedido, PedidosEnCursoSeeder, Entrada 31

### Community 65 - "EstadisticasTest"
Cohesion: 0.12
Nodes (5): EstadisticasRestaurante, PanelController, 17. `feature/panel-admin` 🟠, Entrada 29, EstadisticasTest

### Community 66 - "PedidosEnCursoSeederTest.php"
Cohesion: 0.09
Nodes (9): CartaSeeder, DatabaseSeeder, MesasSeeder, RestaurantesSeeder, UsuariosAdminSeeder, 18. `feature/gestion-personal` 🟠, Entrada 10, Entrada 11 (+1 more)

### Community 70 - "ParteCuenta"
Cohesion: 0.18
Nodes (9): {closure#1}(), {closure#3}(), {closure#4}(), {closure#5}(), {closure#6}(), ParteCuenta, Tablas, Entrada 24 (+1 more)

### Community 72 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.23
Nodes (3): Alergeno, {closure#1}(), RegistroDePrueba

### Community 74 - "require-dev"
Cohesion: 0.22
Nodes (9): require-dev, fakerphp/faker, laravel/breeze, laravel/pail, laravel/pao, laravel/pint, mockery/mockery, nunomaduro/collision (+1 more)

### Community 75 - "scripts"
Cohesion: 0.22
Nodes (9): scripts, dev, post-autoload-dump, post-create-project-cmd, post-root-package-install, post-update-cmd, pre-package-uninstall, setup (+1 more)

### Community 76 - "[v0.1] – MVP – 22/11/2026"
Cohesion: 0.22
Nodes (4): Adelantado de la v1.0, Añadido, Changelog, [v0.1] – MVP – 22/11/2026

### Community 77 - "VentasDemoSeeder.php"
Cohesion: 0.24
Nodes (3): {closure#2}(), {closure#1}(), VentasDemoSeeder

### Community 81 - "config"
Cohesion: 0.29
Nodes (7): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, preferred-install, sort-packages

### Community 83 - "2. Qué incluye cada rama"
Cohesion: 0.11
Nodes (18): 10. `feature/dividir-cuenta`, 11. `feature/pago-stripe`, 12. `feature/efectivo-y-cierre`, 13. `feature/estadisticas`, 14. `feature/platos-compartidos` 🟠, 19. `feature/rediseno-base` 🟠, 20. `feature/rediseno-cocina-camarero` 🟠, 22. `feature/rediseno-admin` 🟠 (+10 more)

### Community 84 - "Controller"
Cohesion: 0.09
Nodes (9): EstadisticaController, ConfirmablePasswordController, EmailVerificationNotificationController, EmailVerificationPromptController, {closure#1}(), NewPasswordController, PasswordResetLinkController, VerifyEmailController (+1 more)

### Community 89 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 90 - "require"
Cohesion: 0.33
Nodes (6): require, laravel/framework, laravel/tinker, php, simplesoftwareio/simple-qrcode, stripe/stripe-php

### Community 91 - "autoload-dev"
Cohesion: 0.67
Nodes (3): autoload-dev, psr-4, Tests\\

### Community 92 - "extra"
Cohesion: 0.67
Nodes (3): extra, laravel, dont-discover

### Community 109 - "InicioTest"
Cohesion: 0.20
Nodes (3): 21. `feature/rediseno-publico` 🟠, Entrada 36, InicioTest

### Community 110 - "Illuminate\Http\Request"
Cohesion: 0.15
Nodes (9): DispositivoController, ComprobarRol, ComprobarSuscripcion, IdentificarDispositivo, Roles, 1. `feature/restaurantes-y-roles`, Entrada 13, Entrada 44 (+1 more)

### Community 111 - "Instalación de MesaYa en local"
Cohesion: 0.13
Nodes (14): 1. Requisitos, 2. Descargar el proyecto, 3. Bases de datos, 4. Archivo `.env`, 5. Tablas, datos de prueba y fotos, 6. Arrancar, 7. Problemas frecuentes, 8. Despliegue (+6 more)

### Community 112 - "Illuminate\View\View"
Cohesion: 0.11
Nodes (7): AlergenoController, MesaController, ComandaController, AppLayout, GuestLayout, Entrada 17, Entrada 42

### Community 126 - "Registro de uso de IA"
Cohesion: 0.11
Nodes (18): Entrada 1, Entrada 16, Entrada 2, Entrada 23, Entrada 27, Entrada 3, Entrada 34, Entrada 35 (+10 more)

### Community 138 - "1. Requisitos funcionales"
Cohesion: 0.15
Nodes (12): 1. Requisitos funcionales, 2. Requisitos no funcionales, Acceso, roles y restaurantes, Carta y alérgenos (admin), Cliente por QR (sin login), Comandas, cocina y avisos (personal), Estadísticas (admin), Extras (🟣, solo si sobra tiempo) (+4 more)

### Community 141 - "App\Models\Pedido"
Cohesion: 0.22
Nodes (8): {closure#4}(), CambiarRestauranteController, {closure#1}(), {closure#2}(), {closure#1}(), {closure#2}(), {closure#3}(), CuentaController

### Community 142 - "App\Http\Controllers\Camarero\AvisoController"
Cohesion: 0.21
Nodes (4): AvisoController, {closure#1}(), {closure#2}(), PedidoController

### Community 143 - "InicioController.php"
Cohesion: 0.33
Nodes (4): InicioController, 16. `feature/pagina-inicio` 🟠, Entrada 30, Entrada 32

### Community 149 - "DividirCuentaRequest"
Cohesion: 0.05
Nodes (8): DividirCuentaRequest, CategoriaFactory, MesaFactory, PlatoFactory, RestauranteFactory, UserFactory, Entrada 15, Entrada 43

### Community 151 - "1. Seguimiento"
Cohesion: 0.18
Nodes (10): 1. Seguimiento, 3. Decisiones pendientes, 4. Cómo pedir cada rama a Claude Code, 🟢 MVP (v0.1) — objetivo: 23/11, Plan de trabajo de MesaYa, Reparto propuesto, Semana 4 — hasta el 07/12, 🟠 Siguientes: mejoras propias (Iván, mientras Gabriel hace la 13) (+2 more)

### Community 162 - "Mesa"
Cohesion: 0.15
Nodes (10): CrearPedido, Categoria, PerteneceARestaurante, Mesa, Pedido, Plato, Base de datos, AlergenosSeeder (+2 more)

### Community 168 - "MesaYa"
Cohesion: 0.22
Nodes (9): Cuentas de prueba, Cómo trabajamos, Documentación, Lo que la hace segura y fiable, MesaYa, Puesta en marcha rápida, Qué hace, Tecnologías (+1 more)

### Community 169 - "Entrada 9"
Cohesion: 0.11
Nodes (3): Entrada 9, AislamientoRestaurantesTest, ComprobarRolTest

### Community 170 - "Pago"
Cohesion: 0.14
Nodes (7): CobroController, Pago, 2. Base de datos, Diagrama entidad-relación, Estados y reglas, 15. `feature/pagar-al-pedir` 🟠, Entrada 26

### Community 171 - "3. Rutas"
Cohesion: 0.18
Nodes (10): 1. Arquitectura, 3. Rutas, 4. Estructura de carpetas (lo nuestro), Admin (`auth` + `rol:admin`), Camarero (`dispositivo` + `auth` + `rol:camarero` + `suscripcion`), Cocina (`dispositivo` + `auth` + `rol:cocina` + `suscripcion`), Diseño técnico de MesaYa, Dispositivos (sin login) (+2 more)

### Community 176 - "3. Resto de tests por funcionalidad"
Cohesion: 0.25
Nodes (5): 1. Cómo ejecutarlos, 2. Golden tests, 3. Resto de tests por funcionalidad, 4. Pruebas manuales en el navegador, Pruebas de MesaYa

### Community 178 - "0001_01_01_000002_create_jobs_table.php"
Cohesion: 0.33
Nodes (3): {closure#1}(), {closure#2}(), {closure#3}()

## Knowledge Gaps
- **7 isolated node(s):** `camarero.partes.cobro-efectivo`, `profile.partials.update-profile-information-form`, `profile.partials.update-password-form`, `camarero.partes.cobro-efectivo`, `layouts.navigation` (+2 more)
  These have ≤1 connection - possible missing edges or undocumented components. (Counts symbols only; 594 node(s) total have ≤1 connection when file, concept and rationale nodes are included.)
- **139 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `MesasTest`, `EfectivoYCierreTest`, `Dispositivo`, `App\Http\Requests\ProfileUpdateRequest`, `Illuminate\Http\RedirectResponse`, `InicioController.php`, `Cuenta`, `DividirCuentaRequest`, `Restaurante`, `TestCase`, `PagoStripeTest`, `PanelAdminTest`, `Mesa`, `CobroCamareroTest`, `Admin/DispositivoController.php`, `ComandasCamareroTest`, `Entrada 9`, `Pago`, `CambiarRestauranteTest`, `PantallaCocinaTest`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `PedidosEnCursoSeeder`, `PedidosEnCursoSeederTest.php`, `web.php`, `GestionCartaTest`, `AvisosCamareroTest`, `ParteCuenta`, `Illuminate\Validation\ValidationException`, `Illuminate\Database\Eloquent\Model`, `PasswordResetTest.php`, `DispositivosTest`, `GestionPersonalTest`, `Controller`, `Illuminate\Http\Request`, `Illuminate\View\View`, `EmailVerificationTest.php`, `PagarAlPedirTest`, `EmpleadoRequest`?**
  _High betweenness centrality (0.133) - this node is a cross-community bridge._
- **Are the 7 inferred relationships involving `User` (e.g. with `Base de datos` and `TODO en castellano (obligatorio)`) actually correct?**
  _`User` has 7 INFERRED edges - model-reasoned connections that need verification._
- **What connects `camarero.partes.cobro-efectivo`, `profile.partials.update-profile-information-form`, `profile.partials.update-password-form` to the rest of the system?**
  _7 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `package.json` be split into smaller, more focused modules?**
  _Cohesion score 0.053544494720965306 - nodes in this community are weakly interconnected._
- **Why does `Restaurante` connect `Restaurante` to `AutopedidoTest`, `CartaPublicaTest`, `MesasTest`, `Illuminate\Http\RedirectResponse`, `Dispositivo`, `Illuminate\Http\RedirectResponse`, `App\Models\Pedido`, `Cuenta`, `DividirCuentaRequest`, `TestCase`, `PagoStripeTest`, `PanelAdminTest`, `Mesa`, `PedidosEnCursoSeederTest`, `ComandasCamareroTest`, `Entrada 9`, `CambiarRestauranteTest`, `PantallaCocinaTest`, `PedidosEnCursoSeeder`, `EstadisticasTest`, `PedidosEnCursoSeederTest.php`, `AvisosCamareroTest`, `ParteCuenta`, `Illuminate\Database\Eloquent\Model`, `VentasDemoSeeder.php`, `DispositivosTest`, `GestionPersonalTest`, `Illuminate\Http\Request`?**
  _High betweenness centrality (0.095) - this node is a cross-community bridge._
- **Are the 4 inferred relationships involving `Restaurante` (e.g. with `Base de datos` and `Tablas`) actually correct?**
  _`Restaurante` has 4 INFERRED edges - model-reasoned connections that need verification._
- **Should `Propuesta inicial: MesaYa` be split into smaller, more focused modules?**
  _Cohesion score 0.14285714285714285 - nodes in this community are weakly interconnected._