# Graph Report - ProyectoIA_APPWeb  (2026-11-16)

## Corpus Check
- 102 files · ~25,505 words
- Verdict: corpus is large enough that graph structure adds value.
- Unclassified: 11 file(s) not represented in the graph (top: (none) 7, .example 1, .xml 1)

## Summary
- 438 nodes · 547 edges · 64 communities (15 shown, 49 thin omitted)
- Extraction: 99% EXTRACTED · 1% INFERRED · 0% AMBIGUOUS · INFERRED: 4 edges (avg confidence: 0.92)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `fedba887`
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
- TestCase
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

## God Nodes (most connected - your core abstractions)
1. `TestCase` - 22 edges
2. `User` - 19 edges
3. `Controller` - 18 edges
4. `MesaYa — Instrucciones para Claude Code` - 17 edges
5. `2. Qué incluye cada rama` - 14 edges
6. `Propuesta inicial: MesaYa` - 11 edges
7. `LoginRequest` - 9 edges
8. `require-dev` - 9 edges
9. `scripts` - 9 edges
10. `ComponentesDisenoTest` - 8 edges

## Surprising Connections (you probably didn't know these)
- `Base de datos` --references--> `User`  [INFERRED]
  CLAUDE.md → app/Models/User.php
- `TODO en castellano (obligatorio)` --references--> `User`  [INFERRED]
  CLAUDE.md → app/Models/User.php
- `Entrada 6` --references--> `RegistroDesactivadoTest`  [INFERRED]
  docs/registre-ia.md → tests/Feature/Auth/RegistroDesactivadoTest.php
- `{closure#1}()` --references--> `User`  [EXTRACTED]
  app/Http/Controllers/Auth/NewPasswordController.php → app/Models/User.php
- `RegistroDesactivadoTest` --inherits--> `TestCase`  [EXTRACTED]
  tests/Feature/Auth/RegistroDesactivadoTest.php → tests/TestCase.php

## Import Cycles
- None detected.

## Communities (64 total, 49 thin omitted)

### Community 0 - "User"
Cohesion: 0.12
Nodes (5): User, Base de datos, TODO en castellano (obligatorio), UserFactory, DatabaseSeeder

### Community 1 - "composer.json"
Cohesion: 0.04
Nodes (48): pestphp/pest-plugin, php-http/discovery, autoload, autoload-dev, psr-4, psr-4, config, allow-plugins (+40 more)

### Community 2 - "package.json"
Cohesion: 0.08
Nodes (30): devDependencies, alpinejs@^3.4.2, autoprefixer@^10.4.2, concurrently@^10.0.3, @fontsource-variable/inter@^5.3.0, laravel-vite-plugin@^3.1, @material-symbols/font-400@^0.49.0, postcss@^8.4.31 (+22 more)

### Community 3 - "Illuminate\Database\Schema\Blueprint"
Cohesion: 0.16
Nodes (8): {closure#1}(), {closure#2}(), {closure#3}(), {closure#1}(), {closure#2}(), {closure#1}(), {closure#2}(), {closure#3}()

### Community 4 - "MesaYa — Instrucciones para Claude Code"
Cohesion: 0.12
Nodes (16): Comandos útiles, Cómo tienes que trabajar con nosotros, Diseño visual (obligatorio para cualquier vista), Documentación del repo, Funcionalidades y prioridad, Git y GitHub, Grafo del proyecto (graphify), MesaYa — Instrucciones para Claude Code (+8 more)

### Community 5 - "Propuesta inicial: MesaYa"
Cohesion: 0.13
Nodes (14): 10. Riesgos y cómo los abordamos, 1. La idea en una frase, 2. Problema que resuelve, 3. Usuarios y roles, 4. Funcionalidades, 5. Flujo de la demo, 6. Tecnologías, 7. Reparto del trabajo (+6 more)

### Community 6 - "bootstrap/app.php"
Cohesion: 0.32
Nodes (3): {closure#1}(), {closure#2}(), {closure#3}()

### Community 8 - "TestCase"
Cohesion: 0.06
Nodes (8): AuthenticationTest, EmailVerificationTest, PasswordConfirmationTest, PasswordResetTest, PasswordUpdateTest, ExampleTest, ProfileTest, TestCase

### Community 9 - "Prompts por pantalla"
Cohesion: 0.25
Nodes (7): Admin (escritorio), Camarero (móvil), Cliente (móvil), Cocina (Tablet horizontal), Prompt Maestro, PROMPTS para la IA de diseño Web STITCH, Prompts por pantalla

### Community 10 - "README.md"
Cohesion: 0.25
Nodes (7): About Laravel, Agentic Development, Code of Conduct, Contributing, Learning Laravel, License, Security Vulnerabilities

### Community 11 - "2. Qué incluye cada rama"
Cohesion: 0.08
Nodes (23): 10. `feature/dividir-cuenta`, 11. `feature/pago-stripe`, 12. `feature/efectivo-y-cierre`, 13. `feature/estadisticas`, 1. `feature/restaurantes-y-roles`, 1. Seguimiento, 2. `feature/diseno-base`, 2. Qué incluye cada rama (+15 more)

### Community 13 - "Registro de uso de IA"
Cohesion: 0.15
Nodes (9): Entrada 1, Entrada 2, Entrada 3, Entrada 4, Entrada 5, Entrada 6, Entrada 7, Registro de uso de IA (+1 more)

### Community 17 - "Laravel Application"
Cohesion: 0.50
Nodes (3): Agent Setup, Laravel Application, Prerequisites

### Community 22 - "Illuminate\Http\Request"
Cohesion: 0.08
Nodes (13): AuthenticatedSessionController, ConfirmablePasswordController, EmailVerificationNotificationController, EmailVerificationPromptController, {closure#1}(), NewPasswordController, PasswordController, PasswordResetLinkController (+5 more)

### Community 34 - "edit.blade.php"
Cohesion: 0.50
Nodes (3): profile.partials.delete-user-form, profile.partials.update-password-form, profile.partials.update-profile-information-form

## Knowledge Gaps
- **4 isolated node(s):** `layouts.navigation`, `profile.partials.update-profile-information-form`, `profile.partials.update-password-form`, `profile.partials.delete-user-form`
  These have ≤1 connection - possible missing edges or undocumented components. (Counts symbols only; 245 node(s) total have ≤1 connection when file, concept and rationale nodes are included.)
- **49 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `TestCase`, `Illuminate\Http\Request`, `LoginRequest`?**
  _High betweenness centrality (0.126) - this node is a cross-community bridge._
- **Are the 2 inferred relationships involving `User` (e.g. with `Base de datos` and `TODO en castellano (obligatorio)`) actually correct?**
  _`User` has 2 INFERRED edges - model-reasoned connections that need verification._
- **What connects `layouts.navigation`, `profile.partials.update-profile-information-form`, `profile.partials.update-password-form` to the rest of the system?**
  _4 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `User` be split into smaller, more focused modules?**
  _Cohesion score 0.11857707509881422 - nodes in this community are weakly interconnected._
- **Why does `TestCase` connect `TestCase` to `Registro de uso de IA`, `ComponentesDisenoTest`?**
  _High betweenness centrality (0.066) - this node is a cross-community bridge._
- **Should `composer.json` be split into smaller, more focused modules?**
  _Cohesion score 0.04081632653061224 - nodes in this community are weakly interconnected._
- **Why does `MesaYa — Instrucciones para Claude Code` connect `MesaYa — Instrucciones para Claude Code` to `User`?**
  _High betweenness centrality (0.033) - this node is a cross-community bridge._