# Graph Report - ProyectoIA_APPWeb  (2026-11-26)

## Corpus Check
- 272 files · ~121,792 words
- Verdict: corpus is large enough that graph structure adds value.
- Unclassified: 11 file(s) not represented in the graph (top: (none) 7, .example 1, .xml 1)

## Summary
- 1751 nodes · 3987 edges · 196 communities (57 shown, 139 thin omitted)
- Extraction: 89% EXTRACTED · 11% INFERRED · 0% AMBIGUOUS · INFERRED: 443 edges (avg confidence: 0.93)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `1dc33644`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- AutopedidoTest
- composer.json
- package.json
- Illuminate\Database\Schema\Blueprint
- CartaPublicaTest
- Propuesta inicial: MesaYa
- bootstrap/app.php
- IntegracionStripeTest
- Restaurante.php
- Prompts por pantalla
- Dispositivo
- PasarelaStripe
- AppServiceProvider
- SuscripcionTest
- ComponentesDisenoTest
- Laravel Application
- ParteCuenta
- PagoStripeTest
- User
- .claude/CLAUDE.md
- CrearPedido
- edit.blade.php
- layouts.navigation
- PantallaCocinaTest
- Mesa
- App\Models\Pedido
- EstadisticasTest
- Illuminate\Database\Seeder
- web.php
- GestionCartaTest
- Illuminate\Http\Request
- EfectivoYCierreTest
- Illuminate\Validation\ValidationException
- App\Servicios\PasarelaStripe
- PasswordResetTest.php
- require-dev
- scripts
- [v0.1] – MVP – 22/11/2026
- .delQr
- DispositivosTest
- Illuminate\Foundation\Http\FormRequest
- config
- GestionPersonalTest
- 2. Qué incluye cada rama
- Controller
- RegistroRestauranteRequest
- ProfileTest
- psr-4
- require
- autoload-dev
- extra
- crear.blade.php
- editar.blade.php
- Cuenta
- Illuminate\Http\RedirectResponse
- Instalación de MesaYa en local
- Illuminate\View\View
- CategoriaRequest
- RegistroDesactivadoTest
- 2026_11_16_000002_add_rol_y_restaurante_id_to_users_table.php
- 2026_11_17_000001_create_categorias_table.php
- VentasDemoSeeder.php
- 2026_11_23_000003_add_cuenta_id_to_pedidos_table.php
- Illuminate\Support\Facades\Schema
- PagarAlPedirTest
- Illuminate\Database\Migrations\Migration
- 2026_11_17_000003_create_platos_table.php
- Tests\TestCase
- EmpleadoRequest
- Registro de uso de IA
- 2026_11_23_000002_create_partes_cuenta_table.php
- 2026_11_23_000004_create_pagos_table.php
- StripeWebhookTest
- MesasTest
- camarero.partes.cobro-efectivo
- 1. Requisitos funcionales
- InicioTest
- PedidosEnCursoSeederTest.php
- App\Http\Controllers\Cocina\PedidoController
- Entrada 20
- UserFactory.php
- PanelAdminTest
- 1. Seguimiento
- cobro-efectivo.blade.php
- SelectorVistaTest
- ComandasCamareroTest
- 0001_01_01_000001_create_cache_table.php
- 2026_11_23_000005_add_pedido_id_to_partes_cuenta_table.php
- ComandaRequest
- Restaurante
- CobroCamareroTest
- PedidosEnCursoSeederTest
- StripeWebhookController
- App\Http\Controllers\Admin\MesaController
- README.md
- MesaYa
- AislamientoRestaurantesTest
- PlatoRequest
- 3. Rutas
- 2026_11_17_000006_create_lineas_pedido_table.php
- 2026_11_23_000001_create_cuentas_table.php
- PlatoRequest
- PedidoClienteRequest
- AvisosCamareroTest
- 0001_01_01_000002_create_jobs_table.php
- 2026_11_24_000001_add_puede_cambiar_restaurante_to_users_table.php
- 2026_11_25_000001_persona_opcional_en_partes_cuenta.php
- 2026_11_25_000005_add_suscripcion_to_restaurantes_table.php
- 2026_11_25_000002_create_dispositivos_table.php
- CambiarRestauranteTest
- 2026_11_26_000001_add_stripe_cobros_to_restaurantes_table.php
- Restaurante
- 2026_11_17_000002_create_alergenos_table.php

