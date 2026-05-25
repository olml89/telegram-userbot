#!/bin/sh
set -eu

# 1) It checks if staged files have CRLF line endings
# 2) It checks if dependencies are in sync (application, bot-runtime, bot, bot-manager, backend)
# 3) It checks if the require section of the dev/composer.json is synced with required php extensions from the services
# 4) It checks if the dependencies are in sync (dev)
#
# Usage:
#   check-repository.sh [-f]
#
# Options:
#   -f     Automatically convert CRLF line endings to LF and adds modified files to the git staged files
#          Force update the dev/composer.json with the missing php extensions from services
#          Automatically update composer.lock, and add composer.json and composer.lock to the git staged files

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

# 1) Check if staged files have CRLF line endings
set -- ./bin/git/commit/check-repository/check-staged-files-line-endings.sh

if $FORCE_UPDATE; then
    set -- "$@" -f
fi

"$@"

# 2) Check if dependencies are in sync (application, bot-runtime, bot, bot-manager, backend)
./bin/git/commit/check-repository/dependencies/check-dependencies-sync.sh \
    application \
    bot-runtime \
    bot \
    bot-manager \
    backend

# 3) Check if the require section of the dev/composer.json is synced with required php extensions from the services
set -- ./bin/git/commit/check-repository/dev/check-dev-php-extensions.sh

if $FORCE_UPDATE; then
    set -- "$@" -f
fi

"$@"

# 4) Check if the dependencies are in sync (dev)
./bin/git/commit/check-repository/dependencies/check-dependencies-sync.sh dev
