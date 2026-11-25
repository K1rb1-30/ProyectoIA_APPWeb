# Graph Report - ProyectoIA_APPWeb  (2026-11-26)

## Corpus Check
- 264 files · ~112,880 words
- Verdict: corpus is large enough that graph structure adds value.
- Unclassified: 11 file(s) not represented in the graph (top: (none) 7, .example 1, .xml 1)

## Summary
- 1657 nodes · 3726 edges · 184 communities (53 shown, 131 thin omitted)
- Extraction: 90% EXTRACTED · 10% INFERRED · 0% AMBIGUOUS · INFERRED: 391 edges (avg confidence: 0.93)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `984d088a`
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
- Illuminate\Http\RedirectResponse
- AppServiceProvider
- Illuminate\Http\RedirectResponse
- ComponentesDisenoTest
- Laravel Application
- Cuenta
- PagoStripeTest
- User
- .claude/CLAUDE.md
- DividirCuenta
- edit.blade.php
- layouts.navigation
- MesaYa — Instrucciones para Claude Code
- Pago
- PedidosEnCursoSeeder
- EstadisticasTest
- PedidosEnCursoSeederTest.php
- GestionCartaTest
- Illuminate\Database\Eloquent\Factories\Factory
- ParteCuenta
- Illuminate\Validation\ValidationException
- App\Http\Controllers\Cliente\AutopedidoController
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
- CrearPedido
- MesasTest
- camarero.partes.cobro-efectivo
- 1. Requisitos funcionales
- RedireccionTrasLoginTest
- App\Models\Pedido
- Pedido
- InicioController.php
- CambiarRestauranteTest
- 1. Seguimiento
- cobro-efectivo.blade.php
- SelectorVistaTest
- 0001_01_01_000001_create_cache_table.php
- 2026_11_23_000005_add_pedido_id_to_partes_cuenta_table.php
- Illuminate\Foundation\Http\FormRequest
- Restaurante
- CobroCamareroTest
- PedidosEnCursoSeederTest
- Admin/DispositivoController.php
- CategoriaController
- MesaYa
- AislamientoRestaurantesTest
- 3. Rutas
- 2026_11_17_000006_create_lineas_pedido_table.php
- 2026_11_23_000001_create_cuentas_table.php
- PlatoRequest
- 3. Resto de tests por funcionalidad
- 0001_01_01_000002_create_jobs_table.php
- 2026_11_24_000001_add_puede_cambiar_restaurante_to_users_table.php
- 2026_11_25_000001_persona_opcional_en_partes_cuenta.php
- 2026_11_25_000005_add_suscripcion_to_restaurantes_table.php
- 2026_11_25_000002_create_dispositivos_table.php

## God Nodes (most connected - your core abstractions)
1. `User` - 97 edges
2. `Restaurante` - 90 edges
3. `TestCase` - 77 edges
4. `Mesa` - 50 edges
5. `Registro de uso de IA` - 49 edges
6. `ParteCuenta` - 44 edges
7. `Controller` - 44 edges
8. `Pedido` - 43 edges
9. `Plato` - 40 edges
10. `PasarelaStripe` - 38 edges

## Surprising Connections (you probably didn't know these)
- `3. Decisiones pendientes` --references--> `PasarelaStripe`  [INFERRED]
  docs/plan-de-trabajo.md → app/Servicios/PasarelaStripe.php
- `3. `feature/gestion-carta`` --references--> `AlergenosSeeder`  [INFERRED]
  docs/plan-de-trabajo.md → database/seeders/AlergenosSeeder.php
- `6. `feature/pantalla-cocina`` --references--> `Pedido`  [INFERRED]
  docs/plan-de-trabajo.md → app/Models/Pedido.php
- `14. `feature/platos-compartidos` 🟠` --references--> `DividirCuenta`  [INFERRED]
  docs/plan-de-trabajo.md → app/Acciones/DividirCuenta.php
- `2. Requisitos no funcionales` --references--> `PerteneceARestaurante`  [INFERRED]
  docs/02-requisits.md → app/Models/Concerns/PerteneceARestaurante.php

## Import Cycles
- None detected.

## Communities (184 total, 131 thin omitted)

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
Cohesion: 0.16
Nodes (4): AutopedidoController, PagoEfectivoController, PagoTarjetaController, Entrada 20

### Community 8 - "StripeWebhookTest"
Cohesion: 0.12
Nodes (4): StripeWebhookController, 24b. `feature/webhook-suscripcion` 🟠, Entrada 48, StripeWebhookTest

