#!/bin/sh
set -eu

# It checks if the dependencies of a service are in sync:
# 	composer.json 	<-> 	composer.lock 		(application, bot, bot-runtime, bot-manager, backend, dev)
# 	package.json 	<-> 	package-lock.json	(backend)
#
# Usage:
#   check-dependencies-sync.sh [SERVICES...]

PROJECT_ROOT="$(cd "$(dirname "$0")/../../../../../.." && pwd)"
SERVICES="${*:-application bot-runtime bot bot-manager backend dev}"

check_composer_dependencies() {
    SERVICE="$1"
  	DIRECTORY="$PROJECT_ROOT/$1"

    if [ ! -f "$DIRECTORY/composer.json" ]; then
        echo "❌ $DIRECTORY missing composer.json"

        exit 1
    fi

    if [ ! -f "$DIRECTORY/composer.lock" ]; then
        echo "❌ $DIRECTORY missing composer.lock"

        exit 1
    fi

    set -- composer validate \
        --ansi \
        --check-lock \
        --no-check-all \
        --no-interaction \
        --working-dir="$DIRECTORY"

    printf '🔍 [check-dependencies-sync.sh][%s] %s\n' "$SERVICE" "$*"
    "$@"

    if [ ! -f "$DIRECTORY/Dockerfile" ]; then
        return
    fi

    ./bin/git/commit/check-repository/dependencies/check-dockerfile-sync.php "$SERVICE"
}

check_npm_dependencies() {
    SERVICE="$1"
  	DIRECTORY="$PROJECT_ROOT/$SERVICE"

    if [ ! -f "$DIRECTORY/package.json" ]; then
        echo "❌ $DIRECTORY missing package.json"

        exit 1
    fi

    if [ ! -f "$DIRECTORY/package-lock.json" ]; then
        echo "❌ $DIRECTORY missing package-lock.json"

        exit 1
    fi

    set -- npm \
        --prefix \
        "$DIRECTORY" \
        ci --dry-run

    printf '🔍 [check-dependencies-sync.sh][%s] %s\n' "$SERVICE" "$*"
}

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
