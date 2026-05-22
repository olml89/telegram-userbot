#!/bin/sh
set -eu

# typecheck is a script defined in the backend/package.json.
#
# TypeScript resolves node_modules and tsconfig.json relative to the execution context (cwd).
# The dev container runs from /telegram-userbot/dev, while the frontend workspace lives in /telegram-userbot/backend,
# so the npm script ensures tsc executes within the correct workspace context.
if ! npm --prefix /telegram-userbot/backend run typecheck; then
    echo "❌ tsc found errors"
    exit 1
fi

exit 0
