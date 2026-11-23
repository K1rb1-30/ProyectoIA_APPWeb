# Graph Report - ProyectoIA_APPWeb  (2026-11-23)

## Corpus Check
- 196 files · ~59,140 words
- Verdict: corpus is large enough that graph structure adds value.
- Unclassified: 11 file(s) not represented in the graph (top: (none) 7, .example 1, .xml 1)

## Summary
- 1008 nodes · 1892 edges · 133 communities (38 shown, 95 thin omitted)
- Extraction: 93% EXTRACTED · 7% INFERRED · 0% AMBIGUOUS · INFERRED: 134 edges (avg confidence: 0.93)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `a4285856`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- AutopedidoTest
- composer.json
- package.json
- Illuminate\Database\Schema\Blueprint
- MesaYa — Instrucciones para Claude Code
- Propuesta inicial: MesaYa
- bootstrap/app.php
- LoginRequest
- Illuminate\Foundation\Testing\RefreshDatabase
- Prompts por pantalla
- README.md
- 2. Qué incluye cada rama
- AppServiceProvider
- Registro de uso de IA
- ComponentesDisenoTest
- Laravel Application
- Cuenta
- web.php
- Controller
- .claude/CLAUDE.md
- edit.blade.php
- layouts.navigation
- Pedido
- Plato
- ComandasCamareroTest
- Illuminate\View\View
- GestionCartaTest
- Illuminate\Http\Request
- PagoStripeTest.php
- Illuminate\Http\RedirectResponse
- TestCase
- PasswordResetTest.php
- require-dev
- scripts
- [v0.1] – MVP – 22/11/2026
- PasswordConfirmationTest
- Entrada 9
- Illuminate\View\Component
- config
- EmailVerificationTest.php
- ProfileTest
- CartaPublicaTest
- AuthenticationTest
- PasswordUpdateTest.php
- psr-4
- require
- autoload-dev
- extra
- crear.blade.php
- editar.blade.php
- CategoriaRequest
- 1. Seguimiento
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
- Stack

## God Nodes (most connected - your core abstractions)
1. `User` - 35 edges
2. `Mesa` - 34 edges
3. `Pedido` - 30 edges
4. `Registro de uso de IA` - 24 edges
5. `Plato` - 24 edges
6. `GestionCartaTest` - 24 edges
7. `AutopedidoTest` - 23 edges
8. `TestCase` - 22 edges
9. `DividirCuentaTest` - 21 edges
10. `PagoStripeTest` - 21 edges

## Surprising Connections (you probably didn't know these)
- `6. `feature/pantalla-cocina`` --references--> `Pedido`  [INFERRED]
  docs/plan-de-trabajo.md → app/Models/Pedido.php
- `3. Decisiones pendientes` --references--> `PasarelaStripe`  [INFERRED]
  docs/plan-de-trabajo.md → app/Servicios/PasarelaStripe.php
- `Entrada 6` --references--> `RegistroDesactivadoTest`  [INFERRED]
  docs/registre-ia.md → tests/Feature/Auth/RegistroDesactivadoTest.php
- `Entrada 23` --references--> `PagoTarjetaController`  [INFERRED]
  docs/registre-ia.md → app/Http/Controllers/Cliente/PagoTarjetaController.php
- `Entrada 22` --references--> `ParteCuenta`  [INFERRED]
  docs/registre-ia.md → app/Models/ParteCuenta.php

## Import Cycles
- None detected.

## Communities (133 total, 95 thin omitted)

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

### Community 7 - "LoginRequest"
Cohesion: 0.08
Nodes (5): LoginRequest, ComandaRequest, DividirCuentaRequest, PedidoClienteRequest, ProfileUpdateRequest

