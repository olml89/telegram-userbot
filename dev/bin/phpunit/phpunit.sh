#!/bin/sh

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

if [ -z "$SERVICES" ]; then
    SERVICES="application bot-runtime bot bot-manager backend"
fi

run() {
    if $DEBUG; then
        XDEBUG_TRIGGER=1 "$@"
    else
        "$@"
    fi
}

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

    [ -n "$FILTER" ] && set -- "$@" --filter "$FILTER"
    $COVERAGE_TEXT && set -- "$@" --coverage-text
    $COVERAGE_CLOVER && set -- "$@" --coverage-clover var/clover.xml

    printf '🔍 [%s] %s%s\n' "$SERVICE" "$XDEBUG_TRIGGER_FLAG" "$*"

    if ! run "$@"; then
        echo "❌ phpunit found errors in $SERVICE"
        exit 1
    fi
}

for SERVICE in $SERVICES; do
    run_phpunit "$SERVICE"
done

exit 0
