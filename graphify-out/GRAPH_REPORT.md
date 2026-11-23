# Graph Report - ProyectoIA_APPWeb  (2026-11-23)

## Corpus Check
- 200 files · ~64,136 words
- Verdict: corpus is large enough that graph structure adds value.
- Unclassified: 11 file(s) not represented in the graph (top: (none) 7, .example 1, .xml 1)

## Summary
- 1056 nodes · 1991 edges · 147 communities (43 shown, 104 thin omitted)
- Extraction: 91% EXTRACTED · 9% INFERRED · 0% AMBIGUOUS · INFERRED: 189 edges (avg confidence: 0.94)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `f3a483d4`
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
- Mesa
- Prompts por pantalla
- README.md
- 2. Qué incluye cada rama
- AppServiceProvider
- Registro de uso de IA
- ComponentesDisenoTest
- Laravel Application
- DividirCuenta
- Illuminate\Database\Eloquent\Model
- Controller
- .claude/CLAUDE.md
- edit.blade.php
- layouts.navigation
- web.php
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
- Illuminate\Database\Eloquent\Factories\Factory
- ComandasCamareroTest
- config
- EmailVerificationTest.php
- ProfileTest
- CartaPublicaTest
- AuthenticationTest
- Illuminate\Foundation\Testing\RefreshDatabase
- psr-4
- require
- autoload-dev
- extra
- crear.blade.php
- editar.blade.php
- CategoriaRequest
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
- Pedido
- Entrada 22
- .delQr
- 1. Requisitos funcionales
- Pedido.php
- LineaPedido
- Entrada 9
- Entrada 15
- AislamientoRestaurantesTest
- 3. Resto de tests por funcionalidad
- 2. Golden tests

## God Nodes (most connected - your core abstractions)
1. `Mesa` - 36 edges
2. `User` - 36 edges
3. `Pedido` - 32 edges
4. `Plato` - 26 edges
5. `Registro de uso de IA` - 25 edges
6. `GestionCartaTest` - 25 edges
7. `AutopedidoTest` - 24 edges
8. `PantallaCocinaTest` - 22 edges
9. `ComandasCamareroTest` - 22 edges
10. `DividirCuentaTest` - 22 edges

## Surprising Connections (you probably didn't know these)
- `2. Requisitos no funcionales` --references--> `PerteneceARestaurante`  [INFERRED]
  docs/02-requisits.md → app/Models/Concerns/PerteneceARestaurante.php
- `3. `feature/gestion-carta`` --references--> `AlergenosSeeder`  [INFERRED]
  docs/plan-de-trabajo.md → database/seeders/AlergenosSeeder.php
- `6. `feature/pantalla-cocina`` --references--> `Pedido`  [INFERRED]
  docs/plan-de-trabajo.md → app/Models/Pedido.php
- `3. Decisiones pendientes` --references--> `PasarelaStripe`  [INFERRED]
  docs/plan-de-trabajo.md → app/Servicios/PasarelaStripe.php
- `Decisiones importantes` --references--> `CrearPedido`  [INFERRED]
  docs/03-disseny.md → app/Acciones/CrearPedido.php

## Import Cycles
- None detected.

## Communities (147 total, 104 thin omitted)

### Community 1 - "composer.json"
Cohesion: 0.22
Nodes (8): description, keywords, license, minimum-stability, name, prefer-stable, $schema, type

### Community 2 - "package.json"
Cohesion: 0.07
Nodes (33): Entrada 21, devDependencies, alpinejs@^3.4.2, autoprefixer@^10.4.2, concurrently@^10.0.3, @fontsource-variable/inter@^5.3.0, laravel-vite-plugin@^3.1, @material-symbols/font-400@^0.49.0 (+25 more)

