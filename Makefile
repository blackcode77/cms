.DEFAULT_GOAL := help

export UID := $(shell id -u)
export GID := $(shell id -g)

# Production server settings (DEPLOY_HOST, DEPLOY_PATH, DEPLOY_SSH_KEY, DEPLOY_URL). Local file, see deploy.env.example.
-include deploy.env
DEPLOY_PATH    ?= /opt/cms
DEPLOY_SSH_KEY ?=
DEPLOY_URL     ?=
DEPLOY_SSH     := ssh $(if $(DEPLOY_SSH_KEY),-i $(DEPLOY_SSH_KEY) -o IdentitiesOnly=yes) -o BatchMode=yes
# Not uploaded: local-only files, dependencies and runtime data (the server keeps its own .env, uploads and DB).
DEPLOY_EXCLUDES := /.git /.idea /.vscode /.claude /.scratch /docs /.env /deploy.env node_modules vendor \
	/backend/runtime /backend/storage /frontend/.nuxt /frontend/.output

DC  := docker compose
PHP := $(DC) run --rm -u $(UID):$(GID) php
PHP_TEST := $(DC) run --rm -u $(UID):$(GID) -e APP_ENV=test php
NODE := $(DC) run --rm --no-deps node

help: ## Show this help
	@grep -E '^[a-zA-Z_-]+:.*?## ' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36m%-16s\033[0m %s\n", $$1, $$2}'

.env:
	cp .env.example .env
	sed -i "s|^JWT_SECRET=.*|JWT_SECRET=$$(openssl rand -hex 32)|" .env
	sed -i "s|^ADMIN_PASSWORD=.*|ADMIN_PASSWORD=$$(openssl rand -hex 12)|" .env
	@echo "Generated JWT_SECRET and ADMIN_PASSWORD in .env (sign in as ADMIN_EMAIL with ADMIN_PASSWORD)"

init: .env build composer-install npm-install up migrate ## First-time setup: build, install deps, start, migrate

build: ## Build docker images
	$(DC) build

up: ## Start the stack
	$(DC) up -d

down: ## Stop the stack
	$(DC) down --remove-orphans

restart: down up ## Restart the stack

logs: ## Tail logs (service=name to filter)
	$(DC) logs -f $(service)

clean: ## Stop and remove containers AND volumes (drops the database)
	$(DC) down --volumes --remove-orphans

sh-php: ## Shell in php container
	$(DC) exec -u $(UID):$(GID) php bash

sh-node: ## Shell in node container
	$(DC) exec node sh

psql: ## psql into the dev database
	$(DC) exec postgres sh -c 'psql -U $$POSTGRES_USER -d $$POSTGRES_DB'

composer-install: ## composer install
	$(PHP) composer install

composer: ## Run composer, e.g. make composer c="require foo/bar"
	$(PHP) composer $(c)

npm-install: ## npm install in frontend
	$(NODE) npm install

npm: ## Run npm, e.g. make npm c="run build"
	$(NODE) npm $(c)

migrate: ## Apply DB migrations (dev + test DBs)
	$(PHP) ./yii migrate:up --no-interaction
	$(PHP_TEST) ./yii migrate:up --no-interaction

migrate-create: ## Create migration: make migrate-create name=CreateFooTable
	$(PHP) ./yii migrate:create $(name)

test: test-back test-front ## Run all tests

test-back: ## Run backend tests (Codeception)
	$(PHP_TEST) composer test

test-one: ## Run one test: make test-one f=tests/Api/HealthCest.php[:method]
	$(PHP_TEST) php -d register_argc_argv=On vendor/bin/codecept run $(f)

test-front: ## Run frontend tests (Vitest)
	$(NODE) npm run test

lint: ## psalm + php-cs-fixer (check) + eslint + steiger
	$(PHP) composer psalm
	$(PHP) composer cs-check
	$(NODE) npm run lint

cs-fix: ## Fix PHP code style
	$(PHP) composer cs-fix

# Production images are built here, not on the server (it has ~1 GB RAM: `nuxt build` runs out of memory there).
# Names must match `image:` in compose.prod.yaml.
ship-images:
	docker build -f docker/php/Dockerfile --target prod -t cms-prod-php .
	docker build -f docker/node/Dockerfile --target prod -t cms-prod-node .
	docker save cms-prod-php cms-prod-node | gzip | $(DEPLOY_SSH) $(DEPLOY_HOST) 'gunzip | docker load'

deploy-prod: ## Deploy to production: sync code, restart stack (build=1 builds images locally and ships them), migrate, seed, smoke-check
	@test -n "$(DEPLOY_HOST)" || { echo "Set DEPLOY_HOST in deploy.env (see deploy.env.example)"; exit 1; }
	rsync -az --delete --no-owner --no-group $(foreach p,$(DEPLOY_EXCLUDES),--exclude=$(p)) \
		-e "$(DEPLOY_SSH)" ./ $(DEPLOY_HOST):$(DEPLOY_PATH)/
	$(if $(build),$(MAKE) --no-print-directory ship-images)
	$(DEPLOY_SSH) $(DEPLOY_HOST) 'cd $(DEPLOY_PATH) && set -e && \
		docker compose -f compose.prod.yaml up -d --no-build --remove-orphans && \
		docker compose -f compose.prod.yaml exec -T -u www-data php php yii migrate:up --no-interaction && \
		docker compose -f compose.prod.yaml exec -T -u www-data php php yii app:seed && \
		docker compose -f compose.prod.yaml ps'
	@test -z "$(DEPLOY_URL)" || curl -fsS -o /dev/null -w "smoke check $(DEPLOY_URL): HTTP %{http_code}\n" $(DEPLOY_URL)
