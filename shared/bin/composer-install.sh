#!/bin/sh
set -e

echo "🔧 Installing dependencies..."
echo "🔧 Environment: ${APP_ENV}..."

if [ "$APP_ENV" != "production" ]; then
	for SERVICE in "$@"; do
        case "$SERVICE" in
            dev|shared|backend|bot|bot-manager)
				COMPOSER_CMD="composer install --no-interaction --no-progress --prefer-dist --optimize-autoloader --working-dir=/telegram-userbot/$SERVICE"

				if [ "$APP_ENV" = "ci" ]; then
					COMPOSER_CMD="$COMPOSER_CMD --ignore-platform-reqs"
				fi

				echo "🔧 $COMPOSER_CMD"
				eval "$COMPOSER_CMD"
                ;;
            *)
                echo "❌ Unknown service: $SERVICE"
                exit 1
                ;;
        esac
    done
fi

echo "✅ Dependencies already installed"
exit 0
