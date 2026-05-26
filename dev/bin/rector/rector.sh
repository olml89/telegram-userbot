#!/bin/sh
set -eu

# Runs rector
#
# Usage:
#   rector.sh [SERVICES...] [--dry-run]
#
# Arguments:
# 	[SERVICES...] 	The services to analyse (application, bot-runtime, bot, bot-manager, backend, dev)
#
# Options:
#   --dry-run		Only show the suggested refactorings, without applying them

SERVICES=""
DRY_RUN=false

while [ $# -gt 0 ]; do
    case $1 in
        application|bot-runtime|bot|bot-manager|backend)
            SERVICES="$SERVICES $1"
            ;;
        --dry-run)
            DRY_RUN=true
            ;;
        *)
            echo "❌ Unknown option: $1"
            exit 1
            ;;
    esac
    shift
done

SERVICES="${SERVICES:-application bot-runtime bot bot-manager backend}"

run_rector() {
    SERVICE=$1

    # Dynamic config file
    CONFIG="/telegram-userbot/$SERVICE/rector.php"

    # Enable Opcache
    #
    # The --ansi flag forces colored output, even when the command is run in a non-interactive shell
    # (e.g., from within a Git hook). This helps maintain readable output with syntax highlighting.
    set -- php -n -c "/usr/local/etc/php/docker-php-ext-opcache.ini" \
        ./vendor/bin/rector \
        --ansi \
        --config="$CONFIG" \

    if $DRY_RUN; then
        set -- "$@" --dry-run
    fi

    printf '🔍 [rector][%s] %s\n' "$SERVICE" "$*"
    "$@"
}

for SERVICE in $SERVICES; do
    run_rector "$SERVICE"
done
