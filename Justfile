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


# ============================================================================
# CONTAINER LIFECYCLE
# ============================================================================

# Build Docker containers (optionally specify a container)
# Example: just build backend
build SERVICE='':
	@echo "🔨 Building ${SERVICE:-containers}..."
	docker compose {{DOCKER_COMPOSE}} build --no-cache {{SERVICE}}

# Start containers in foreground (optionally specify a container)
# Example: just up backend
up SERVICE='':
	@echo "🟢 Starting ${SERVICE:-containers}..."
	docker compose {{DOCKER_COMPOSE}} up --remove-orphans {{SERVICE}}

# Start containers in detached mode (optionally specify a container)
# Example: just upd backend
upd SERVICE='':
	@echo "🟢 [DETACHED] Starting ${SERVICE:-containers}..."
	docker compose {{DOCKER_COMPOSE}} up -d --remove-orphans {{SERVICE}}

# Stop containers (optionally specify a container)
# Example: just stop backend
stop SERVICE='':
	@echo "⛔ Stopping ${SERVICE:-containers}..."
	docker compose {{DOCKER_COMPOSE}} stop {{SERVICE}}

# Shut down and remove containers (optionally specify a container)
# Example: just down backend
down SERVICE='':
	@echo "🛑 Shutting down and removing ${SERVICE:-containers}..."
	docker compose {{DOCKER_COMPOSE}} down {{SERVICE}}


# ============================================================================
# INSTALLATION & SETUP
# ============================================================================

# Install application (accepts '--reset' and '--build' as arguments)
# Example: just install reset build
install *ARGS:
	bash dev/bin/install/install.sh {{ARGS}}

# Run database migrations and clear Symfony cache
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

# Get the last changes from origin and point the repository to a specific branch
# Set the application in a clean state
# Example: just deploy feature/TBOT-xx-feature-description
deploy BRANCH='main':
	@echo "🚀 Fetching repository (branch: {{BRANCH}})..."
	git fetch origin
	git checkout "$BRANCH"
	git reset --hard origin/"$BRANCH"

	@just install {{ if env('APP_ENV', 'prod') == 'prod' { '--build' } else { '--reset' } }}
	@just setup


# ============================================================================
# DEBUGGING
# ============================================================================

# Restart a container
# Example: just restart backend
restart SERVICE:
	docker compose {{DOCKER_COMPOSE}} restart {{SERVICE}}

# Force a shell into a container, even if it cannot start normally
# Example: just debug backend
debug SERVICE:
	docker compose {{DOCKER_COMPOSE}} run \
		--rm \
		--entrypoint \
		/bin/sh \
		{{SERVICE}}


# ============================================================================
# SHELL ACCESS
# ============================================================================

alloy:
	docker compose {{DOCKER_COMPOSE}} exec \
		alloy \
		/bin/sh

backend:
	docker compose {{DOCKER_COMPOSE}} exec \
		backend \
		/bin/sh

bot:
	docker compose {{DOCKER_COMPOSE}} exec \
		bot \
		/bin/sh

bot-manager:
	docker compose {{DOCKER_COMPOSE}} exec \
		bot-manager \
		/bin/sh

dev:
	docker compose {{DOCKER_COMPOSE}} exec \
		dev \
		/bin/sh

grafana:
	docker compose {{DOCKER_COMPOSE}} exec \
		grafana \
		/bin/sh

nginx:
	docker compose {{DOCKER_COMPOSE}} exec \
		nginx \
		/bin/sh

tusd:
	docker compose {{DOCKER_COMPOSE}} exec \
		tusd \
		/bin/sh

postgres:
	docker compose {{DOCKER_COMPOSE}} exec \
		postgres \
		/bin/sh

postgres-psql:
	$(just _env)
	docker compose {{DOCKER_COMPOSE}} exec \
		-e PGPASSWORD="$DB_PASSWORD" \
		postgres psql \
		-U "$DB_USER" \
		-d "$DB_NAME"

redis:
	docker compose {{DOCKER_COMPOSE}} exec \
		redis \
		/bin/sh

redis-cli:
	docker compose {{DOCKER_COMPOSE}} exec \
		redis \
		redis-cli

vite:
	docker compose {{DOCKER_COMPOSE}} exec \
		vite \
		/bin/sh


# ============================================================================
# CODE QUALITY TOOLS
# ============================================================================

# Run PHPStan (accepts services and '--no-progress' flag)
# Example: just phpstan backend application --no-progress
phpstan *ARGS:
	docker compose {{DOCKER_COMPOSE}} exec \
		-T \
		dev \
		composer phpstan -- {{ARGS}}

# Run Pint (accepts services and '--test' flag)
# Example: just pint backend application --test
pint *ARGS:
	docker compose {{DOCKER_COMPOSE}} exec \
		-T \
		dev \
		composer pint -- {{ARGS}}

# Run Rector (accepts services and '--dry-run' flag)
# Example: just rector backend application --dry-run
rector *ARGS:
	docker compose {{DOCKER_COMPOSE}} exec \
		-T \
		dev \
		composer rector -- {{ARGS}}

# Run PHPUnit (accepts services and '--filter', '--debug', '--coverage-text', '--coverage-clover' flags)
# Example: just phpunit backend application --coverage-clover
# Note: If a --filter is passed, be careful to pass the correct service where to apply it
phpunit *ARGS:
	docker compose {{DOCKER_COMPOSE}} exec \
		-T \
		dev \
		composer phpunit -- {{ARGS}}

# It checks if the dev/composer.json is updated with the platform requirements (dev/bin/git/commit/sync-platform-reqs.php)
# and runs the code quality tools. This is used by the pre-commit git hook but it can be used standalone
validate-commit:
	docker compose {{DOCKER_COMPOSE}} exec \
		-T \
		dev \
		composer validate-commit
