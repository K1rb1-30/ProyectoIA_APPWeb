# Graph Report - ProyectoIA_APPWeb  (2026-11-17)

## Corpus Check
- 162 files · ~41,278 words
- Verdict: corpus is large enough that graph structure adds value.
- Unclassified: 11 file(s) not represented in the graph (top: (none) 7, .example 1, .xml 1)

## Summary
- 748 nodes · 1224 edges · 110 communities (27 shown, 83 thin omitted)
- Extraction: 94% EXTRACTED · 6% INFERRED · 0% AMBIGUOUS · INFERRED: 75 edges (avg confidence: 0.94)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `02e70248`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- User
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
- ExampleTest
- Illuminate\Http\Request
- .claude/CLAUDE.md
- edit.blade.php
- layouts.navigation
- Mesa
- Plato
- ComandasCamareroTest
- Illuminate\View\View
- GestionCartaTest
- Illuminate\Http\RedirectResponse
- MesasTest
- NewPasswordController.php
- TestCase
- PasswordResetTest.php
- require-dev
- scripts
- ComprobarRolTest
- routes/auth.php
- Entrada 9
- Illuminate\View\Component
- config
- EmailVerificationTest.php
- ProfileTest
- AislamientoRestaurantesTest
- AuthenticationTest
- RedireccionTrasLoginTest
- Tests\TestCase
- VerifyEmailController.php
- psr-4
- require
- autoload-dev
- extra
- crear.blade.php
- editar.blade.php

## God Nodes (most connected - your core abstractions)
1. `User` - 38 edges
2. `Plato` - 26 edges
3. `Mesa` - 25 edges
4. `GestionCartaTest` - 24 edges
5. `Restaurante` - 22 edges
6. `TestCase` - 22 edges
7. `ComandasCamareroTest` - 20 edges
8. `MesasTest` - 19 edges
9. `Categoria` - 18 edges
10. `MesaYa — Instrucciones para Claude Code` - 17 edges

## Surprising Connections (you probably didn't know these)
- `6. `feature/pantalla-cocina`` --references--> `Pedido`  [INFERRED]
  docs/plan-de-trabajo.md → app/Models/Pedido.php
- `Entrada 14` --references--> `SelectorVistaTest`  [INFERRED]
  docs/registre-ia.md → tests/Feature/SelectorVistaTest.php
- `Entrada 12` --references--> `AlergenoController`  [INFERRED]
  docs/registre-ia.md → app/Http/Controllers/Admin/AlergenoController.php
- `Entrada 12` --references--> `CategoriaController`  [INFERRED]
  docs/registre-ia.md → app/Http/Controllers/Admin/CategoriaController.php
- `Entrada 15` --references--> `MesaController`  [INFERRED]
  docs/registre-ia.md → app/Http/Controllers/Admin/MesaController.php

## Import Cycles
- None detected.

## Communities (110 total, 83 thin omitted)

### Community 1 - "composer.json"
Cohesion: 0.22
Nodes (8): description, keywords, license, minimum-stability, name, prefer-stable, $schema, type

### Community 2 - "package.json"
Cohesion: 0.08
Nodes (30): devDependencies, alpinejs@^3.4.2, autoprefixer@^10.4.2, concurrently@^10.0.3, @fontsource-variable/inter@^5.3.0, laravel-vite-plugin@^3.1, @material-symbols/font-400@^0.49.0, postcss@^8.4.31 (+22 more)

