# Graph Report - ProyectoIA_APPWeb  (2026-11-23)

## Corpus Check
- 235 files · ~88,370 words
- Verdict: corpus is large enough that graph structure adds value.
- Unclassified: 11 file(s) not represented in the graph (top: (none) 7, .example 1, .xml 1)

## Summary
- 1351 nodes · 2899 edges · 165 communities (47 shown, 118 thin omitted)
- Extraction: 90% EXTRACTED · 10% INFERRED · 0% AMBIGUOUS · INFERRED: 304 edges (avg confidence: 0.93)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `6f8c8dd8`
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
- Illuminate\Foundation\Http\FormRequest
- 3. Resto de tests por funcionalidad
- Prompts por pantalla
- README.md
- CartaPublicaTest
- AppServiceProvider
- MesaYa — Instrucciones para Claude Code
- ComponentesDisenoTest
- Laravel Application
- App\Http\Controllers\Cliente\CartaController
- ParteCuenta
- PagoStripeTest
- .claude/CLAUDE.md
- DividirCuenta
- edit.blade.php
- layouts.navigation
- Pedido
- LineaPedido
- EstadisticasTest
- ComandasCamareroTest
- App\Http\Controllers\Controller
- GestionCartaTest
- PersonalController
- PasarelaStripe
- Plato
- App\Models\Mesa
- PasswordResetTest.php
- require-dev
- scripts
- [v0.1] – MVP – 22/11/2026
- Illuminate\Http\RedirectResponse
- Entrada 9
- Mesa
- config
- GestionPersonalTest
- PanelAdminTest
- 2. Qué incluye cada rama
- Illuminate\Http\Request
- PlatosCompartidosTest
- psr-4
- require
- autoload-dev
- extra
- crear.blade.php
- editar.blade.php
- EmpleadoRequest
- 3. Rutas
- Instalación de MesaYa en local
- Illuminate\View\View
- 1. Seguimiento
- ComprobarRol
- 2026_11_16_000002_add_rol_y_restaurante_id_to_users_table.php
- Illuminate\Support\Facades\Schema
- Illuminate\Database\Migrations\Migration
- 2026_11_23_000003_add_cuenta_id_to_pedidos_table.php
- Illuminate\Database\Migrations\Migration
- 2026_11_16_000003_create_mesas_table.php
- 2026_11_17_000002_create_alergenos_table.php
- 2026_11_17_000003_create_platos_table.php
- 2026_11_17_000005_create_pedidos_table.php
- ComprobarRolTest
- Plato
- 2026_11_23_000002_create_partes_cuenta_table.php
- 2026_11_23_000004_create_pagos_table.php
- LoginRequest
- MesasTest
- camarero.partes.cobro-efectivo
- 1. Requisitos funcionales
- DividirCuentaTest
- 3. Resto de tests por funcionalidad
- Controller
- ComandaRequest
- DividirCuentaRequest
- User
- PedidoClienteRequest
- cobro-efectivo.blade.php
- CategoriaRequest
- AislamientoRestaurantesTest
- 0001_01_01_000001_create_cache_table.php
- 2026_11_23_000005_add_pedido_id_to_partes_cuenta_table.php
- RestaurantesSeeder
- 2. Golden tests
- Stack

## God Nodes (most connected - your core abstractions)
1. `User` - 72 edges
2. `Mesa` - 56 edges
3. `ParteCuenta` - 45 edges
4. `Pedido` - 42 edges
5. `Controller` - 39 edges
6. `TestCase` - 38 edges
7. `Plato` - 38 edges
8. `Restaurante` - 37 edges
9. `Registro de uso de IA` - 34 edges
10. `CrearPedido` - 33 edges

## Surprising Connections (you probably didn't know these)
- `2. Requisitos no funcionales` --references--> `PerteneceARestaurante`  [INFERRED]
  docs/02-requisits.md → app/Models/Concerns/PerteneceARestaurante.php
- `3. `feature/gestion-carta`` --references--> `AlergenosSeeder`  [INFERRED]
  docs/plan-de-trabajo.md → database/seeders/AlergenosSeeder.php
