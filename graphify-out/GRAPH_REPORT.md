# Graph Report - ProyectoIA_APPWeb  (2026-11-24)

## Corpus Check
- 235 files · ~90,003 words
- Verdict: corpus is large enough that graph structure adds value.
- Unclassified: 11 file(s) not represented in the graph (top: (none) 7, .example 1, .xml 1)

## Summary
- 1364 nodes · 2925 edges · 175 communities (56 shown, 119 thin omitted)
- Extraction: 90% EXTRACTED · 10% INFERRED · 0% AMBIGUOUS · INFERRED: 299 edges (avg confidence: 0.93)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `b166ca09`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- PagarAlPedirTest
- composer.json
- package.json
- Illuminate\Database\Schema\Blueprint
- CartaPublicaTest
- Propuesta inicial: MesaYa
- bootstrap/app.php
- Illuminate\Http\RedirectResponse
- EfectivoYCierreTest
- Prompts por pantalla
- README.md
- User
- AppServiceProvider
- MesaYa — Instrucciones para Claude Code
- ComponentesDisenoTest
- Laravel Application
- Cuenta
- PagoStripeTest
- Illuminate\Foundation\Testing\RefreshDatabase
- .claude/CLAUDE.md
- Mesa
- edit.blade.php
- layouts.navigation
- Pedido
- Illuminate\Database\Eloquent\Relations\BelongsTo
- EstadisticasTest
- UsuariosAdminSeederTest
- web.php
- GestionCartaTest
- 3. Resto de tests por funcionalidad
- ParteCuenta
- Illuminate\Foundation\Http\FormRequest
- Illuminate\Database\Eloquent\Model
- PasswordResetTest.php
- require-dev
- scripts
- [v0.1] – MVP – 22/11/2026
- .delQr
- Plato
- config
- GestionPersonalTest
- 2. Qué incluye cada rama
- routes/auth.php
- Controller
- CrearPedido
- psr-4
- require
- autoload-dev
- extra
- crear.blade.php
- editar.blade.php
- InicioTest
- ComprobarRol
- Instalación de MesaYa en local
- Illuminate\View\View
- EmailVerificationTest.php
- TestCase
- 2026_11_16_000002_add_rol_y_restaurante_id_to_users_table.php
- 2026_11_17_000001_create_categorias_table.php
- Illuminate\Database\Migrations\Migration
- 2026_11_23_000003_add_cuenta_id_to_pedidos_table.php
- Illuminate\Support\Facades\Schema
- 2026_11_16_000003_create_mesas_table.php
- 2026_11_17_000002_create_alergenos_table.php
- 2026_11_17_000003_create_platos_table.php
- 2026_11_17_000005_create_pedidos_table.php
- EmpleadoRequest
- Registro de uso de IA
- 2026_11_23_000002_create_partes_cuenta_table.php
- 2026_11_23_000004_create_pagos_table.php
- PanelController
- MesasTest
- detalle-mesa.blade.php
- 1. Requisitos funcionales
- Illuminate\Http\Request
- Entrada 15
- Illuminate\Http\Request
- InicioController.php
- Illuminate\Database\Eloquent\Factories\Factory
- Restaurante
- 1. Seguimiento
- cobro-efectivo.blade.php
- SelectorVistaTest
- Entrada 9
- 0001_01_01_000001_create_cache_table.php
- 2026_11_23_000005_add_pedido_id_to_partes_cuenta_table.php
- Entrada 20
- Illuminate\Database\Seeder
- AislamientoRestaurantesTest
- RedireccionTrasLoginTest
- Stack
- CategoriaController
- ComandasCamareroTest
- MesaYa
- ComprobarRolTest
- Decisiones importantes
- 3. Rutas
- 2026_11_17_000006_create_lineas_pedido_table.php
- 2026_11_23_000001_create_cuentas_table.php
- 4. Funcionalidades

## God Nodes (most connected - your core abstractions)
1. `Mesa` - 81 edges
2. `User` - 73 edges
3. `TestCase` - 66 edges
4. `Pedido` - 59 edges
5. `Controller` - 53 edges
6. `Restaurante` - 50 edges
7. `Plato` - 49 edges
8. `ParteCuenta` - 45 edges
9. `Registro de uso de IA` - 39 edges
10. `Categoria` - 37 edges

