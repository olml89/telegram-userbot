#!/usr/bin/env sh
set -eu

echo "🔧 Creating /telegram-userbot/shared/var/log/postgres..."
mkdir -p /telegram-userbot/shared/var/log/postgres
chown -R postgres:postgres /telegram-userbot/shared/var/log/postgres

echo "✅ Container up [postgres]."
exec /usr/local/bin/docker-entrypoint.sh postgres "$@"
