#!/bin/sh
set -eu

# Runs tsc
#
# Usage:
#   tsc.sh [SERVICES...] [--noEmit]
#
# Arguments:
# 	[SERVICES...] 	The services to analyse (application, bot-runtime, bot, bot-manager, backend, dev)
#
# Options:
#   --noEmit		Only execute type checks, without compiling

SERVICES=""
NO_EMIT=false

while [ $# -gt 0 ]; do
    case $1 in
        backend)
            SERVICES="$SERVICES $1"
            ;;
        --noEmit)
            NO_EMIT=true
            ;;
        *)
            echo "❌ Unknown option: $1"
            exit 1
            ;;
    esac
    shift
done

SERVICES="${SERVICES:-backend}"

run_tsc() {
    SERVICE=$1

    # type-check is a script defined in the backend/package.json.
    #
    # TypeScript resolves node_modules and tsconfig.json relative to the execution context (cwd).
    # The dev container runs from /telegram-userbot/dev, while the frontend workspace lives in /telegram-userbot/backend,
    # so the npm script ensures tsc executes within the correct workspace context.
    set -- npm \
        --prefix \
        "/telegram-userbot/$SERVICE" \
        run type-check --

    if $NO_EMIT; then
        set -- "$@" --noEmit
    fi

    printf '🔍 [tsc][%s] %s\n' "$SERVICE" "$*"
    "$@"
}

for SERVICE in $SERVICES; do
    run_tsc "$SERVICE"
done
