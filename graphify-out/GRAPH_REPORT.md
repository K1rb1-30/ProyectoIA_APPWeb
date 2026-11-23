# Graph Report - ProyectoIA_APPWeb  (2026-11-23)

## Corpus Check
- 224 files · ~78,103 words
- Verdict: corpus is large enough that graph structure adds value.
- Unclassified: 11 file(s) not represented in the graph (top: (none) 7, .example 1, .xml 1)

## Summary
- 1237 nodes · 2563 edges · 152 communities (43 shown, 109 thin omitted)
- Extraction: 89% EXTRACTED · 11% INFERRED · 0% AMBIGUOUS · INFERRED: 274 edges (avg confidence: 0.93)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `39fa5a06`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- Registro de uso de IA
- composer.json
- package.json
- Illuminate\Database\Schema\Blueprint
- 2. Qué incluye cada rama
- Propuesta inicial: MesaYa
- bootstrap/app.php
- Illuminate\Foundation\Http\FormRequest
- 3. Resto de tests por funcionalidad
- Prompts por pantalla
- README.md
- CartaPublicaTest
- AppServiceProvider
- MesaYa — Instrucciones para Claude Code
- Registro de uso de IA
- Laravel Application
- VentasDemoSeeder.php
- ParteCuenta
- Pedido
- .claude/CLAUDE.md
- CobroCamareroTest
- edit.blade.php
- layouts.navigation
- PantallaCocinaTest
- EstadisticasTest
- ComandasCamareroTest
- Illuminate\View\View
- GestionCartaTest
- LineaPedido
- PagoStripeTest.php
- Plato
- Illuminate\Database\Eloquent\Model
- PasswordResetTest.php
- require-dev
- scripts
- [v0.1] – MVP – 22/11/2026
- Mesa
- Entrada 9
- Illuminate\Http\JsonResponse
- config
- EmailVerificationTest.php
- VerifyEmailController.php
- AvisosCamareroTest
- Illuminate\Http\Request
- User
- psr-4
- require
- autoload-dev
- extra
- crear.blade.php
- editar.blade.php
- Entrada 15
- 3. Rutas
- Instalación de MesaYa en local
- Illuminate\View\Component
- AvisoController
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
- EstadisticasTest.php
- MesasTest
- detalle-mesa.blade.php
- 1. Requisitos funcionales
- DividirCuentaTest
- web.php
- Plato
- TestCase
- cobro-efectivo.blade.php

## God Nodes (most connected - your core abstractions)
1. `Mesa` - 61 edges
2. `TestCase` - 54 edges
3. `User` - 50 edges
4. `Controller` - 49 edges
5. `Pedido` - 45 edges
6. `Restaurante` - 42 edges
7. `ParteCuenta` - 42 edges
8. `Plato` - 41 edges
9. `Registro de uso de IA` - 31 edges
10. `Categoria` - 31 edges

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

## Communities (152 total, 109 thin omitted)

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

### Community 4 - "2. Qué incluye cada rama"
Cohesion: 0.05
Nodes (28): 10. `feature/dividir-cuenta`, 11. `feature/pago-stripe`, 12. `feature/efectivo-y-cierre`, 13. `feature/estadisticas`, 14. `feature/platos-compartidos` 🟠, 15. `feature/pagar-al-pedir` 🟠, 16. `feature/pagina-inicio` 🟠, 1. Seguimiento (+20 more)

### Community 5 - "Propuesta inicial: MesaYa"
Cohesion: 0.13
Nodes (14): 10. Riesgos y cómo los abordamos, 1. La idea en una frase, 2. Problema que resuelve, 3. Usuarios y roles, 4. Funcionalidades, 5. Flujo de la demo, 6. Tecnologías, 7. Reparto del trabajo (+6 more)

