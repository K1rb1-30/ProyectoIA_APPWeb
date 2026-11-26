# Graph Report - ProyectoIA_APPWeb  (2026-11-26)

## Corpus Check
- 266 files · ~116,958 words
- Verdict: corpus is large enough that graph structure adds value.
- Unclassified: 11 file(s) not represented in the graph (top: (none) 7, .example 1, .xml 1)

## Summary
- 1682 nodes · 3780 edges · 194 communities (59 shown, 135 thin omitted)
- Extraction: 89% EXTRACTED · 11% INFERRED · 0% AMBIGUOUS · INFERRED: 406 edges (avg confidence: 0.93)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `5bfc6894`
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
- .delQr
- App\Models\Restaurante
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
- Pago
- PedidosEnCursoSeeder
- EstadisticasTest
- Illuminate\Database\Seeder
- web.php
- GestionCartaTest
- DividirCuenta
- EfectivoYCierreTest
- Illuminate\Validation\ValidationException
- PasswordResetTest.php
- require-dev
- scripts
- [v0.1] – MVP – 22/11/2026
- App\Http\Controllers\Cliente\AutopedidoController
- DispositivosTest
- DividirCuentaRequest
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
- Entrada 25
- Illuminate\Http\RedirectResponse
- Instalación de MesaYa en local
- Illuminate\View\View
- CategoriaRequest
- RegistroDesactivadoTest
- 2026_11_16_000002_add_rol_y_restaurante_id_to_users_table.php
- 2026_11_17_000001_create_categorias_table.php
- Illuminate\Database\Migrations\Migration
- 2026_11_23_000003_add_cuenta_id_to_pedidos_table.php
- Illuminate\Support\Facades\Schema
- PagarAlPedirTest
- Illuminate\Database\Migrations\Migration
- 2026_11_17_000003_create_platos_table.php
- 2026_11_17_000005_create_pedidos_table.php
- EmpleadoRequest
- Registro de uso de IA
- 2026_11_23_000002_create_partes_cuenta_table.php
- 2026_11_23_000004_create_pagos_table.php
- App\Models\ParteCuenta
- MesasTest
- camarero.partes.cobro-efectivo
- 1. Requisitos funcionales
- RedireccionTrasLoginTest
- DetalleMesaController
- App\Http\Controllers\Cocina\PedidoController
- InicioController.php
- CambiarRestauranteTest
- PanelAdminTest
- 1. Seguimiento
- cobro-efectivo.blade.php
- SelectorVistaTest
- ComandasCamareroTest
- 0001_01_01_000001_create_cache_table.php
- 2026_11_23_000005_add_pedido_id_to_partes_cuenta_table.php
- Illuminate\Foundation\Http\FormRequest
- Restaurante
- CobroCamareroTest
- PedidosEnCursoSeederTest
- StripeWebhookController
- App\Http\Controllers\Admin\MesaController
- Diseño técnico de MesaYa
- MesaYa
- AislamientoRestaurantesTest
- PlatoRequest
- 3. Rutas
- 2026_11_17_000006_create_lineas_pedido_table.php
- 2026_11_23_000001_create_cuentas_table.php
- PlatoRequest
- 4. Archivo `.env`
- 2. Golden tests
- 0001_01_01_000002_create_jobs_table.php
- 2026_11_24_000001_add_puede_cambiar_restaurante_to_users_table.php
- 2026_11_25_000001_persona_opcional_en_partes_cuenta.php
- 2026_11_25_000005_add_suscripcion_to_restaurantes_table.php
- 2026_11_25_000002_create_dispositivos_table.php
- ComprobarRolTest
- Restaurante
- README.md
- PanelController

## God Nodes (most connected - your core abstractions)
1. `User` - 93 edges
2. `Restaurante` - 81 edges
3. `TestCase` - 73 edges
4. `Mesa` - 49 edges
5. `ParteCuenta` - 44 edges
6. `Controller` - 44 edges
7. `Pedido` - 43 edges
8. `Plato` - 40 edges
9. `CrearPedido` - 38 edges
10. `Cuenta` - 37 edges

## Surprising Connections (you probably didn't know these)
- `3. `feature/gestion-carta`` --references--> `AlergenosSeeder`  [INFERRED]
  docs/plan-de-trabajo.md → database/seeders/AlergenosSeeder.php
- `6. `feature/pantalla-cocina`` --references--> `Pedido`  [INFERRED]
  docs/plan-de-trabajo.md → app/Models/Pedido.php
- `14. `feature/platos-compartidos` 🟠` --references--> `DividirCuenta`  [INFERRED]
  docs/plan-de-trabajo.md → app/Acciones/DividirCuenta.php
