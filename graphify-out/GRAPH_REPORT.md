# Graph Report - ProyectoIA_APPWeb  (2026-11-23)

## Corpus Check
- 230 files · ~83,964 words
- Verdict: corpus is large enough that graph structure adds value.
- Unclassified: 11 file(s) not represented in the graph (top: (none) 7, .example 1, .xml 1)

## Summary
- 1303 nodes · 2775 edges · 162 communities (48 shown, 114 thin omitted)
- Extraction: 90% EXTRACTED · 10% INFERRED · 0% AMBIGUOUS · INFERRED: 288 edges (avg confidence: 0.93)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `201fb6df`
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
- ComponentesDisenoTest
- Laravel Application
- App\Http\Controllers\Cliente\CartaController
- ParteCuenta
- PagarAlPedirTest
- .claude/CLAUDE.md
- DividirCuenta
- edit.blade.php
- layouts.navigation
- Pedido
- EstadisticasTest
- ComandasCamareroTest
- App\Http\Controllers\Controller
- GestionCartaTest
- App\Models\Pedido
- PasarelaStripe
- Plato
- App\Models\Mesa
- PasswordResetTest.php
- require-dev
- scripts
- [v0.1] – MVP – 22/11/2026
- Illuminate\Http\RedirectResponse
- Illuminate\Database\Eloquent\Factories\Factory
- Mesa
- config
- EmailVerificationTest.php
- PanelAdminTest
- AvisosCamareroTest
- Illuminate\Http\Request
- RedireccionTrasLoginTest
- psr-4
- require
- autoload-dev
- extra
- crear.blade.php
- editar.blade.php
- Entrada 15
- 3. Rutas
- Instalación de MesaYa en local
- Illuminate\View\View
- TestCase
- Registro de uso de IA
- 2026_11_16_000002_add_rol_y_restaurante_id_to_users_table.php
- Illuminate\Support\Facades\Schema
- Illuminate\Database\Migrations\Migration
- 2026_11_23_000003_add_cuenta_id_to_pedidos_table.php
- Illuminate\Database\Migrations\Migration
- 2026_11_16_000003_create_mesas_table.php
- 2026_11_17_000002_create_alergenos_table.php
- 2026_11_17_000003_create_platos_table.php
- 2026_11_17_000005_create_pedidos_table.php
- App\Http\Controllers\Admin\PlatoController
- Entrada 12
- 2026_11_23_000002_create_partes_cuenta_table.php
- 2026_11_23_000004_create_pagos_table.php
- Illuminate\Database\Seeder
- MesasTest
- camarero.partes.cobro-efectivo
- 1. Requisitos funcionales
- DividirCuentaTest
- Controller
- Restaurante
- ComprobarRol
- TestCase
- ComprobarRolTest
- Entrada 9
- cobro-efectivo.blade.php
- Entrada 20
- AislamientoRestaurantesTest
- PanelController
- 0001_01_01_000001_create_cache_table.php
- 2026_11_23_000005_add_pedido_id_to_partes_cuenta_table.php

## God Nodes (most connected - your core abstractions)
1. `Mesa` - 56 edges
2. `User` - 55 edges
3. `ParteCuenta` - 45 edges
4. `Pedido` - 42 edges
5. `Controller` - 39 edges
6. `Restaurante` - 38 edges
7. `TestCase` - 38 edges
8. `Plato` - 38 edges
9. `CrearPedido` - 33 edges
10. `Registro de uso de IA` - 32 edges

## Surprising Connections (you probably didn't know these)
- `3. Decisiones pendientes` --references--> `PasarelaStripe`  [INFERRED]
  docs/plan-de-trabajo.md → app/Servicios/PasarelaStripe.php
- `2. Requisitos no funcionales` --references--> `PerteneceARestaurante`  [INFERRED]
  docs/02-requisits.md → app/Models/Concerns/PerteneceARestaurante.php
- `6. `feature/pantalla-cocina`` --references--> `Pedido`  [INFERRED]
  docs/plan-de-trabajo.md → app/Models/Pedido.php
- `14. `feature/platos-compartidos` 🟠` --references--> `DividirCuenta`  [INFERRED]
  docs/plan-de-trabajo.md → app/Acciones/DividirCuenta.php
