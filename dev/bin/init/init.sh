#!/bin/sh
set -eu

# Reinitializes the application by recreating containers and required runtime directories.
#
# It will:
# - Destroy existing containers
# - Create required runtime directories
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

setup_runtime_directories() {
    echo "🔧 Initializing runtime directories..."

    # Service var directories
    DIRECTORIES="bot/var"
    DIRECTORIES="${DIRECTORIES} bot-manager/var"
    DIRECTORIES="${DIRECTORIES} backend/var backend/var/cache"

    if [ "$APP_ENV" = "dev" ]; then
        # Runtime directories. On prod they are on named volumes
        DIRECTORIES="${DIRECTORIES} .runtime/uploads .runtime/content"

    	# Var directories for libraries, needed for static analysis on local development
    	DIRECTORIES="${DIRECTORIES} application/var"
    	DIRECTORIES="${DIRECTORIES} bot-runtime/var"

    	# Var directories for containers needed on local development
        DIRECTORIES="${DIRECTORIES} dev/var dev/var/npm"
        DIRECTORIES="${DIRECTORIES} vite/var vite/var/npm"
    fi

    for DIRECTORY in $DIRECTORIES; do
        if [ ! -d "$DIRECTORY" ] && mkdir -p "$DIRECTORY"; then
            echo "Created: $DIRECTORY"
        fi
    done
}

just down

if $RESET; then
    reset_cache_directories
fi

setup_runtime_directories

if $BUILD; then
    just build
fi

just upd
