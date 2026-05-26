#!/bin/sh
set -eu

# Runs pint
#
# Usage:
#   pint.sh [SERVICES...] [--test]
#
# Arguments:
# 	[SERVICES...] 	The services to analyse (application, bot-runtime, bot, bot-manager, backend, dev)
#
# Options:
#   --test			Only show the suggested code changes to follow the PER coding style, without applying them

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

SERVICES="${SERVICES:-application bot-runtime bot bot-manager backend}"

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

    printf '🔍 [pint][%s] %s\n' "$SERVICE" "$*"
    "$@"
}

for SERVICE in $SERVICES; do
    run_pint "$SERVICE"
done