## Surprising Connections (you probably didn't know these)
- `6. `feature/pantalla-cocina`` --references--> `Pedido`  [INFERRED]
  docs/plan-de-trabajo.md → app/Models/Pedido.php
- `14. `feature/platos-compartidos` 🟠` --references--> `DividirCuenta`  [INFERRED]
  docs/plan-de-trabajo.md → app/Acciones/DividirCuenta.php
- `3. Decisiones pendientes` --references--> `PasarelaStripe`  [INFERRED]
  docs/plan-de-trabajo.md → app/Servicios/PasarelaStripe.php
- `2. Requisitos no funcionales` --references--> `PerteneceARestaurante`  [INFERRED]
  docs/02-requisits.md → app/Models/Concerns/PerteneceARestaurante.php
- `1. `feature/restaurantes-y-roles`` --references--> `ComprobarRol`  [INFERRED]
  docs/plan-de-trabajo.md → app/Http/Middleware/ComprobarRol.php

## Import Cycles
- None detected.

## Communities (175 total, 119 thin omitted)

### Community 0 - "PagarAlPedirTest"
Cohesion: 0.08
Nodes (7): {closure#1}(), PasarelaStripe, Entrada 24, Entrada 31, PasarelaStripeFalsa, AutopedidoTest, PagarAlPedirTest

### Community 1 - "composer.json"
Cohesion: 0.22
Nodes (8): description, keywords, license, minimum-stability, name, prefer-stable, $schema, type

### Community 2 - "package.json"
Cohesion: 0.06
Nodes (41): dependencies, chart.js@^4.6.1, devDependencies, alpinejs@^3.4.2, autoprefixer@^10.4.2, concurrently@^10.0.3, @fontsource/ibm-plex-mono@^5.3.0, @fontsource-variable/schibsted-grotesk@^5.3.0 (+33 more)

### Community 3 - "Illuminate\Database\Schema\Blueprint"
Cohesion: 0.23
Nodes (6): {closure#1}(), {closure#2}(), {closure#3}(), {closure#1}(), {closure#2}(), {closure#3}()

### Community 5 - "Propuesta inicial: MesaYa"
Cohesion: 0.20
Nodes (10): 10. Riesgos y cómo los abordamos, 1. La idea en una frase, 2. Problema que resuelve, 3. Usuarios y roles, 5. Flujo de la demo, 6. Tecnologías, 7. Reparto del trabajo, 8. Planificación (+2 more)

### Community 6 - "bootstrap/app.php"
Cohesion: 0.32
Nodes (3): {closure#1}(), {closure#2}(), {closure#3}()

### Community 9 - "Prompts por pantalla"
Cohesion: 0.25
Nodes (7): Admin (escritorio), Camarero (móvil), Cliente (móvil), Cocina (Tablet horizontal), Prompt Maestro, PROMPTS para la IA de diseño Web STITCH, Prompts por pantalla

### Community 13 - "MesaYa — Instrucciones para Claude Code"
Cohesion: 0.14
Nodes (13): Comandos útiles, Cómo tienes que trabajar con nosotros, Diseño visual (obligatorio para cualquier vista), Documentación del repo, Funcionalidades y prioridad, Git y GitHub, Grafo del proyecto (graphify), MesaYa — Instrucciones para Claude Code (+5 more)

### Community 14 - "ComponentesDisenoTest"
Cohesion: 0.16
Nodes (3): Entrada 8, ComponentesDisenoTest, LayoutsTest

### Community 17 - "Laravel Application"
Cohesion: 0.50
Nodes (3): Agent Setup, Laravel Application, Prerequisites

### Community 19 - "Cuenta"
Cohesion: 0.23
Nodes (3): {closure#1}(), Cuenta, Entrada 25

### Community 21 - "PagoStripeTest"
Cohesion: 0.16
Nodes (9): {closure#1}(), {closure#2}(), {closure#3}(), {closure#4}(), {closure#5}(), {closure#6}(), {closure#7}(), {closure#8}() (+1 more)

### Community 22 - "Illuminate\Foundation\Testing\RefreshDatabase"
Cohesion: 0.14
Nodes (3): PasswordConfirmationTest, PasswordUpdateTest, ProfileTest

### Community 30 - "Mesa"
Cohesion: 0.07
Nodes (14): DividirCuenta, {closure#3}(), PagarPedido, CobroController, DetalleMesaController, CuentaController, Mesa, TODO en castellano (obligatorio) (+6 more)

### Community 34 - "edit.blade.php"
Cohesion: 0.50
Nodes (3): profile.partials.delete-user-form, profile.partials.update-password-form, profile.partials.update-profile-information-form

### Community 58 - "Pedido"
Cohesion: 0.07
Nodes (17): {closure#4}(), AvisoController, {closure#1}(), {closure#2}(), {closure#1}(), {closure#2}(), {closure#1}(), {closure#2}() (+9 more)

### Community 65 - "EstadisticasTest"
Cohesion: 0.07
Nodes (6): EstadisticasRestaurante, {closure#1}(), VentasDemoSeeder, Entrada 29, EstadisticasTest, PanelAdminTest

### Community 66 - "UsuariosAdminSeederTest"
Cohesion: 0.17
Nodes (5): DatabaseSeeder, UsuariosAdminSeeder, Entrada 10, Entrada 11, UsuariosAdminSeederTest

### Community 69 - "3. Resto de tests por funcionalidad"
Cohesion: 0.12
Nodes (6): 1. Cómo ejecutarlos, 2. Golden tests, 3. Resto de tests por funcionalidad, 4. Pruebas manuales en el navegador, Pruebas de MesaYa, AvisosCamareroTest

### Community 70 - "ParteCuenta"
Cohesion: 0.16
Nodes (4): {closure#3}(), {closure#4}(), {closure#6}(), ParteCuenta

### Community 71 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.09
Nodes (4): LoginRequest, ComandaRequest, DividirCuentaRequest, ProfileUpdateRequest

### Community 72 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.15
Nodes (6): Alergeno, PerteneceARestaurante, {closure#1}(), RestauranteFactory, {closure#1}(), RegistroDePrueba

### Community 74 - "require-dev"
Cohesion: 0.22
Nodes (9): require-dev, fakerphp/faker, laravel/breeze, laravel/pail, laravel/pao, laravel/pint, mockery/mockery, nunomaduro/collision (+1 more)

### Community 75 - "scripts"
Cohesion: 0.22
Nodes (9): scripts, dev, post-autoload-dump, post-create-project-cmd, post-root-package-install, post-update-cmd, pre-package-uninstall, setup (+1 more)

### Community 76 - "[v0.1] – MVP – 22/11/2026"
Cohesion: 0.40
Nodes (4): Adelantado de la v1.0, Añadido, Changelog, [v0.1] – MVP – 22/11/2026

### Community 77 - ".delQr"
Cohesion: 0.16
Nodes (4): AutopedidoController, {closure#2}(), CartaController, {closure#2}()

### Community 80 - "Plato"
Cohesion: 0.17
Nodes (4): PlatoController, PlatoRequest, Plato, Entrada 12

### Community 81 - "config"
Cohesion: 0.29
Nodes (7): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, preferred-install, sort-packages

### Community 83 - "2. Qué incluye cada rama"
Cohesion: 0.12
Nodes (17): 10. `feature/dividir-cuenta`, 11. `feature/pago-stripe`, 12. `feature/efectivo-y-cierre`, 13. `feature/estadisticas`, 14. `feature/platos-compartidos` 🟠, 19. `feature/rediseno-base` 🟠, 20. `feature/rediseno-cocina-camarero` 🟠, 22. `feature/rediseno-admin` 🟠 (+9 more)

### Community 84 - "routes/auth.php"
Cohesion: 0.13
Nodes (3): AuthenticatedSessionController, ConfirmablePasswordController, PasswordResetLinkController

### Community 85 - "Controller"
Cohesion: 0.15
Nodes (6): AlergenoController, EstadisticaController, EmailVerificationPromptController, PasswordController, VerifyEmailController, Controller

### Community 87 - "CrearPedido"
Cohesion: 0.10
Nodes (5): CrearPedido, {closure#3}(), Entrada 28, DividirCuentaTest, PlatosCompartidosTest

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
Cohesion: 0.18
Nodes (3): 21. `feature/rediseno-publico` 🟠, Entrada 36, InicioTest

### Community 110 - "ComprobarRol"
Cohesion: 0.32
Nodes (4): ComprobarRol, Roles, 1. `feature/restaurantes-y-roles`, Entrada 13

### Community 111 - "Instalación de MesaYa en local"
Cohesion: 0.13
Nodes (14): 1. Requisitos, 2. Descargar el proyecto, 3. Bases de datos, 4. Archivo `.env`, 5. Tablas, datos de prueba y fotos, 6. Arrancar, 7. Problemas frecuentes, 8. Despliegue (+6 more)

### Community 112 - "Illuminate\View\View"
Cohesion: 0.24
Nodes (3): MesaController, AppLayout, GuestLayout

### Community 115 - "TestCase"
Cohesion: 0.15
Nodes (5): Entrada 6, AuthenticationTest, RegistroDesactivadoTest, ExampleTest, TestCase

### Community 125 - "EmpleadoRequest"
Cohesion: 0.23
Nodes (3): {closure#1}(), EmpleadoRequest, Entrada 33

### Community 126 - "Registro de uso de IA"
Cohesion: 0.14
Nodes (14): Entrada 1, Entrada 16, Entrada 2, Entrada 23, Entrada 27, Entrada 3, Entrada 34, Entrada 35 (+6 more)

### Community 138 - "1. Requisitos funcionales"
Cohesion: 0.20
Nodes (10): 1. Requisitos funcionales, Acceso, roles y restaurantes, Carta y alérgenos (admin), Cliente por QR (sin login), Comandas, cocina y avisos (personal), Estadísticas (admin), Extras (🟣, solo si sobra tiempo), Mesas y QR (admin) (+2 more)

### Community 140 - "Illuminate\Http\Request"
Cohesion: 0.16
Nodes (3): EmailVerificationNotificationController, NewPasswordController, ProfileController

### Community 141 - "Entrada 15"
Cohesion: 0.33
Nodes (3): MesaFactory, Entrada 15, Entrada 18

### Community 143 - "InicioController.php"
Cohesion: 0.22
Nodes (4): InicioController, 16. `feature/pagina-inicio` 🟠, Entrada 30, Entrada 32

### Community 150 - "Restaurante"
Cohesion: 0.17
Nodes (7): Categoria, Pago, Restaurante, Base de datos, Tablas, 15. `feature/pagar-al-pedir` 🟠, {closure#1}()

### Community 151 - "1. Seguimiento"
Cohesion: 0.18
Nodes (10): 1. Seguimiento, 3. Decisiones pendientes, 4. Cómo pedir cada rama a Claude Code, 🟢 MVP (v0.1) — objetivo: 23/11, Plan de trabajo de MesaYa, Reparto propuesto, Semana 4 — hasta el 07/12, 🟠 Siguientes: mejoras propias (Iván, mientras Gabriel hace la 13) (+2 more)

### Community 160 - "Entrada 20"
Cohesion: 0.18
Nodes (5): PedidoClienteRequest, Entrada 20, Entrada 21, avisosCamarero(), carrito()

### Community 162 - "Illuminate\Database\Seeder"
Cohesion: 0.18
Nodes (6): AlergenosSeeder, CartaSeeder, MesasSeeder, RestaurantesSeeder, 18. `feature/gestion-personal` 🟠, 3. `feature/gestion-carta`

### Community 165 - "Stack"
Cohesion: 0.67
Nodes (3): MySQL, PHP en local (`php.ini`), Stack

### Community 168 - "MesaYa"
Cohesion: 0.22
Nodes (9): Cuentas de prueba, Cómo trabajamos, Documentación, Lo que la hace segura y fiable, MesaYa, Puesta en marcha rápida, Qué hace, Tecnologías (+1 more)

### Community 170 - "Decisiones importantes"
Cohesion: 0.33
Nodes (6): 1. Arquitectura, 2. Base de datos, 4. Estructura de carpetas (lo nuestro), Decisiones importantes, Diagrama entidad-relación, Diseño técnico de MesaYa

### Community 171 - "3. Rutas"
Cohesion: 0.33
Nodes (6): 3. Rutas, Admin (`auth` + `rol:admin`), Camarero (`auth` + `rol:camarero`), Cocina (`auth` + `rol:cocina`), Públicas (cliente, sin login), Todos los roles

### Community 174 - "4. Funcionalidades"
Cohesion: 0.50
Nodes (4): 4. Funcionalidades, 🔵 Entrega final (v1.0), 🟣 Extras (solo si sobra tiempo; si no, pasan a "mejoras futuras"), 🟢 MVP (v0.1)

## Knowledge Gaps
- **8 isolated node(s):** `camarero.partes.cobro-efectivo`, `camarero.partes.cobro-efectivo`, `profile.partials.delete-user-form`, `profile.partials.update-password-form`, `profile.partials.update-profile-information-form` (+3 more)
  These have ≤1 connection - possible missing edges or undocumented components. (Counts symbols only; 520 node(s) total have ≤1 connection when file, concept and rationale nodes are included.)
- **119 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `PagarAlPedirTest`, `MesasTest`, `EfectivoYCierreTest`, `Illuminate\Http\Request`, `InicioController.php`, `Cuenta`, `Restaurante`, `Illuminate\Foundation\Testing\RefreshDatabase`, `SelectorVistaTest`, `Entrada 9`, `Mesa`, `Illuminate\Database\Seeder`, `RedireccionTrasLoginTest`, `ComandasCamareroTest`, `ComprobarRolTest`, `Pedido`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `EstadisticasTest`, `UsuariosAdminSeederTest`, `web.php`, `GestionCartaTest`, `3. Resto de tests por funcionalidad`, `Illuminate\Foundation\Http\FormRequest`, `Illuminate\Database\Eloquent\Model`, `PasswordResetTest.php`, `GestionPersonalTest.php`, `GestionPersonalTest`, `CrearPedido`, `InicioTest`, `EmailVerificationTest.php`, `TestCase`, `EmpleadoRequest`?**
  _High betweenness centrality (0.108) - this node is a cross-community bridge._
- **Are the 4 inferred relationships involving `Mesa` (e.g. with `Base de datos` and `Decisiones importantes`) actually correct?**
  _`Mesa` has 4 INFERRED edges - model-reasoned connections that need verification._
- **What connects `camarero.partes.cobro-efectivo`, `camarero.partes.cobro-efectivo`, `profile.partials.delete-user-form` to the rest of the system?**
  _8 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `PagarAlPedirTest` be split into smaller, more focused modules?**
  _Cohesion score 0.08051948051948052 - nodes in this community are weakly interconnected._
- **Why does `Mesa` connect `Mesa` to `PagarAlPedirTest`, `PanelController`, `MesasTest`, `Illuminate\Http\RedirectResponse`, `EfectivoYCierreTest`, `Illuminate\Http\Request`, `Entrada 15`, `InicioController.php`, `Illuminate\Database\Eloquent\Factories\Factory`, `Restaurante`, `PagoStripeTest`, `Illuminate\Database\Seeder`, `ComandasCamareroTest`, `Decisiones importantes`, `Pedido`, `EstadisticasTest`, `web.php`, `3. Resto de tests por funcionalidad`, `Illuminate\Database\Eloquent\Model`, `.delQr`, `GestionPersonalTest.php`, `CrearPedido`, `InicioTest`, `Illuminate\View\View`?**
  _High betweenness centrality (0.098) - this node is a cross-community bridge._
- **Are the 6 inferred relationships involving `User` (e.g. with `Base de datos` and `TODO en castellano (obligatorio)`) actually correct?**
  _`User` has 6 INFERRED edges - model-reasoned connections that need verification._
- **Should `package.json` be split into smaller, more focused modules?**
  _Cohesion score 0.06086956521739131 - nodes in this community are weakly interconnected._