# Graph Report - elastic-khorana-a60b17  (2026-11-26)

## Corpus Check
- 264 files · ~114,535 words
- Verdict: corpus is large enough that graph structure adds value.
- Unclassified: 12 file(s) not represented in the graph (top: (none) 8, .example 1, .xml 1)

## Summary
- 1670 nodes · 3751 edges · 197 communities (54 shown, 143 thin omitted)
- Extraction: 89% EXTRACTED · 11% INFERRED · 0% AMBIGUOUS · INFERRED: 396 edges (avg confidence: 0.93)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `35701a75`
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
- StripeWebhookTest
- Prompts por pantalla
- Dispositivo
- App\Http\Controllers\Admin\DispositivoController
- AppServiceProvider
- PasarelaStripe
- ComponentesDisenoTest
- Laravel Application
- ParteCuenta
- PagoStripeTest
- User
- .claude/CLAUDE.md
- DividirCuenta
- edit.blade.php
- layouts.navigation
- PantallaCocinaTest
- Pago
- PedidosEnCursoSeeder
- EstadisticasTest
- Illuminate\Database\Seeder
- web.php
- GestionCartaTest
- Illuminate\Database\Eloquent\Factories\Factory
- EfectivoYCierreTest
- Illuminate\Validation\ValidationException
- Mesa
- PasswordResetTest.php
- require-dev
- scripts
- [v0.1] – MVP – 22/11/2026
- LineaPedido
- DispositivosTest
- DividirCuentaRequest
- config
- GestionPersonalTest
- 2. Qué incluye cada rama
- Controller
- RegistroRestauranteRequest
- ProfileTest
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
- ComprobarRol
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
- RedireccionTrasLoginTest
- App\Models\Mesa
- App\Http\Controllers\Cocina\PedidoController
- InicioController.php
- CambiarRestauranteTest
- PanelAdminTest
- 1. Seguimiento
- cobro-efectivo.blade.php
- SelectorVistaTest
- ComandasCamareroTest
- 0001_01_01_000001_create_cache_table.php
- 2026_11_23_000005_add_pedido_id_to_partes_cuenta_table.php
- ComandaRequest
- Restaurante
- CobroCamareroTest
- PedidosEnCursoSeederTest
- Admin/DispositivoController.php
- App\Http\Controllers\Admin\MesaController
- MesaYa
- AislamientoRestaurantesTest
- PlatoRequest
- 3. Rutas
- 2026_11_17_000006_create_lineas_pedido_table.php
- 2026_11_23_000001_create_cuentas_table.php
- PlatoRequest
- Entrada 9
- 3. Resto de tests por funcionalidad
- AvisosCamareroTest
- 0001_01_01_000002_create_jobs_table.php
- 2026_11_24_000001_add_puede_cambiar_restaurante_to_users_table.php
- 2026_11_25_000001_persona_opcional_en_partes_cuenta.php
- 2026_11_25_000005_add_suscripcion_to_restaurantes_table.php
- 2026_11_25_000002_create_dispositivos_table.php
- ComprobarRolTest
- PedidosEnCursoSeederTest.php
- Alergeno
- PlatoFactory.php
- README.md
- PanelController
- PedidoClienteRequest

## God Nodes (most connected - your core abstractions)
1. `User` - 95 edges
2. `Restaurante` - 85 edges
3. `TestCase` - 75 edges
4. `Registro de uso de IA` - 51 edges
5. `Mesa` - 50 edges
6. `ParteCuenta` - 44 edges
7. `Controller` - 44 edges
8. `Pedido` - 43 edges
9. `Plato` - 40 edges
10. `CrearPedido` - 38 edges

## Surprising Connections (you probably didn't know these)
- `6. `feature/pantalla-cocina`` --references--> `Pedido`  [INFERRED]
  docs/plan-de-trabajo.md → app/Models/Pedido.php
- `14. `feature/platos-compartidos` 🟠` --references--> `DividirCuenta`  [INFERRED]
  docs/plan-de-trabajo.md → app/Acciones/DividirCuenta.php
- `3. Decisiones pendientes` --references--> `PasarelaStripe`  [INFERRED]
  docs/plan-de-trabajo.md → app/Servicios/PasarelaStripe.php
- `2. Requisitos no funcionales` --references--> `PerteneceARestaurante`  [INFERRED]
  docs/02-requisits.md → app/Models/Concerns/PerteneceARestaurante.php
- `5. Tablas, datos de prueba y fotos` --references--> `VentasDemoSeeder`  [INFERRED]
  docs/04-installacio.md → database/seeders/VentasDemoSeeder.php

## Import Cycles
- None detected.

## Communities (197 total, 143 thin omitted)

