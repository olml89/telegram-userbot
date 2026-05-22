#!/bin/sh
set -eu

SERVICES=""
TEST=false

while [ $# -gt 0 ]; do
    case $1 in
        application|bot-runtime|bot|bot-manager|backend)
            SERVICES="$SERVICES $1"
            ;;
        --test)
            TEST=true
            ;;
        *)
            echo "❌ Unknown option: $1"
            exit 1
            ;;
    esac
    shift
done

if [ -z "$SERVICES" ]; then
    SERVICES="application bot-runtime bot bot-manager backend"
fi

run_pint() {
    SERVICE=$1

    # Dynamic codebase path
    CODE_PATH="/telegram-userbot/$SERVICE"

    # Dynamic config file
    CONFIG="$CODE_PATH/pint.json"

    # Enable Opcache
    #
    # The --ansi flag forces colored output, even when the command is run in a non-interactive shell
    # (e.g., from within a Git hook). This helps maintain readable output with syntax highlighting.
    set -- php -n -c "/usr/local/etc/php/docker-php-ext-opcache.ini" \
        ./vendor/bin/pint \
        --ansi \
        --config="$CONFIG" \
        "$CODE_PATH"

    if $TEST; then
        set -- "$@" --test
    fi

    printf '🔍 [%s] %s\n' "$SERVICE" "$*"

    if ! "$@"; then
        echo "❌ pint found errors in $SERVICE"
        exit 1
    fi
}

for SERVICE in $SERVICES; do
    run_pint "$SERVICE"
done

exit 0
