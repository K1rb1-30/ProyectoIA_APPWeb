# Graph Report - ProyectoIA_APPWeb  (2026-11-15)

## Corpus Check
- 40 files · ~14,149 words
- Verdict: corpus is large enough that graph structure adds value.
- Unclassified: 11 file(s) not represented in the graph (top: (none) 7, .example 1, .xml 1)

## Summary
- 229 nodes · 240 edges · 31 communities (14 shown, 17 thin omitted)
- Extraction: 99% EXTRACTED · 1% INFERRED · 0% AMBIGUOUS · INFERRED: 2 edges (avg confidence: 0.95)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `a8ae3d87`
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
- scripts
- psr-4
- Prompts por pantalla
- README.md
- config
- AppServiceProvider
- Registro de uso de IA
- TestCase
- DatabaseSeeder.php
- Laravel Application
- ExampleTest
- Controller.php
- .claude/CLAUDE.md

## God Nodes (most connected - your core abstractions)
1. `MesaYa — Instrucciones para Claude Code` - 17 edges
2. `Propuesta inicial: MesaYa` - 11 edges
3. `User` - 10 edges
4. `scripts` - 9 edges
5. `require-dev` - 8 edges
6. `Registro de uso de IA` - 6 edges
7. `UserFactory` - 5 edges
8. `AppServiceProvider` - 5 edges
9. `config` - 5 edges
10. `Prompts por pantalla` - 5 edges

## Surprising Connections (you probably didn't know these)
- `TODO en castellano (obligatorio)` --references--> `User`  [INFERRED]
  CLAUDE.md → app/Models/User.php
- `Base de datos` --references--> `User`  [INFERRED]
  CLAUDE.md → app/Models/User.php
- `ExampleTest` --inherits--> `TestCase`  [EXTRACTED]
  tests/Feature/ExampleTest.php → tests/TestCase.php

## Import Cycles
- None detected.

## Communities (31 total, 17 thin omitted)

### Community 0 - "User"
Cohesion: 0.11
Nodes (4): User, Base de datos, TODO en castellano (obligatorio), UserFactory

### Community 1 - "composer.json"
Cohesion: 0.07
Nodes (26): autoload-dev, psr-4, description, extra, laravel, keywords, dont-discover, license (+18 more)

### Community 2 - "package.json"
Cohesion: 0.13
Nodes (20): devDependencies, concurrently@^10.0.3, laravel-vite-plugin@^3.1, tailwindcss@^4.0.0, @tailwindcss/vite@^4.0.0, vite@^8.0.0, optionalDependencies, @laravel/multiplex@^0.4.1 (+12 more)

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
Cohesion: 0.33
Nodes (3): {closure#1}(), {closure#2}(), {closure#3}()

### Community 7 - "scripts"
Cohesion: 0.22
Nodes (9): scripts, dev, post-autoload-dump, post-create-project-cmd, post-root-package-install, post-update-cmd, pre-package-uninstall, setup (+1 more)

### Community 8 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 9 - "Prompts por pantalla"
Cohesion: 0.25
Nodes (7): Admin (escritorio), Camarero (móvil), Cliente (móvil), Cocina (Tablet horizontal), Prompt Maestro, PROMPTS para la IA de diseño Web STITCH, Prompts por pantalla

### Community 10 - "README.md"
Cohesion: 0.25
Nodes (7): About Laravel, Agentic Development, Code of Conduct, Contributing, Learning Laravel, License, Security Vulnerabilities

### Community 11 - "config"
Cohesion: 0.29
Nodes (7): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, preferred-install, sort-packages

### Community 13 - "Registro de uso de IA"
Cohesion: 0.29
Nodes (6): Entrada 1, Entrada 2, Entrada 3, Entrada 4, Entrada 5, Registro de uso de IA

### Community 17 - "Laravel Application"
Cohesion: 0.50
Nodes (3): Agent Setup, Laravel Application, Prerequisites

## Knowledge Gaps
- **17 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `DatabaseSeeder.php`?**
  _High betweenness centrality (0.026) - this node is a cross-community bridge._
- **Are the 2 inferred relationships involving `User` (e.g. with `Base de datos` and `TODO en castellano (obligatorio)`) actually correct?**
  _`User` has 2 INFERRED edges - model-reasoned connections that need verification._
- **Should `User` be split into smaller, more focused modules?**
  _Cohesion score 0.10507246376811594 - nodes in this community are weakly interconnected._
- **Why does `MesaYa — Instrucciones para Claude Code` connect `MesaYa — Instrucciones para Claude Code` to `User`?**
  _High betweenness centrality (0.023) - this node is a cross-community bridge._
- **Should `composer.json` be split into smaller, more focused modules?**
  _Cohesion score 0.07407407407407407 - nodes in this community are weakly interconnected._
- **Why does `scripts` connect `scripts` to `composer.json`?**
  _High betweenness centrality (0.013) - this node is a cross-community bridge._
- **Should `package.json` be split into smaller, more focused modules?**
  _Cohesion score 0.12554112554112554 - nodes in this community are weakly interconnected._