## God Nodes (most connected - your core abstractions)
1. `Restaurante` - 105 edges
2. `User` - 90 edges
3. `TestCase` - 69 edges
4. `Mesa` - 46 edges
5. `PasarelaStripe` - 45 edges
6. `Controller` - 42 edges
7. `Pedido` - 41 edges
8. `Plato` - 39 edges
9. `CrearPedido` - 38 edges
10. `ParteCuenta` - 37 edges

## Surprising Connections (you probably didn't know these)
- `3. Decisiones pendientes` --references--> `PasarelaStripe`  [INFERRED]
  docs/plan-de-trabajo.md → app/Servicios/PasarelaStripe.php
- `3. `feature/gestion-carta`` --references--> `AlergenosSeeder`  [INFERRED]
  docs/plan-de-trabajo.md → database/seeders/AlergenosSeeder.php
- `6. `feature/pantalla-cocina`` --references--> `Pedido`  [INFERRED]
  docs/plan-de-trabajo.md → app/Models/Pedido.php
- `14. `feature/platos-compartidos` 🟠` --references--> `DividirCuenta`  [INFERRED]
  docs/plan-de-trabajo.md → app/Acciones/DividirCuenta.php
- `2. Requisitos no funcionales` --references--> `PerteneceARestaurante`  [INFERRED]
  docs/02-requisits.md → app/Models/Concerns/PerteneceARestaurante.php

## Import Cycles
- None detected.

## Communities (196 total, 139 thin omitted)

### Community 1 - "composer.json"
Cohesion: 0.22
Nodes (8): description, keywords, license, minimum-stability, name, prefer-stable, $schema, type

### Community 2 - "package.json"
Cohesion: 0.06
Nodes (41): dependencies, chart.js@^4.6.1, devDependencies, alpinejs@^3.4.2, autoprefixer@^10.4.2, concurrently@^10.0.3, @fontsource/ibm-plex-mono@^5.3.0, @fontsource-variable/schibsted-grotesk@^5.3.0 (+33 more)