- `6. `feature/pantalla-cocina`` --references--> `Pedido`  [INFERRED]
  docs/plan-de-trabajo.md → app/Models/Pedido.php
- `14. `feature/platos-compartidos` 🟠` --references--> `DividirCuenta`  [INFERRED]
  docs/plan-de-trabajo.md → app/Acciones/DividirCuenta.php
- `3. Decisiones pendientes` --references--> `PasarelaStripe`  [INFERRED]
  docs/plan-de-trabajo.md → app/Servicios/PasarelaStripe.php

## Import Cycles
- None detected.

## Communities (165 total, 118 thin omitted)

### Community 0 - "AutopedidoTest"
Cohesion: 0.12
Nodes (3): PasarelaStripeFalsa, AutopedidoTest, PagarAlPedirTest

### Community 1 - "composer.json"
Cohesion: 0.22
Nodes (8): description, keywords, license, minimum-stability, name, prefer-stable, $schema, type

### Community 2 - "package.json"
Cohesion: 0.06
Nodes (39): dependencies, chart.js@^4.6.1, devDependencies, alpinejs@^3.4.2, autoprefixer@^10.4.2, concurrently@^10.0.3, @fontsource-variable/inter@^5.3.0, laravel-vite-plugin@^3.1 (+31 more)

### Community 3 - "Illuminate\Database\Schema\Blueprint"
Cohesion: 0.21
Nodes (7): {closure#1}(), {closure#2}(), {closure#3}(), {closure#1}(), {closure#2}(), {closure#3}(), {closure#1}()

### Community 4 - "Registro de uso de IA"
Cohesion: 0.05
Nodes (22): InicioController, DatabaseSeeder, UsuariosAdminSeeder, 16. `feature/pagina-inicio` 🟠, Entrada 1, Entrada 10, Entrada 11, Entrada 16 (+14 more)

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
Cohesion: 0.14
Nodes (13): Comandos útiles, Cómo tienes que trabajar con nosotros, Diseño visual (obligatorio para cualquier vista), Documentación del repo, Funcionalidades y prioridad, Git y GitHub, Grafo del proyecto (graphify), MesaYa — Instrucciones para Claude Code (+5 more)

### Community 14 - "ComponentesDisenoTest"
Cohesion: 0.16
Nodes (3): Entrada 8, ComponentesDisenoTest, LayoutsTest

### Community 17 - "Laravel Application"
Cohesion: 0.50
Nodes (3): Agent Setup, Laravel Application, Prerequisites

### Community 21 - "ParteCuenta"
Cohesion: 0.07
Nodes (14): CobroController, DetalleMesaController, {closure#1}(), {closure#1}(), Cuenta, {closure#3}(), {closure#4}(), {closure#5}() (+6 more)

### Community 22 - "PagoStripeTest"
Cohesion: 0.15
Nodes (10): {closure#1}(), {closure#1}(), {closure#2}(), {closure#3}(), {closure#4}(), {closure#5}(), {closure#6}(), {closure#7}() (+2 more)

### Community 30 - "DividirCuenta"
Cohesion: 0.11
Nodes (8): {closure#3}(), {closure#4}(), DividirCuenta, Entrada 22, Entrada 28, CobroCamareroTest, ExampleTest, RepartoCuentaTest

### Community 34 - "edit.blade.php"
Cohesion: 0.50
Nodes (3): profile.partials.delete-user-form, profile.partials.update-password-form, profile.partials.update-profile-information-form

### Community 58 - "Pedido"
Cohesion: 0.06
Nodes (20): AvisoController, {closure#1}(), {closure#2}(), {closure#1}(), {closure#2}(), {closure#1}(), {closure#2}(), {closure#3}() (+12 more)

### Community 59 - "LineaPedido"
Cohesion: 0.12
Nodes (3): LineaPedido, {closure#1}(), TODO en castellano (obligatorio)

### Community 65 - "EstadisticasTest"
Cohesion: 0.09
Nodes (7): EstadisticasRestaurante, PanelController, {closure#1}(), VentasDemoSeeder, 17. `feature/panel-admin` 🟠, Entrada 29, EstadisticasTest

### Community 67 - "App\Http\Controllers\Controller"
Cohesion: 0.16
Nodes (4): ConfirmablePasswordController, EmailVerificationNotificationController, EmailVerificationPromptController, VerifyEmailController

### Community 70 - "PasarelaStripe"
Cohesion: 0.12
Nodes (7): PagarPedido, AutopedidoController, {closure#2}(), {closure#1}(), PasarelaStripe, Entrada 24, Entrada 31

### Community 74 - "require-dev"
Cohesion: 0.22
Nodes (9): require-dev, fakerphp/faker, laravel/breeze, laravel/pail, laravel/pao, laravel/pint, mockery/mockery, nunomaduro/collision (+1 more)

### Community 75 - "scripts"
Cohesion: 0.22
Nodes (9): scripts, dev, post-autoload-dump, post-create-project-cmd, post-root-package-install, post-update-cmd, pre-package-uninstall, setup (+1 more)

### Community 76 - "[v0.1] – MVP – 22/11/2026"
Cohesion: 0.40
Nodes (4): Adelantado de la v1.0, Añadido, Changelog, [v0.1] – MVP – 22/11/2026

### Community 77 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.22
Nodes (3): CuentaController, PagoEfectivoController, PagoTarjetaController

### Community 79 - "Entrada 9"
Cohesion: 0.08
Nodes (6): CategoriaFactory, PlatoFactory, RestauranteFactory, UserFactory, Entrada 9, RedireccionTrasLoginTest

### Community 80 - "Mesa"
Cohesion: 0.12
Nodes (7): CrearPedido, PerteneceARestaurante, Mesa, Pago, Decisiones importantes, 15. `feature/pagar-al-pedir` 🟠, RegistroDePrueba

### Community 81 - "config"
Cohesion: 0.29
Nodes (7): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, preferred-install, sort-packages

### Community 84 - "2. Qué incluye cada rama"
Cohesion: 0.14
Nodes (14): 10. `feature/dividir-cuenta`, 11. `feature/pago-stripe`, 12. `feature/efectivo-y-cierre`, 13. `feature/estadisticas`, 14. `feature/platos-compartidos` 🟠, 2. `feature/diseno-base`, 2. Qué incluye cada rama, 3. `feature/gestion-carta` (+6 more)

### Community 85 - "Illuminate\Http\Request"
Cohesion: 0.13
Nodes (4): {closure#1}(), NewPasswordController, PasswordController, PasswordResetLinkController

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

### Community 109 - "EmpleadoRequest"
Cohesion: 0.23
Nodes (3): {closure#1}(), EmpleadoRequest, Entrada 33

### Community 110 - "3. Rutas"
Cohesion: 0.17
Nodes (11): 1. Arquitectura, 2. Base de datos, 3. Rutas, 4. Estructura de carpetas (lo nuestro), Admin (`auth` + `rol:admin`), Camarero (`auth` + `rol:camarero`), Cocina (`auth` + `rol:cocina`), Diagrama entidad-relación (+3 more)

### Community 111 - "Instalación de MesaYa en local"
Cohesion: 0.13
Nodes (14): 1. Requisitos, 2. Descargar el proyecto, 3. Bases de datos, 4. Archivo `.env`, 5. Tablas, datos de prueba y fotos, 6. Arrancar, 7. Problemas frecuentes, 8. Despliegue (+6 more)

### Community 112 - "Illuminate\View\View"
Cohesion: 0.19
Nodes (4): MesaController, ComandaController, AppLayout, GuestLayout

### Community 114 - "1. Seguimiento"
Cohesion: 0.18
Nodes (10): 1. Seguimiento, 3. Decisiones pendientes, 4. Cómo pedir cada rama a Claude Code, 🟢 MVP (v0.1) — objetivo: 23/11, Plan de trabajo de MesaYa, Reparto propuesto, Semana 4 — hasta el 07/12, 🟠 Siguientes: mejoras propias (Iván, mientras Gabriel hace la 13) (+2 more)

### Community 115 - "ComprobarRol"
Cohesion: 0.32
Nodes (4): ComprobarRol, Roles, 1. `feature/restaurantes-y-roles`, Entrada 13

### Community 126 - "Plato"
Cohesion: 0.13
Nodes (10): Alergeno, Categoria, Plato, Restaurante, Base de datos, AlergenosSeeder, CartaSeeder, MesasSeeder (+2 more)

### Community 138 - "1. Requisitos funcionales"
Cohesion: 0.15
Nodes (12): 1. Requisitos funcionales, 2. Requisitos no funcionales, Acceso, roles y restaurantes, Carta y alérgenos (admin), Cliente por QR (sin login), Comandas, cocina y avisos (personal), Estadísticas (admin), Extras (🟣, solo si sobra tiempo) (+4 more)

### Community 141 - "3. Resto de tests por funcionalidad"
Cohesion: 0.20
Nodes (6): 1. Cómo ejecutarlos, 3. Resto de tests por funcionalidad, 4. Pruebas manuales en el navegador, Pruebas de MesaYa, Entrada 14, SelectorVistaTest

### Community 142 - "Controller"
Cohesion: 0.08
Nodes (6): AlergenoController, CategoriaController, EstadisticaController, AuthenticatedSessionController, Controller, ProfileController

### Community 150 - "User"
Cohesion: 0.08
Nodes (7): User, AuthenticationTest, EmailVerificationTest, PasswordConfirmationTest, PasswordUpdateTest, ExampleTest, TestCase

### Community 156 - "AislamientoRestaurantesTest"
Cohesion: 0.20
Nodes (3): MesaFactory, Entrada 15, AislamientoRestaurantesTest

### Community 164 - "Stack"
Cohesion: 0.67
Nodes (3): MySQL, PHP en local (`php.ini`), Stack

## Knowledge Gaps
- **8 isolated node(s):** `camarero.partes.cobro-efectivo`, `camarero.partes.cobro-efectivo`, `profile.partials.delete-user-form`, `profile.partials.update-password-form`, `profile.partials.update-profile-information-form` (+3 more)
  These have ≤1 connection - possible missing edges or undocumented components. (Counts symbols only; 521 node(s) total have ≤1 connection when file, concept and rationale nodes are included.)
- **118 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `AutopedidoTest`, `Registro de uso de IA`, `MesasTest`, `Illuminate\Foundation\Http\FormRequest`, `Controller`, `ParteCuenta`, `DividirCuenta`, `RestaurantesSeeder`, `Pedido`, `LineaPedido`, `ComandasCamareroTest`, `GestionCartaTest`, `PersonalController`, `App\Models\Mesa`, `PasswordResetTest.php`, `Entrada 9`, `Mesa`, `GestionPersonalTest`, `PanelAdminTest`, `Illuminate\Http\Request`, `EmpleadoRequest`, `Plato`?**
  _High betweenness centrality (0.119) - this node is a cross-community bridge._
- **Are the 6 inferred relationships involving `User` (e.g. with `Base de datos` and `TODO en castellano (obligatorio)`) actually correct?**
  _`User` has 6 INFERRED edges - model-reasoned connections that need verification._
- **What connects `camarero.partes.cobro-efectivo`, `camarero.partes.cobro-efectivo`, `profile.partials.delete-user-form` to the rest of the system?**
  _8 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `AutopedidoTest` be split into smaller, more focused modules?**
  _Cohesion score 0.12051282051282051 - nodes in this community are weakly interconnected._
- **Why does `Mesa` connect `Mesa` to `EstadisticasTest`, `ComandasCamareroTest`, `PasarelaStripe`, `App\Models\Mesa`, `CartaPublicaTest`, `Illuminate\Http\RedirectResponse`, `Illuminate\View\View`, `App\Http\Controllers\Cliente\CartaController`, `ParteCuenta`, `Illuminate\Http\Request`, `DividirCuenta`, `PagoStripeTest`, `Pedido`, `LineaPedido`, `AislamientoRestaurantesTest`, `Plato`?**
  _High betweenness centrality (0.062) - this node is a cross-community bridge._
- **Are the 4 inferred relationships involving `Mesa` (e.g. with `Base de datos` and `Decisiones importantes`) actually correct?**
  _`Mesa` has 4 INFERRED edges - model-reasoned connections that need verification._
- **Should `package.json` be split into smaller, more focused modules?**
  _Cohesion score 0.06342494714587738 - nodes in this community are weakly interconnected._