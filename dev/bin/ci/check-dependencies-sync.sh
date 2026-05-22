#!/bin/sh
set -eu

# Checks if any service has synced dependencies:
# - If it is a PHP repository: composer.json  <-> composer.lock
# - If it is a npm repository: package.json   <-> package-lock.json
#
# Usage:
#   check-dependencies-sync.sh [SERVICES...]

SERVICES="${1:-}"
PROJECT_ROOT="$(cd "$(dirname "$0")/../../.." && pwd)"
EXIT=0

check_composer_dependencies() {
    SERVICE="$1"
  	DIRECTORY="$PROJECT_ROOT/$1"

    if [ ! -f "$DIRECTORY/composer.json" ]; then
        echo "❌ $DIRECTORY missing composer.json"
        EXIT=1

        return
    fi

    if [ ! -f "$DIRECTORY/composer.lock" ]; then
        echo "❌ $DIRECTORY missing composer.lock"
        EXIT=1

        return
    fi

    set -- composer install \
        --dry-run \
        --no-interaction \
        --working-dir="$DIRECTORY"

    printf '🔍 [%s>composer dependencies] %s\n' "$SERVICE" "$*"

    if ! "$@"; then
        echo "❌ $DIRECTORY/composer.json is not in sync with $DIRECTORY/composer.lock"
        EXIT=1
    else
        echo "✅ $DIRECTORY/composer.json is in sync with $DIRECTORY/composer.lock"
    fi
}

check_npm_dependencies() {
    SERVICE="$1"
  	DIRECTORY="$PROJECT_ROOT/$SERVICE"

    if [ ! -f "$DIRECTORY/package.json" ]; then
        echo "❌ $DIRECTORY missing package.json"
        EXIT=1

        return
    fi

    if [ ! -f "$DIRECTORY/package-lock.json" ]; then
        echo "❌ $DIRECTORY missing package-lock.json"
        EXIT=1

        return
    fi

    set -- npm \
        --prefix \
        "$DIRECTORY" \
        ci --dry-run

    printf '🔍 [%s>npm dependencies] %s\n' "$SERVICE" "$*"

    if ! "$@"; then
        echo "❌ $DIRECTORY/composer.json is not in sync with $DIRECTORY/composer.lock"
        EXIT=1
    else
        echo "✅ $DIRECTORY/composer.json is in sync with $DIRECTORY/composer.lock"
    fi
}

if [ -z "$SERVICES" ]; then
    SERVICES="application bot-runtime bot bot-manager backend dev"
fi

for SERVICE in $SERVICES; do
    case $SERVICE in
        application|bot-runtime|bot|bot-manager|dev)
            check_composer_dependencies "$SERVICE"
            ;;
        backend)
            check_composer_dependencies "$SERVICE"
            check_npm_dependencies "$SERVICE"
            ;;
        *)
            echo "❌ Unknown service: $1"
            exit 1
            ;;
    esac
done

exit $EXIT