### Community 0 - "AutopedidoTest"
Cohesion: 0.13
Nodes (6): Entrada 20, Entrada 21, avisosCamarero(), carrito(), PasarelaStripeFalsa, AutopedidoTest

### Community 1 - "composer.json"
Cohesion: 0.22
Nodes (8): description, keywords, license, minimum-stability, name, prefer-stable, $schema, type

### Community 2 - "package.json"
Cohesion: 0.06
Nodes (41): dependencies, chart.js@^4.6.1, devDependencies, alpinejs@^3.4.2, autoprefixer@^10.4.2, concurrently@^10.0.3, @fontsource/ibm-plex-mono@^5.3.0, @fontsource-variable/schibsted-grotesk@^5.3.0 (+33 more)

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
Cohesion: 0.18
Nodes (3): AutopedidoController, PagoEfectivoController, PagoTarjetaController

### Community 9 - "Prompts por pantalla"
Cohesion: 0.25
Nodes (7): Admin (escritorio), Camarero (móvil), Cliente (móvil), Cocina (Tablet horizontal), Prompt Maestro, PROMPTS para la IA de diseño Web STITCH, Prompts por pantalla

### Community 13 - "PasarelaStripe"
Cohesion: 0.06
Nodes (11): {closure#1}(), StripeWebhookController, PasarelaStripe, 24. `feature/suscripcion-restaurantes` 🟠, 24b. `feature/webhook-suscripcion` 🟠, 24c. `feature/reactivar-suscripcion-impagada` 🟠, Entrada 24, Entrada 47 (+3 more)

### Community 14 - "ComponentesDisenoTest"
Cohesion: 0.16
Nodes (3): Entrada 8, ComponentesDisenoTest, LayoutsTest

### Community 17 - "Laravel Application"
Cohesion: 0.50
Nodes (3): Agent Setup, Laravel Application, Prerequisites

### Community 19 - "ParteCuenta"
Cohesion: 0.13
Nodes (11): Cuenta, Pago, ParteCuenta, Base de datos, 2. Base de datos, Diagrama entidad-relación, Estados y reglas, Tablas (+3 more)

### Community 21 - "PagoStripeTest"
Cohesion: 0.10
Nodes (21): {closure#1}(), {closure#1}(), {closure#2}(), {closure#3}(), {closure#4}(), {closure#5}(), {closure#6}(), {closure#7}() (+13 more)

### Community 22 - "User"
Cohesion: 0.08
Nodes (7): User, AuthenticationTest, EmailVerificationTest, PasswordConfirmationTest, PasswordUpdateTest, ExampleTest, TestCase

### Community 30 - "DividirCuenta"
Cohesion: 0.10
Nodes (6): {closure#3}(), Entrada 22, Entrada 28, DividirCuentaTest, ExampleTest, RepartoCuentaTest

### Community 58 - "PantallaCocinaTest"
Cohesion: 0.07
Nodes (19): {closure#1}(), Comandos útiles, Cómo tienes que trabajar con nosotros, Diseño visual (obligatorio para cualquier vista), Documentación del repo, Funcionalidades y prioridad, Git y GitHub, Grafo del proyecto (graphify) (+11 more)

### Community 59 - "Pago"
Cohesion: 0.09
Nodes (5): {closure#1}(), {closure#1}(), {closure#3}(), {closure#4}(), {closure#6}()

### Community 62 - "PedidosEnCursoSeeder"
Cohesion: 0.10
Nodes (12): {closure#4}(), {closure#3}(), PagarPedido, {closure#1}(), {closure#2}(), {closure#1}(), {closure#2}(), {closure#3}() (+4 more)

### Community 65 - "EstadisticasTest"
Cohesion: 0.14
Nodes (4): EstadisticasRestaurante, EstadisticaController, Entrada 29, EstadisticasTest

### Community 66 - "Illuminate\Database\Seeder"
Cohesion: 0.16
Nodes (6): DatabaseSeeder, MesasSeeder, UsuariosAdminSeeder, Entrada 10, Entrada 11, UsuariosAdminSeederTest

### Community 72 - "Mesa"
Cohesion: 0.15
Nodes (7): CrearPedido, ComandaController, Categoria, Mesa, Pedido, Decisiones importantes, Entrada 17

### Community 74 - "require-dev"
Cohesion: 0.22
Nodes (9): require-dev, fakerphp/faker, laravel/breeze, laravel/pail, laravel/pao, laravel/pint, mockery/mockery, nunomaduro/collision (+1 more)

### Community 75 - "scripts"
Cohesion: 0.22
Nodes (9): scripts, dev, post-autoload-dump, post-create-project-cmd, post-root-package-install, post-update-cmd, pre-package-uninstall, setup (+1 more)

### Community 76 - "[v0.1] – MVP – 22/11/2026"
Cohesion: 0.40
Nodes (4): Adelantado de la v1.0, Añadido, Changelog, [v0.1] – MVP – 22/11/2026

### Community 77 - "LineaPedido"
Cohesion: 0.12
Nodes (5): {closure#2}(), {closure#2}(), LineaPedido, {closure#1}(), VentasDemoSeeder

### Community 81 - "config"
Cohesion: 0.29
Nodes (7): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, preferred-install, sort-packages

### Community 83 - "2. Qué incluye cada rama"
Cohesion: 0.12
Nodes (17): 10. `feature/dividir-cuenta`, 11. `feature/pago-stripe`, 12. `feature/efectivo-y-cierre`, 13. `feature/estadisticas`, 14. `feature/platos-compartidos` 🟠, 19. `feature/rediseno-base` 🟠, 20. `feature/rediseno-cocina-camarero` 🟠, 22. `feature/rediseno-admin` 🟠 (+9 more)

### Community 84 - "Controller"
Cohesion: 0.06
Nodes (11): AlergenoController, AuthenticatedSessionController, ConfirmablePasswordController, EmailVerificationNotificationController, EmailVerificationPromptController, {closure#1}(), PasswordController, PasswordResetLinkController (+3 more)

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
Cohesion: 0.16
Nodes (3): SuscripcionController, NewPasswordController, DispositivoController

### Community 111 - "Instalación de MesaYa en local"
Cohesion: 0.12
Nodes (16): 1. Requisitos, 2. Descargar el proyecto, 3. Bases de datos, 4. Archivo `.env`, 5. Tablas, datos de prueba y fotos, 6. Arrancar, 7. Problemas frecuentes, 8. Despliegue (+8 more)

### Community 112 - "Illuminate\View\View"
Cohesion: 0.13
Nodes (4): MesaController, CartaController, AppLayout, GuestLayout

### Community 114 - "ComprobarRol"
Cohesion: 0.21
Nodes (7): ComprobarRol, ComprobarSuscripcion, IdentificarDispositivo, Roles, 1. `feature/restaurantes-y-roles`, Entrada 13, Entrada 44

### Community 125 - "EmpleadoRequest"
Cohesion: 0.15
Nodes (6): PersonalController, EmpleadoRequest, RestaurantesSeeder, 18. `feature/gestion-personal` 🟠, Entrada 33, Entrada 46

### Community 126 - "Registro de uso de IA"
Cohesion: 0.11
Nodes (18): Entrada 1, Entrada 16, Entrada 2, Entrada 23, Entrada 27, Entrada 3, Entrada 34, Entrada 35 (+10 more)

### Community 138 - "1. Requisitos funcionales"
Cohesion: 0.20
Nodes (10): 1. Requisitos funcionales, Acceso, roles y restaurantes, Carta y alérgenos (admin), Cliente por QR (sin login), Comandas, cocina y avisos (personal), Estadísticas (admin), Extras (🟣, solo si sobra tiempo), Mesas y QR (admin) (+2 more)

### Community 141 - "App\Models\Mesa"
Cohesion: 0.16
Nodes (5): DividirCuenta, CambiarRestauranteController, CobroController, DetalleMesaController, CuentaController

### Community 142 - "App\Http\Controllers\Cocina\PedidoController"
Cohesion: 0.21
Nodes (4): AvisoController, {closure#1}(), {closure#2}(), PedidoController

### Community 143 - "InicioController.php"
Cohesion: 0.22
Nodes (4): InicioController, 16. `feature/pagina-inicio` 🟠, Entrada 30, Entrada 32

### Community 151 - "1. Seguimiento"
Cohesion: 0.18
Nodes (10): 1. Seguimiento, 3. Decisiones pendientes, 4. Cómo pedir cada rama a Claude Code, 🟢 MVP (v0.1) — objetivo: 23/11, Plan de trabajo de MesaYa, Reparto propuesto, Semana 4 — hasta el 07/12, 🟠 Siguientes: mejoras propias (Iván, mientras Gabriel hace la 13) (+2 more)

### Community 162 - "Restaurante"
Cohesion: 0.14
Nodes (3): PerteneceARestaurante, Restaurante, RegistroDePrueba

### Community 166 - "App\Http\Controllers\Admin\MesaController"
Cohesion: 0.14
Nodes (3): CategoriaController, ProfileController, CategoriaRequest

### Community 168 - "MesaYa"
Cohesion: 0.22
Nodes (9): Cuentas de prueba, Cómo trabajamos, Documentación, Lo que la hace segura y fiable, MesaYa, Puesta en marcha rápida, Qué hace, Tecnologías (+1 more)

### Community 169 - "AislamientoRestaurantesTest"
Cohesion: 0.17
Nodes (4): MesaFactory, Entrada 15, Entrada 18, AislamientoRestaurantesTest

### Community 171 - "3. Rutas"
Cohesion: 0.18
Nodes (10): 1. Arquitectura, 3. Rutas, 4. Estructura de carpetas (lo nuestro), Admin (`auth` + `rol:admin`), Camarero (`dispositivo` + `auth` + `rol:camarero` + `suscripcion`), Cocina (`dispositivo` + `auth` + `rol:cocina` + `suscripcion`), Diseño técnico de MesaYa, Dispositivos (sin login) (+2 more)

### Community 174 - "PlatoRequest"
Cohesion: 0.29
Nodes (3): PlatoController, Plato, Entrada 12

### Community 175 - "Entrada 9"
Cohesion: 0.39
Nodes (3): UserFactory, Entrada 43, Entrada 9

### Community 176 - "3. Resto de tests por funcionalidad"
Cohesion: 0.25
Nodes (5): 1. Cómo ejecutarlos, 2. Golden tests, 3. Resto de tests por funcionalidad, 4. Pruebas manuales en el navegador, Pruebas de MesaYa

### Community 178 - "0001_01_01_000002_create_jobs_table.php"
Cohesion: 0.33
Nodes (3): {closure#1}(), {closure#2}(), {closure#3}()

### Community 192 - "Alergeno"
Cohesion: 0.29
Nodes (3): Alergeno, AlergenosSeeder, 3. `feature/gestion-carta`

## Knowledge Gaps
- **7 isolated node(s):** `camarero.partes.cobro-efectivo`, `camarero.partes.cobro-efectivo`, `profile.partials.update-password-form`, `profile.partials.update-profile-information-form`, `layouts.navigation` (+2 more)
  These have ≤1 connection - possible missing edges or undocumented components. (Counts symbols only; 599 node(s) total have ≤1 connection when file, concept and rationale nodes are included.)
- **143 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `MesasTest`, `StripeWebhookTest`, `Dispositivo`, `RedireccionTrasLoginTest`, `InicioController.php`, `ParteCuenta`, `CambiarRestauranteTest`, `PanelAdminTest`, `ComandasCamareroTest`, `Restaurante`, `CobroCamareroTest`, `Admin/DispositivoController.php`, `App\Http\Controllers\Admin\MesaController`, `UserFactory.php`, `PlatoRequest`, `Entrada 9`, `AvisosCamareroTest`, `PantallaCocinaTest`, `Pago`, `PedidosEnCursoSeeder`, `web.php`, `GestionCartaTest`, `EfectivoYCierreTest`, `Mesa`, `PasswordResetTest.php`, `LineaPedido`, `DispositivosTest`, `GestionPersonalTest`, `Controller`, `RegistroRestauranteRequest`, `ComprobarRol`, `PagarAlPedirTest`, `EmpleadoRequest`?**
  _High betweenness centrality (0.143) - this node is a cross-community bridge._
- **Are the 7 inferred relationships involving `User` (e.g. with `Base de datos` and `TODO en castellano (obligatorio)`) actually correct?**
  _`User` has 7 INFERRED edges - model-reasoned connections that need verification._
- **What connects `camarero.partes.cobro-efectivo`, `camarero.partes.cobro-efectivo`, `profile.partials.update-password-form` to the rest of the system?**
  _7 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `AutopedidoTest` be split into smaller, more focused modules?**
  _Cohesion score 0.13054187192118227 - nodes in this community are weakly interconnected._
- **Why does `Restaurante` connect `Restaurante` to `AutopedidoTest`, `CartaPublicaTest`, `MesasTest`, `StripeWebhookTest`, `Dispositivo`, `PasarelaStripe`, `App\Models\Mesa`, `ParteCuenta`, `CambiarRestauranteTest`, `User`, `PagoStripeTest`, `PanelAdminTest`, `ComandasCamareroTest`, `PedidosEnCursoSeederTest`, `UserFactory.php`, `Entrada 9`, `AvisosCamareroTest`, `PantallaCocinaTest`, `Pago`, `PedidosEnCursoSeeder`, `PedidosEnCursoSeederTest.php`, `EstadisticasTest`, `Illuminate\Database\Seeder`, `web.php`, `Illuminate\Database\Eloquent\Factories\Factory`, `Mesa`, `LineaPedido`, `DispositivosTest`, `GestionPersonalTest`, `Illuminate\Http\Request`, `Illuminate\View\View`, `ComprobarRol`?**
  _High betweenness centrality (0.098) - this node is a cross-community bridge._
- **Are the 4 inferred relationships involving `Restaurante` (e.g. with `Base de datos` and `Tablas`) actually correct?**
  _`Restaurante` has 4 INFERRED edges - model-reasoned connections that need verification._
- **Should `package.json` be split into smaller, more focused modules?**
  _Cohesion score 0.06086956521739131 - nodes in this community are weakly interconnected._