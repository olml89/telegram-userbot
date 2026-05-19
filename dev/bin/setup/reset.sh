#!/bin/sh
set -eu

if [ "$APP_ENV" = "prod" ]; then
    echo "❌ This command cannot be run in prod";
    exit 1;
fi

echo "🔧 Setting application in a factory reset state..."

SERVICES="application bot-runtime bot bot-manager backend vite dev"
TARGETS="composer.lock node_modules package-lock.json var vendor"

for SERVICE in $SERVICES; do
    for TARGET in $TARGETS; do
        CURRENT_TARGET="$SERVICE/$TARGET"

        # Check it it exists, it can be both a file or a directory
        if [ -e "$CURRENT_TARGET" ]; then
            echo "Deleting $CURRENT_TARGET"
            rm -rf "$CURRENT_TARGET"
        fi
    done
done