### Community 8 - "Illuminate\Foundation\Testing\RefreshDatabase"
Cohesion: 0.05
Nodes (11): {closure#2}(), {closure#1}(), PerteneceARestaurante, Mesa, Restaurante, User, UsuariosAdminSeeder, Entrada 14 (+3 more)

### Community 9 - "Prompts por pantalla"
Cohesion: 0.25
Nodes (7): Admin (escritorio), Camarero (móvil), Cliente (móvil), Cocina (Tablet horizontal), Prompt Maestro, PROMPTS para la IA de diseño Web STITCH, Prompts por pantalla

### Community 10 - "README.md"
Cohesion: 0.25
Nodes (7): About Laravel, Agentic Development, Code of Conduct, Contributing, Learning Laravel, License, Security Vulnerabilities

### Community 11 - "2. Qué incluye cada rama"
Cohesion: 0.17
Nodes (12): 10. `feature/dividir-cuenta`, 11. `feature/pago-stripe`, 12. `feature/efectivo-y-cierre`, 13. `feature/estadisticas`, 2. `feature/diseno-base`, 2. Qué incluye cada rama, 4. `feature/mesas-qr`, 5. `feature/comandas-camarero` (+4 more)

### Community 13 - "Registro de uso de IA"
Cohesion: 0.08
Nodes (17): DatabaseSeeder, RestaurantesSeeder, Entrada 1, Entrada 10, Entrada 11, Entrada 16, Entrada 2, Entrada 21 (+9 more)

### Community 14 - "ComponentesDisenoTest"
Cohesion: 0.16
Nodes (3): Entrada 8, ComponentesDisenoTest, LayoutsTest

### Community 17 - "Laravel Application"
Cohesion: 0.50
Nodes (3): Agent Setup, Laravel Application, Prerequisites

### Community 19 - "Cuenta"
Cohesion: 0.10
Nodes (7): {closure#3}(), DividirCuenta, CuentaController, Cuenta, Entrada 22, ExampleTest, RepartoCuentaTest

### Community 22 - "Controller"
Cohesion: 0.08
Nodes (9): ConfirmablePasswordController, EmailVerificationNotificationController, EmailVerificationPromptController, {closure#1}(), NewPasswordController, PasswordController, PasswordResetLinkController, VerifyEmailController (+1 more)

### Community 34 - "edit.blade.php"
Cohesion: 0.50
Nodes (3): profile.partials.delete-user-form, profile.partials.update-password-form, profile.partials.update-profile-information-form

### Community 58 - "Pedido"
Cohesion: 0.07
Nodes (16): {closure#4}(), AvisoController, {closure#1}(), {closure#2}(), {closure#1}(), {closure#2}(), {closure#1}(), {closure#2}() (+8 more)

### Community 65 - "Plato"
Cohesion: 0.09
Nodes (11): PlatoController, PlatoRequest, Alergeno, Categoria, Plato, Base de datos, AlergenosSeeder, CartaSeeder (+3 more)

### Community 66 - "ComandasCamareroTest"
Cohesion: 0.13
Nodes (3): CrearPedido, ComandasCamareroTest, DividirCuentaTest

### Community 67 - "Illuminate\View\View"
Cohesion: 0.14
Nodes (4): AlergenoController, MesaController, ComandaController, CartaController

### Community 69 - "Illuminate\Http\Request"
Cohesion: 0.19
Nodes (3): AuthenticatedSessionController, AutopedidoController, ProfileController

### Community 70 - "PagoStripeTest.php"
Cohesion: 0.08
Nodes (14): {closure#1}(), Pago, ParteCuenta, PasarelaStripe, Entrada 23, {closure#1}(), {closure#2}(), {closure#3}() (+6 more)

### Community 72 - "TestCase"
Cohesion: 0.28
Nodes (3): RegistroDesactivadoTest, ExampleTest, TestCase

### Community 74 - "require-dev"
Cohesion: 0.22
Nodes (9): require-dev, fakerphp/faker, laravel/breeze, laravel/pail, laravel/pao, laravel/pint, mockery/mockery, nunomaduro/collision (+1 more)

### Community 75 - "scripts"
Cohesion: 0.22
Nodes (9): scripts, dev, post-autoload-dump, post-create-project-cmd, post-root-package-install, post-update-cmd, pre-package-uninstall, setup (+1 more)

### Community 76 - "[v0.1] – MVP – 22/11/2026"
Cohesion: 0.40
Nodes (4): Adelantado de la v1.0, Añadido, Changelog, [v0.1] – MVP – 22/11/2026

### Community 79 - "Entrada 9"
Cohesion: 0.07
Nodes (7): CategoriaFactory, PlatoFactory, RestauranteFactory, UserFactory, Entrada 9, AislamientoRestaurantesTest, RedireccionTrasLoginTest

### Community 81 - "config"
Cohesion: 0.29
Nodes (7): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, preferred-install, sort-packages

### Community 84 - "CartaPublicaTest"
Cohesion: 0.07
Nodes (5): MesaFactory, Entrada 15, Entrada 18, CartaPublicaTest, MesasTest

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

### Community 111 - "1. Seguimiento"
Cohesion: 0.20
Nodes (9): 1. Seguimiento, 3. Decisiones pendientes, 4. Cómo pedir cada rama a Claude Code, 🟢 MVP (v0.1) — objetivo: 23/11, Plan de trabajo de MesaYa, Reparto propuesto, Semana 4 — hasta el 07/12, Tareas previas (+1 more)

### Community 112 - "ComprobarRol"
Cohesion: 0.32
Nodes (4): ComprobarRol, Roles, 1. `feature/restaurantes-y-roles`, Entrada 13

### Community 115 - "0001_01_01_000002_create_jobs_table.php"
Cohesion: 0.33
Nodes (3): {closure#1}(), {closure#2}(), {closure#3}()

### Community 129 - "Stack"
Cohesion: 0.67
Nodes (3): MySQL, PHP en local (`php.ini`), Stack

## Knowledge Gaps
- **6 isolated node(s):** `profile.partials.delete-user-form`, `profile.partials.update-password-form`, `profile.partials.update-profile-information-form`, `layouts.navigation`, `admin.carta._formulario` (+1 more)
  These have ≤1 connection - possible missing edges or undocumented components. (Counts symbols only; 425 node(s) total have ≤1 connection when file, concept and rationale nodes are included.)
- **95 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `Illuminate\Foundation\Testing\RefreshDatabase` to `Plato`, `GestionCartaTest`, `LoginRequest`, `PasswordResetTest.php`, `Entrada 9`, `EmailVerificationTest.php`, `CartaPublicaTest`, `Controller`, `Pedido`?**
  _High betweenness centrality (0.058) - this node is a cross-community bridge._
- **What connects `profile.partials.delete-user-form`, `profile.partials.update-password-form`, `profile.partials.update-profile-information-form` to the rest of the system?**
  _6 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `package.json` be split into smaller, more focused modules?**
  _Cohesion score 0.08199643493761141 - nodes in this community are weakly interconnected._
- **Why does `Mesa` connect `Illuminate\Foundation\Testing\RefreshDatabase` to `AutopedidoTest`, `Plato`, `ComandasCamareroTest`, `Illuminate\View\View`, `Illuminate\Http\Request`, `Illuminate\Http\RedirectResponse`, `CartaPublicaTest`, `web.php`?**
  _High betweenness centrality (0.058) - this node is a cross-community bridge._
- **Should `MesaYa — Instrucciones para Claude Code` be split into smaller, more focused modules?**
  _Cohesion score 0.14285714285714285 - nodes in this community are weakly interconnected._
- **Why does `AutopedidoTest` connect `AutopedidoTest` to `Illuminate\Foundation\Testing\RefreshDatabase`, `Illuminate\Http\RedirectResponse`?**
  _High betweenness centrality (0.055) - this node is a cross-community bridge._
- **Should `Propuesta inicial: MesaYa` be split into smaller, more focused modules?**
  _Cohesion score 0.13333333333333333 - nodes in this community are weakly interconnected._