- `Entrada 6` --references--> `RegistroDesactivadoTest`  [INFERRED]
  docs/registre-ia.md → tests/Feature/Auth/RegistroDesactivadoTest.php

## Import Cycles
- None detected.

## Communities (162 total, 114 thin omitted)

### Community 1 - "composer.json"
Cohesion: 0.22
Nodes (8): description, keywords, license, minimum-stability, name, prefer-stable, $schema, type

### Community 2 - "package.json"
Cohesion: 0.06
Nodes (39): dependencies, chart.js@^4.6.1, devDependencies, alpinejs@^3.4.2, autoprefixer@^10.4.2, concurrently@^10.0.3, @fontsource-variable/inter@^5.3.0, laravel-vite-plugin@^3.1 (+31 more)

### Community 3 - "Illuminate\Database\Schema\Blueprint"
Cohesion: 0.21
Nodes (7): {closure#1}(), {closure#2}(), {closure#3}(), {closure#1}(), {closure#2}(), {closure#3}(), {closure#1}()

### Community 4 - "2. Qué incluye cada rama"
Cohesion: 0.05
Nodes (28): InicioController, 10. `feature/dividir-cuenta`, 11. `feature/pago-stripe`, 12. `feature/efectivo-y-cierre`, 13. `feature/estadisticas`, 14. `feature/platos-compartidos` 🟠, 15. `feature/pagar-al-pedir` 🟠, 16. `feature/pagina-inicio` 🟠 (+20 more)

### Community 5 - "Propuesta inicial: MesaYa"
Cohesion: 0.13
Nodes (14): 10. Riesgos y cómo los abordamos, 1. La idea en una frase, 2. Problema que resuelve, 3. Usuarios y roles, 4. Funcionalidades, 5. Flujo de la demo, 6. Tecnologías, 7. Reparto del trabajo (+6 more)

### Community 6 - "bootstrap/app.php"
Cohesion: 0.32
Nodes (3): {closure#1}(), {closure#2}(), {closure#3}()

### Community 7 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.08
Nodes (5): CategoriaRequest, LoginRequest, ComandaRequest, PedidoClienteRequest, ProfileUpdateRequest

### Community 8 - "3. Resto de tests por funcionalidad"
Cohesion: 0.18
Nodes (5): 1. Cómo ejecutarlos, 3. Resto de tests por funcionalidad, 4. Pruebas manuales en el navegador, Pruebas de MesaYa, ProfileTest

### Community 9 - "Prompts por pantalla"
Cohesion: 0.25
Nodes (7): Admin (escritorio), Camarero (móvil), Cliente (móvil), Cocina (Tablet horizontal), Prompt Maestro, PROMPTS para la IA de diseño Web STITCH, Prompts por pantalla

### Community 10 - "README.md"
Cohesion: 0.25
Nodes (7): About Laravel, Agentic Development, Code of Conduct, Contributing, Learning Laravel, License, Security Vulnerabilities

### Community 13 - "MesaYa — Instrucciones para Claude Code"
Cohesion: 0.11
Nodes (17): Comandos útiles, Cómo tienes que trabajar con nosotros, Diseño visual (obligatorio para cualquier vista), Documentación del repo, Funcionalidades y prioridad, Git y GitHub, Grafo del proyecto (graphify), MesaYa — Instrucciones para Claude Code (+9 more)

### Community 14 - "ComponentesDisenoTest"
Cohesion: 0.16
Nodes (3): Entrada 8, ComponentesDisenoTest, LayoutsTest

### Community 17 - "Laravel Application"
Cohesion: 0.50
Nodes (3): Agent Setup, Laravel Application, Prerequisites

### Community 21 - "ParteCuenta"
Cohesion: 0.05
Nodes (25): CobroController, {closure#1}(), {closure#1}(), {closure#3}(), {closure#4}(), {closure#5}(), {closure#6}(), ParteCuenta (+17 more)

### Community 30 - "DividirCuenta"
Cohesion: 0.07
Nodes (12): {closure#3}(), DividirCuenta, CuentaController, DividirCuentaRequest, {closure#1}(), Cuenta, Entrada 22, Entrada 25 (+4 more)

### Community 34 - "edit.blade.php"
Cohesion: 0.50
Nodes (3): profile.partials.delete-user-form, profile.partials.update-password-form, profile.partials.update-profile-information-form

### Community 58 - "Pedido"
Cohesion: 0.06
Nodes (17): AvisoController, {closure#1}(), {closure#2}(), {closure#1}(), {closure#2}(), {closure#1}(), {closure#2}(), {closure#3}() (+9 more)

### Community 65 - "EstadisticasTest"
Cohesion: 0.10
Nodes (6): EstadisticasRestaurante, Pago, {closure#1}(), VentasDemoSeeder, Entrada 29, EstadisticasTest

### Community 67 - "App\Http\Controllers\Controller"
Cohesion: 0.12
Nodes (5): AuthenticatedSessionController, ConfirmablePasswordController, EmailVerificationNotificationController, EmailVerificationPromptController, VerifyEmailController

### Community 69 - "App\Models\Pedido"
Cohesion: 0.22
Nodes (4): {closure#4}(), {closure#2}(), PerteneceARestaurante, RegistroDePrueba

### Community 70 - "PasarelaStripe"
Cohesion: 0.17
Nodes (4): {closure#3}(), PagarPedido, PasarelaStripe, Entrada 31

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
Cohesion: 0.21
Nodes (3): AutopedidoController, PagoEfectivoController, PagoTarjetaController

### Community 79 - "Illuminate\Database\Eloquent\Factories\Factory"
Cohesion: 0.22
Nodes (3): CategoriaFactory, PlatoFactory, RestauranteFactory

### Community 80 - "Mesa"
Cohesion: 0.21
Nodes (3): ComandaController, DetalleMesaController, Mesa

### Community 81 - "config"
Cohesion: 0.29
Nodes (7): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, preferred-install, sort-packages

### Community 85 - "Illuminate\Http\Request"
Cohesion: 0.10
Nodes (5): {closure#1}(), NewPasswordController, PasswordController, PasswordResetLinkController, ProfileController

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
Cohesion: 0.25
Nodes (3): MesaFactory, Entrada 15, Entrada 18

### Community 110 - "3. Rutas"
Cohesion: 0.18
Nodes (10): 1. Arquitectura, 3. Rutas, 4. Estructura de carpetas (lo nuestro), Admin (`auth` + `rol:admin`), Camarero (`auth` + `rol:camarero`), Cocina (`auth` + `rol:cocina`), Decisiones importantes, Diseño técnico de MesaYa (+2 more)

### Community 111 - "Instalación de MesaYa en local"
Cohesion: 0.13
Nodes (14): 1. Requisitos, 2. Descargar el proyecto, 3. Bases de datos, 4. Archivo `.env`, 5. Tablas, datos de prueba y fotos, 6. Arrancar, 7. Problemas frecuentes, 8. Despliegue (+6 more)

### Community 112 - "Illuminate\View\View"
Cohesion: 0.20
Nodes (3): MesaController, AppLayout, GuestLayout

### Community 114 - "TestCase"
Cohesion: 0.18
Nodes (4): PasswordConfirmationTest, RegistroDesactivadoTest, ExampleTest, TestCase

### Community 115 - "Registro de uso de IA"
Cohesion: 0.17
Nodes (11): Entrada 1, Entrada 16, Entrada 2, Entrada 23, Entrada 27, Entrada 3, Entrada 4, Entrada 5 (+3 more)

### Community 126 - "Entrada 12"
Cohesion: 0.22
Nodes (3): PlatoRequest, CartaSeeder, Entrada 12

### Community 129 - "Illuminate\Database\Seeder"
Cohesion: 0.12
Nodes (9): AlergenosSeeder, DatabaseSeeder, MesasSeeder, RestaurantesSeeder, UsuariosAdminSeeder, 3. `feature/gestion-carta`, Entrada 10, Entrada 11 (+1 more)

### Community 138 - "1. Requisitos funcionales"
Cohesion: 0.17
Nodes (11): 1. Requisitos funcionales, 2. Requisitos no funcionales, Acceso, roles y restaurantes, Carta y alérgenos (admin), Cliente por QR (sin login), Comandas, cocina y avisos (personal), Estadísticas (admin), Extras (🟣, solo si sobra tiempo) (+3 more)

### Community 140 - "DividirCuentaTest"
Cohesion: 0.14
Nodes (3): CrearPedido, DividirCuentaTest, PlatosCompartidosTest

### Community 142 - "Controller"
Cohesion: 0.11
Nodes (4): AlergenoController, CategoriaController, EstadisticaController, Controller

### Community 143 - "Restaurante"
Cohesion: 0.23
Nodes (3): Categoria, Restaurante, Base de datos

### Community 149 - "ComprobarRol"
Cohesion: 0.32
Nodes (4): ComprobarRol, Roles, 1. `feature/restaurantes-y-roles`, Entrada 13

### Community 150 - "TestCase"
Cohesion: 0.10
Nodes (5): User, Entrada 14, AuthenticationTest, PasswordUpdateTest, SelectorVistaTest

### Community 155 - "Entrada 20"
Cohesion: 0.38
Nodes (4): Entrada 20, Entrada 21, avisosCamarero(), carrito()

## Knowledge Gaps
- **8 isolated node(s):** `camarero.partes.cobro-efectivo`, `camarero.partes.cobro-efectivo`, `profile.partials.delete-user-form`, `profile.partials.update-password-form`, `profile.partials.update-profile-information-form` (+3 more)
  These have ≤1 connection - possible missing edges or undocumented components. (Counts symbols only; 505 node(s) total have ≤1 connection when file, concept and rationale nodes are included.)
- **114 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `TestCase` to `Illuminate\Database\Seeder`, `2. Qué incluye cada rama`, `MesasTest`, `Illuminate\Foundation\Http\FormRequest`, `MesaYa — Instrucciones para Claude Code`, `Database\Factories\UserFactory`, `Restaurante`, `ParteCuenta`, `PagarAlPedirTest`, `Entrada 9`, `DividirCuenta`, `Pedido`, `App\Models\Cuenta`, `EstadisticasTest`, `ComandasCamareroTest`, `GestionCartaTest`, `App\Models\Pedido`, `PasarelaStripe`, `App\Models\Mesa`, `PasswordResetTest.php`, `EmailVerificationTest.php`, `PanelAdminTest`, `AvisosCamareroTest`, `Illuminate\Http\Request`, `RedireccionTrasLoginTest`, `App\Http\Controllers\Admin\PlatoController`?**
  _High betweenness centrality (0.085) - this node is a cross-community bridge._
- **Are the 4 inferred relationships involving `Mesa` (e.g. with `Base de datos` and `Decisiones importantes`) actually correct?**
  _`Mesa` has 4 INFERRED edges - model-reasoned connections that need verification._
- **What connects `camarero.partes.cobro-efectivo`, `camarero.partes.cobro-efectivo`, `profile.partials.delete-user-form` to the rest of the system?**
  _8 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `package.json` be split into smaller, more focused modules?**
  _Cohesion score 0.06342494714587738 - nodes in this community are weakly interconnected._
- **Why does `Mesa` connect `Mesa` to `EstadisticasTest`, `ComandasCamareroTest`, `App\Models\Pedido`, `PasarelaStripe`, `App\Models\Mesa`, `Illuminate\Http\RedirectResponse`, `Controller`, `Entrada 15`, `Illuminate\View\View`, `Restaurante`, `3. Rutas`, `App\Http\Controllers\Cliente\CartaController`, `AvisosCamareroTest`, `ParteCuenta`, `TestCase`, `Pedido`, `App\Models\Cuenta`, `DividirCuenta`?**
  _High betweenness centrality (0.062) - this node is a cross-community bridge._
- **Are the 4 inferred relationships involving `User` (e.g. with `Base de datos` and `TODO en castellano (obligatorio)`) actually correct?**
  _`User` has 4 INFERRED edges - model-reasoned connections that need verification._
- **Should `2. Qué incluye cada rama` be split into smaller, more focused modules?**
  _Cohesion score 0.04878048780487805 - nodes in this community are weakly interconnected._