#!/usr/bin/env sh
set -eu

echo "🔧 Creating /telegram-userbot/shared/var/log/redis..."
mkdir -p /telegram-userbot/shared/var/log/redis
chown -R redis:redis /telegram-userbot/shared/var/log/redis

echo "✅ Container up [redis-server]."
exec su -s /bin/sh -c "redis-server /usr/local/etc/redis/redis.conf" redis
