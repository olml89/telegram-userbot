# Export all variables so sub-makes and shell commands inherit them
export


# Load APP_ENV environment variable safely
-include .env
APP_ENV ?= prod


# Base docker-compose file
DOCKER_COMPOSE := -f docker-compose.yml


# Override the -f and the --env-file options on the docker compose commands depending on the environment
ifeq ($(APP_ENV),prod)
    $(info Using production environment -> adding docker-compose.prod.yml)
    DOCKER_COMPOSE += -f docker-compose.prod.yml
else
    $(info Using development environment -> adding docker-compose.dev.yml)
    DOCKER_COMPOSE += -f docker-compose.dev.yml --env-file .env --env-file backend/.env --env-file shared/.env

	# Load variables from backend/.env file and shared/.env
	-include backend/.env
	-include shared/.env
endif


# Build containers
.PHONY: build up upd stop down deploy

# Guarantee build order: backend and nginx first (backend builds assets), then the rest
build:
	$(eval SERVICE := $(word 2, $(MAKECMDGOALS)))
	$(if $(SERVICE), \
		docker compose $(DOCKER_COMPOSE) build --no-cache $(SERVICE), \
		docker compose $(DOCKER_COMPOSE) build --no-cache backend && \
		docker compose $(DOCKER_COMPOSE) build --no-cache nginx && \
		docker compose $(DOCKER_COMPOSE) build --no-cache \
	)

up:
	$(eval SERVICE := $(word 2, $(MAKECMDGOALS)))
	$(if $(SERVICE), \
		docker compose $(DOCKER_COMPOSE) up --remove-orphans $(SERVICE), \
		docker compose $(DOCKER_COMPOSE) up --remove-orphans \
	)

upd:
	$(eval SERVICE := $(word 2, $(MAKECMDGOALS)))
	$(if $(SERVICE), \
		docker compose $(DOCKER_COMPOSE) up -d --remove-orphans $(SERVICE), \
		docker compose $(DOCKER_COMPOSE) up -d --remove-orphans \
	)

stop:
	$(eval SERVICE := $(word 2, $(MAKECMDGOALS)))
	$(if $(SERVICE), \
		docker compose $(DOCKER_COMPOSE) stop $(SERVICE), \
		docker compose $(DOCKER_COMPOSE) stop \
	)

down:
	$(eval SERVICE := $(word 2, $(MAKECMDGOALS)))
	$(if $(SERVICE), \
		docker compose $(DOCKER_COMPOSE) down $(SERVICE), \
		docker compose $(DOCKER_COMPOSE) down \
	)

deploy:
	@echo "🚀 Starting deployment..."
	$(MAKE) down
	$(MAKE) build
	$(MAKE) upd
	@echo "⏳ Waiting for containers to be ready..."
	docker compose $(DOCKER_COMPOSE) exec -T postgres sh -c 'until pg_isready -U $$POSTGRES_USER; do sleep 1; done'
	docker compose $(DOCKER_COMPOSE) exec -T backend sh -c 'until php -r "exit(0);" 2>/dev/null; do sleep 1; done'
	@echo "🔄 Running database migrations..."
	docker compose $(DOCKER_COMPOSE) exec -T backend php bin/console doctrine:migrations:migrate --no-interaction
	@echo "🧹 Clearing Symfony cache..."
	docker compose $(DOCKER_COMPOSE) exec -T backend php bin/console cache:clear --env=prod
	@echo "✅ Deployment completed successfully!"

# Debug containers
# Make syntax, to avoid dependence on bash/unix
.PHONY: restart debug

restart:
	$(eval SERVICE := $(word 2, $(MAKECMDGOALS)))
	$(if $(SERVICE),,$(error you must specify a container to restart, for example: make restart backend))
	docker compose $(DOCKER_COMPOSE) restart $(SERVICE)

debug:
	$(eval SERVICE := $(word 2, $(MAKECMDGOALS)))
	$(if $(SERVICE),,$(error you must specify a container to debug, for example: make debug backend))
	docker compose $(DOCKER_COMPOSE) run --rm --entrypoint sh $(SERVICE)


# Shell access containers
.PHONY: alloy backend bot bot-manager dev grafana loki nginx tusd postgres postgres-psql redis vite

alloy:
	docker compose $(DOCKER_COMPOSE) exec alloy /bin/sh

backend:
	docker compose $(DOCKER_COMPOSE) exec backend /bin/sh

bot:
	docker compose $(DOCKER_COMPOSE) exec bot /bin/sh

bot-manager:
	docker compose $(DOCKER_COMPOSE) exec bot-manager /bin/sh

dev:
	docker compose $(DOCKER_COMPOSE) exec dev /bin/sh

