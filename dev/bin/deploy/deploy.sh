#!/bin/sh
set -eu

BRANCH="${1:-main}"

echo "🚀 Starting deployment..."

# Create www-data user if it doesn't exist on prod
if [ "$APP_ENV" = "prod" ] && ! id -u www-data > /dev/null 2>&1; then
    echo "📝 Creating www-data user..."
    useradd -r -s /usr/sbin/nologin -u 33 www-data || true
fi

echo "🔄 Syncing repository (branch: $BRANCH)..."
git fetch origin
git checkout "$BRANCH"
git reset --hard origin/"$BRANCH"

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
