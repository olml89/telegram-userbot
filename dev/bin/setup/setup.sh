#!/bin/sh
set -eu

echo "🔧 Initializing runtime directories..."

# Backend file managing directories
if [ "$APP_ENV" != "prod" ]; then
	mkdir -p .runtime/uploads
	mkdir -p .runtime/content
fi

# var directories
mkdir -p application/var
mkdir -p backend/var
mkdir -p backend/var/cache
mkdir -p bot/var
mkdir -p bot-manager/var
mkdir -p bot-runtime/var

echo "✅ Runtime ready"