- `3. Decisiones pendientes` --references--> `PasarelaStripe`  [INFERRED]
  docs/plan-de-trabajo.md → app/Servicios/PasarelaStripe.php
- `2. Requisitos no funcionales` --references--> `PerteneceARestaurante`  [INFERRED]
  docs/02-requisits.md → app/Models/Concerns/PerteneceARestaurante.php

## Import Cycles
- None detected.

## Communities (194 total, 135 thin omitted)

### Community 0 - "AutopedidoTest"
Cohesion: 0.09
Nodes (7): Entrada 20, Entrada 21, avisosCamarero(), carrito(), PasarelaStripeFalsa, AutopedidoTest, AvisosCamareroTest

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

### Community 8 - "App\Models\Restaurante"
Cohesion: 0.06
Nodes (15): ComprobarRol, ComprobarSuscripcion, IdentificarDispositivo, Roles, 1. `feature/restaurantes-y-roles`, 21. `feature/rediseno-publico` 🟠, 25. `feature/informar-suscripcion` 🟠, Entrada 13 (+7 more)

### Community 9 - "Prompts por pantalla"
Cohesion: 0.25
Nodes (7): Admin (escritorio), Camarero (móvil), Cliente (móvil), Cocina (Tablet horizontal), Prompt Maestro, PROMPTS para la IA de diseño Web STITCH, Prompts por pantalla

### Community 10 - "Dispositivo"
Cohesion: 0.12
Nodes (3): DispositivoController, DispositivoController, Dispositivo

### Community 11 - "PasarelaStripe"
Cohesion: 0.31
Nodes (5): RegistroRestauranteController, PasarelaStripe, 24. `feature/suscripcion-restaurantes` 🟠, Entrada 24, Entrada 47

### Community 14 - "ComponentesDisenoTest"
Cohesion: 0.16
Nodes (3): Entrada 8, ComponentesDisenoTest, LayoutsTest

### Community 17 - "Laravel Application"
Cohesion: 0.50
Nodes (3): Agent Setup, Laravel Application, Prerequisites

