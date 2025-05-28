DOCKER_COMPOSE := -f docker/docker-compose.prod.yml
ENV := --env-file .env

.PHONY: dev
dev:
	$(eval override DOCKER_COMPOSE += -f docker/docker-compose.dev.yml)

.PHONY: build
build:
	docker-compose $(DOCKER_COMPOSE) $(ENV) build

.PHONY: upd
upd:
	docker-compose $(DOCKER_COMPOSE) $(ENV) up -d --build --remove-orphans

.PHONY: down
down:
	docker-compose $(DOCKER_COMPOSE) $(ENV) down

.PHONY: ssh
ssh:
	docker-compose $(DOCKER_COMPOSE) $(ENV) exec php-fpm /bin/sh
