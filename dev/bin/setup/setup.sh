#!/bin/sh
set -eu

echo "🔧 Initializing runtime directories..."

if [ "$APP_ENV" != "prod" ]; then
	mkdir -p .runtime/uploads
	mkdir -p .runtime/content
	mkdir -p dev/var
	mkdir -p vite/var vite/var/npm
fi

mkdir -p application/var
mkdir -p backend/var backend/var/cache
mkdir -p bot/var
mkdir -p bot-manager/var
mkdir -p bot-runtime/var

echo "✅ Runtime ready"