### Community 3 - "Illuminate\Database\Schema\Blueprint"
Cohesion: 0.22
Nodes (6): {closure#1}(), {closure#2}(), {closure#3}(), {closure#1}(), {closure#2}(), {closure#1}()

### Community 4 - "MesaYa — Instrucciones para Claude Code"
Cohesion: 0.12
Nodes (16): Comandos útiles, Cómo tienes que trabajar con nosotros, Diseño visual (obligatorio para cualquier vista), Documentación del repo, Funcionalidades y prioridad, Git y GitHub, Grafo del proyecto (graphify), MesaYa — Instrucciones para Claude Code (+8 more)

### Community 5 - "Propuesta inicial: MesaYa"
Cohesion: 0.13
Nodes (14): 10. Riesgos y cómo los abordamos, 1. La idea en una frase, 2. Problema que resuelve, 3. Usuarios y roles, 4. Funcionalidades, 5. Flujo de la demo, 6. Tecnologías, 7. Reparto del trabajo (+6 more)

### Community 6 - "bootstrap/app.php"
Cohesion: 0.32
Nodes (3): {closure#1}(), {closure#2}(), {closure#3}()

### Community 7 - "LoginRequest"
Cohesion: 0.08
Nodes (5): LoginRequest, ComandaRequest, DividirCuentaRequest, PedidoClienteRequest, ProfileUpdateRequest

### Community 9 - "Prompts por pantalla"
Cohesion: 0.25
Nodes (7): Admin (escritorio), Camarero (móvil), Cliente (móvil), Cocina (Tablet horizontal), Prompt Maestro, PROMPTS para la IA de diseño Web STITCH, Prompts por pantalla

### Community 10 - "README.md"
Cohesion: 0.25
Nodes (7): About Laravel, Agentic Development, Code of Conduct, Contributing, Learning Laravel, License, Security Vulnerabilities

### Community 11 - "2. Qué incluye cada rama"
Cohesion: 0.09
Nodes (22): 10. `feature/dividir-cuenta`, 11. `feature/pago-stripe`, 12. `feature/efectivo-y-cierre`, 13. `feature/estadisticas`, 1. Seguimiento, 2. `feature/diseno-base`, 2. Qué incluye cada rama, 3. Decisiones pendientes (+14 more)

### Community 13 - "Registro de uso de IA"
Cohesion: 0.20
Nodes (9): Entrada 1, Entrada 16, Entrada 2, Entrada 23, Entrada 3, Entrada 4, Entrada 5, Entrada 7 (+1 more)

### Community 14 - "ComponentesDisenoTest"
Cohesion: 0.16
Nodes (3): Entrada 8, ComponentesDisenoTest, LayoutsTest

### Community 17 - "Laravel Application"
Cohesion: 0.50
Nodes (3): Agent Setup, Laravel Application, Prerequisites

### Community 19 - "DividirCuenta"
Cohesion: 0.20
Nodes (3): {closure#3}(), DividirCuenta, CuentaController

### Community 21 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.13
Nodes (3): {closure#1}(), Cuenta, RegistroDePrueba

### Community 22 - "Controller"
Cohesion: 0.09
Nodes (8): EmailVerificationNotificationController, EmailVerificationPromptController, {closure#1}(), NewPasswordController, PasswordController, PasswordResetLinkController, VerifyEmailController, Controller

### Community 34 - "edit.blade.php"
Cohesion: 0.50
Nodes (3): profile.partials.delete-user-form, profile.partials.update-password-form, profile.partials.update-profile-information-form

### Community 58 - "web.php"
Cohesion: 0.09
Nodes (6): AvisoController, {closure#1}(), {closure#2}(), {closure#2}(), {closure#2}(), PedidoController

### Community 65 - "Plato"
Cohesion: 0.06
Nodes (20): PlatoController, PlatoRequest, Alergeno, Categoria, PerteneceARestaurante, Plato, Restaurante, Base de datos (+12 more)

### Community 67 - "Illuminate\View\View"
Cohesion: 0.14
Nodes (5): AlergenoController, MesaController, ComandaController, AppLayout, GuestLayout

### Community 70 - "PagoStripeTest.php"
Cohesion: 0.08
Nodes (14): {closure#1}(), Pago, ParteCuenta, PasarelaStripe, Entrada 24, {closure#1}(), {closure#2}(), {closure#3}() (+6 more)

### Community 72 - "TestCase"
Cohesion: 0.24
Nodes (4): Entrada 6, RegistroDesactivadoTest, ExampleTest, TestCase

### Community 74 - "require-dev"
Cohesion: 0.22
Nodes (9): require-dev, fakerphp/faker, laravel/breeze, laravel/pail, laravel/pao, laravel/pint, mockery/mockery, nunomaduro/collision (+1 more)

### Community 75 - "scripts"
Cohesion: 0.22
Nodes (9): scripts, dev, post-autoload-dump, post-create-project-cmd, post-root-package-install, post-update-cmd, pre-package-uninstall, setup (+1 more)

### Community 76 - "[v0.1] – MVP – 22/11/2026"
Cohesion: 0.40
Nodes (4): Adelantado de la v1.0, Añadido, Changelog, [v0.1] – MVP – 22/11/2026

### Community 79 - "Illuminate\Database\Eloquent\Factories\Factory"
Cohesion: 0.22
Nodes (3): CategoriaFactory, PlatoFactory, RestauranteFactory

### Community 81 - "config"
Cohesion: 0.29
Nodes (7): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, preferred-install, sort-packages

### Community 87 - "Illuminate\Foundation\Testing\RefreshDatabase"
Cohesion: 0.13
Nodes (5): User, Entrada 14, PasswordUpdateTest, RedireccionTrasLoginTest, SelectorVistaTest

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
Cohesion: 0.15
Nodes (12): 1. Arquitectura, 2. Base de datos, 3. Rutas, 4. Estructura de carpetas (lo nuestro), Admin (`auth` + `rol:admin`), Camarero (`auth` + `rol:camarero`), Cocina (`auth` + `rol:cocina`), Diagrama entidad-relación (+4 more)

### Community 135 - "Pedido"
Cohesion: 0.27
Nodes (7): {closure#4}(), {closure#1}(), {closure#2}(), {closure#1}(), Pedido, Entrada 17, Entrada 19

### Community 136 - "Entrada 22"
Cohesion: 0.24
Nodes (3): Entrada 22, ExampleTest, RepartoCuentaTest

### Community 137 - ".delQr"
Cohesion: 0.29
Nodes (3): AutopedidoController, CartaController, Entrada 20

### Community 138 - "1. Requisitos funcionales"
Cohesion: 0.18
Nodes (10): 1. Requisitos funcionales, 2. Requisitos no funcionales, Acceso, roles y restaurantes, Carta y alérgenos (admin), Cliente por QR (sin login), Comandas, cocina y avisos (personal), Estadísticas (admin), Extras (🟣, solo si sobra tiempo) (+2 more)

### Community 141 - "LineaPedido"
Cohesion: 0.29
Nodes (3): LineaPedido, {closure#1}(), TODO en castellano (obligatorio)

### Community 143 - "Entrada 15"
Cohesion: 0.33
Nodes (3): MesaFactory, Entrada 15, Entrada 18

### Community 145 - "3. Resto de tests por funcionalidad"
Cohesion: 0.40
Nodes (4): 1. Cómo ejecutarlos, 3. Resto de tests por funcionalidad, 4. Pruebas manuales en el navegador, Pruebas de MesaYa

## Knowledge Gaps
- **6 isolated node(s):** `profile.partials.delete-user-form`, `profile.partials.update-password-form`, `profile.partials.update-profile-information-form`, `layouts.navigation`, `admin.carta._formulario` (+1 more)
  These have ≤1 connection - possible missing edges or undocumented components. (Counts symbols only; 452 node(s) total have ≤1 connection when file, concept and rationale nodes are included.)
- **104 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `Illuminate\Foundation\Testing\RefreshDatabase` to `Plato`, `ComandasCamareroTest`, `GestionCartaTest`, `MesasTest`, `LoginRequest`, `Mesa`, `PasswordResetTest.php`, `UserFactory.php`, `LineaPedido`, `Entrada 9`, `ComandasCamareroTest`, `EmailVerificationTest.php`, `Illuminate\Database\Eloquent\Model`, `Controller`?**
  _High betweenness centrality (0.072) - this node is a cross-community bridge._
- **What connects `profile.partials.delete-user-form`, `profile.partials.update-password-form`, `profile.partials.update-profile-information-form` to the rest of the system?**
  _6 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `package.json` be split into smaller, more focused modules?**
  _Cohesion score 0.06923076923076923 - nodes in this community are weakly interconnected._
- **Why does `3. Resto de tests por funcionalidad` connect `3. Resto de tests por funcionalidad` to `AutopedidoTest`, `Plato`, `ComandasCamareroTest`, `PantallaCocinaTest`, `GestionCartaTest`, `PagoStripeTest.php`, `MesasTest`, `Pedido.php`, `ComponentesDisenoTest`, `AislamientoRestaurantesTest`, `ComandasCamareroTest`, `ComprobarRolTest`, `ProfileTest`, `CartaPublicaTest`, `Illuminate\Foundation\Testing\RefreshDatabase`?**
  _High betweenness centrality (0.067) - this node is a cross-community bridge._
- **Should `MesaYa — Instrucciones para Claude Code` be split into smaller, more focused modules?**
  _Cohesion score 0.11764705882352941 - nodes in this community are weakly interconnected._
- **Why does `Mesa` connect `Mesa` to `AutopedidoTest`, `Plato`, `ComandasCamareroTest`, `Illuminate\View\View`, `Illuminate\Http\Request`, `.delQr`, `Entrada 15`, `ComandasCamareroTest`, `Illuminate\Database\Eloquent\Model`, `Illuminate\Foundation\Testing\RefreshDatabase`, `web.php`?**
  _High betweenness centrality (0.067) - this node is a cross-community bridge._
- **Should `Propuesta inicial: MesaYa` be split into smaller, more focused modules?**
  _Cohesion score 0.13333333333333333 - nodes in this community are weakly interconnected._