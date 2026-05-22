#!/bin/sh
set -eu

NO_EMIT=false

while [ $# -gt 0 ]; do
    case $1 in
        application|bot-runtime|bot|bot-manager|backend)
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

# type-check is a script defined in the backend/package.json.
#
# TypeScript resolves node_modules and tsconfig.json relative to the execution context (cwd).
# The dev container runs from /telegram-userbot/dev, while the frontend workspace lives in /telegram-userbot/backend,
# so the npm script ensures tsc executes within the correct workspace context.
set -- npm \
    --prefix \
    /telegram-userbot/backend \
    run type-check --

if $NO_EMIT; then
    set -- "$@" --noEmit
fi

printf '🔍 [backend] %s\n' "$*"

if ! "$@"; then
    echo "❌ tsc found errors"
    exit 1
fi
