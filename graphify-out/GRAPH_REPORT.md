# Graph Report - ProyectoIA_APPWeb  (2026-11-23)

## Corpus Check
- 216 files · ~75,990 words
- Verdict: corpus is large enough that graph structure adds value.
- Unclassified: 11 file(s) not represented in the graph (top: (none) 7, .example 1, .xml 1)

## Summary
- 1194 nodes · 2465 edges · 152 communities (45 shown, 107 thin omitted)
- Extraction: 90% EXTRACTED · 10% INFERRED · 0% AMBIGUOUS · INFERRED: 258 edges (avg confidence: 0.93)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `c23ea50f`
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
- AvisosCamareroTest
- Prompts por pantalla
- README.md
- CartaPublicaTest
- AppServiceProvider
- Illuminate\Http\RedirectResponse
- ComponentesDisenoTest
- Laravel Application
- App\Http\Controllers\Cliente\AutopedidoController
- EfectivoYCierreTest
- Controller
- .claude/CLAUDE.md
- edit.blade.php
- layouts.navigation
- App\Models\Pedido
- EstadisticasTest
- ComandasCamareroTest
- Mesa
- Plato
- Illuminate\Http\Request
- PagoStripeTest.php
- Illuminate\Http\RedirectResponse
- Illuminate\Database\Eloquent\Model
- PasswordResetTest.php
- require-dev
- scripts
- [v0.1] – MVP – 22/11/2026
- CobroCamareroTest
- Entrada 9
- config
- EmailVerificationTest.php
- Illuminate\View\Component
- Registro de uso de IA
- PerteneceARestaurante.php
- User
- psr-4
- require
- autoload-dev
- extra
- crear.blade.php
- editar.blade.php
- PlatoFactory.php
- 3. Rutas
- Instalación de MesaYa en local
- SelectorVistaTest
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
- UsuariosAdminSeederTest
- Decisiones importantes
- MesasTest
- Cuenta
- detalle-mesa.blade.php
- 1. Requisitos funcionales
- ParteCuenta
- Entrada 22
- LineaPedido
- web.php
- Restaurante
- 3. Resto de tests por funcionalidad
- TestCase
- ParteCuenta.php
- Illuminate\Foundation\Testing\RefreshDatabase
- cobro-efectivo.blade.php

## God Nodes (most connected - your core abstractions)
1. `Mesa` - 67 edges
2. `TestCase` - 56 edges
3. `User` - 51 edges
4. `Pedido` - 49 edges
5. `Controller` - 48 edges
6. `Restaurante` - 43 edges
7. `ParteCuenta` - 42 edges
8. `Plato` - 42 edges
9. `Categoria` - 32 edges
10. `Cuenta` - 29 edges

## Surprising Connections (you probably didn't know these)
- `2. Requisitos no funcionales` --references--> `PerteneceARestaurante`  [INFERRED]
  docs/02-requisits.md → app/Models/Concerns/PerteneceARestaurante.php
- `6. `feature/pantalla-cocina`` --references--> `Pedido`  [INFERRED]
  docs/plan-de-trabajo.md → app/Models/Pedido.php
- `3. Decisiones pendientes` --references--> `PasarelaStripe`  [INFERRED]
  docs/plan-de-trabajo.md → app/Servicios/PasarelaStripe.php
- `Decisiones importantes` --references--> `CrearPedido`  [INFERRED]
  docs/03-disseny.md → app/Acciones/CrearPedido.php
- `Entrada 20` --references--> `CrearPedido`  [INFERRED]
  docs/registre-ia.md → app/Acciones/CrearPedido.php

## Import Cycles
- None detected.

## Communities (152 total, 107 thin omitted)

### Community 0 - "Registro de uso de IA"
Cohesion: 0.16
Nodes (5): Entrada 20, Entrada 21, avisosCamarero(), carrito(), AutopedidoTest

### Community 1 - "composer.json"
Cohesion: 0.22
Nodes (8): description, keywords, license, minimum-stability, name, prefer-stable, $schema, type