### Community 6 - "bootstrap/app.php"
Cohesion: 0.32
Nodes (3): {closure#1}(), {closure#2}(), {closure#3}()

### Community 7 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.06
Nodes (7): CategoriaController, CategoriaRequest, LoginRequest, ComandaRequest, DividirCuentaRequest, PedidoClienteRequest, ProfileUpdateRequest

### Community 8 - "3. Resto de tests por funcionalidad"
Cohesion: 0.22
Nodes (5): 1. Cómo ejecutarlos, 2. Golden tests, 3. Resto de tests por funcionalidad, 4. Pruebas manuales en el navegador, Pruebas de MesaYa

### Community 9 - "Prompts por pantalla"
Cohesion: 0.25
Nodes (7): Admin (escritorio), Camarero (móvil), Cliente (móvil), Cocina (Tablet horizontal), Prompt Maestro, PROMPTS para la IA de diseño Web STITCH, Prompts por pantalla

### Community 10 - "README.md"
Cohesion: 0.25
Nodes (7): About Laravel, Agentic Development, Code of Conduct, Contributing, Learning Laravel, License, Security Vulnerabilities

### Community 13 - "MesaYa — Instrucciones para Claude Code"
Cohesion: 0.11
Nodes (17): Comandos útiles, Cómo tienes que trabajar con nosotros, Diseño visual (obligatorio para cualquier vista), Documentación del repo, Funcionalidades y prioridad, Git y GitHub, Grafo del proyecto (graphify), MesaYa — Instrucciones para Claude Code (+9 more)

### Community 14 - "Registro de uso de IA"
Cohesion: 0.05
Nodes (20): DatabaseSeeder, UsuariosAdminSeeder, Entrada 1, Entrada 10, Entrada 11, Entrada 16, Entrada 2, Entrada 23 (+12 more)

### Community 17 - "Laravel Application"
Cohesion: 0.50
Nodes (3): Agent Setup, Laravel Application, Prerequisites

### Community 19 - "VentasDemoSeeder.php"
Cohesion: 0.16
Nodes (4): AutopedidoController, {closure#2}(), CartaController, {closure#2}()

### Community 21 - "ParteCuenta"
Cohesion: 0.06
Nodes (17): CobroController, {closure#1}(), PagoTarjetaController, {closure#1}(), {closure#1}(), Cuenta, {closure#3}(), {closure#4}() (+9 more)

### Community 22 - "Pedido"
Cohesion: 0.25
Nodes (9): {closure#1}(), {closure#2}(), {closure#1}(), {closure#2}(), {closure#3}(), {closure#1}(), Pedido, Entrada 17 (+1 more)

### Community 34 - "edit.blade.php"
Cohesion: 0.50
Nodes (3): profile.partials.delete-user-form, profile.partials.update-password-form, profile.partials.update-profile-information-form

### Community 65 - "EstadisticasTest"
Cohesion: 0.10
Nodes (6): EstadisticasRestaurante, EstadisticaController, {closure#1}(), VentasDemoSeeder, Entrada 29, EstadisticasTest

### Community 67 - "Illuminate\View\View"
Cohesion: 0.12
Nodes (7): AlergenoController, MesaController, AuthenticatedSessionController, ConfirmablePasswordController, EmailVerificationPromptController, ComandaController, Controller

### Community 69 - "LineaPedido"
Cohesion: 0.24
Nodes (5): {closure#1}(), {closure#2}(), {closure#2}(), LineaPedido, {closure#1}()

### Community 70 - "PagoStripeTest.php"
Cohesion: 0.15
Nodes (10): {closure#1}(), {closure#1}(), {closure#2}(), {closure#3}(), {closure#4}(), {closure#5}(), {closure#6}(), {closure#7}() (+2 more)

### Community 71 - "Plato"
Cohesion: 0.16
Nodes (3): PlatoController, PlatoRequest, Plato

### Community 74 - "require-dev"
Cohesion: 0.22
Nodes (9): require-dev, fakerphp/faker, laravel/breeze, laravel/pail, laravel/pao, laravel/pint, mockery/mockery, nunomaduro/collision (+1 more)

### Community 75 - "scripts"
Cohesion: 0.22
Nodes (9): scripts, dev, post-autoload-dump, post-create-project-cmd, post-root-package-install, post-update-cmd, pre-package-uninstall, setup (+1 more)

### Community 76 - "[v0.1] – MVP – 22/11/2026"
Cohesion: 0.40
Nodes (4): Adelantado de la v1.0, Añadido, Changelog, [v0.1] – MVP – 22/11/2026

### Community 77 - "Mesa"
Cohesion: 0.15
Nodes (6): DividirCuenta, DetalleMesaController, CuentaController, PagoEfectivoController, Mesa, Decisiones importantes

### Community 79 - "Entrada 9"
Cohesion: 0.05
Nodes (10): ComprobarRol, Roles, CategoriaFactory, PlatoFactory, RestauranteFactory, UserFactory, 1. `feature/restaurantes-y-roles`, Entrada 13 (+2 more)

### Community 81 - "config"
Cohesion: 0.29
Nodes (7): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, preferred-install, sort-packages

### Community 85 - "Illuminate\Http\Request"
Cohesion: 0.09
Nodes (6): EmailVerificationNotificationController, {closure#1}(), NewPasswordController, PasswordController, PasswordResetLinkController, ProfileController

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

### Community 109 - "Entrada 15"
Cohesion: 0.17
Nodes (4): MesaFactory, Entrada 15, Entrada 18, AislamientoRestaurantesTest

### Community 110 - "3. Rutas"
Cohesion: 0.17
Nodes (11): 1. Arquitectura, 2. Base de datos, 3. Rutas, 4. Estructura de carpetas (lo nuestro), Admin (`auth` + `rol:admin`), Camarero (`auth` + `rol:camarero`), Cocina (`auth` + `rol:cocina`), Diagrama entidad-relación (+3 more)

### Community 111 - "Instalación de MesaYa en local"
Cohesion: 0.13
Nodes (14): 1. Requisitos, 2. Descargar el proyecto, 3. Bases de datos, 4. Archivo `.env`, 5. Tablas, datos de prueba y fotos, 6. Arrancar, 7. Problemas frecuentes, 8. Despliegue (+6 more)

### Community 115 - "0001_01_01_000002_create_jobs_table.php"
Cohesion: 0.33
Nodes (3): {closure#1}(), {closure#2}(), {closure#3}()

### Community 129 - "EstadisticasTest.php"
Cohesion: 0.25
Nodes (3): CartaSeeder, MesasSeeder, RestaurantesSeeder

### Community 138 - "1. Requisitos funcionales"
Cohesion: 0.17
Nodes (11): 1. Requisitos funcionales, 2. Requisitos no funcionales, Acceso, roles y restaurantes, Carta y alérgenos (admin), Cliente por QR (sin login), Comandas, cocina y avisos (personal), Estadísticas (admin), Extras (🟣, solo si sobra tiempo) (+3 more)

### Community 140 - "DividirCuentaTest"
Cohesion: 0.09
Nodes (8): CrearPedido, {closure#3}(), Entrada 22, Entrada 28, DividirCuentaTest, PlatosCompartidosTest, ExampleTest, RepartoCuentaTest

### Community 143 - "Plato"
Cohesion: 0.14
Nodes (9): Alergeno, Categoria, Pago, Restaurante, Base de datos, AlergenosSeeder, Tablas, Entrada 12 (+1 more)

### Community 150 - "TestCase"
Cohesion: 0.09
Nodes (8): Entrada 14, AuthenticationTest, PasswordConfirmationTest, PasswordUpdateTest, ExampleTest, ProfileTest, SelectorVistaTest, TestCase

## Knowledge Gaps
- **8 isolated node(s):** `camarero.partes.cobro-efectivo`, `camarero.partes.cobro-efectivo`, `profile.partials.delete-user-form`, `profile.partials.update-password-form`, `profile.partials.update-profile-information-form` (+3 more)
  These have ≤1 connection - possible missing edges or undocumented components. (Counts symbols only; 495 node(s) total have ≤1 connection when file, concept and rationale nodes are included.)
- **109 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `Mesa` connect `Mesa` to `Registro de uso de IA`, `EstadisticasTest`, `ComandasCamareroTest`, `Illuminate\View\View`, `EstadisticasTest.php`, `PagoStripeTest.php`, `Plato`, `Illuminate\Database\Eloquent\Model`, `Entrada 15`, `web.php`, `Plato`, `Entrada 9`, `VentasDemoSeeder.php`, `Illuminate\Http\Request`, `Pedido`, `ParteCuenta`, `User`, `CobroCamareroTest`?**
  _High betweenness centrality (0.089) - this node is a cross-community bridge._
- **Are the 4 inferred relationships involving `Mesa` (e.g. with `Base de datos` and `Decisiones importantes`) actually correct?**
  _`Mesa` has 4 INFERRED edges - model-reasoned connections that need verification._
- **What connects `camarero.partes.cobro-efectivo`, `camarero.partes.cobro-efectivo`, `profile.partials.delete-user-form` to the rest of the system?**
  _8 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `package.json` be split into smaller, more focused modules?**
  _Cohesion score 0.06342494714587738 - nodes in this community are weakly interconnected._
- **Why does `User` connect `User` to `EstadisticasTest.php`, `2. Qué incluye cada rama`, `MesasTest`, `Illuminate\Foundation\Http\FormRequest`, `LineaPedido`, `MesaYa — Instrucciones para Claude Code`, `Plato`, `ParteCuenta`, `TestCase`, `CobroCamareroTest`, `PantallaCocinaTest`, `ComandasCamareroTest`, `GestionCartaTest`, `Plato`, `Illuminate\Database\Eloquent\Model`, `PasswordResetTest.php`, `Mesa`, `Entrada 9`, `EmailVerificationTest.php`, `AvisosCamareroTest`, `Illuminate\Http\Request`?**
  _High betweenness centrality (0.078) - this node is a cross-community bridge._
- **Should `2. Qué incluye cada rama` be split into smaller, more focused modules?**
  _Cohesion score 0.05405405405405406 - nodes in this community are weakly interconnected._
- **Why does `TestCase` connect `TestCase` to `Registro de uso de IA`, `EstadisticasTest.php`, `MesasTest`, `CartaPublicaTest`, `Registro de uso de IA`, `Plato`, `ParteCuenta`, `CobroCamareroTest`, `PantallaCocinaTest`, `EstadisticasTest`, `ComandasCamareroTest`, `GestionCartaTest`, `PagoStripeTest.php`, `Plato`, `Illuminate\Database\Eloquent\Model`, `PasswordResetTest.php`, `Entrada 9`, `EmailVerificationTest.php`, `AvisosCamareroTest`, `User`, `Entrada 15`?**
  _High betweenness centrality (0.074) - this node is a cross-community bridge._