### Community 3 - "Illuminate\Database\Schema\Blueprint"
Cohesion: 0.19
Nodes (9): {closure#1}(), {closure#2}(), {closure#3}(), {closure#1}(), {closure#2}(), {closure#3}(), {closure#4}(), {closure#5}() (+1 more)

### Community 5 - "Propuesta inicial: MesaYa"
Cohesion: 0.14
Nodes (14): 10. Riesgos y cómo los abordamos, 1. La idea en una frase, 2. Problema que resuelve, 3. Usuarios y roles, 4. Funcionalidades, 5. Flujo de la demo, 6. Tecnologías, 7. Reparto del trabajo (+6 more)

### Community 6 - "bootstrap/app.php"
Cohesion: 0.32
Nodes (3): {closure#1}(), {closure#2}(), {closure#3}()

### Community 8 - "Restaurante.php"
Cohesion: 0.22
Nodes (5): ComprobarSuscripcion, IdentificarDispositivo, Roles, Entrada 44, {closure#2}()

### Community 9 - "Prompts por pantalla"
Cohesion: 0.25
Nodes (7): Admin (escritorio), Camarero (móvil), Cliente (móvil), Cocina (Tablet horizontal), Prompt Maestro, PROMPTS para la IA de diseño Web STITCH, Prompts por pantalla

### Community 10 - "Dispositivo"
Cohesion: 0.12
Nodes (3): DispositivoController, Dispositivo, AccesoCamareroPinTest

### Community 11 - "PasarelaStripe"
Cohesion: 0.20
Nodes (6): PasarelaStripe, RestauranteFactory, 24. `feature/suscripcion-restaurantes` 🟠, 26. `feature/integracion-stripe-restaurante` 🟠, Entrada 47, Entrada 52

### Community 14 - "ComponentesDisenoTest"
Cohesion: 0.16
Nodes (3): Entrada 8, ComponentesDisenoTest, LayoutsTest

### Community 17 - "Laravel Application"
Cohesion: 0.50
Nodes (3): Agent Setup, Laravel Application, Prerequisites

### Community 19 - "ParteCuenta"
Cohesion: 0.11
Nodes (14): {closure#1}(), Pago, {closure#3}(), {closure#4}(), {closure#6}(), ParteCuenta, Base de datos, AlergenosSeeder (+6 more)

### Community 21 - "PagoStripeTest"
Cohesion: 0.07
Nodes (30): {closure#1}(), {closure#1}(), {closure#10}(), {closure#2}(), {closure#3}(), {closure#4}(), {closure#5}(), {closure#6}() (+22 more)

### Community 22 - "User"
Cohesion: 0.08
Nodes (8): Categoria, User, AuthenticationTest, EmailVerificationTest, PasswordConfirmationTest, PasswordUpdateTest, ExampleTest, TestCase

### Community 30 - "CrearPedido"
Cohesion: 0.15
Nodes (3): CrearPedido, DividirCuentaTest, PlatosCompartidosTest

### Community 58 - "PantallaCocinaTest"
Cohesion: 0.07
Nodes (19): {closure#1}(), Comandos útiles, Cómo tienes que trabajar con nosotros, Diseño visual (obligatorio para cualquier vista), Documentación del repo, Funcionalidades y prioridad, Git y GitHub, Grafo del proyecto (graphify) (+11 more)

### Community 59 - "Mesa"
Cohesion: 0.10
Nodes (11): {closure#2}(), {closure#2}(), PerteneceARestaurante, LineaPedido, Mesa, {closure#1}(), Pedido, TODO en castellano (obligatorio) (+3 more)

### Community 62 - "App\Models\Pedido"
Cohesion: 0.05
Nodes (20): {closure#3}(), {closure#4}(), DividirCuenta, {closure#3}(), PagarPedido, {closure#1}(), {closure#2}(), DetalleMesaController (+12 more)

### Community 65 - "EstadisticasTest"
Cohesion: 0.11
Nodes (6): EstadisticasRestaurante, EstadisticaController, PanelController, 17. `feature/panel-admin` 🟠, Entrada 29, EstadisticasTest

### Community 66 - "Illuminate\Database\Seeder"
Cohesion: 0.16
Nodes (6): DatabaseSeeder, MesasSeeder, UsuariosAdminSeeder, Entrada 10, Entrada 11, UsuariosAdminSeederTest

### Community 69 - "Illuminate\Http\Request"
Cohesion: 0.19
Nodes (3): IntegracionController, NewPasswordController, DispositivoController

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
Cohesion: 0.13
Nodes (3): AutopedidoController, CartaController, PagoTarjetaController

### Community 81 - "config"
Cohesion: 0.29
Nodes (7): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, preferred-install, sort-packages

### Community 83 - "2. Qué incluye cada rama"
Cohesion: 0.11
Nodes (19): 10. `feature/dividir-cuenta`, 11. `feature/pago-stripe`, 12. `feature/efectivo-y-cierre`, 13. `feature/estadisticas`, 14. `feature/platos-compartidos` 🟠, 15. `feature/pagar-al-pedir` 🟠, 19. `feature/rediseno-base` 🟠, 20. `feature/rediseno-cocina-camarero` 🟠 (+11 more)

### Community 84 - "Controller"
Cohesion: 0.07
Nodes (10): AlergenoController, AuthenticatedSessionController, ConfirmablePasswordController, EmailVerificationNotificationController, EmailVerificationPromptController, {closure#1}(), PasswordController, PasswordResetLinkController (+2 more)

### Community 87 - "ProfileTest"
Cohesion: 0.20
Nodes (5): 1. Cómo ejecutarlos, 3. Resto de tests por funcionalidad, 4. Pruebas manuales en el navegador, Pruebas de MesaYa, ProfileTest

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

### Community 109 - "Cuenta"
Cohesion: 0.15
Nodes (4): {closure#1}(), {closure#1}(), Cuenta, Entrada 25

### Community 110 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.19
Nodes (3): SuscripcionController, PagoEfectivoController, RegistroRestauranteController

### Community 111 - "Instalación de MesaYa en local"
Cohesion: 0.12
Nodes (16): 1. Requisitos, 2. Descargar el proyecto, 3. Bases de datos, 4. Archivo `.env`, 5. Tablas, datos de prueba y fotos, 6. Arrancar, 7. Problemas frecuentes, 8. Despliegue (+8 more)

### Community 112 - "Illuminate\View\View"
Cohesion: 0.12
Nodes (4): MesaController, ComandaController, AppLayout, GuestLayout

### Community 118 - "VentasDemoSeeder.php"
Cohesion: 0.16
Nodes (3): {closure#2}(), {closure#1}(), VentasDemoSeeder

### Community 124 - "Tests\TestCase"
Cohesion: 0.14
Nodes (3): 25. `feature/informar-suscripcion` 🟠, Entrada 51, InformarSuscripcionTest

### Community 125 - "EmpleadoRequest"
Cohesion: 0.14
Nodes (4): PersonalController, EmpleadoRequest, 18. `feature/gestion-personal` 🟠, Entrada 33

### Community 126 - "Registro de uso de IA"
Cohesion: 0.10
Nodes (19): Entrada 1, Entrada 16, Entrada 2, Entrada 23, Entrada 27, Entrada 3, Entrada 34, Entrada 35 (+11 more)

### Community 138 - "1. Requisitos funcionales"
Cohesion: 0.15
Nodes (12): 1. Requisitos funcionales, 2. Requisitos no funcionales, Acceso, roles y restaurantes, Carta y alérgenos (admin), Cliente por QR (sin login), Comandas, cocina y avisos (personal), Estadísticas (admin), Extras (🟣, solo si sobra tiempo) (+4 more)

### Community 140 - "InicioTest"
Cohesion: 0.20
Nodes (3): 21. `feature/rediseno-publico` 🟠, Entrada 36, InicioTest

### Community 142 - "App\Http\Controllers\Cocina\PedidoController"
Cohesion: 0.19
Nodes (3): AvisoController, {closure#1}(), PedidoController

### Community 143 - "Entrada 20"
Cohesion: 0.38
Nodes (4): Entrada 20, Entrada 21, avisosCamarero(), carrito()

### Community 150 - "PanelAdminTest"
Cohesion: 0.12
Nodes (5): InicioController, 16. `feature/pagina-inicio` 🟠, Entrada 30, Entrada 32, PanelAdminTest

### Community 151 - "1. Seguimiento"
Cohesion: 0.18
Nodes (10): 1. Seguimiento, 3. Decisiones pendientes, 4. Cómo pedir cada rama a Claude Code, 🟢 MVP (v0.1) — objetivo: 23/11, Plan de trabajo de MesaYa, Reparto propuesto, Semana 4 — hasta el 07/12, 🟠 Siguientes: mejoras propias (Iván, mientras Gabriel hace la 13) (+2 more)

### Community 165 - "StripeWebhookController"
Cohesion: 0.48
Nodes (3): StripeWebhookController, 24b. `feature/webhook-suscripcion` 🟠, Entrada 48

### Community 167 - "README.md"
Cohesion: 0.25
Nodes (3): 1. Arquitectura, 4. Estructura de carpetas (lo nuestro), Diseño técnico de MesaYa

### Community 168 - "MesaYa"
Cohesion: 0.22
Nodes (9): Cuentas de prueba, Cómo trabajamos, Documentación, Lo que la hace segura y fiable, MesaYa, Puesta en marcha rápida, Qué hace, Tecnologías (+1 more)

### Community 169 - "AislamientoRestaurantesTest"
Cohesion: 0.17
Nodes (4): MesaFactory, Entrada 15, Entrada 18, AislamientoRestaurantesTest

### Community 170 - "PlatoRequest"
Cohesion: 0.19
Nodes (4): {closure#1}(), {closure#1}(), PlatoRequest, Entrada 42

### Community 171 - "3. Rutas"
Cohesion: 0.29
Nodes (7): 3. Rutas, Admin (`auth` + `rol:admin`), Camarero (`dispositivo` + `auth` + `rol:camarero` + `suscripcion`), Cocina (`dispositivo` + `auth` + `rol:cocina` + `suscripcion`), Dispositivos (sin login), Públicas (cliente, sin login), Todos los roles

### Community 174 - "PlatoRequest"
Cohesion: 0.29
Nodes (3): PlatoController, Plato, Entrada 12

### Community 178 - "0001_01_01_000002_create_jobs_table.php"
Cohesion: 0.33
Nodes (3): {closure#1}(), {closure#2}(), {closure#3}()

### Community 190 - "CambiarRestauranteTest"
Cohesion: 0.07
Nodes (9): ComprobarRol, UserFactory, 1. `feature/restaurantes-y-roles`, Entrada 13, Entrada 43, Entrada 9, CambiarRestauranteTest, ComprobarRolTest (+1 more)

### Community 192 - "Restaurante"
Cohesion: 0.16
Nodes (4): {closure#1}(), Restaurante, RestaurantesSeeder, PasarelaStripeFalsa

## Knowledge Gaps
- **7 isolated node(s):** `camarero.partes.cobro-efectivo`, `camarero.partes.cobro-efectivo`, `profile.partials.update-password-form`, `profile.partials.update-profile-information-form`, `layouts.navigation` (+2 more)
  These have ≤1 connection - possible missing edges or undocumented components. (Counts symbols only; 617 node(s) total have ≤1 connection when file, concept and rationale nodes are included.)
- **139 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `MesasTest`, `Dispositivo`, `ParteCuenta`, `UserFactory.php`, `PanelAdminTest`, `ComandasCamareroTest`, `Restaurante`, `CobroCamareroTest`, `App\Http\Controllers\Admin\MesaController`, `PlatoRequest`, `AvisosCamareroTest`, `PantallaCocinaTest`, `Mesa`, `App\Models\Pedido`, `CambiarRestauranteTest`, `Illuminate\Database\Seeder`, `GestionCartaTest`, `EfectivoYCierreTest`, `PasswordResetTest.php`, `DispositivosTest`, `GestionPersonalTest`, `Controller`, `Cuenta`, `Illuminate\Http\RedirectResponse`, `Tests\TestCase`, `EmpleadoRequest`?**
  _High betweenness centrality (0.108) - this node is a cross-community bridge._
- **Are the 5 inferred relationships involving `Restaurante` (e.g. with `Base de datos` and `Tablas`) actually correct?**
  _`Restaurante` has 5 INFERRED edges - model-reasoned connections that need verification._
- **What connects `camarero.partes.cobro-efectivo`, `camarero.partes.cobro-efectivo`, `profile.partials.update-password-form` to the rest of the system?**
  _7 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `package.json` be split into smaller, more focused modules?**
  _Cohesion score 0.06086956521739131 - nodes in this community are weakly interconnected._
- **Why does `Restaurante` connect `Restaurante` to `AutopedidoTest`, `CartaPublicaTest`, `MesasTest`, `IntegracionStripeTest`, `Restaurante.php`, `Dispositivo`, `PasarelaStripe`, `PedidosEnCursoSeederTest.php`, `ParteCuenta`, `UserFactory.php`, `User`, `PagoStripeTest`, `PanelAdminTest`, `ComandasCamareroTest`, `Restaurante`, `PedidosEnCursoSeederTest`, `StripeWebhookController`, `AvisosCamareroTest`, `PantallaCocinaTest`, `Mesa`, `CambiarRestauranteTest`, `App\Models\Pedido`, `EstadisticasTest`, `Illuminate\Database\Seeder`, `web.php`, `Illuminate\Http\Request`, `App\Servicios\PasarelaStripe`, `.delQr`, `DispositivosTest`, `GestionPersonalTest`, `Cuenta`, `Illuminate\Http\RedirectResponse`, `VentasDemoSeeder.php`?**
  _High betweenness centrality (0.095) - this node is a cross-community bridge._
- **Are the 7 inferred relationships involving `User` (e.g. with `Base de datos` and `TODO en castellano (obligatorio)`) actually correct?**
  _`User` has 7 INFERRED edges - model-reasoned connections that need verification._
- **Should `Propuesta inicial: MesaYa` be split into smaller, more focused modules?**
  _Cohesion score 0.14285714285714285 - nodes in this community are weakly interconnected._