#!/bin/sh
set -eu

# Runs phpunit
#
# Usage:
#   phpunit.sh [SERVICES...] [--filter EXPRESSION] [--debug] [--coverage-text] [--coverage-clover]
#
# Arguments:
# 	[SERVICES...] 	The services to analyse (application, bot-runtime, bot, bot-manager, backend, dev)
#
# Options:
#   --filter EXPRESSION		Run only tests that match the given expression in the given services
# 	--debug					Enable the ability to set breakpoints on tests
# 	--coverage-text			Add text coverage through the CLI
#	--coverage-clover		Add clover coverage (useful during CI/CD pipelines)

SERVICES=""
FILTER=""
DEBUG=false
COVERAGE_TEXT=false
COVERAGE_CLOVER=false

while [ $# -gt 0 ]; do
    case $1 in
        application|bot-runtime|bot|bot-manager|backend)
            SERVICES="$SERVICES $1"
            ;;
        --filter)
            shift
            FILTER="$1"
            ;;
        --debug)
            DEBUG=true
            ;;
        --coverage-text)
            COVERAGE_TEXT=true
            ;;
        --coverage-clover)
            COVERAGE_CLOVER=true
            ;;
        *)
            echo "❌ Unknown option: $1"
            exit 1
            ;;
    esac
    shift
done

SERVICES="${SERVICES:-application bot-runtime bot bot-manager backend}"

run_phpunit() {
    SERVICE=$1

    # Extension
    ($DEBUG || $COVERAGE_TEXT || $COVERAGE_CLOVER) \
        && EXTENSION="/usr/local/etc/php/docker-php-ext-xdebug.ini" \
        || EXTENSION="/usr/local/etc/php/docker-php-ext-opcache.ini"

    # Dynamic configuration file
    CONFIG="/telegram-userbot/$SERVICE/phpunit.xml.dist"

    # XDEBUG_TRIGGER flag
    ($DEBUG || $COVERAGE_TEXT || $COVERAGE_CLOVER) \
        && XDEBUG_TRIGGER_FLAG="XDEBUG_TRIGGER=1 " \
        || XDEBUG_TRIGGER_FLAG=

    # Build arguments
    set -- php -n -c "$EXTENSION" ./vendor/bin/phpunit \
        --colors=always \
        --configuration "$CONFIG"

    if  [ -n "$FILTER" ]; then
        set -- "$@" --filter "$FILTER"
    fi

    if $COVERAGE_TEXT; then
        set -- "$@" --coverage-text
    fi

    if $COVERAGE_CLOVER; then
        set -- "$@" --coverage-clover var/clover.xml
    fi

    printf '🔍 [phpunit][%s] %s%s\n' "$SERVICE" "$XDEBUG_TRIGGER_FLAG" "$*"

    if $DEBUG; then
        XDEBUG_TRIGGER=1 "$@"
    else
        "$@"
    fi
}

for SERVICE in $SERVICES; do
    run_phpunit "$SERVICE"
done
