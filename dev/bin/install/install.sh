#!/bin/sh
set -eu

BUILD=false
RESET=false

for arg in "$@"; do
    echo "DEBUG arg=$arg"
    case "$arg" in
        --build)
            BUILD=true
            ;;
        --reset)
            RESET=true
            ;;
        *)
            echo "❌ Unknown argument: $arg"
            exit 1
            ;;
    esac
done

setup_runtime_directories() {
    echo "🔧 Initializing runtime directories..."

    # Service var directories
    DIRECTORIES="bot/var"
    DIRECTORIES="${DIRECTORIES} bot-manager/var"
    DIRECTORIES="${DIRECTORIES} backend/var backend/var/cache"

    if [ "$APP_ENV" != "prod" ]; then
        # File runtime directories. On prod they are on telegram-userbot-uploads and telegram-userbot-content
        # named volumes
        DIRECTORIES="${DIRECTORIES} .runtime/uploads .runtime/content"

    	# Var directories for libraries, needed for static analysis on local development
    	DIRECTORIES="${DIRECTORIES} application/var"
    	DIRECTORIES="${DIRECTORIES} bot-runtime/var"

    	# Var directories for containers needed on local development
        DIRECTORIES="${DIRECTORIES} dev/var"
        DIRECTORIES="${DIRECTORIES} vite/var vite/var/npm"
    fi

    for DIRECTORY in $DIRECTORIES; do
        if [ ! -d "$DIRECTORY" ] && mkdir -p "$DIRECTORY"; then
            echo "Created: $DIRECTORY"
        fi
    done
}

reset_mounted_directories() {
    echo "🔧 Setting application in a factory reset state..."

    SERVICES="application bot-runtime bot bot-manager backend vite dev"
    DIRECTORIES="composer.lock node_modules var vendor"

    for SERVICE in $SERVICES; do
        for DIRECTORY in $DIRECTORIES; do
            CURRENT_TARGET="$SERVICE/$DIRECTORY"

            if [ -e "$CURRENT_TARGET" ] && rm -rf "$CURRENT_TARGET"; then
                echo "Deleted: $CURRENT_TARGET"
            fi
        done
    done
}

make down

if [ "$APP_ENV" != "prod" ] && [ "$RESET" = true ]; then
    reset_mounted_directories
fi

setup_runtime_directories

if [ "$APP_ENV" = "prod" ] || [ "$BUILD" = true ]; then
    make build
fi

make upd
