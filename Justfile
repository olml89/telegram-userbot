# Automatically load .env files
# Needed on production to read from the generic .env
# On development we use direnv to automatically reload all the needed env vars in the shell
set dotenv-load


# ============================================================================
# ENVIRONMENT
# ============================================================================

DOCKER_COMPOSE := '-f docker-compose.yml ' + if env('APP_ENV', 'prod') == 'prod' {
	'-f docker-compose.prod.yml'
} else {
	'-f docker-compose.dev.yml'
}

_env-dev:
	@if [ "${APP_ENV:-}" != "dev" ]; then \
		echo "❌ only allowed in development (APP_ENV=${APP_ENV:-unset})"; \
		exit 1; \
	fi

_env-prod:
	@if [ "${APP_ENV:-}" != "prod" ]; then \
		echo "❌ only allowed in production (APP_ENV=${APP_ENV:-unset})"; \
		exit 1; \
	fi


# ============================================================================
# CONTAINER LIFECYCLE
# ============================================================================

# > It builds containers
#
# Arguments:
#	[SERVICES...] 		The services to build (all of them if no service is specified)
build *SERVICES:
	@echo "🔨 Building {{ if SERVICES == '' { 'containers' } else { SERVICES } }}..."
	docker compose {{DOCKER_COMPOSE}} build --no-cache {{SERVICES}}

# > It starts containers in foreground
#
# Arguments:
#	[SERVICES...] 		The services to start (all of them if no service is specified)
up *SERVICES: _dev-startup
	@echo "🟢 Starting {{ if SERVICES == '' { 'containers' } else { SERVICES } }}..."
	docker compose {{DOCKER_COMPOSE}} up --remove-orphans {{SERVICES}}

# > It starts containers in detached mode
#
# Arguments:
#	[SERVICES...] 		The services to start (all of them if no service is specified)
upd *SERVICES: _dev-startup
	@echo "🟢 [DETACHED] Starting {{ if SERVICES == '' { 'containers' } else { SERVICES } }}..."
	docker compose {{DOCKER_COMPOSE}} up -d --remove-orphans {{SERVICES}}

# > It stops containers
#
# Arguments:
#	[SERVICES...] 		The services to stop (all of them if no service is specified)
stop *SERVICES:
	@echo "⛔ Stopping {{ if SERVICES == '' { 'containers' } else { SERVICES } }}..."
	docker compose {{DOCKER_COMPOSE}} stop {{SERVICES}}

# > It shuts down and removes containers
#
# Arguments:
#	[SERVICES...] 		The services to shut down and remove (all of them if no service is specified)
down *SERVICES:
	@echo "🛑 Shutting down and removing {{ if SERVICES == '' { 'containers' } else { SERVICES } }}..."
	docker compose {{DOCKER_COMPOSE}} down {{SERVICES}}

# > It restarts a container
#
# Arguments:
#	[SERVICES...] 		The containers to restart (all of them if no service is specified)
restart *SERVICES:
	@echo "🔄 Restarting {{ if SERVICES == '' { 'containers' } else { SERVICES } }}..."
	docker compose {{DOCKER_COMPOSE}} restart {{SERVICES}}


# ============================================================================
# SHELL ACCESS
# ============================================================================

# > It forces SSH access into a container, even if it cannot start normally
#
# Arguments:
#	SERVICE 		The container to log into
debug SERVICE:
	@echo "🐞 Forcing shell access to {{ SERVICE }}"
	docker compose {{DOCKER_COMPOSE}} run \
		--rm \
		--entrypoint \
		/bin/sh \
		{{SERVICE}}

# > It opens an interactive shell inside the container via docker exec (bin/ssh)
#
# Arguments:
#	SERVICE 	The service/container to attach to
#
# Note:
# 	loki cannot be accessed doing `just ssh loki` as it is a distroless container without shell
sh SERVICE:
	@echo "💻 Getting shell access to {{ SERVICE }}"
	docker compose {{DOCKER_COMPOSE}} exec {{SERVICE}} /bin/sh

# > It runs psql inside the postgres container
psql:
	docker compose {{DOCKER_COMPOSE}} exec \
		-e PGPASSWORD="$DB_PASSWORD" \
		postgres psql \
		-U "$DB_USER" \
		-d "$DB_NAME"

# > It runs redis-cli inside the redis container
redis-cli:
	docker compose {{DOCKER_COMPOSE}} exec redis redis-cli


# ============================================================================
# INSTALLATION & SETUP
# ============================================================================

# > It ensures runtime directories exist on dev
_dev-startup:
	@if [ "${APP_ENV}" = "dev" ]; then \
		mkdir -p .runtime/uploads; \
		echo "🔧 Created: .runtime/uploads"; \
		mkdir -p .runtime/content; \
		echo "🔧 Created: .runtime/content"; \
	fi

# > It reinitializes the application by recreating containers and required runtime directories.
#
# Options:
#   --reset		Remove mounted node_modules, var, and vendor directories
#             	(not applicable in production)
#
#   --build		Rebuild containers before starting them
init *OPTIONS:
	bash dev/bin/init/init.sh {{OPTIONS}}

