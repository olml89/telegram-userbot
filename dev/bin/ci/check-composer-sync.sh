#!/bin/sh
set -eu

# Checks if any service has synced composer.json and composer.lock files.
#
# Usage:
#   check-composer-sync.sh -> Will check all the services
#   check-composer-sync.sh <service> [service...] -> Will check a particular service

if ! command -v composer >/dev/null 2>&1; then
	echo "❌ composer is not installed"
	exit 1
fi

PROJECT_ROOT="$(cd "$(dirname "$0")/../../.." && pwd)"
SERVICES=""
EXIT=0

while [ $# -gt 0 ]; do
	case $1 in
		application|bot-runtime|bot|bot-manager|backend|dev)
			if [ -z "$SERVICES" ]; then
				SERVICES="$1"
			else
				SERVICES="$SERVICES $1"
			fi
			;;
		*)
			echo "❌ Unknown service: $1"
			exit 1
			;;
	esac
	shift
done

check_composer() {
  	SERVICE="$PROJECT_ROOT/$1"
    echo "🔍 Checking $SERVICE..."

    if [ ! -f "$SERVICE/composer.json" ]; then
        echo "❌ $SERVICE missing composer.json"
        EXIT=1

        return
    fi

    if [ ! -f "$SERVICE/composer.lock" ]; then
        echo "❌ $SERVICE missing composer.lock"
        EXIT=1

        return
    fi

    if ! composer install \
        --dry-run \
        --no-interaction \
        --working-dir="$SERVICE"
    then
        echo "❌ $SERVICE/composer.json is not in sync with $SERVICE/composer.lock"
        EXIT=1
    else
        echo "✅ $SERVICE/composer.json is in sync with $SERVICE/composer.lock"
    fi
}

if [ -z "$SERVICES" ]; then
    SERVICES="application bot-runtime bot bot-manager backend dev"
fi

for SERVICE in $SERVICES; do
    check_composer "$SERVICE"
done

exit $EXIT
