# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

QuickAlive — a PHP web app (TFG/thesis project) for discovering and recommending activities to users, with admin management of activities/preferences/users, and a premium AI assistant feature.

## Running it

Two ways to run:

- **Docker (preferred, no local XAMPP needed):**
  ```
  cp .env.example .env   # fill in ANTHROPIC_API_KEY
  docker compose up -d
  ```
  App at `http://localhost:8080`, phpMyAdmin at `http://localhost:8081`. The `db` service auto-imports `sql/quickalivedb.sql` on first run (only — it won't re-import if the `db_data` volume already exists).
- **Local PHP/Apache (XAMPP-style):** requires PHP 8.1+, the `mysqli` extension, and the same env vars Docker sets (`DB_HOST`, `DB_USER`, `DB_PASS`, `DB_NAME`, `ANTHROPIC_API_KEY`) available via `getenv()`.

There is no front-controller/router: every feature is its own PHP entry point reached by direct URL (see Architecture below), not dispatched through `index.php`.

## Dependencies

`composer install` (PHP). No JS package manager — frontend JS/CSS are plain files served as-is.

## Tests

PHPUnit (`vendor/bin/phpunit`), tests live under `test/<feature>/test<Feature>.php`. There is no `phpunit.xml` — run files directly, e.g.:
```
vendor/bin/phpunit test/testUsuario/testUsuario.php
```
**Tests hit a real database** (no mocking) via relative `require_once "../../backend/bd/bd.php"` — run them from the repo root with the DB env vars set (e.g. against the Dockerized `db` service), not against production data, since they insert/generate real rows.

## Architecture

**Feature-sliced, not MVC-routed.** Each feature is a self-contained vertical slice under three mirrored trees, all keyed by the same feature folder name:
- `backend/{admin,user,api}/<gestionFeature>/` — PHP entry points (do `session_start()`, check `$_SESSION['loggedin']`/role, talk to `BD`, render a Twig template, echo the result). Reached directly by URL — there's no shared router.
- `frontend/<area>/templates/<gestionFeature>/` — Twig templates for that feature.
- `frontend/<area>/css/<gestionFeature>/` and `frontend/<area>/js/` — feature-scoped styling/JS.

`<area>` is `admin`, `user`, or `common` (shared templates like login). When adding a feature, replicate this folder triplet under the matching area rather than centralizing routes.

**Data access:** `backend/bd/bd.php` (`BD` class) is the single data-access layer — holds the `mysqli` connection (built from `getenv('DB_HOST'|'DB_USER'|'DB_PASS'|'DB_NAME')`) and every prepared-statement query method. `backend/bd/usuario/usuario.php` and `backend/bd/actividad/actividad.php` are plain domain/DTO classes (no DB logic) used alongside it. New queries go on `BD`, not on the DTO classes.

**Templating:** Twig, loaded fresh per entry point with a `FilesystemLoader` pointed at that feature's template directory (not a single shared environment) — see the pattern in `backend/user/gestionAsistenteIA/renderAsistenteIA.php` or `backend/api/sesiones/login.php`.

**Auth/session:** plain PHP sessions. `$_SESSION['loggedin']` and `$_SESSION['rol']` gate access; `$_SESSION['idUsuario']` identifies the user. No middleware — every entry point repeats the session check itself.

**Premium gating:** `usuario.isPremium` (tinyint) + `BD::esUsuarioPremium($idUsuario)`. `BD::activarPremiumDemo()` is a demo-only activation with no real payment gateway behind it yet (see TODO in `backend/bd/bd.php`) — don't treat it as production billing logic.

**External APIs called via env-configured keys:** Anthropic SDK (`ANTHROPIC_API_KEY`, the AI assistant in `gestionAsistenteIA`) and TicketMaster (`API_KEY`, in `backend/api/variablesEntorno/variablesTicketMaster.php`).
