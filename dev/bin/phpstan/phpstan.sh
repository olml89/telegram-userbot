#!/bin/sh
set -eu

# Runs phpstan
#
# Usage:
#   phpstan.sh [SERVICES...] [--no-progress]
#
# Arguments:
# 	[SERVICES...] 	The services to analyse (application, bot-runtime, bot, bot-manager, backend, dev)
#
# Options:
#   --no-progress	Remove mounted node_modules, var, and vendor directories
#             		(not applicable in production)

SERVICES=""
NO_PROGRESS=false

while [ $# -gt 0 ]; do
    case $1 in
        application|bot-runtime|bot|bot-manager|backend)
            SERVICES="$SERVICES $1"
            ;;
        --no-progress)
            NO_PROGRESS=true
            ;;
        *)
            echo "❌ Unknown option: $1"
            exit 1
            ;;
    esac
    shift
done

run_integration_analysis() {
    # Integration config file
    CONFIG="/telegram-userbot/dev/bin/phpstan/phpstan.integration.neon"

    # Enable Opcache
    #
    # The --ansi flag forces colored output, even when the command is run in a non-interactive shell
    # (e.g., from within a Git hook). This helps maintain readable output with syntax highlighting.
    set -- php -n -c "/usr/local/etc/php/docker-php-ext-opcache.ini" \
        -d memory_limit=4G \
        ./vendor/bin/phpstan analyse \
        --ansi \
        --configuration="$CONFIG"

    if $NO_PROGRESS; then
        set -- "$@" --no-progress
    fi

    printf '🔍 [phpstan][integration] %s\n' "$*"
    "$@"
}

run_service_analysis() {
    SERVICE=$1

    # Dynamic config file
    CONFIG="/telegram-userbot/$SERVICE/phpstan.neon"

    # Enable Opcache
    #
    # The --ansi flag forces colored output, even when the command is run in a non-interactive shell
    # (e.g., from within a Git hook). This helps maintain readable output with syntax highlighting.
    set -- php -n -c "/usr/local/etc/php/docker-php-ext-opcache.ini" \
        -d memory_limit=4G \
        ./vendor/bin/phpstan analyse \
        --ansi \
        --configuration="$CONFIG" \

    if $NO_PROGRESS; then
        set -- "$@" --no-progress
    fi

    printf '🔍 [phpstan][%s] %s\n' "$SERVICE" "$*"
    "$@"
}

if [ -z "$SERVICES" ]; then
    run_integration_analysis
else
    for SERVICE in $SERVICES; do
        run_service_analysis "$SERVICE"
    done
fi
