#!/bin/sh
set -eu

PROJECT_ROOT="$(cd "$(dirname "$0")/../../.." && pwd)"
EXIT=0

check_composer() {
  	SERVICE="$PROJECT_ROOT/$1"
    echo "🔍 Checking $SERVICE..."

    if [ ! -f "$SERVICE/composer.json" ]; then
        echo "❌ $SERVICE missing composer.json"
        EXIT=1

        return
    fi

    if [ ! -f "$SERVICE/composer.lock" ]; then
        echo "❌ $SERVICE missing composer.lock"
        EXIT=1

        return
    fi

    if ! composer install \
        --dry-run \
        --no-interaction \
        --working-dir="$SERVICE"
    then
        echo "❌ $SERVICE/composer.json is not in sync with $SERVICE/composer.lock"
        EXIT=1
    else
        echo "✅ $SERVICE/composer.json is in sync with $SERVICE/composer.lock"
    fi
}

check_composer "application"
check_composer "bot-runtime"
check_composer "bot"
check_composer "bot-manager"
check_composer "backend"
check_composer "dev"

exit $EXIT
