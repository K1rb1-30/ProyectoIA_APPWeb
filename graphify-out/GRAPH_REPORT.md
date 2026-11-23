# Graph Report - ProyectoIA_APPWeb  (2026-11-23)

## Corpus Check
- 209 files · ~71,326 words
- Verdict: corpus is large enough that graph structure adds value.
- Unclassified: 11 file(s) not represented in the graph (top: (none) 7, .example 1, .xml 1)

## Summary
- 1138 nodes · 2286 edges · 155 communities (47 shown, 108 thin omitted)
- Extraction: 89% EXTRACTED · 11% INFERRED · 0% AMBIGUOUS · INFERRED: 245 edges (avg confidence: 0.93)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `1d87540b`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- Registro de uso de IA
- composer.json
- package.json
- Illuminate\Database\Schema\Blueprint
- MesaYa — Instrucciones para Claude Code
- Propuesta inicial: MesaYa
- bootstrap/app.php
- Illuminate\Foundation\Http\FormRequest
- Prompts por pantalla
- README.md
- 2. Qué incluye cada rama
- AppServiceProvider
- PlatoController
- ComponentesDisenoTest
- Laravel Application
- App\Http\Controllers\Cliente\AutopedidoController
- EfectivoYCierreTest
- Controller
- .claude/CLAUDE.md
- edit.blade.php
- layouts.navigation
- App\Models\Pedido
- Illuminate\Database\Seeder
- ComandasCamareroTest
- Illuminate\View\View
- GestionCartaTest
- Illuminate\Http\Request
- PagoStripeTest.php
- Illuminate\Http\RedirectResponse
- Plato
- PasswordResetTest.php
- require-dev
- scripts
- [v0.1] – MVP – 22/11/2026
- 1. Seguimiento
- Entrada 9
- ParteCuenta
- config
- EmailVerificationTest.php
- ProfileTest
- Registro de uso de IA
- CobroController.php
- Illuminate\Foundation\Testing\RefreshDatabase
- psr-4
- require
- autoload-dev
- extra
- crear.blade.php
- editar.blade.php
- Stack
- Instalación de MesaYa en local
- ComprobarRol
- ComprobarRolTest
- 0001_01_01_000002_create_jobs_table.php
- 2026_11_16_000002_add_rol_y_restaurante_id_to_users_table.php
- Illuminate\Support\Facades\Schema
- Illuminate\Database\Migrations\Migration
- 2026_11_23_000003_add_cuenta_id_to_pedidos_table.php
- 2026_11_16_000001_create_restaurantes_table.php
- 2026_11_16_000003_create_mesas_table.php
- 2026_11_17_000002_create_alergenos_table.php
- 2026_11_17_000003_create_platos_table.php
- 2026_11_17_000005_create_pedidos_table.php
- 2026_11_17_000006_create_lineas_pedido_table.php
- 2026_11_23_000001_create_cuentas_table.php
- 2026_11_23_000002_create_partes_cuenta_table.php
- 2026_11_23_000004_create_pagos_table.php
- PantallaCocinaTest
- 3. Rutas
- MesasTest
- Cuenta
- detalle-mesa.blade.php
- 1. Requisitos funcionales
- DetalleMesaController
- Entrada 22
- LineaPedido
- Mesa
- ComandasCamareroTest
- 3. Resto de tests por funcionalidad
- TestCase
- DividirCuenta
- CategoriaRequest
- ParteCuenta.php
- AuthenticationTest
- PasswordConfirmationTest
- PasswordUpdateTest
- cobro-efectivo.blade.php

## God Nodes (most connected - your core abstractions)
1. `ParteCuenta` - 38 edges
2. `Mesa` - 37 edges
3. `User` - 36 edges
4. `Registro de uso de IA` - 27 edges
5. `Pedido` - 27 edges
6. `EfectivoYCierreTest` - 26 edges
7. `Plato` - 26 edges
8. `GestionCartaTest` - 25 edges
9. `AutopedidoTest` - 24 edges
10. `Cuenta` - 23 edges

## Surprising Connections (you probably didn't know these)
- `2. Requisitos no funcionales` --references--> `PerteneceARestaurante`  [INFERRED]
  docs/02-requisits.md → app/Models/Concerns/PerteneceARestaurante.php
