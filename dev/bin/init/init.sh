#!/bin/sh
set -eu

# Reinitializes the application by recreating containers
#
# It will:
# - Destroy existing containers
# - Start the containers again
#
# Usage:
#   init.sh [--reset] [--build]
#
# Options:
#   --reset   Remove mounted node_modules, var, and vendor directories
#             (not applicable in production)
#
#   --build   Rebuild containers before starting them

BUILD=false
RESET=false

for arg in "$@"; do
    case "$arg" in
        --build)
            BUILD=true
            ;;
        --reset)
            if [ "$APP_ENV" != "dev" ]; then
                echo "❌ The --reset flag can only be applied in dev"
                exit 1
            fi
            RESET=true
            ;;
        *)
            echo "❌ Unknown argument: $arg"
            exit 1
            ;;
    esac
done

reset_cache_directories() {
    echo "🔧 Setting application in a factory reset state..."

    SERVICES="application bot-runtime bot bot-manager backend vite dev"
    DIRECTORIES="node_modules var vendor"

    for SERVICE in $SERVICES; do
        for DIRECTORY in $DIRECTORIES; do
            CURRENT_TARGET="$SERVICE/$DIRECTORY"

            if [ -e "$CURRENT_TARGET" ] && rm -rf "$CURRENT_TARGET"; then
                echo "Deleted: $CURRENT_TARGET"
            fi
        done
    done
}

just down

if $RESET; then
    reset_cache_directories
fi

if $BUILD; then
    just build
fi

just upd
