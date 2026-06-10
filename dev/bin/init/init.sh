#!/bin/sh
set -eu

# Reinitializes the application by recreating containers
#
# It will:
# - Destroy existing containers
# - Start the containers again
#
# Usage:
#   init.sh [--reset-deps] [--reset-cache] [--build]
#
# Options:
#   --reset-deps    Remove mounted node_modules and vendor directories
#                   (not applicable in production)
#
#   --reset-cache   Remove mounted var mounted directory
#                   (not applicable in production)
#
#   --build         Rebuild containers before starting them

BUILD=false
RESET_DEPS=false
RESET_CACHE=false

SERVICES="
application
bot-runtime
bot
bot-manager
backend
dev
vite
"

for arg in "$@"; do
    case "$arg" in
        --build)
            BUILD=true
            ;;
        --reset-deps)
            if [ "$APP_ENV" != "dev" ]; then
                echo "❌ The --reset-deps flag can only be applied in dev"
                exit 1
            fi
            RESET_DEPS=true
            ;;
        --reset-cache)
            if [ "$APP_ENV" != "dev" ]; then
                echo "❌ The --reset-cache flag can only be applied in dev"
                exit 1
            fi
            RESET_CACHE=true
            ;;
        *)
            echo "❌ Unknown argument: $arg"
            exit 1
            ;;
    esac
done

reset_deps() {
    echo "🔧 Deleting dependency directories..."

    for SERVICE in $SERVICES; do
        if [ -e "${SERVICE:?}/node_modules" ] && rm -rf "${SERVICE:?}/node_modules"; then
            echo "Deleted: ${SERVICE:?}/node_modules"
        fi
        if [ -e "${SERVICE:?}/vendor" ] && rm -rf "${SERVICE:?}/vendor"; then
            echo "Deleted: ${SERVICE:?}/vendor"
        fi
    done
}

reset_cache() {
    echo "🔧 Deleting cache directories..."

    for SERVICE in $SERVICES; do
        if [ -e "${SERVICE:?}/var" ] && rm -rf "${SERVICE:?}/var"; then
            echo "Deleted: ${SERVICE:?}/var"
        fi
    done
}

just down

if $RESET_DEPS; then
    reset_deps
fi

if $RESET_CACHE; then
    reset_cache
fi

if $BUILD; then
    just build
fi

just upd