### Community 9 - "Prompts por pantalla"
Cohesion: 0.25
Nodes (7): Admin (escritorio), Camarero (móvil), Cliente (móvil), Cocina (Tablet horizontal), Prompt Maestro, PROMPTS para la IA de diseño Web STITCH, Prompts por pantalla

### Community 11 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.12
Nodes (3): CambiarRestauranteController, DispositivoController, PersonalController

### Community 13 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.18
Nodes (6): {closure#1}(), {closure#1}(), RegistroRestauranteController, PasarelaStripe, 24. `feature/suscripcion-restaurantes` 🟠, Entrada 47

### Community 14 - "ComponentesDisenoTest"
Cohesion: 0.16
Nodes (3): Entrada 8, ComponentesDisenoTest, LayoutsTest

### Community 17 - "Laravel Application"
Cohesion: 0.50
Nodes (3): Agent Setup, Laravel Application, Prerequisites

### Community 21 - "PagoStripeTest"
Cohesion: 0.07
Nodes (19): {closure#1}(), {closure#1}(), {closure#2}(), {closure#3}(), {closure#4}(), {closure#5}(), {closure#6}(), {closure#7}() (+11 more)

### Community 22 - "User"
Cohesion: 0.10
Nodes (7): User, Entrada 33, AuthenticationTest, PasswordConfirmationTest, PasswordUpdateTest, ExampleTest, TestCase

### Community 30 - "DividirCuenta"
Cohesion: 0.10
Nodes (6): {closure#3}(), Entrada 22, Entrada 28, DividirCuentaTest, ExampleTest, RepartoCuentaTest

### Community 58 - "MesaYa — Instrucciones para Claude Code"
Cohesion: 0.06
Nodes (24): ComprobarRol, ComprobarSuscripcion, IdentificarDispositivo, Comandos útiles, Cómo tienes que trabajar con nosotros, Diseño visual (obligatorio para cualquier vista), Documentación del repo, Funcionalidades y prioridad (+16 more)

### Community 62 - "PedidosEnCursoSeeder"
Cohesion: 0.23
Nodes (5): {closure#3}(), PagarPedido, TODO en castellano (obligatorio), PedidosEnCursoSeeder, Entrada 31

### Community 65 - "EstadisticasTest"
Cohesion: 0.07
Nodes (7): EstadisticasRestaurante, EstadisticaController, PanelController, 17. `feature/panel-admin` 🟠, Entrada 29, EstadisticasTest, PanelAdminTest

### Community 66 - "PedidosEnCursoSeederTest.php"
Cohesion: 0.09
Nodes (10): AlergenosSeeder, CartaSeeder, DatabaseSeeder, MesasSeeder, RestaurantesSeeder, UsuariosAdminSeeder, 18. `feature/gestion-personal` 🟠, Entrada 10 (+2 more)

### Community 69 - "Illuminate\Database\Eloquent\Factories\Factory"
Cohesion: 0.15
Nodes (3): CategoriaFactory, PlatoFactory, RestauranteFactory

### Community 70 - "ParteCuenta"
Cohesion: 0.07
Nodes (14): DividirCuenta, CobroController, DetalleMesaController, CuentaController, {closure#3}(), {closure#4}(), {closure#5}(), {closure#6}() (+6 more)

### Community 72 - "App\Http\Controllers\Cliente\AutopedidoController"
Cohesion: 0.16
Nodes (3): CartaController, {closure#2}(), Entrada 18

### Community 74 - "require-dev"
Cohesion: 0.22
Nodes (9): require-dev, fakerphp/faker, laravel/breeze, laravel/pail, laravel/pao, laravel/pint, mockery/mockery, nunomaduro/collision (+1 more)

### Community 75 - "scripts"
Cohesion: 0.22
Nodes (9): scripts, dev, post-autoload-dump, post-create-project-cmd, post-root-package-install, post-update-cmd, pre-package-uninstall, setup (+1 more)

### Community 76 - "[v0.1] – MVP – 22/11/2026"
Cohesion: 0.22
Nodes (4): Adelantado de la v1.0, Añadido, Changelog, [v0.1] – MVP – 22/11/2026

### Community 77 - "LineaPedido"
Cohesion: 0.16
Nodes (5): {closure#2}(), LineaPedido, {closure#1}(), {closure#1}(), VentasDemoSeeder

### Community 81 - "config"
Cohesion: 0.29
Nodes (7): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, preferred-install, sort-packages

### Community 83 - "2. Qué incluye cada rama"
Cohesion: 0.11
Nodes (19): 10. `feature/dividir-cuenta`, 11. `feature/pago-stripe`, 12. `feature/efectivo-y-cierre`, 13. `feature/estadisticas`, 14. `feature/platos-compartidos` 🟠, 15. `feature/pagar-al-pedir` 🟠, 19. `feature/rediseno-base` 🟠, 20. `feature/rediseno-cocina-camarero` 🟠 (+11 more)

### Community 84 - "Controller"
Cohesion: 0.07
Nodes (10): AlergenoController, AuthenticatedSessionController, ConfirmablePasswordController, EmailVerificationNotificationController, EmailVerificationPromptController, {closure#1}(), PasswordController, PasswordResetLinkController (+2 more)

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
Cohesion: 0.20
Nodes (3): SuscripcionController, NewPasswordController, DispositivoController

### Community 111 - "Instalación de MesaYa en local"
Cohesion: 0.13
Nodes (14): 1. Requisitos, 2. Descargar el proyecto, 3. Bases de datos, 4. Archivo `.env`, 5. Tablas, datos de prueba y fotos, 6. Arrancar, 7. Problemas frecuentes, 8. Despliegue (+6 more)

### Community 112 - "Illuminate\View\View"
Cohesion: 0.11
Nodes (4): MesaController, ProfileController, AppLayout, GuestLayout

### Community 126 - "Registro de uso de IA"
Cohesion: 0.11
Nodes (18): Entrada 1, Entrada 16, Entrada 2, Entrada 23, Entrada 27, Entrada 3, Entrada 34, Entrada 35 (+10 more)

### Community 129 - "CrearPedido"
Cohesion: 0.11
Nodes (5): CrearPedido, ComandaController, Entrada 17, ComandasCamareroTest, PlatosCompartidosTest

### Community 138 - "1. Requisitos funcionales"
Cohesion: 0.15
Nodes (12): 1. Requisitos funcionales, 2. Requisitos no funcionales, Acceso, roles y restaurantes, Carta y alérgenos (admin), Cliente por QR (sin login), Comandas, cocina y avisos (personal), Estadísticas (admin), Extras (🟣, solo si sobra tiempo) (+4 more)

### Community 141 - "App\Models\Pedido"
Cohesion: 0.23
Nodes (6): {closure#4}(), {closure#1}(), {closure#2}(), {closure#1}(), {closure#2}(), {closure#3}()

### Community 142 - "Pedido"
Cohesion: 0.06
Nodes (13): AvisoController, {closure#1}(), {closure#2}(), {closure#2}(), {closure#1}(), PedidoController, Pedido, Entrada 19 (+5 more)

### Community 143 - "InicioController.php"
Cohesion: 0.22
Nodes (4): InicioController, 16. `feature/pagina-inicio` 🟠, Entrada 30, Entrada 32

### Community 149 - "CambiarRestauranteTest"
Cohesion: 0.09
Nodes (3): UserFactory, Entrada 43, CambiarRestauranteTest

### Community 151 - "1. Seguimiento"
Cohesion: 0.18
Nodes (10): 1. Seguimiento, 3. Decisiones pendientes, 4. Cómo pedir cada rama a Claude Code, 🟢 MVP (v0.1) — objetivo: 23/11, Plan de trabajo de MesaYa, Reparto propuesto, Semana 4 — hasta el 07/12, 🟠 Siguientes: mejoras propias (Iván, mientras Gabriel hace la 13) (+2 more)

### Community 160 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.15
Nodes (3): ComandaRequest, PedidoClienteRequest, ProfileUpdateRequest

### Community 162 - "Restaurante"
Cohesion: 0.12
Nodes (12): Alergeno, Categoria, PerteneceARestaurante, Mesa, Plato, Restaurante, Base de datos, Decisiones importantes (+4 more)

### Community 168 - "MesaYa"
Cohesion: 0.22
Nodes (9): Cuentas de prueba, Cómo trabajamos, Documentación, Lo que la hace segura y fiable, MesaYa, Puesta en marcha rápida, Qué hace, Tecnologías (+1 more)

### Community 169 - "AislamientoRestaurantesTest"
Cohesion: 0.20
Nodes (3): MesaFactory, Entrada 15, AislamientoRestaurantesTest

### Community 171 - "3. Rutas"
Cohesion: 0.15
Nodes (12): 1. Arquitectura, 2. Base de datos, 3. Rutas, 4. Estructura de carpetas (lo nuestro), Admin (`auth` + `rol:admin`), Camarero (`dispositivo` + `auth` + `rol:camarero` + `suscripcion`), Cocina (`dispositivo` + `auth` + `rol:cocina` + `suscripcion`), Diagrama entidad-relación (+4 more)

### Community 174 - "PlatoRequest"
Cohesion: 0.20
Nodes (3): PlatoController, PlatoRequest, Entrada 42

### Community 176 - "3. Resto de tests por funcionalidad"
Cohesion: 0.25
Nodes (5): 1. Cómo ejecutarlos, 2. Golden tests, 3. Resto de tests por funcionalidad, 4. Pruebas manuales en el navegador, Pruebas de MesaYa

### Community 178 - "0001_01_01_000002_create_jobs_table.php"
Cohesion: 0.33
Nodes (3): {closure#1}(), {closure#2}(), {closure#3}()

## Knowledge Gaps
- **7 isolated node(s):** `camarero.partes.cobro-efectivo`, `camarero.partes.cobro-efectivo`, `profile.partials.update-password-form`, `profile.partials.update-profile-information-form`, `layouts.navigation` (+2 more)
  These have ≤1 connection - possible missing edges or undocumented components. (Counts symbols only; 596 node(s) total have ≤1 connection when file, concept and rationale nodes are included.)
- **131 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `Restaurante` connect `Restaurante` to `AutopedidoTest`, `CrearPedido`, `CartaPublicaTest`, `MesasTest`, `StripeWebhookTest`, `Dispositivo`, `Illuminate\Http\RedirectResponse`, `Illuminate\Http\RedirectResponse`, `App\Models\Pedido`, `Pedido`, `Cuenta`, `CambiarRestauranteTest`, `User`, `PagoStripeTest`, `PedidosEnCursoSeederTest`, `MesaYa — Instrucciones para Claude Code`, `Pago`, `PedidosEnCursoSeeder`, `EstadisticasTest`, `PedidosEnCursoSeederTest.php`, `Illuminate\Database\Eloquent\Factories\Factory`, `App\Http\Controllers\Cliente\AutopedidoController`, `LineaPedido`, `DispositivosTest`, `GestionPersonalTest`, `Illuminate\Http\Request`?**
  _High betweenness centrality (0.136) - this node is a cross-community bridge._
- **Are the 7 inferred relationships involving `User` (e.g. with `Base de datos` and `TODO en castellano (obligatorio)`) actually correct?**
  _`User` has 7 INFERRED edges - model-reasoned connections that need verification._
- **What connects `camarero.partes.cobro-efectivo`, `camarero.partes.cobro-efectivo`, `profile.partials.update-password-form` to the rest of the system?**
  _7 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `package.json` be split into smaller, more focused modules?**
  _Cohesion score 0.06086956521739131 - nodes in this community are weakly interconnected._
- **Why does `User` connect `User` to `CrearPedido`, `MesasTest`, `StripeWebhookTest`, `Dispositivo`, `Illuminate\Http\RedirectResponse`, `RedireccionTrasLoginTest`, `Illuminate\Http\RedirectResponse`, `App\Models\Pedido`, `InicioController.php`, `Pedido`, `Cuenta`, `CambiarRestauranteTest`, `PagoStripeTest`, `Restaurante`, `CobroCamareroTest`, `Admin/DispositivoController.php`, `PlatoRequest`, `MesaYa — Instrucciones para Claude Code`, `Pago`, `PedidosEnCursoSeeder`, `EstadisticasTest`, `PedidosEnCursoSeederTest.php`, `GestionCartaTest`, `ParteCuenta`, `PasswordResetTest.php`, `LineaPedido`, `DispositivosTest`, `GestionPersonalTest`, `Controller`, `Illuminate\View\View`, `EmailVerificationTest.php`, `PagarAlPedirTest`, `EmpleadoRequest`?**
  _High betweenness centrality (0.135) - this node is a cross-community bridge._
- **Are the 4 inferred relationships involving `Restaurante` (e.g. with `Base de datos` and `Tablas`) actually correct?**
  _`Restaurante` has 4 INFERRED edges - model-reasoned connections that need verification._
- **Should `Propuesta inicial: MesaYa` be split into smaller, more focused modules?**
  _Cohesion score 0.14285714285714285 - nodes in this community are weakly interconnected._