### Community 3 - "Illuminate\Database\Schema\Blueprint"
Cohesion: 0.06
Nodes (19): {closure#1}(), {closure#2}(), {closure#3}(), {closure#1}(), {closure#2}(), {closure#1}(), {closure#2}(), {closure#3}() (+11 more)

### Community 4 - "MesaYa — Instrucciones para Claude Code"
Cohesion: 0.09
Nodes (20): ComprobarRol, Comandos útiles, Cómo tienes que trabajar con nosotros, Diseño visual (obligatorio para cualquier vista), Documentación del repo, Funcionalidades y prioridad, Git y GitHub, Grafo del proyecto (graphify) (+12 more)

### Community 5 - "Propuesta inicial: MesaYa"
Cohesion: 0.13
Nodes (14): 10. Riesgos y cómo los abordamos, 1. La idea en una frase, 2. Problema que resuelve, 3. Usuarios y roles, 4. Funcionalidades, 5. Flujo de la demo, 6. Tecnologías, 7. Reparto del trabajo (+6 more)

### Community 6 - "bootstrap/app.php"
Cohesion: 0.32
Nodes (3): {closure#1}(), {closure#2}(), {closure#3}()

### Community 7 - "LoginRequest"
Cohesion: 0.12
Nodes (3): LoginRequest, ComandaRequest, ProfileUpdateRequest

### Community 9 - "Prompts por pantalla"
Cohesion: 0.25
Nodes (7): Admin (escritorio), Camarero (móvil), Cliente (móvil), Cocina (Tablet horizontal), Prompt Maestro, PROMPTS para la IA de diseño Web STITCH, Prompts por pantalla

### Community 10 - "README.md"
Cohesion: 0.25
Nodes (7): About Laravel, Agentic Development, Code of Conduct, Contributing, Learning Laravel, License, Security Vulnerabilities

### Community 11 - "2. Qué incluye cada rama"
Cohesion: 0.09
Nodes (21): 10. `feature/dividir-cuenta`, 11. `feature/pago-stripe`, 12. `feature/efectivo-y-cierre`, 13. `feature/estadisticas`, 1. Seguimiento, 2. `feature/diseno-base`, 2. Qué incluye cada rama, 3. Decisiones pendientes (+13 more)

### Community 13 - "Registro de uso de IA"
Cohesion: 0.07
Nodes (19): AlergenosSeeder, CartaSeeder, DatabaseSeeder, MesasSeeder, RestaurantesSeeder, UsuariosAdminSeeder, 3. `feature/gestion-carta`, Entrada 1 (+11 more)

### Community 14 - "ComponentesDisenoTest"
Cohesion: 0.16
Nodes (3): Entrada 8, ComponentesDisenoTest, LayoutsTest

### Community 17 - "Laravel Application"
Cohesion: 0.50
Nodes (3): Agent Setup, Laravel Application, Prerequisites

### Community 22 - "Illuminate\Http\Request"
Cohesion: 0.15
Nodes (6): ConfirmablePasswordController, EmailVerificationNotificationController, EmailVerificationPromptController, NewPasswordController, Controller, ProfileController

### Community 34 - "edit.blade.php"
Cohesion: 0.50
Nodes (3): profile.partials.delete-user-form, profile.partials.update-password-form, profile.partials.update-profile-information-form

### Community 58 - "Mesa"
Cohesion: 0.06
Nodes (17): Alergeno, Categoria, {closure#1}(), PerteneceARestaurante, LineaPedido, Mesa, {closure#1}(), Pedido (+9 more)

### Community 65 - "Plato"
Cohesion: 0.15
Nodes (4): PlatoController, PlatoRequest, Plato, Entrada 12

### Community 67 - "Illuminate\View\View"
Cohesion: 0.17
Nodes (3): AlergenoController, MesaController, ComandaController

### Community 69 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.20
Nodes (3): CategoriaController, AuthenticatedSessionController, CategoriaRequest

### Community 72 - "TestCase"
Cohesion: 0.24
Nodes (4): Entrada 6, RegistroDesactivadoTest, ExampleTest, TestCase

### Community 74 - "require-dev"
Cohesion: 0.22
Nodes (9): require-dev, fakerphp/faker, laravel/breeze, laravel/pail, laravel/pao, laravel/pint, mockery/mockery, nunomaduro/collision (+1 more)

### Community 75 - "scripts"
Cohesion: 0.22
Nodes (9): scripts, dev, post-autoload-dump, post-create-project-cmd, post-root-package-install, post-update-cmd, pre-package-uninstall, setup (+1 more)

### Community 81 - "config"
Cohesion: 0.29
Nodes (7): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, preferred-install, sort-packages

### Community 89 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 90 - "require"
Cohesion: 0.40
Nodes (5): require, laravel/framework, laravel/tinker, php, simplesoftwareio/simple-qrcode

### Community 91 - "autoload-dev"
Cohesion: 0.67
Nodes (3): autoload-dev, psr-4, Tests\\

### Community 92 - "extra"
Cohesion: 0.67
Nodes (3): extra, laravel, dont-discover

## Knowledge Gaps
- **6 isolated node(s):** `admin.carta._formulario`, `admin.carta._formulario`, `profile.partials.delete-user-form`, `profile.partials.update-password-form`, `profile.partials.update-profile-information-form` (+1 more)
  These have ≤1 connection - possible missing edges or undocumented components. (Counts symbols only; 374 node(s) total have ≤1 connection when file, concept and rationale nodes are included.)
- **83 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `Plato`, `ComandasCamareroTest`, `GestionCartaTest`, `MesasTest`, `LoginRequest`, `NewPasswordController.php`, `Illuminate\Foundation\Testing\RefreshDatabase`, `PasswordResetTest.php`, `UserFactory.php`, `Registro de uso de IA`, `ComprobarRolTest`, `Entrada 9`, `EmailVerificationTest.php`, `ProfileTest`, `AuthenticationTest`, `RedireccionTrasLoginTest`, `Tests\TestCase`, `Mesa`?**
  _High betweenness centrality (0.123) - this node is a cross-community bridge._
- **Are the 3 inferred relationships involving `User` (e.g. with `Base de datos` and `TODO en castellano (obligatorio)`) actually correct?**
  _`User` has 3 INFERRED edges - model-reasoned connections that need verification._
- **What connects `admin.carta._formulario`, `admin.carta._formulario`, `profile.partials.delete-user-form` to the rest of the system?**
  _6 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `package.json` be split into smaller, more focused modules?**
  _Cohesion score 0.07899159663865546 - nodes in this community are weakly interconnected._
- **Why does `Mesa` connect `Mesa` to `ComandasCamareroTest`, `Illuminate\View\View`?**
  _High betweenness centrality (0.048) - this node is a cross-community bridge._
- **Should `Illuminate\Database\Schema\Blueprint` be split into smaller, more focused modules?**
  _Cohesion score 0.05868118572292801 - nodes in this community are weakly interconnected._
- **Why does `GestionCartaTest` connect `GestionCartaTest` to `User`, `Plato`, `Illuminate\Foundation\Testing\RefreshDatabase`, `Tests\TestCase`, `Mesa`?**
  _High betweenness centrality (0.041) - this node is a cross-community bridge._