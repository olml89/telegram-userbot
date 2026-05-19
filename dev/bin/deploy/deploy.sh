#!/bin/sh
set -eu

BRANCH="${1:-main}"

echo "🚀 Starting deployment..."

# Create www-data user if it doesn't exist on prod
if [ "$APP_ENV" = "prod" ] && ! id -u www-data > /dev/null 2>&1; then
    echo "📝 Creating www-data user..."
    useradd -r -s /usr/sbin/nologin -u 33 www-data || true
fi

# Deploy-safe sync strategy:
# - If the repository already exists on the server, we update it to match the remote branch exactly
#   using a hard reset (ensures no drift, no local modifications, and avoids broken refs like origin/main missing).
# - If the repository does not exist, we perform a fresh clone of the target branch.
#
# This approach replaces incremental fetch/merge workflows, which can become inconsistent in CI/CD environments
# due to stale references, branch renames (e.g. master → main), or partial states on the server.
#
# The goal is deterministic deployments: the server filesystem always reflects the exact state of the remote branch.
echo "🔄 Syncing repository (branch: $BRANCH)..."
cd /home/tbot

if [ -d telegram-userbot ]; then
    cd telegram-userbot
    git fetch origin
    git checkout "$BRANCH"
    git reset --hard origin/"$BRANCH"
else
    git clone -b "$BRANCH" git@github.com:olml89/telegram-userbot.git telegram-userbot
    cd telegram-userbot
fi

# Run installation recipe
make install

echo "⏳ Waiting for containers to be ready..."
docker compose exec -T postgres sh -c 'until pg_isready -U $POSTGRES_USER; do sleep 1; done'
docker compose exec -T backend sh -c 'until php -r "exit(0);" 2>/dev/null; do sleep 1; done'

echo "🔄 Running database migrations..."
docker compose exec -T backend bin/console doctrine:migrations:migrate --no-interaction

echo "🧹 Clearing Symfony cache..."
docker compose exec -T backend bin/console cache:clear --env=prod

echo "✅ Deployment completed successfully!"