- `3. `feature/gestion-carta`` --references--> `AlergenosSeeder`  [INFERRED]
  docs/plan-de-trabajo.md → database/seeders/AlergenosSeeder.php
- `6. `feature/pantalla-cocina`` --references--> `Pedido`  [INFERRED]
  docs/plan-de-trabajo.md → app/Models/Pedido.php
- `3. Decisiones pendientes` --references--> `PasarelaStripe`  [INFERRED]
  docs/plan-de-trabajo.md → app/Servicios/PasarelaStripe.php
- `Entrada 26` --references--> `CobroController`  [INFERRED]
  docs/registre-ia.md → app/Http/Controllers/Camarero/CobroController.php

## Import Cycles
- None detected.

## Communities (155 total, 108 thin omitted)

### Community 0 - "Registro de uso de IA"
Cohesion: 0.16
Nodes (5): Entrada 20, Entrada 21, avisosCamarero(), carrito(), AutopedidoTest

### Community 1 - "composer.json"
Cohesion: 0.22
Nodes (8): description, keywords, license, minimum-stability, name, prefer-stable, $schema, type

### Community 2 - "package.json"
Cohesion: 0.08
Nodes (30): devDependencies, alpinejs@^3.4.2, autoprefixer@^10.4.2, concurrently@^10.0.3, @fontsource-variable/inter@^5.3.0, laravel-vite-plugin@^3.1, @material-symbols/font-400@^0.49.0, postcss@^8.4.31 (+22 more)

