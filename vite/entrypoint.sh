#!/bin/sh
set -eu

# The WORKDIR is /telegram-userbot/backend
# This container has backend mounted inside, so this installs npm dependencies from backend
echo "🔧 Installing npm dependencies (including dev for Vite hot-reload)..."
npm install --include=dev

echo "✅ Container up [vite]."
exec npm run dev -- --host 0.0.0.0 --port 5173
