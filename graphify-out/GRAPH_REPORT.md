# Graph Report - ProyectoIA_APPWeb  (2026-11-24)

## Corpus Check
- 235 files · ~89,067 words
- Verdict: corpus is large enough that graph structure adds value.
- Unclassified: 11 file(s) not represented in the graph (top: (none) 7, .example 1, .xml 1)

## Summary
- 1354 nodes · 2905 edges · 156 communities (45 shown, 111 thin omitted)
- Extraction: 90% EXTRACTED · 10% INFERRED · 0% AMBIGUOUS · INFERRED: 297 edges (avg confidence: 0.93)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `5a201209`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- AutopedidoTest
- composer.json
- package.json
- Illuminate\Database\Schema\Blueprint
- Registro de uso de IA
- Propuesta inicial: MesaYa
- bootstrap/app.php
- ProfileController.php
- 3. Resto de tests por funcionalidad
- Prompts por pantalla
- README.md
- CartaPublicaTest
- AppServiceProvider
- MesaYa — Instrucciones para Claude Code
- ComponentesDisenoTest
- Laravel Application
- PedidoController.php
- ParteCuenta
- PagoStripeTest
- .claude/CLAUDE.md
- DividirCuenta
- edit.blade.php
- layouts.navigation
- PantallaCocinaTest
- Pago
- EstadisticasTest
- ComandasCamareroTest
- web.php
- GestionCartaTest
- AvisosCamareroTest
- AutopedidoController.php
- Illuminate\Foundation\Http\FormRequest
- Pedido
- PasswordResetTest.php
- require-dev
- scripts
- [v0.1] – MVP – 22/11/2026
- Illuminate\Http\RedirectResponse
- Entrada 9
- Plato
- config
- GestionPersonalTest
- 2. Qué incluye cada rama
- EmailVerificationTest.php
- Illuminate\Http\Request
- CrearPedido
- psr-4
- require
- autoload-dev
- extra
- crear.blade.php
- editar.blade.php
- InicioTest
- 3. Rutas
- Instalación de MesaYa en local
- Illuminate\View\View
- AuthenticationTest
- PasswordConfirmationTest
- 2026_11_16_000002_add_rol_y_restaurante_id_to_users_table.php
- Illuminate\Support\Facades\Schema
- Illuminate\Database\Migrations\Migration
- 2026_11_23_000003_add_cuenta_id_to_pedidos_table.php
- Illuminate\Database\Migrations\Migration
- 2026_11_16_000003_create_mesas_table.php
- 2026_11_17_000002_create_alergenos_table.php
- 2026_11_17_000003_create_platos_table.php
- 2026_11_17_000005_create_pedidos_table.php
- ExampleTest
- Cuenta
- 2026_11_23_000002_create_partes_cuenta_table.php
- 2026_11_23_000004_create_pagos_table.php
- MesasTest
- detalle-mesa.blade.php
- 1. Requisitos funcionales
- 3. Resto de tests por funcionalidad
- Controller
- Mesa
- cobro-efectivo.blade.php
- CategoriaController
- 0001_01_01_000001_create_cache_table.php
- 2026_11_23_000005_add_pedido_id_to_partes_cuenta_table.php
- EstadisticasTest.php

## God Nodes (most connected - your core abstractions)
1. `Mesa` - 81 edges
2. `User` - 73 edges
3. `TestCase` - 66 edges
4. `Pedido` - 59 edges
5. `Controller` - 53 edges
6. `Restaurante` - 50 edges
7. `Plato` - 49 edges
8. `ParteCuenta` - 45 edges
9. `Categoria` - 37 edges
10. `CrearPedido` - 36 edges

## Surprising Connections (you probably didn't know these)
- `2. Requisitos no funcionales` --references--> `PerteneceARestaurante`  [INFERRED]
  docs/02-requisits.md → app/Models/Concerns/PerteneceARestaurante.php
- `6. `feature/pantalla-cocina`` --references--> `Pedido`  [INFERRED]
  docs/plan-de-trabajo.md → app/Models/Pedido.php
