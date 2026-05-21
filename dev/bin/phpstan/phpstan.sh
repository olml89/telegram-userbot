#!/bin/sh
set -eu

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

    $NO_PROGRESS && set -- "$@" --no-progress

    printf '🔍 [integration] %s\n' "$*"

    if ! "$@"; then
        echo "❌ phpstan found errors in the integration analysis"
        exit 1
    fi
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

    $NO_PROGRESS && set -- "$@" --no-progress

    printf '🔍 [%s] %s\n' "$SERVICE" "$*"

    if ! "$@"; then
        echo "❌ phpstan found errors in $SERVICE"
        exit 1
    fi
}

if [ -z "$SERVICES" ]; then
    run_integration_analysis
else
    for SERVICE in $SERVICES; do
        run_service_analysis "$SERVICE"
    done
fi

exit 0