# > It runs database migrations and clears Symfony cache
setup:
	@echo "⏳ Waiting for containers to be ready..."
	docker compose {{DOCKER_COMPOSE}} up postgres -d --wait 2>/dev/null
	docker compose {{DOCKER_COMPOSE}} up backend -d --wait 2>/dev/null

	@echo "🔄 Running database migrations..."
	docker compose {{DOCKER_COMPOSE}} exec \
		-it \
		backend \
		bin/console doctrine:migrations:migrate --no-interaction

	@echo "🧹 Clearing Symfony cache..."
	docker compose {{DOCKER_COMPOSE}} exec \
		-it \
		backend \
		bin/console cache:clear --env=prod

# [PRODUCTION]
#
# > It fetches the last changes from origin and points the repository to a specific branch (main if not specified)
#
# Arguments:
#	BRANCH 		Git branch to deploy (default: main)
deploy BRANCH='main':
	@just _env-prod

	@echo "🚀 Fetching repository (branch: {{BRANCH}})..."
	git fetch origin
	git checkout {{BRANCH}}
	git reset --hard origin/{{BRANCH}}

	@just init {{ if env('APP_ENV', 'prod') == 'prod' { '--build' } else { '' } }}
	@just setup


# ============================================================================
# CODE QUALITY TOOLS [DEVELOPMENT]
# ============================================================================

# > It runs phpstan
#
# Arguments:
# 	[SERVICES...] 	The services to analyse (application, bot-runtime, bot, bot-manager, backend, dev)
#
# Options:
#   --no-progress	Remove mounted node_modules, var, and vendor directories
#             		(not applicable in production)
phpstan *ARGS:
	@just _env-dev
	docker compose {{DOCKER_COMPOSE}} exec -T dev ./bin/phpstan/phpstan.sh {{ARGS}}

# > It runs pint
#
# Arguments:
# 	[SERVICES...] 	The services to analyse (application, bot-runtime, bot, bot-manager, backend, dev)
#
# Options:
#   --test			Only show the suggested code changes to follow the PER coding style, without applying them
pint *ARGS:
	@just _env-dev
	docker compose {{DOCKER_COMPOSE}} exec -T dev ./bin/pint/pint.sh {{ARGS}}

# > It runs rector
#
# Arguments:
# 	[SERVICES...] 	The services to analyse (application, bot-runtime, bot, bot-manager, backend, dev)
#
# Options:
#   --dry-run		Only show the suggested refactorings, without applying them
rector *ARGS:
	@just _env-dev
	docker compose {{DOCKER_COMPOSE}} exec -T dev ./bin/rector/rector.sh {{ARGS}}

# > It runs phpunit
#
# Arguments:
# 	[SERVICES...] 			The services to analyse (application, bot-runtime, bot, bot-manager, backend, dev)
#
# Options:
#   --filter EXPRESSION		Run only tests that match the given expression in the given services
# 	--debug					Enable the ability to set breakpoints on tests
# 	--coverage-text			Add text coverage through the CLI
#	--coverage-clover		Add clover coverage (useful during CI/CD pipelines)
phpunit *ARGS:
	@just _env-dev
	docker compose {{DOCKER_COMPOSE}} exec -T dev ./bin/phpunit/phpunit.sh {{ARGS}}

# > It runs tsc
#
# Arguments:
# 	[SERVICES...] 			The services to analyse (backend)
tsc:
	@just _env-dev
	docker compose {{DOCKER_COMPOSE}} exec -T dev ./bin/tsc/tsc.sh --noEmit

# > Is the equivalent of running:
#	just phpunit [SERVICES...]
#	just phpstan [SERVICES...]
#	just pint --test [SERVICES...]
#	just rector --dry-run [SERVICES...]
#	just tsc [SERVICES...]
#
# Arguments:
# 	[SERVICES...] 			The services to analyse (application, bot-runtime, bot, bot-manager, backend, dev)
code-quality *SERVICES:
	@just _env-dev
	docker compose {{DOCKER_COMPOSE}} exec -T dev ./bin/git/commit/code-quality.sh {{SERVICES}}

# > It checks if the dependencies are in sync
# 	composer.json 	<-> 	composer.lock 		(application, bot, bot-runtime, bot-manager, backend, dev)
# 	package.json 	<-> 	package-lock.json	(backend)
#
# Arguments:
# 	[SERVICES...] 			The services to analyse (application, bot-runtime, bot, bot-manager, backend, dev)
check-dependencies *SERVICES:
	@just _env-dev
	docker compose {{DOCKER_COMPOSE}} exec -T dev ./bin/commit/dependencies/check-dependencies-sync.sh {{SERVICES}}

# > It checks if staged files have CRLF line endings
# > It checks if dependencies are in sync (application, bot-runtime, bot, bot-manager, backend)
# > It checks if the require section of the dev/composer.json is in sync with required php extensions from the services
# > It checks if the dependencies are in sync (dev)
#
# Options:
#   -f		Automatically convert CRLF line endings to LF and git add the modified files
#			Force update the dev/composer.json with the missing php extensions from services
#			Automatically update composer.lock, and add composer.json and composer.lock to the git staged files
check-repository *ARGS:
	@just _env-dev
	docker compose {{DOCKER_COMPOSE}} exec -T dev ./bin/git/commit/check-repository/check-repository.sh {{ARGS}}
