#!/bin/sh
set -eu

BRANCH="${1:-main}"

echo "🚀 Starting deployment (branch: $BRANCH)..."
git fetch origin
git checkout "$BRANCH"
git reset --hard origin/"$BRANCH"

# Install: re-create the containers
if [ "$APP_ENV" = "prod" ]; then
    make install --build
else
    make install --reset
fi

# Setup: run database migrations and clean Symfony cache
make setup