### Community 2 - "package.json"
Cohesion: 0.06
Nodes (39): dependencies, chart.js@^4.6.1, devDependencies, alpinejs@^3.4.2, autoprefixer@^10.4.2, concurrently@^10.0.3, @fontsource-variable/inter@^5.3.0, laravel-vite-plugin@^3.1 (+31 more)

### Community 3 - "Illuminate\Database\Schema\Blueprint"
Cohesion: 0.22
Nodes (6): {closure#1}(), {closure#2}(), {closure#3}(), {closure#1}(), {closure#2}(), {closure#1}()

### Community 4 - "MesaYa — Instrucciones para Claude Code"
Cohesion: 0.04
Nodes (42): ComprobarRol, Comandos útiles, Cómo tienes que trabajar con nosotros, Diseño visual (obligatorio para cualquier vista), Documentación del repo, Funcionalidades y prioridad, Git y GitHub, Grafo del proyecto (graphify) (+34 more)

### Community 5 - "Propuesta inicial: MesaYa"
Cohesion: 0.13
Nodes (14): 10. Riesgos y cómo los abordamos, 1. La idea en una frase, 2. Problema que resuelve, 3. Usuarios y roles, 4. Funcionalidades, 5. Flujo de la demo, 6. Tecnologías, 7. Reparto del trabajo (+6 more)

### Community 6 - "bootstrap/app.php"
Cohesion: 0.32
Nodes (3): {closure#1}(), {closure#2}(), {closure#3}()

### Community 7 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.07
Nodes (5): LoginRequest, ComandaRequest, DividirCuentaRequest, PedidoClienteRequest, ProfileUpdateRequest

### Community 9 - "Prompts por pantalla"
Cohesion: 0.25
Nodes (7): Admin (escritorio), Camarero (móvil), Cliente (móvil), Cocina (Tablet horizontal), Prompt Maestro, PROMPTS para la IA de diseño Web STITCH, Prompts por pantalla

### Community 10 - "README.md"
Cohesion: 0.25
Nodes (7): About Laravel, Agentic Development, Code of Conduct, Contributing, Learning Laravel, License, Security Vulnerabilities

### Community 14 - "ComponentesDisenoTest"
Cohesion: 0.16
Nodes (3): Entrada 8, ComponentesDisenoTest, LayoutsTest

### Community 17 - "Laravel Application"
Cohesion: 0.50
Nodes (3): Agent Setup, Laravel Application, Prerequisites

### Community 19 - "App\Http\Controllers\Cliente\AutopedidoController"
Cohesion: 0.17
Nodes (4): AutopedidoController, {closure#2}(), CartaController, {closure#2}()

### Community 22 - "Controller"
Cohesion: 0.10
Nodes (8): AlergenoController, ConfirmablePasswordController, EmailVerificationNotificationController, EmailVerificationPromptController, PasswordController, PasswordResetLinkController, VerifyEmailController, Controller

### Community 34 - "edit.blade.php"
Cohesion: 0.50
Nodes (3): profile.partials.delete-user-form, profile.partials.update-password-form, profile.partials.update-profile-information-form

### Community 58 - "App\Models\Pedido"
Cohesion: 0.07
Nodes (17): {closure#3}(), {closure#4}(), AvisoController, {closure#1}(), {closure#2}(), {closure#1}(), {closure#2}(), {closure#1}() (+9 more)

### Community 65 - "EstadisticasTest"
Cohesion: 0.06
Nodes (15): EstadisticasRestaurante, EstadisticaController, AlergenosSeeder, CartaSeeder, DatabaseSeeder, MesasSeeder, RestaurantesSeeder, UsuariosAdminSeeder (+7 more)

### Community 66 - "ComandasCamareroTest"
Cohesion: 0.10
Nodes (3): CrearPedido, ComandasCamareroTest, DividirCuentaTest

### Community 67 - "Mesa"
Cohesion: 0.17
Nodes (4): DividirCuenta, MesaController, CuentaController, Mesa

### Community 68 - "Plato"
Cohesion: 0.06
Nodes (8): CategoriaController, PlatoController, CategoriaRequest, PlatoRequest, Categoria, Plato, Entrada 12, GestionCartaTest

### Community 69 - "Illuminate\Http\Request"
Cohesion: 0.11
Nodes (4): AuthenticatedSessionController, {closure#1}(), NewPasswordController, ProfileController

### Community 70 - "PagoStripeTest.php"
Cohesion: 0.10
Nodes (12): PasarelaStripe, Entrada 24, {closure#1}(), {closure#1}(), {closure#2}(), {closure#3}(), {closure#4}(), {closure#5}() (+4 more)

### Community 72 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.22
Nodes (3): Alergeno, PerteneceARestaurante, RegistroDePrueba

### Community 74 - "require-dev"
Cohesion: 0.22
Nodes (9): require-dev, fakerphp/faker, laravel/breeze, laravel/pail, laravel/pao, laravel/pint, mockery/mockery, nunomaduro/collision (+1 more)

### Community 75 - "scripts"
Cohesion: 0.22
Nodes (9): scripts, dev, post-autoload-dump, post-create-project-cmd, post-root-package-install, post-update-cmd, pre-package-uninstall, setup (+1 more)

### Community 76 - "[v0.1] – MVP – 22/11/2026"
Cohesion: 0.40
Nodes (4): Adelantado de la v1.0, Añadido, Changelog, [v0.1] – MVP – 22/11/2026

### Community 81 - "config"
Cohesion: 0.29
Nodes (7): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, preferred-install, sort-packages

### Community 84 - "Registro de uso de IA"
Cohesion: 0.13
Nodes (12): MesaFactory, Entrada 1, Entrada 15, Entrada 16, Entrada 18, Entrada 2, Entrada 23, Entrada 3 (+4 more)

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
Cohesion: 0.33
Nodes (6): 3. Rutas, Admin (`auth` + `rol:admin`), Camarero (`auth` + `rol:camarero`), Cocina (`auth` + `rol:cocina`), Públicas (cliente, sin login), Todos los roles

### Community 111 - "Instalación de MesaYa en local"
Cohesion: 0.13
Nodes (14): 1. Requisitos, 2. Descargar el proyecto, 3. Bases de datos, 4. Archivo `.env`, 5. Tablas, datos de prueba y fotos, 6. Arrancar, 7. Problemas frecuentes, 8. Despliegue (+6 more)

### Community 115 - "0001_01_01_000002_create_jobs_table.php"
Cohesion: 0.33
Nodes (3): {closure#1}(), {closure#2}(), {closure#3}()

### Community 133 - "Decisiones importantes"
Cohesion: 0.29
Nodes (6): 1. Arquitectura, 2. Base de datos, 4. Estructura de carpetas (lo nuestro), Decisiones importantes, Diagrama entidad-relación, Diseño técnico de MesaYa

### Community 135 - "Cuenta"
Cohesion: 0.18
Nodes (5): {closure#1}(), {closure#1}(), Cuenta, Tablas, Entrada 25

### Community 138 - "1. Requisitos funcionales"
Cohesion: 0.18
Nodes (10): 1. Requisitos funcionales, 2. Requisitos no funcionales, Acceso, roles y restaurantes, Carta y alérgenos (admin), Cliente por QR (sin login), Comandas, cocina y avisos (personal), Estadísticas (admin), Extras (🟣, solo si sobra tiempo) (+2 more)

### Community 139 - "ParteCuenta"
Cohesion: 0.20
Nodes (6): CobroController, Pago, ParteCuenta, Base de datos, Estados y reglas, Entrada 26

### Community 140 - "Entrada 22"
Cohesion: 0.24
Nodes (3): Entrada 22, ExampleTest, RepartoCuentaTest

### Community 143 - "Restaurante"
Cohesion: 0.21
Nodes (3): Restaurante, CategoriaFactory, RestauranteFactory

### Community 145 - "3. Resto de tests por funcionalidad"
Cohesion: 0.18
Nodes (5): 1. Cómo ejecutarlos, 3. Resto de tests por funcionalidad, 4. Pruebas manuales en el navegador, Pruebas de MesaYa, AislamientoRestaurantesTest

### Community 146 - "TestCase"
Cohesion: 0.14
Nodes (5): Entrada 6, RegistroDesactivadoTest, ExampleTest, ProfileTest, TestCase

### Community 149 - "ParteCuenta.php"
Cohesion: 0.39
Nodes (3): {closure#3}(), {closure#4}(), {closure#6}()

### Community 150 - "Illuminate\Foundation\Testing\RefreshDatabase"
Cohesion: 0.12
Nodes (3): AuthenticationTest, PasswordConfirmationTest, PasswordUpdateTest

## Knowledge Gaps
- **8 isolated node(s):** `admin.carta._formulario`, `admin.carta._formulario`, `camarero.partes.cobro-efectivo`, `camarero.partes.cobro-efectivo`, `layouts.navigation` (+3 more)
  These have ≤1 connection - possible missing edges or undocumented components. (Counts symbols only; 475 node(s) total have ≤1 connection when file, concept and rationale nodes are included.)
- **107 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `Mesa` connect `Mesa` to `Registro de uso de IA`, `Decisiones importantes`, `MesasTest`, `Cuenta`, `AvisosCamareroTest`, `ParteCuenta`, `Illuminate\Http\RedirectResponse`, `web.php`, `Restaurante`, `LineaPedido`, `App\Http\Controllers\Cliente\AutopedidoController`, `EfectivoYCierreTest`, `Illuminate\Foundation\Testing\RefreshDatabase`, `App\Models\Pedido`, `EstadisticasTest`, `ComandasCamareroTest`, `Plato`, `Illuminate\Http\Request`, `PagoStripeTest.php`, `Illuminate\Http\RedirectResponse`, `Illuminate\Database\Eloquent\Model`, `CobroCamareroTest`, `Registro de uso de IA`?**
  _High betweenness centrality (0.112) - this node is a cross-community bridge._
- **Are the 4 inferred relationships involving `Mesa` (e.g. with `Base de datos` and `Decisiones importantes`) actually correct?**
  _`Mesa` has 4 INFERRED edges - model-reasoned connections that need verification._
- **What connects `admin.carta._formulario`, `admin.carta._formulario`, `camarero.partes.cobro-efectivo` to the rest of the system?**
  _8 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `package.json` be split into smaller, more focused modules?**
  _Cohesion score 0.06342494714587738 - nodes in this community are weakly interconnected._
- **Why does `TestCase` connect `TestCase` to `Registro de uso de IA`, `UsuariosAdminSeederTest`, `MesasTest`, `AvisosCamareroTest`, `CartaPublicaTest`, `LineaPedido`, `ComponentesDisenoTest`, `Restaurante`, `3. Resto de tests por funcionalidad`, `EfectivoYCierreTest`, `Illuminate\Foundation\Testing\RefreshDatabase`, `App\Models\Pedido`, `EstadisticasTest`, `ComandasCamareroTest`, `Mesa`, `Plato`, `PagoStripeTest.php`, `Illuminate\Database\Eloquent\Model`, `PasswordResetTest.php`, `CobroCamareroTest`, `EmailVerificationTest.php`, `User`, `SelectorVistaTest`, `ComprobarRolTest`?**
  _High betweenness centrality (0.083) - this node is a cross-community bridge._
- **Are the 4 inferred relationships involving `User` (e.g. with `Base de datos` and `TODO en castellano (obligatorio)`) actually correct?**
  _`User` has 4 INFERRED edges - model-reasoned connections that need verification._
- **Should `MesaYa — Instrucciones para Claude Code` be split into smaller, more focused modules?**
  _Cohesion score 0.04343971631205674 - nodes in this community are weakly interconnected._