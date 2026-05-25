#!/bin/sh
set -eu

echo "🔧 Installing dependencies (environment: ${APP_ENV})"

if [ "$APP_ENV" = "prod" ]; then
	echo "❌ Dependencies cannot be dynamically installed in prod environment"
	exit 1
fi

if [ $# -eq 0 ]; then
  echo "❌ No services provided"
  exit 1
fi

for SERVICE in "$@"; do
	case "$SERVICE" in
		application|bot-runtime|bot|bot-manager|backend|dev)
			WORKING_DIR="/telegram-userbot/$SERVICE";

			if [ -d "$WORKING_DIR/vendor" ]; then
				echo "✅ Dependencies already installed in $SERVICE"
				continue;
			fi

			echo "🔧 Installing dependencies in $SERVICE..."

			# Build arguments
            set -- composer install \
                --no-interaction \
                --no-progress \
                --optimize-autoloader \
                --prefer-dist \
                --working-dir="$WORKING_DIR"

			if [ "$APP_ENV" = "ci" ]; then
				set -- "$@" --ignore-platform-reqs
			fi

            printf '🔍 [%s] %s\n' "$SERVICE" "$*"
            "$@"
			;;
		*)
			echo "❌ Unknown service: $SERVICE"
			exit 1
			;;
	esac
done

echo "✅ Dependencies successfully installed"