grafana:
	docker compose $(DOCKER_COMPOSE) exec grafana /bin/sh

loki:
	docker compose $(DOCKER_COMPOSE) exec loki /bin/sh

nginx:
	docker compose $(DOCKER_COMPOSE) exec nginx /bin/sh

tusd:
	docker compose $(DOCKER_COMPOSE) exec tusd /bin/sh

postgres:
	docker compose $(DOCKER_COMPOSE) exec postgres /bin/sh

postgres-psql:
	docker compose $(DOCKER_COMPOSE) exec -e PGPASSWORD=$(DB_PASSWORD) postgres psql -U $(DB_USER) -d $(DB_NAME)

redis:
	docker compose $(DOCKER_COMPOSE) exec redis redis-cli

vite:
	docker compose $(DOCKER_COMPOSE) restart vite

# Development recipes
# The -T flag disables TTY, required when running from non-interactive environments like Git hooks
.PHONY: phpstan pint rector phpunit

# 1) Converts (bot, bot-manager, backend, shared) to --service=(bot, bot-manager, backend, shared)
# 2) Converts ci to --ci (it runs phpstan without showing progress)
phpstan:
	$(eval ARGS := $(wordlist 2, $(words $(MAKECMDGOALS)), $(MAKECMDGOALS)))
	$(eval CI :=)
	$(foreach arg,$(ARGS),\
		$(if $(filter ci,$(arg)),\
			$(eval CI := --ci)))
	$(eval SERVICE := $(filter-out ci,$(ARGS)))
	docker compose $(DOCKER_COMPOSE) exec -T dev composer phpstan -- $(if $(SERVICE),--service=$(SERVICE)) $(CI)

# 1) Converts (bot, bot-manager, backend, shared) to --service=(bot, bot-manager, backend, shared)
# 2) Converts test to --test (it runs checks without applying linting)
pint:
	$(eval ARGS := $(wordlist 2, $(words $(MAKECMDGOALS)), $(MAKECMDGOALS)))
	$(eval TEST :=)
	$(foreach arg,$(ARGS),\
		$(if $(filter test,$(arg)),\
			$(eval TEST := --test)))
	$(eval SERVICE := $(filter-out test,$(ARGS)))
	docker compose $(DOCKER_COMPOSE) exec -T dev composer pint -- $(if $(SERVICE),--service=$(SERVICE)) $(TEST)

# 1) Converts (bot, bot-manager, backend, shared) to --service=(bot, bot-manager, backend, shared)
# 2) Converts dry-run to --dry-run (it runs checks without applying refactoring)
rector:
	$(eval ARGS := $(wordlist 2, $(words $(MAKECMDGOALS)), $(MAKECMDGOALS)))
	$(eval DRY-RUN :=)
	$(foreach arg,$(ARGS),\
		$(if $(filter dry-run,$(arg)),\
			$(eval DRY-RUN := --dry-run)))
	$(eval SERVICE := $(filter-out dry-run,$(ARGS)))
	docker compose $(DOCKER_COMPOSE) exec -T dev composer rector -- $(if $(SERVICE),--service=$(SERVICE)) $(DRY-RUN)

# 1) Converts (bot, bot-manager, backend, shared) to --service=(bot, bot-manager, backend, shared)
# 2) Converts ci to --ci
# 3) Converts coverage to --coverage
# 4) Converts debug to --debug
# 4) Converts any other argument to --filter=argument
#
# Valid examples of this version (and every combination between them):
#
# make phpunit backend
# make phpunit MyTest1 MyTest2 MyTest3 (MyTest1, MyTest2, MyTest3 are not services, so they are parsed as filters)
# make phpunit --coverage
# make phpunit --ci
# make phpunit --debug
phpunit:
	$(eval ARGS := $(filter-out $@,$(MAKECMDGOALS)))
	$(eval FINAL_ARGS :=)
	$(foreach arg,$(ARGS),\
		$(if $(filter bot bot-manager backend shared,$(arg)),\
			$(eval FINAL_ARGS += --service=$(arg)),\
			$(if $(filter ci,$(arg)),\
				$(eval FINAL_ARGS += --ci),\
				$(if $(filter coverage,$(arg)),\
					$(eval FINAL_ARGS += --coverage),\
					$(if $(filter debug,$(arg)),\
						$(eval FINAL_ARGS += --debug),\
						$(eval FINAL_ARGS += --filter=$(arg))\
					)\
				)\
			)\
		)\
	)
	docker compose $(DOCKER_COMPOSE) exec -T dev composer phpunit -- $(FINAL_ARGS)

# Catch-all pattern rule to prevent Make from complaining about unknown targets
%:
	@:
