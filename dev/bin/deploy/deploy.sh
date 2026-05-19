#!/bin/sh
set -eu

BRANCH="${1:-main}"

echo "🚀 Starting deployment (branch: $BRANCH)..."
git fetch origin
git checkout "$BRANCH"
git reset --hard origin/"$BRANCH"

# Install: build and spin up the containers
make install

# Setup: run database migrations and clean Symfony cache
make setup
