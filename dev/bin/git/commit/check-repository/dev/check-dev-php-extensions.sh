#!/bin/sh
set -eu

# Checks if the require section of the dev/composer.json is in sync with required php extensions from the services
#
# Usage:
#   check-dev-php-extensions.sh [-f]
#
# Options:
#   -f     Force update the dev/composer.json with the missing php extensions from services
#          Automatically update composer.lock, and add composer.json and composer.lock to the git staged files

PROJECT_ROOT=$(cd "$(dirname "$0")/../../../../../.." && pwd)
FORCE_UPDATE=false;

while [ $# -gt 0 ]; do
    case $1 in
        -f)
            FORCE_UPDATE=true
            ;;
        *)
            echo "❌ Unknown option: $1"
            exit 1
            ;;
    esac
    shift
done

if $FORCE_UPDATE; then
    ./bin/git/commit/check-repository/dev/check-dev-php-extensions.php -f

    # Update dev/composer.lock, git add dev/composer.json and dev/composer.lock
    if ! git -C "$PROJECT_ROOT" diff --quiet dev/composer.json 2>/dev/null; then
        set -- composer update \
            --lock \
            --no-interaction \
            --working-dir="$PROJECT_ROOT/dev"

        printf '📦 [update-dev-php-extensions.sh] %s\n' "$*"
        "$@"

        set -- git \
            -C "$PROJECT_ROOT" \
            add dev/composer.json dev/composer.lock

        printf '📦 [update-dev-php-extensions.sh] %s\n' "$*"
        "$@"
    fi
else
    ./bin/git/commit/check-repository/dev/check-dev-php-extensions.php
fi