- `3. Decisiones pendientes` --references--> `PasarelaStripe`  [INFERRED]
  docs/plan-de-trabajo.md → app/Servicios/PasarelaStripe.php
- `3. `feature/gestion-carta`` --references--> `AlergenosSeeder`  [INFERRED]
  docs/plan-de-trabajo.md → database/seeders/AlergenosSeeder.php
- `Decisiones importantes` --references--> `CrearPedido`  [INFERRED]
  docs/03-disseny.md → app/Acciones/CrearPedido.php

## Import Cycles
- None detected.

## Communities (156 total, 111 thin omitted)

### Community 0 - "AutopedidoTest"
Cohesion: 0.08
Nodes (6): {closure#1}(), PasarelaStripe, Entrada 31, PasarelaStripeFalsa, AutopedidoTest, PagarAlPedirTest

### Community 1 - "composer.json"
Cohesion: 0.22
Nodes (8): description, keywords, license, minimum-stability, name, prefer-stable, $schema, type

### Community 2 - "package.json"
Cohesion: 0.06
Nodes (41): dependencies, chart.js@^4.6.1, devDependencies, alpinejs@^3.4.2, autoprefixer@^10.4.2, concurrently@^10.0.3, @fontsource/ibm-plex-mono@^5.3.0, @fontsource-variable/schibsted-grotesk@^5.3.0 (+33 more)

### Community 3 - "Illuminate\Database\Schema\Blueprint"
Cohesion: 0.21
Nodes (7): {closure#1}(), {closure#2}(), {closure#3}(), {closure#1}(), {closure#2}(), {closure#3}(), {closure#1}()

### Community 4 - "Registro de uso de IA"
Cohesion: 0.06
Nodes (24): DatabaseSeeder, UsuariosAdminSeeder, Entrada 1, Entrada 10, Entrada 11, Entrada 14, Entrada 16, Entrada 2 (+16 more)

### Community 5 - "Propuesta inicial: MesaYa"
Cohesion: 0.13
Nodes (14): 10. Riesgos y cómo los abordamos, 1. La idea en una frase, 2. Problema que resuelve, 3. Usuarios y roles, 4. Funcionalidades, 5. Flujo de la demo, 6. Tecnologías, 7. Reparto del trabajo (+6 more)

### Community 6 - "bootstrap/app.php"
Cohesion: 0.32
Nodes (3): {closure#1}(), {closure#2}(), {closure#3}()

### Community 9 - "Prompts por pantalla"
Cohesion: 0.25
Nodes (7): Admin (escritorio), Camarero (móvil), Cliente (móvil), Cocina (Tablet horizontal), Prompt Maestro, PROMPTS para la IA de diseño Web STITCH, Prompts por pantalla

### Community 10 - "README.md"
Cohesion: 0.25
Nodes (7): About Laravel, Agentic Development, Code of Conduct, Contributing, Learning Laravel, License, Security Vulnerabilities

### Community 13 - "MesaYa — Instrucciones para Claude Code"
Cohesion: 0.06
Nodes (21): ComprobarRol, Comandos útiles, Cómo tienes que trabajar con nosotros, Diseño visual (obligatorio para cualquier vista), Documentación del repo, Funcionalidades y prioridad, Git y GitHub, Grafo del proyecto (graphify) (+13 more)

### Community 14 - "ComponentesDisenoTest"
Cohesion: 0.16
Nodes (3): Entrada 8, ComponentesDisenoTest, LayoutsTest

### Community 17 - "Laravel Application"
Cohesion: 0.50
Nodes (3): Agent Setup, Laravel Application, Prerequisites

### Community 19 - "PedidoController.php"
Cohesion: 0.19
Nodes (5): AvisoController, {closure#1}(), {closure#2}(), {closure#2}(), PedidoController

### Community 21 - "ParteCuenta"
Cohesion: 0.07
Nodes (13): CobroController, {closure#1}(), {closure#1}(), {closure#3}(), {closure#4}(), {closure#5}(), {closure#6}(), ParteCuenta (+5 more)

### Community 22 - "PagoStripeTest"
Cohesion: 0.15
Nodes (10): {closure#1}(), {closure#1}(), {closure#2}(), {closure#3}(), {closure#4}(), {closure#5}(), {closure#6}(), {closure#7}() (+2 more)

### Community 30 - "DividirCuenta"
Cohesion: 0.07
Nodes (16): {closure#3}(), {closure#4}(), DividirCuenta, {closure#1}(), {closure#2}(), DetalleMesaController, {closure#1}(), {closure#2}() (+8 more)

### Community 34 - "edit.blade.php"
Cohesion: 0.50
Nodes (3): profile.partials.delete-user-form, profile.partials.update-password-form, profile.partials.update-profile-information-form

### Community 58 - "PantallaCocinaTest"
Cohesion: 0.15
Nodes (3): {closure#1}(), Entrada 19, PantallaCocinaTest

### Community 59 - "Pago"
Cohesion: 0.20
Nodes (3): Pago, {closure#1}(), VentasDemoSeeder

### Community 65 - "EstadisticasTest"
Cohesion: 0.15
Nodes (3): EstadisticasRestaurante, Entrada 29, EstadisticasTest

### Community 70 - "AutopedidoController.php"
Cohesion: 0.12
Nodes (6): {closure#3}(), PagarPedido, AutopedidoController, {closure#2}(), CartaController, {closure#2}()

### Community 71 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.05
Nodes (7): PlatoController, {closure#1}(), PlatoRequest, LoginRequest, ComandaRequest, DividirCuentaRequest, PedidoClienteRequest

### Community 72 - "Pedido"
Cohesion: 0.10
Nodes (8): PerteneceARestaurante, LineaPedido, {closure#1}(), Pedido, TODO en castellano (obligatorio), Decisiones importantes, Entrada 17, RegistroDePrueba

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
Cohesion: 0.06
Nodes (9): CategoriaFactory, MesaFactory, PlatoFactory, RestauranteFactory, UserFactory, Entrada 15, Entrada 9, AislamientoRestaurantesTest (+1 more)

### Community 81 - "config"
Cohesion: 0.29
Nodes (7): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, preferred-install, sort-packages

### Community 82 - "GestionPersonalTest"
Cohesion: 0.08
Nodes (5): PersonalController, EmpleadoRequest, 18. `feature/gestion-personal` 🟠, Entrada 33, GestionPersonalTest

### Community 83 - "2. Qué incluye cada rama"
Cohesion: 0.05
Nodes (30): PanelController, 10. `feature/dividir-cuenta`, 11. `feature/pago-stripe`, 12. `feature/efectivo-y-cierre`, 13. `feature/estadisticas`, 15. `feature/pagar-al-pedir` 🟠, 16. `feature/pagina-inicio` 🟠, 17. `feature/panel-admin` 🟠 (+22 more)

### Community 85 - "Illuminate\Http\Request"
Cohesion: 0.09
Nodes (6): EmailVerificationNotificationController, EmailVerificationPromptController, {closure#1}(), NewPasswordController, PasswordController, PasswordResetLinkController

### Community 87 - "CrearPedido"
Cohesion: 0.15
Nodes (3): CrearPedido, CobroCamareroTest, PlatosCompartidosTest

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

### Community 110 - "3. Rutas"
Cohesion: 0.17
Nodes (11): 1. Arquitectura, 2. Base de datos, 3. Rutas, 4. Estructura de carpetas (lo nuestro), Admin (`auth` + `rol:admin`), Camarero (`auth` + `rol:camarero`), Cocina (`auth` + `rol:cocina`), Diagrama entidad-relación (+3 more)

### Community 111 - "Instalación de MesaYa en local"
Cohesion: 0.13
Nodes (14): 1. Requisitos, 2. Descargar el proyecto, 3. Bases de datos, 4. Archivo `.env`, 5. Tablas, datos de prueba y fotos, 6. Arrancar, 7. Problemas frecuentes, 8. Despliegue (+6 more)

### Community 112 - "Illuminate\View\View"
Cohesion: 0.16
Nodes (4): MesaController, ComandaController, AppLayout, GuestLayout

### Community 126 - "Cuenta"
Cohesion: 0.19
Nodes (6): Alergeno, Cuenta, Base de datos, AlergenosSeeder, Tablas, Entrada 12

### Community 138 - "1. Requisitos funcionales"
Cohesion: 0.15
Nodes (12): 1. Requisitos funcionales, 2. Requisitos no funcionales, Acceso, roles y restaurantes, Carta y alérgenos (admin), Cliente por QR (sin login), Comandas, cocina y avisos (personal), Estadísticas (admin), Extras (🟣, solo si sobra tiempo) (+4 more)

### Community 141 - "3. Resto de tests por funcionalidad"
Cohesion: 0.22
Nodes (5): 1. Cómo ejecutarlos, 2. Golden tests, 3. Resto de tests por funcionalidad, 4. Pruebas manuales en el navegador, Pruebas de MesaYa

### Community 142 - "Controller"
Cohesion: 0.14
Nodes (6): AlergenoController, EstadisticaController, AuthenticatedSessionController, ConfirmablePasswordController, VerifyEmailController, Controller

### Community 150 - "Mesa"
Cohesion: 0.14
Nodes (6): Categoria, Mesa, Restaurante, User, PasswordUpdateTest, TestCase

### Community 162 - "EstadisticasTest.php"
Cohesion: 0.24
Nodes (3): CartaSeeder, MesasSeeder, RestaurantesSeeder

## Knowledge Gaps
- **8 isolated node(s):** `admin.carta._formulario`, `admin.carta._formulario`, `camarero.partes.cobro-efectivo`, `camarero.partes.cobro-efectivo`, `layouts.navigation` (+3 more)
  These have ≤1 connection - possible missing edges or undocumented components. (Counts symbols only; 523 node(s) total have ≤1 connection when file, concept and rationale nodes are included.)
- **111 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `Mesa` to `AutopedidoTest`, `MesasTest`, `ParteCuenta`, `EstadisticasTest.php`, `PantallaCocinaTest`, `ComandasCamareroTest`, `web.php`, `GestionCartaTest`, `AvisosCamareroTest`, `Illuminate\Foundation\Http\FormRequest`, `Pedido`, `PasswordResetTest.php`, `Entrada 9`, `Plato`, `GestionPersonalTest`, `2. Qué incluye cada rama`, `EmailVerificationTest.php`, `Illuminate\Http\Request`, `CrearPedido`, `Cuenta`?**
  _High betweenness centrality (0.119) - this node is a cross-community bridge._
- **Are the 4 inferred relationships involving `Mesa` (e.g. with `Base de datos` and `Decisiones importantes`) actually correct?**
  _`Mesa` has 4 INFERRED edges - model-reasoned connections that need verification._
- **What connects `admin.carta._formulario`, `admin.carta._formulario`, `camarero.partes.cobro-efectivo` to the rest of the system?**
  _8 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `AutopedidoTest` be split into smaller, more focused modules?**
  _Cohesion score 0.08345428156748912 - nodes in this community are weakly interconnected._
- **Why does `Mesa` connect `Mesa` to `AutopedidoTest`, `CartaPublicaTest`, `ParteCuenta`, `PagoStripeTest`, `DividirCuenta`, `EstadisticasTest.php`, `Pago`, `EstadisticasTest`, `ComandasCamareroTest`, `web.php`, `AutopedidoController.php`, `Pedido`, `Illuminate\Http\RedirectResponse`, `Entrada 9`, `Plato`, `2. Qué incluye cada rama`, `Illuminate\Http\Request`, `CrearPedido`, `Illuminate\View\View`, `Cuenta`?**
  _High betweenness centrality (0.114) - this node is a cross-community bridge._
- **Are the 6 inferred relationships involving `User` (e.g. with `Base de datos` and `TODO en castellano (obligatorio)`) actually correct?**
  _`User` has 6 INFERRED edges - model-reasoned connections that need verification._
- **Should `package.json` be split into smaller, more focused modules?**
  _Cohesion score 0.06086956521739131 - nodes in this community are weakly interconnected._