### Community 19 - "ParteCuenta"
Cohesion: 0.21
Nodes (5): CobroController, {closure#1}(), ParteCuenta, Estados y reglas, Entrada 26

### Community 21 - "PagoStripeTest"
Cohesion: 0.10
Nodes (20): {closure#1}(), {closure#1}(), {closure#2}(), {closure#3}(), {closure#4}(), {closure#5}(), {closure#6}(), {closure#7}() (+12 more)

### Community 22 - "User"
Cohesion: 0.08
Nodes (9): Categoria, Mesa, User, AuthenticationTest, EmailVerificationTest, PasswordConfirmationTest, PasswordUpdateTest, ExampleTest (+1 more)

### Community 30 - "CrearPedido"
Cohesion: 0.14
Nodes (5): CrearPedido, {closure#3}(), Entrada 28, DividirCuentaTest, PlatosCompartidosTest

### Community 58 - "PantallaCocinaTest"
Cohesion: 0.07
Nodes (19): {closure#1}(), Comandos útiles, Cómo tienes que trabajar con nosotros, Diseño visual (obligatorio para cualquier vista), Documentación del repo, Funcionalidades y prioridad, Git y GitHub, Grafo del proyecto (graphify) (+11 more)

### Community 59 - "Pago"
Cohesion: 0.07
Nodes (7): {closure#1}(), Cuenta, LineaPedido, Pedido, TODO en castellano (obligatorio), Decisiones importantes, Entrada 17

### Community 62 - "PedidosEnCursoSeeder"
Cohesion: 0.26
Nodes (3): PagarPedido, PedidosEnCursoSeeder, Entrada 31

### Community 65 - "EstadisticasTest"
Cohesion: 0.11
Nodes (5): EstadisticasRestaurante, {closure#1}(), VentasDemoSeeder, Entrada 29, EstadisticasTest

### Community 66 - "Illuminate\Database\Seeder"
Cohesion: 0.10
Nodes (8): CartaSeeder, DatabaseSeeder, MesasSeeder, RestaurantesSeeder, UsuariosAdminSeeder, Entrada 10, Entrada 11, UsuariosAdminSeederTest

### Community 69 - "DividirCuenta"
Cohesion: 0.24
Nodes (4): DividirCuenta, Entrada 22, ExampleTest, RepartoCuentaTest

### Community 74 - "require-dev"
Cohesion: 0.22
Nodes (9): require-dev, fakerphp/faker, laravel/breeze, laravel/pail, laravel/pao, laravel/pint, mockery/mockery, nunomaduro/collision (+1 more)

### Community 75 - "scripts"
Cohesion: 0.22
Nodes (9): scripts, dev, post-autoload-dump, post-create-project-cmd, post-root-package-install, post-update-cmd, pre-package-uninstall, setup (+1 more)

### Community 76 - "[v0.1] – MVP – 22/11/2026"
Cohesion: 0.40
Nodes (4): Adelantado de la v1.0, Añadido, Changelog, [v0.1] – MVP – 22/11/2026

### Community 77 - "App\Http\Controllers\Cliente\AutopedidoController"
Cohesion: 0.15
Nodes (4): AutopedidoController, {closure#2}(), CartaController, {closure#2}()

### Community 79 - "DispositivosTest"
Cohesion: 0.09
Nodes (8): {closure#4}(), {closure#1}(), {closure#2}(), {closure#1}(), {closure#2}(), {closure#3}(), {closure#1}(), DispositivosTest

### Community 81 - "config"
Cohesion: 0.29
Nodes (7): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, preferred-install, sort-packages

### Community 82 - "GestionPersonalTest"
Cohesion: 0.07
Nodes (3): Entrada 45, AccesoCamareroPinTest, GestionPersonalTest

### Community 83 - "2. Qué incluye cada rama"
Cohesion: 0.10
Nodes (20): 10. `feature/dividir-cuenta`, 11. `feature/pago-stripe`, 12. `feature/efectivo-y-cierre`, 13. `feature/estadisticas`, 14. `feature/platos-compartidos` 🟠, 15. `feature/pagar-al-pedir` 🟠, 19. `feature/rediseno-base` 🟠, 20. `feature/rediseno-cocina-camarero` 🟠 (+12 more)

### Community 84 - "Controller"
Cohesion: 0.07
Nodes (10): AlergenoController, EstadisticaController, AuthenticatedSessionController, ConfirmablePasswordController, {closure#1}(), NewPasswordController, PasswordController, PasswordResetLinkController (+2 more)

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

### Community 109 - "Entrada 25"
Cohesion: 0.31
Nodes (3): PagoEfectivoController, {closure#1}(), Entrada 25

### Community 110 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.15
Nodes (4): CambiarRestauranteController, SuscripcionController, EmailVerificationNotificationController, EmailVerificationPromptController

### Community 111 - "Instalación de MesaYa en local"
Cohesion: 0.17
Nodes (12): 1. Requisitos, 2. Descargar el proyecto, 3. Bases de datos, 5. Tablas, datos de prueba y fotos, 6. Arrancar, 7. Problemas frecuentes, 8. Despliegue, Cuentas de prueba (+4 more)

### Community 112 - "Illuminate\View\View"
Cohesion: 0.12
Nodes (4): MesaController, ComandaController, AppLayout, GuestLayout

### Community 125 - "EmpleadoRequest"
Cohesion: 0.15
Nodes (4): PersonalController, EmpleadoRequest, 18. `feature/gestion-personal` 🟠, Entrada 33

### Community 126 - "Registro de uso de IA"
Cohesion: 0.10
Nodes (19): Entrada 1, Entrada 16, Entrada 2, Entrada 23, Entrada 27, Entrada 3, Entrada 34, Entrada 35 (+11 more)

### Community 129 - "App\Models\ParteCuenta"
Cohesion: 0.39
Nodes (3): {closure#3}(), {closure#4}(), {closure#6}()

### Community 138 - "1. Requisitos funcionales"
Cohesion: 0.20
Nodes (10): 1. Requisitos funcionales, Acceso, roles y restaurantes, Carta y alérgenos (admin), Cliente por QR (sin login), Comandas, cocina y avisos (personal), Estadísticas (admin), Extras (🟣, solo si sobra tiempo), Mesas y QR (admin) (+2 more)

### Community 142 - "App\Http\Controllers\Cocina\PedidoController"
Cohesion: 0.19
Nodes (5): AvisoController, {closure#1}(), {closure#2}(), {closure#2}(), PedidoController

### Community 143 - "InicioController.php"
Cohesion: 0.22
Nodes (4): InicioController, 16. `feature/pagina-inicio` 🟠, Entrada 30, Entrada 32

### Community 149 - "CambiarRestauranteTest"
Cohesion: 0.06
Nodes (6): CategoriaFactory, PlatoFactory, RestauranteFactory, UserFactory, Entrada 43, CambiarRestauranteTest

### Community 151 - "1. Seguimiento"
Cohesion: 0.18
Nodes (10): 1. Seguimiento, 3. Decisiones pendientes, 4. Cómo pedir cada rama a Claude Code, 🟢 MVP (v0.1) — objetivo: 23/11, Plan de trabajo de MesaYa, Reparto propuesto, Semana 4 — hasta el 07/12, 🟠 Siguientes: mejoras propias (Iván, mientras Gabriel hace la 13) (+2 more)

### Community 155 - "SelectorVistaTest"
Cohesion: 0.20
Nodes (6): 1. Cómo ejecutarlos, 3. Resto de tests por funcionalidad, 4. Pruebas manuales en el navegador, Pruebas de MesaYa, Entrada 14, SelectorVistaTest

### Community 160 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.14
Nodes (3): ComandaRequest, PedidoClienteRequest, ProfileUpdateRequest

### Community 162 - "Restaurante"
Cohesion: 0.12
Nodes (4): {closure#3}(), PerteneceARestaurante, Pago, RegistroDePrueba

### Community 165 - "StripeWebhookController"
Cohesion: 0.48
Nodes (3): StripeWebhookController, 24b. `feature/webhook-suscripcion` 🟠, Entrada 48

### Community 167 - "Diseño técnico de MesaYa"
Cohesion: 0.33
Nodes (5): 1. Arquitectura, 2. Base de datos, 4. Estructura de carpetas (lo nuestro), Diagrama entidad-relación, Diseño técnico de MesaYa

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

### Community 175 - "4. Archivo `.env`"
Cohesion: 0.50
Nodes (4): 4. Archivo `.env`, Claves de Stripe (modo test), Suscripción de los restaurantes (rama 24), Webhook de Stripe (rama 24b)

### Community 178 - "0001_01_01_000002_create_jobs_table.php"
Cohesion: 0.33
Nodes (3): {closure#1}(), {closure#2}(), {closure#3}()

### Community 192 - "Restaurante"
Cohesion: 0.16
Nodes (8): Alergeno, Plato, Restaurante, Base de datos, AlergenosSeeder, Tablas, Entrada 12, Entrada 9

## Knowledge Gaps
- **7 isolated node(s):** `camarero.partes.cobro-efectivo`, `camarero.partes.cobro-efectivo`, `profile.partials.update-password-form`, `profile.partials.update-profile-information-form`, `layouts.navigation` (+2 more)
  These have ≤1 connection - possible missing edges or undocumented components. (Counts symbols only; 607 node(s) total have ≤1 connection when file, concept and rationale nodes are included.)
- **135 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `AutopedidoTest`, `MesasTest`, `App\Models\Restaurante`, `RedireccionTrasLoginTest`, `InicioController.php`, `ParteCuenta`, `CambiarRestauranteTest`, `PanelAdminTest`, `ComandasCamareroTest`, `Restaurante`, `CobroCamareroTest`, `App\Http\Controllers\Admin\MesaController`, `PlatoRequest`, `PantallaCocinaTest`, `Pago`, `PedidosEnCursoSeeder`, `Restaurante`, `EstadisticasTest`, `Illuminate\Database\Seeder`, `web.php`, `GestionCartaTest`, `EfectivoYCierreTest`, `PasswordResetTest.php`, `DispositivosTest`, `GestionPersonalTest`, `Controller`, `Illuminate\Http\RedirectResponse`, `PagarAlPedirTest`, `EmpleadoRequest`?**
  _High betweenness centrality (0.106) - this node is a cross-community bridge._
- **Are the 7 inferred relationships involving `User` (e.g. with `Base de datos` and `TODO en castellano (obligatorio)`) actually correct?**
  _`User` has 7 INFERRED edges - model-reasoned connections that need verification._
- **What connects `camarero.partes.cobro-efectivo`, `camarero.partes.cobro-efectivo`, `profile.partials.update-password-form` to the rest of the system?**
  _7 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `AutopedidoTest` be split into smaller, more focused modules?**
  _Cohesion score 0.08961593172119488 - nodes in this community are weakly interconnected._
- **Why does `Restaurante` connect `Restaurante` to `AutopedidoTest`, `CartaPublicaTest`, `MesasTest`, `App\Models\Restaurante`, `PasarelaStripe`, `CambiarRestauranteTest`, `User`, `PagoStripeTest`, `PanelAdminTest`, `ComandasCamareroTest`, `Restaurante`, `PedidosEnCursoSeederTest`, `StripeWebhookController`, `PantallaCocinaTest`, `Pago`, `PedidosEnCursoSeeder`, `EstadisticasTest`, `Illuminate\Database\Seeder`, `web.php`, `App\Servicios\PasarelaStripe`, `App\Http\Controllers\Cliente\AutopedidoController`, `DispositivosTest`, `GestionPersonalTest`, `Illuminate\Http\RedirectResponse`?**
  _High betweenness centrality (0.092) - this node is a cross-community bridge._
- **Are the 4 inferred relationships involving `Restaurante` (e.g. with `Base de datos` and `Tablas`) actually correct?**
  _`Restaurante` has 4 INFERRED edges - model-reasoned connections that need verification._
- **Should `package.json` be split into smaller, more focused modules?**
  _Cohesion score 0.06086956521739131 - nodes in this community are weakly interconnected._