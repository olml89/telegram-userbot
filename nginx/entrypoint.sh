#!/bin/sh
set -e

echo "🔧 Creating /telegram-userbot/shared/var/log/nginx..."
mkdir -p /telegram-userbot/shared/var/log/nginx

echo "✅ Container up [nginx]."
exec nginx -g 'daemon off;'