### Community 3 - "Illuminate\Database\Schema\Blueprint"
Cohesion: 0.22
Nodes (6): {closure#1}(), {closure#2}(), {closure#3}(), {closure#1}(), {closure#2}(), {closure#1}()

### Community 4 - "MesaYa — Instrucciones para Claude Code"
Cohesion: 0.14
Nodes (13): Comandos útiles, Cómo tienes que trabajar con nosotros, Diseño visual (obligatorio para cualquier vista), Documentación del repo, Funcionalidades y prioridad, Git y GitHub, Grafo del proyecto (graphify), MesaYa — Instrucciones para Claude Code (+5 more)

### Community 5 - "Propuesta inicial: MesaYa"
Cohesion: 0.13
Nodes (14): 10. Riesgos y cómo los abordamos, 1. La idea en una frase, 2. Problema que resuelve, 3. Usuarios y roles, 4. Funcionalidades, 5. Flujo de la demo, 6. Tecnologías, 7. Reparto del trabajo (+6 more)

### Community 6 - "bootstrap/app.php"
Cohesion: 0.32
Nodes (3): {closure#1}(), {closure#2}(), {closure#3}()

### Community 7 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.08
Nodes (5): LoginRequest, ComandaRequest, DividirCuentaRequest, PedidoClienteRequest, ProfileUpdateRequest

### Community 9 - "Prompts por pantalla"
Cohesion: 0.25
Nodes (7): Admin (escritorio), Camarero (móvil), Cliente (móvil), Cocina (Tablet horizontal), Prompt Maestro, PROMPTS para la IA de diseño Web STITCH, Prompts por pantalla

### Community 10 - "README.md"
Cohesion: 0.25
Nodes (7): About Laravel, Agentic Development, Code of Conduct, Contributing, Learning Laravel, License, Security Vulnerabilities

### Community 11 - "2. Qué incluye cada rama"
Cohesion: 0.15
Nodes (13): 10. `feature/dividir-cuenta`, 11. `feature/pago-stripe`, 12. `feature/efectivo-y-cierre`, 13. `feature/estadisticas`, 2. `feature/diseno-base`, 2. Qué incluye cada rama, 3. `feature/gestion-carta`, 4. `feature/mesas-qr` (+5 more)

### Community 14 - "ComponentesDisenoTest"
Cohesion: 0.16
Nodes (3): Entrada 8, ComponentesDisenoTest, LayoutsTest

### Community 17 - "Laravel Application"
Cohesion: 0.50
Nodes (3): Agent Setup, Laravel Application, Prerequisites

### Community 19 - "App\Http\Controllers\Cliente\AutopedidoController"
Cohesion: 0.16
Nodes (4): AutopedidoController, {closure#2}(), CartaController, {closure#2}()

### Community 22 - "Controller"
Cohesion: 0.08
Nodes (9): ConfirmablePasswordController, EmailVerificationNotificationController, EmailVerificationPromptController, {closure#1}(), NewPasswordController, PasswordController, PasswordResetLinkController, VerifyEmailController (+1 more)

### Community 34 - "edit.blade.php"
Cohesion: 0.50
Nodes (3): profile.partials.delete-user-form, profile.partials.update-password-form, profile.partials.update-profile-information-form

### Community 58 - "App\Models\Pedido"
Cohesion: 0.07
Nodes (15): {closure#3}(), {closure#4}(), AvisoController, {closure#1}(), {closure#2}(), {closure#1}(), {closure#2}(), {closure#1}() (+7 more)

### Community 65 - "Illuminate\Database\Seeder"
Cohesion: 0.24
Nodes (3): AlergenosSeeder, MesasSeeder, UsuariosAdminSeeder

### Community 66 - "ComandasCamareroTest"
Cohesion: 0.14
Nodes (3): CrearPedido, CobroCamareroTest, DividirCuentaTest

### Community 67 - "Illuminate\View\View"
Cohesion: 0.14
Nodes (5): AlergenoController, MesaController, ComandaController, AppLayout, GuestLayout

### Community 70 - "PagoStripeTest.php"
Cohesion: 0.15
Nodes (10): {closure#1}(), {closure#1}(), {closure#2}(), {closure#3}(), {closure#4}(), {closure#5}(), {closure#6}(), {closure#7}() (+2 more)

### Community 72 - "Plato"
Cohesion: 0.14
Nodes (9): Alergeno, Categoria, PerteneceARestaurante, Plato, CartaSeeder, Decisiones importantes, Tablas, Entrada 12 (+1 more)

### Community 74 - "require-dev"
Cohesion: 0.22
Nodes (9): require-dev, fakerphp/faker, laravel/breeze, laravel/pail, laravel/pao, laravel/pint, mockery/mockery, nunomaduro/collision (+1 more)

### Community 75 - "scripts"
Cohesion: 0.22
Nodes (9): scripts, dev, post-autoload-dump, post-create-project-cmd, post-root-package-install, post-update-cmd, pre-package-uninstall, setup (+1 more)

### Community 76 - "[v0.1] – MVP – 22/11/2026"
Cohesion: 0.40
Nodes (4): Adelantado de la v1.0, Añadido, Changelog, [v0.1] – MVP – 22/11/2026

### Community 77 - "1. Seguimiento"
Cohesion: 0.20
Nodes (9): 1. Seguimiento, 3. Decisiones pendientes, 4. Cómo pedir cada rama a Claude Code, 🟢 MVP (v0.1) — objetivo: 23/11, Plan de trabajo de MesaYa, Reparto propuesto, Semana 4 — hasta el 07/12, Tareas previas (+1 more)

### Community 79 - "Entrada 9"
Cohesion: 0.06
Nodes (7): CategoriaFactory, PlatoFactory, RestauranteFactory, UserFactory, Entrada 9, AislamientoRestaurantesTest, RedireccionTrasLoginTest

### Community 80 - "ParteCuenta"
Cohesion: 0.21
Nodes (5): {closure#1}(), ParteCuenta, PasarelaStripe, Entrada 24, Entrada 25

### Community 81 - "config"
Cohesion: 0.29
Nodes (7): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, preferred-install, sort-packages

### Community 84 - "Registro de uso de IA"
Cohesion: 0.05
Nodes (20): MesaFactory, DatabaseSeeder, RestaurantesSeeder, Entrada 1, Entrada 10, Entrada 11, Entrada 14, Entrada 15 (+12 more)

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

### Community 110 - "Stack"
Cohesion: 0.67
Nodes (3): MySQL, PHP en local (`php.ini`), Stack

### Community 111 - "Instalación de MesaYa en local"
Cohesion: 0.13
Nodes (14): 1. Requisitos, 2. Descargar el proyecto, 3. Bases de datos, 4. Archivo `.env`, 5. Tablas, datos de prueba y fotos, 6. Arrancar, 7. Problemas frecuentes, 8. Despliegue (+6 more)

### Community 112 - "ComprobarRol"
Cohesion: 0.32
Nodes (4): ComprobarRol, Roles, 1. `feature/restaurantes-y-roles`, Entrada 13

### Community 115 - "0001_01_01_000002_create_jobs_table.php"
Cohesion: 0.33
Nodes (3): {closure#1}(), {closure#2}(), {closure#3}()

### Community 133 - "3. Rutas"
Cohesion: 0.17
Nodes (11): 1. Arquitectura, 2. Base de datos, 3. Rutas, 4. Estructura de carpetas (lo nuestro), Admin (`auth` + `rol:admin`), Camarero (`auth` + `rol:camarero`), Cocina (`auth` + `rol:cocina`), Diagrama entidad-relación (+3 more)

### Community 138 - "1. Requisitos funcionales"
Cohesion: 0.18
Nodes (10): 1. Requisitos funcionales, 2. Requisitos no funcionales, Acceso, roles y restaurantes, Carta y alérgenos (admin), Cliente por QR (sin login), Comandas, cocina y avisos (personal), Estadísticas (admin), Extras (🟣, solo si sobra tiempo) (+2 more)

### Community 139 - "DetalleMesaController"
Cohesion: 0.26
Nodes (4): DetalleMesaController, Pago, Estados y reglas, Entrada 26

### Community 140 - "Entrada 22"
Cohesion: 0.24
Nodes (3): Entrada 22, ExampleTest, RepartoCuentaTest

### Community 141 - "LineaPedido"
Cohesion: 0.14
Nodes (5): LineaPedido, {closure#1}(), Pedido, TODO en castellano (obligatorio), Entrada 17

### Community 143 - "Mesa"
Cohesion: 0.30
Nodes (3): Mesa, Restaurante, Base de datos

### Community 145 - "3. Resto de tests por funcionalidad"
Cohesion: 0.22
Nodes (5): 1. Cómo ejecutarlos, 2. Golden tests, 3. Resto de tests por funcionalidad, 4. Pruebas manuales en el navegador, Pruebas de MesaYa

### Community 146 - "TestCase"
Cohesion: 0.24
Nodes (4): Entrada 6, RegistroDesactivadoTest, ExampleTest, TestCase

### Community 149 - "ParteCuenta.php"
Cohesion: 0.39
Nodes (3): {closure#3}(), {closure#4}(), {closure#6}()

## Knowledge Gaps
- **8 isolated node(s):** `camarero.partes.cobro-efectivo`, `camarero.partes.cobro-efectivo`, `profile.partials.delete-user-form`, `profile.partials.update-password-form`, `profile.partials.update-profile-information-form` (+3 more)
  These have ≤1 connection - possible missing edges or undocumented components. (Counts symbols only; 461 node(s) total have ≤1 connection when file, concept and rationale nodes are included.)
- **108 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `Mesa` connect `Mesa` to `Registro de uso de IA`, `Illuminate\View\View`, `Illuminate\Http\Request`, `Illuminate\Http\RedirectResponse`, `App\Models\Restaurante`, `Plato`, `LineaPedido`, `ComandasCamareroTest`, `App\Http\Controllers\Cliente\AutopedidoController`, `Registro de uso de IA`, `Illuminate\Foundation\Testing\RefreshDatabase`?**
  _High betweenness centrality (0.069) - this node is a cross-community bridge._
- **What connects `camarero.partes.cobro-efectivo`, `camarero.partes.cobro-efectivo`, `profile.partials.delete-user-form` to the rest of the system?**
  _8 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `package.json` be split into smaller, more focused modules?**
  _Cohesion score 0.08199643493761141 - nodes in this community are weakly interconnected._
- **Why does `User` connect `Illuminate\Foundation\Testing\RefreshDatabase` to `Illuminate\Database\Seeder`, `GestionCartaTest`, `MesasTest`, `Illuminate\Foundation\Http\FormRequest`, `App\Models\Restaurante`, `Plato`, `PasswordResetTest.php`, `LineaPedido`, `Entrada 9`, `Mesa`, `EmailVerificationTest.php`, `Controller`?**
  _High betweenness centrality (0.061) - this node is a cross-community bridge._
- **Should `MesaYa — Instrucciones para Claude Code` be split into smaller, more focused modules?**
  _Cohesion score 0.14285714285714285 - nodes in this community are weakly interconnected._
- **Why does `alpinejs` connect `package.json` to `Registro de uso de IA`?**
  _High betweenness centrality (0.055) - this node is a cross-community bridge._
- **Should `Propuesta inicial: MesaYa` be split into smaller, more focused modules?**
  _Cohesion score 0.13333333333333333 - nodes in this community are weakly interconnected._