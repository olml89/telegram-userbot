# Load environment variable safely
-include .env
APP_ENV ?= production


# Load postgres variables safely
-include backend/.env
DB_USER ?= postgres
DB_PASSWORD ?= postgres
DB_NAME ?= postgres


# File args
DOCKER_COMPOSE := -f docker-compose.prod.yml
ENV := --env-file .env --env-file backend/.env


# Override of the docker-compose on development
ifneq ($(APP_ENV),production)
    $(info Using development environment -> adding docker-compose.dev.yml)
    DOCKER_COMPOSE += -f docker-compose.dev.yml
endif


# Build containers
.PHONY: build upd stop down restart debug

build:
	$(eval SERVICE := $(word 2, $(MAKECMDGOALS)))
	$(if $(SERVICE), \
		docker-compose $(DOCKER_COMPOSE) $(ENV) build $(SERVICE), \
		docker-compose $(DOCKER_COMPOSE) $(ENV) build \
	)

upd:
	$(eval SERVICE := $(word 2, $(MAKECMDGOALS)))
	$(if $(SERVICE), \
		docker-compose $(DOCKER_COMPOSE) $(ENV) up -d --build --remove-orphans $(SERVICE), \
		docker-compose $(DOCKER_COMPOSE) $(ENV) up -d --build --remove-orphans \
	)

stop:
	$(eval SERVICE := $(word 2, $(MAKECMDGOALS)))
	$(if $(SERVICE), \
		docker-compose $(DOCKER_COMPOSE) $(ENV) stop $(SERVICE), \
		docker-compose $(DOCKER_COMPOSE) $(ENV) stop \
	)

down:
	docker-compose $(DOCKER_COMPOSE) $(ENV) down


# Debug containers
# Make syntax, to avoid dependence on bash/unix
.PHONY: restart debug

restart:
	$(eval SERVICE := $(word 2, $(MAKECMDGOALS)))
	$(if $(SERVICE),,$(error you must specify a container to restart, for example: make restart backend))
	docker-compose $(DOCKER_COMPOSE) $(ENV) restart $(SERVICE)

debug:
	$(eval SERVICE := $(word 2, $(MAKECMDGOALS)))
	$(if $(SERVICE),,$(error you must specify a container to debug, for example: make debug backend))
	docker-compose $(DOCKER_COMPOSE) $(ENV) run --rm --entrypoint sh $(SERVICE)


# Shell access containers
.PHONY: alloy backend bot bot-manager dev grafana loki nginx postgres redis

alloy:
	docker-compose $(DOCKER_COMPOSE) $(ENV) exec alloy /bin/sh

backend:
	docker-compose $(DOCKER_COMPOSE) $(ENV) exec backend /bin/sh

bot:
	docker-compose $(DOCKER_COMPOSE) $(ENV) exec bot /bin/sh

bot-manager:
	docker-compose $(DOCKER_COMPOSE) $(ENV) exec bot-manager /bin/sh

dev:
	docker-compose $(DOCKER_COMPOSE) $(ENV) exec dev /bin/sh

grafana:
	docker-compose $(DOCKER_COMPOSE) $(ENV) exec grafana /bin/sh

loki:
	docker-compose $(DOCKER_COMPOSE) $(ENV) exec loki /bin/sh

nginx:
	docker-compose $(DOCKER_COMPOSE) $(ENV) exec nginx /bin/sh

postgres:
	docker-compose $(DOCKER_COMPOSE) $(ENV) exec -e PGPASSWORD=$(DB_PASSWORD) postgres psql -U $(DB_USER) -d $(DB_NAME)

redis:
	docker-compose $(DOCKER_COMPOSE) $(ENV) exec redis redis-cli


# Install dependencies (on dev, on prod they are already installed inside the images)
.PHONY: composer-install
SERVICES := bot bot-manager backend shared

composer-install: up $(addprefix install-,$(SERVICES))

install-%:
	@if [ "$*" = "shared" ]; then \
		echo "🔧 Installing shared dependencies (via bot container)..."; \
		docker-compose $(DOCKER_COMPOSE) exec -T bot sh -c "/telegram-userbot/shared/bin/composer-install.sh"; \
	else \
		echo "🔧 Installing $* dependencies..."; \
		docker-compose $(DOCKER_COMPOSE) exec -T $* sh -c "/telegram-userbot/shared/bin/composer-install.sh"; \
	fi

# Development recipes
# The -T flag disables TTY, required when running from non-interactive environments like Git hooks
.PHONY: phpstan pint phpunit

phpstan:
	$(eval ARGS := $(wordlist 2, $(words $(MAKECMDGOALS)), $(MAKECMDGOALS)))
	$(eval CI :=)
	$(foreach arg,$(ARGS),\
		$(if $(filter ci,$(arg)),\
			$(eval CI := --ci)))
	$(eval SERVICE := $(filter-out ci,$(ARGS)))
	docker-compose $(DOCKER_COMPOSE) $(ENV) exec -T dev composer phpstan -- $(if $(SERVICE),--service=$(SERVICE)) $(CI)

pint:
	$(eval ARGS := $(wordlist 2, $(words $(MAKECMDGOALS)), $(MAKECMDGOALS)))
	$(eval TEST :=)
	$(foreach arg,$(ARGS),\
		$(if $(filter test,$(arg)),\
			$(eval TEST := --test)))
	$(eval SERVICE := $(filter-out test,$(ARGS)))
	docker-compose $(DOCKER_COMPOSE) $(ENV) exec -T dev composer pint -- $(if $(SERVICE),--service=$(SERVICE)) $(TEST)

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
	docker-compose $(DOCKER_COMPOSE) $(ENV) exec -T dev composer phpunit -- $(FINAL_ARGS)

# Catch-all pattern rule to prevent Make from complaining about unknown targets
%:
	@:
