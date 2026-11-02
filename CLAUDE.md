# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

Monorepo: `backend/` (Yii3 API, PHP 8.5), `frontend/` (Nuxt 4 SSR + Nuxt UI), `docker/` (nginx, php, node, postgres). Everything runs in Docker via `make`; do not run `php`/`composer`/`npm` on the host.

## Commands

Run `make help` for the full list. Most used:

- `make init` — first-time setup (copies `.env`, builds images, installs deps, starts stack, migrates dev + test DBs)
- `make up` / `down` / `logs service=php`
- `make test` (all), `make test-back` (Codeception), `make test-front` (Vitest)
- `make test-one f=tests/Api/PageCest.php:createRejectsDuplicateSlug` — single backend test
- `make lint` — psalm + php-cs-fixer (check) + eslint + steiger; `make cs-fix` fixes PHP style
- `make migrate`, `make migrate-create name=CreateFooTable`
- `make composer c="require x/y"`, `make npm c="install x"`, `make sh-php`, `make psql`

Frontend single test: `docker compose run --rm --no-deps node npx vitest run tests/widgets/health-status-card.test.ts`.

Entry point is `http://cms.localhost` (nginx, port `HTTP_PORT`): `/api/*` → php-fpm, everything else → Nuxt dev server.

## Backend architecture (`backend/`)

Layered DDD modelled on `yiisoft/demo-blog`. Dependencies point inward: Presentation → Application → Domain; Infrastructure implements Domain interfaces.

- `src/Domain/<Aggregate>/{Entity,Value,Repository}/` — entity, value objects (`PageId` extends `Domain/Shared/Value/UuidValue`, `PageTitle`, `PageSlug`), `*RepositoryInterface`. No framework code. Create every new aggregate with this layout (add `Exception/`, `Event/`, … only when needed).
- `src/Application/<Aggregate>/<UseCase>/{Command,Handler,Result}` — one folder per use case. Handlers generate IDs (`Uuid::uuid7()`) and take time from `Psr\Clock\ClockInterface`. Business errors are exceptions in `src/Application/<Aggregate>/Exception/` marked `#[UserException]` — the API turns them into a 400 JSON response.
- `src/Infrastructure/` — `Repository/Db*Repository` use yiisoft/db **QueryBuilder (no ActiveRecord)**; rows → entities via `DataMapper/EntityHydratorInterface` (builds entities without calling constructors). `ServiceProvider` is the DI wiring for repositories, DB connection, clock. `Table` holds table-name constants. Migrations in `Migration/` (PostgreSQL `uuid` PKs, hand-written SQL).
- `src/Presentation/{Api,Console,Site}` — `Api/<Context>/<Op>/{Action,Input,Presenter}`; actions are invokable, thin, and respond only through `Api/Shared/ResponseFactory` (envelope `{status: success|failed, data | error_message, error_data}`). `Site` is an intentionally empty placeholder.
- `src/Shared/Read/` — read models/queries that bypass repositories (list endpoints). Reading goes here, writing goes through Application handlers.

Config uses `yiisoft/config` groups (`config/configuration.php`): `api` and `console` apps each have their own `di-*`/`params-*`/`events-*`. API routes live in `config/api/routes.php` and are mounted under `/api/v1` in `config/common/di/router.php`. After changing `configuration.php` or adding packages, run `make composer c="yii-config-rebuild"`.

DB selection: `ServiceProvider` reads `DB_*` env vars (set in `compose.yaml`); `APP_ENV=test` switches to `DB_TEST_NAME` (`app_test`). `make test*` and the test migrations pass `APP_ENV=test` explicitly.

### Testing conventions

Every endpoint needs a Cest in `tests/Api/`. The `Api` suite runs the app **in-process** (`ApiTester::sendApiRequest`) against the real PostgreSQL `app_test` database — no HTTP server, no SQLite. Call `$I->cleanDatabase()` in `_before` (add new tables to it). `tests/Unit` holds domain/handler tests (use in-memory repository fakes); `tests/Console` shells out to `./yii`. `tests/Support/_generated` is generated — run `make composer c=codecept-build` after adding suites/modules.

## Frontend architecture (`frontend/`)

Feature-Sliced Design inside Nuxt 4 (`app/` is srcDir):

- `app/pages/` is **Nuxt routing only** — thin files that import a page from `@pages/<slice>`. `app/layouts/`, `app/app.vue` are Nuxt's.
- FSD layers live in `app/layers/{pages,widgets,features,entities,shared}`, imported through aliases `@pages @widgets @features @entities @shared` and each slice's `index.ts` (public API). Component auto-import is disabled (`components.dirs: []`), so import explicitly. `make lint` runs `steiger` to enforce layer rules (lower layers must not import higher ones).
- Data fetching: `useApi()` in `@shared/api` picks the base URL — SSR uses `runtimeConfig.apiInternalUrl` (`http://nginx/api`, docker-internal, env `NUXT_API_INTERNAL_URL`) and forwards the visitor's cookie; the browser uses same-origin `public.apiBase` (`/api`). Always fetch via `useApi()` + `useAsyncData`, never hardcode a host.
- `/admin/**` is SPA (`routeRules ssr: false`); everything else is SSR.
- Tests: Vitest with `@nuxt/test-utils` (`environment: 'nuxt'`); mock API with `registerEndpoint`, and `clearNuxtData(key)` between tests that share a `useAsyncData` key.

## Gotchas

- Nuxt UI's fonts module downloads from external CDNs and, when blocked/slow, crashes SSR with `IPC connection closed` on every request. It is disabled with `ui: { fonts: false }` in `nuxt.config.ts` — keep it that way.
- Containers run as your UID/GID (Makefile exports them); `docker compose run` options like `-u`/`-e` must come **before** the service name.
- `rector` is intentionally not installed (its `phpstan` dependency download stalled); add it back with `make composer c="require --dev rector/rector"` when needed.
- Auth (JWT access + refresh) is planned but not implemented; the intended design is httpOnly cookies, so SSR requests can carry the session.

## Agent skills

### Issue tracker

Issues and specs are local markdown files under `.scratch/<feature-slug>/`. See `docs/agents/issue-tracker.md`.

### Triage labels

Default five-role vocabulary (`needs-triage`, `needs-info`, `ready-for-agent`, `ready-for-human`, `wontfix`), recorded as a `Status:` line. See `docs/agents/triage-labels.md`.

### Domain docs

Single-context: `GLOSSARY.md` and `docs/adr/` at the repo root. See `docs/agents/domain.md`.
