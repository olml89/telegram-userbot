#!/bin/sh
set -eu

echo "🚀 Starting deployment..."

# Restart containers
make down
make build
make upd

echo "⏳ Waiting for containers to be ready..."
docker compose exec -T postgres sh -c 'until pg_isready -U $POSTGRES_USER; do sleep 1; done'
docker compose exec -T backend sh -c 'until php -r "exit(0);" 2>/dev/null; do sleep 1; done'

echo "🔄 Running database migrations..."
docker compose exec -T backend bin/console doctrine:migrations:migrate --no-interaction

echo "🧹 Clearing Symfony cache..."
docker compose exec -T backend bin/console cache:clear --env=prod

echo "✅ Deployment completed successfully!"
