#!/bin/sh
set -e

echo "🔧 Environment: ${APP_ENV}..."

if [ "$APP_ENV" = "production" ]; then
	echo "✅ Dependencies already installed"
else
	echo "🔧 composer install..."
	composer install --no-interaction --no-progress
fi

echo "✅ Dependencies already installed"
